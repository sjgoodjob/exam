<template>
  <view>
    <block v-if="modal == true ? 'show' : ''">
      <view class="login-bg"></view>
      <view class="login-box">
        <view class="login-title">你还没登录</view>
        <text>请选择以下任一登录方式</text>
        <text>登录后再进行操作</text>

        <image src="/static/login.png" mode="widthFix"></image>
        
		<view>
			<!-- #ifdef MP-WEIXIN -->
			<view
			  class="login-btnbox"
			  style="
			    flex-direction: column;
			    align-items: center;
			  "
			>
			  <button
			    class="login-btn"
			    hover-class="none"
			    @tap="getUserProfile"
			    style="
			      background-color: #09ba07;
			      color: #fff;
			      width: 88%;
			      margin-bottom: 10px;
			    "
			    v-show="
			      loginChannel == 'wechat_and_account' || loginChannel == 'wechat'
			    "
			  >
			    微信登录
			  </button>
			
			  <button
			    class="login-btn"
			    @click="goLogin"
			    hover-class="none"
			    withCredentials="true"
			    style="background-color: #5677fc; color: #fff; width: 88%"
			    v-show="
			      loginChannel == 'wechat_and_account' || loginChannel == 'account'
			    "
			  >
			    账号登录
			  </button>
			</view>
			<!-- #endif -->
			
			<!-- #ifndef MP-WEIXIN -->
			<view
			  class="login-btnbox"
			  v-show="
			    loginChannel == 'wechat_and_account' || loginChannel == 'account'
			  "
			>
			  <view
			    class="login-btn"
			    v-on:click="goLogin"
			    style="
			      color: #333;
			      background-color: #5677fc;
			      color: #fff;
			      width: 88%;
			    "
			    >账号登录</view
			  >
			</view>
			<!-- #endif -->
			
			
			<view class="login-btnbox" style="margin-top:50rpx" v-if="loginChannel == 'wechat_and_account'">
			  <view
			    class="login-btn"
			    v-on:click="hideModal"
			    style="color: #333; background-color: #f1f1f1; width: 88%"
			    >暂不登录
			  </view>
			</view>
			
			<view class="login-btnbox" v-else>
			  <view
			    class="login-btn"
			    v-on:click="hideModal"
			    style="color: #333; background-color: #f1f1f1; width: 88%"
			    >暂不登录
			  </view>
			</view>
		</view>
      </view>
    </block>

    <wx-user-info-modal
      v-model="showAuthorizationModal"
      @updated="updatedUserInfoEvent"
    ></wx-user-info-modal>
    <kz-wx-privacy-check ref="kzWxPrivacyCheck"></kz-wx-privacy-check>
  </view>
</template>

<script>
import userApi from "@/common/api/user.js";
import WxUserInfoModal from "@/uni_modules/tuniaoui-wx-user-info/components/tuniaoui-wx-user-info/tuniaoui-wx-user-info.vue";

export default {
  name: "login",
  components: { WxUserInfoModal },
  props: {
    show: {
      default: true,
    },
    pid: {
      default: 0,
    },
  },
  data() {
    return {
      modal: false,
      showAuthorizationModal: false,
      afterMethod: null,
      // system: null,
      showRegBtn: true,
      showMobileRegBtn: false,
      mobile: "",
      loginChannel: "wechat_and_account",
    };
  },
  mounted() {
    if (this.show && !uni.getStorageSync("user")) {
      //this.modal = true;
    }

    // 获取当前页面栈的实例，以数组形式按栈的顺序给出，第一个元素为首页，最后一个元素为当前页面
    const pages = getCurrentPages();
    // 获取到当前页面的路径和参数
    const loginJumpPage = pages[pages.length - 1].$page.fullPath; //完整路由地址
    this.utils.setData("loginJumpPage", loginJumpPage);
    console.log("login first page", loginJumpPage);

    let system = uni.getStorageSync("system");
    console.log("login system", system);
    if (system) {
      if (
        system.open_mobile_register != undefined &&
        parseInt(system.open_mobile_register) === 1
      ) {
        this.showMobileRegBtn = true;
      }

      if (system.login_channel) {
        this.loginChannel = system.login_channel;
        console.log("this.loginChannel", this.loginChannel);
      }
    }

    // this.showAuthorizationModal = true;
  },
  methods: {
    goLogin() {
      this.utils.goto("/pages/user/login-reg");
    },
    async getUserProfile(e, phone = "") {
      let isPrivacySetting =
        await this.$refs.kzWxPrivacyCheck.getPrivacySetting("showPrivacy");
      console.log("isPrivacySetting", isPrivacySetting);
      if (isPrivacySetting) {
        var that = this;
        uni.showLoading({
          title: "登录中..",
          mask: true,
        });
        uni.getUserProfile({
          lang: "zh_CN",
          desc: "用于完善用户资料",
          success: (res) => {
            uni.login({
              provider: "weixin",
              success(e) {
                that.wxlogin(e.code, res.userInfo, phone);
              },
            });
          },
          fail: (err) => {
            uni.hideLoading();
            console.log(err);
          },
        });
      }
    },
    wxlogin(code, userInfo, phone = "") {
      let that = this;
      this.http(
        "user/login",
        {
          code: code,
          userInfo: userInfo,
          pid: this.pid,
          phone: phone,
        },
        "post"
      ).then((res) => {
        uni.hideLoading();
        if (res.code == 0) {
          uni.showModal({
            content: "请求失败：" + res.msg,
            showCancel: false,
            success: function () {
              if (callBack != undefined) {
                callBack();
              }
            },
          });
          uni.$emit("login_fail", "请求失败：" + res.msg);
        } else {
          if (res.data.user.status == "hidden") {
            uni.showModal({
              content: "账号已被禁用，请联系管理员重新开启！",
              showCancel: false,
              success: function () {
                if (callBack != undefined) {
                  callBack();
                }
              },
            });
            uni.$emit("login_fail", "账号已被禁用，请联系管理员重新开启！");
          } else {
            uni.setStorageSync("token", res.data.token);
            uni.setStorageSync("user", res.data.user);
            uni.$emit("login_success", res.data);

            this.modal = false;

            console.log("this.afterMethod", this.afterMethod);
            // 登录后执行之前的操作
            if (this.afterMethod) {
              this.afterMethod();

              // 置空
              this.afterMethod = null;
            }

            if (res.data.user.nickname == "微信用户") {
              this.showAuthorizationModal = true;
              console.log(
                "showAuthorizationModal",
                this.showAuthorizationModal
              );
            }

            this.$emit("succ", res.data);
          }
        }
      });
    },

    hideModal(e) {
      this.modal = false;
    },
    // 获取到的用户信息
    async updatedUserInfoEvent(info) {
      console.log("获取到的用户信息", info);

      let newAvatar = "";
      let uploadResult = "";

      // 上传头像
      if (info.avatar && info.avatar.indexOf("tmp") > 0) {
        await this.utils.uploadFile(this, info.avatar).then((res) => {
          if (res.code == 1) {
            newAvatar = res.data.fullurl;
            uploadResult = "";
          } else {
            uploadResult = res.msg;
          }
        });
      }

      console.log("uploadResult", uploadResult);
      console.log("newAvatar", newAvatar);
      if (uploadResult) {
        this.utils.alert(uploadResult);
        return;
      }

      let data = {
        nickname: info.nickname,
      };
      if (newAvatar) {
        data["avatar"] = newAvatar;
      }
      if (info.mobile) {
        data["mobile"] = info.mobile;
      }

      console.log("save user data", data);
      await userApi.save(this, data).then((res) => {
        console.log("user save res", res);
        if (res.code == 1) {
          uni.setStorageSync("user", this.user);
          this.showAuthorizationModal = false;
          uni.$emit("login_success", res.data);
          this.utils.alert("保存成功");
        } else {
          this.utils.alert(res.msg);
        }
      });
    },
  },
};
</script>

<style>
.login-bg {
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 990;
}

.login-box {
  width: 80%;
  height: auto;
  background-color: #ffffff;
  border-radius: 16rpx;
  box-sizing: border-box;
  padding: 50rpx 30rpx;
  text-align: center;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 991;
}

.login-title {
  font-weight: 500;
  color: #000000;
  padding-bottom: 10rpx;
  font-size: 16px;
}

.login-box > text {
  color: #999999;
  font-size: 28rpx;
  display: block;
}

.login-box > image {
  width: 80%;
  height: auto;
  margin: 20rpx 0 50rpx;
}

.login-btnbox {
  height: 100rpx;
  display: flex;
  justify-content: space-around;
  align-items: center;
}

button,
button::after {
  margin: 0;
  padding: 0;
  border: none;
  outline: none;
  border-radius: 0;
}

.login-btn,
button.login-btn {
  width: 200rpx;
  height: 80rpx;
  line-height: 80rpx;
  text-align: center;
  border-radius: 8rpx;
  color: #ffffff;
  font-size: 28rpx;
}

.login-page {
  width: 100vw;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* 授权按钮 */
.submit-btn {
  width: 100%;
  background-color: #05c160;
  color: #ffffff;
  margin-top: 60rpx;
  border-radius: 10rpx;
  padding: 25rpx;
  font-size: 32rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 30rpx;
}
</style>
