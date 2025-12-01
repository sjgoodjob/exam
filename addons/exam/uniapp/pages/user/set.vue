<template>
  <view class="page">
    <!-- 顶部自定义导航 -->
    <!-- <tn-nav-bar fixed alpha customBack>
			<view slot="back" class='tn-custom-nav-bar__back' @click="goBack">
				<text class='icon tn-icon-left-arrow'></text>
			</view>
		</tn-nav-bar> -->

    <!-- :style="{paddingTop: vuex_custom_bar_height + 'px'}" -->
    <view class="">
      <view class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding">
        <view class="justify-content-item">
          <view class="tn-text-bold tn-text-lg"> 用户头像 </view>
          <view class="tn-color-gray tn-padding-top-xs">
            有趣的头像，百里挑一
          </view>
        </view>
        <button
          class="avatar-wrapper"
          open-type="chooseAvatar"
          @chooseavatar="onChooseAvatar"
        >
          <view class="justify-content-item tn-text-lg tn-color-grey">
            <view class="logo-pic tn-shadow">
              <view class="logo-image">
                <view
                  class="tn-shadow-blur"
                  style="width: 80rpx; height: 80rpx; background-size: cover"
                  :style="{
                    backgroundImage: 'url(' + (user ? user.avatar : '') + ')',
                  }"
                >
                </view>
              </view>
            </view>
          </view>
        </button>
      </view>

      <view
        class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding"
        @click="showModalNickname"
      >
        <view class="justify-content-item">
          <view class="tn-text-bold tn-text-lg"> 用户昵称 </view>
          <view class="tn-color-gray tn-padding-top-xs">
            {{ nickname }}
          </view>
        </view>
        <view class="justify-content-item tn-text-lg tn-color-grey">
          <view class="tn-icon-right tn-padding-top"></view>
        </view>
      </view>

      <view
        class="tn-flex tn-flex-row-between tn-strip-bottom tn-padding"
        @click="showModalMobile"
        v-show="bindMobile > 0"
      >
        <view class="justify-content-item">
          <view class="tn-text-bold tn-text-lg"> 绑定手机号 </view>
          <view class="tn-color-gray tn-padding-top-xs">
            {{ mobile }}
          </view>
        </view>
        <view class="justify-content-item tn-text-lg tn-color-grey">
          <view class="tn-icon-right tn-padding-top"></view>
        </view>
      </view>

      <!-- <view class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding" @click="showModalRealName">
				<view class="justify-content-item">
					<view class="tn-text-bold tn-text-lg">
						姓名
					</view>
					<view class="tn-color-gray tn-padding-top-xs">
						未填写
					</view>
				</view>
				<view class="justify-content-item tn-text-lg tn-color-grey">
					<view class="tn-icon-right tn-padding-top"></view>
				</view>
			</view> -->
      <picker
        @change="bindGenderChange"
        :value="indexGender"
        :range="arrayGender"
      >
        <view
          class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding"
        >
          <view class="justify-content-item">
            <view class="tn-text-bold tn-text-lg"> 性别 </view>
            <view class="tn-color-gray tn-padding-top-xs">
              <view class="tn-color-gray">{{ arrayGender[indexGender] }}</view>
            </view>
          </view>
          <view class="justify-content-item tn-text-lg tn-color-grey">
            <view class="tn-icon-right tn-padding-top"></view>
          </view>
        </view>
      </picker>
      <picker
        @change="bindDateChange"
        mode="date"
        :value="birthday"
        :start="startDate"
        :end="endDate"
      >
        <view
          class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding"
        >
          <view class="justify-content-item">
            <view class="tn-text-bold tn-text-lg"> 生日 </view>
            <view class="tn-color-gray tn-padding-top-xs">
              {{ birthday }}
            </view>
          </view>
          <view class="justify-content-item tn-text-lg tn-color-grey">
            <view class="tn-icon-right tn-padding-top"></view>
          </view>
        </view>
      </picker>
      <!-- <picker @change="bindPickerChange1" :value="index1" :range="array1">
				<view class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding">
					<view class="justify-content-item">
						<view class="tn-text-bold tn-text-lg">
							职业
						</view>
						<view class="tn-color-gray tn-padding-top-xs">
							{{array1[index1]}}
						</view>
					</view>
					<view class="justify-content-item tn-text-lg tn-color-grey">
						<view class="tn-icon-right tn-padding-top"></view>
					</view>
				</view>
			</picker> -->

      <tn-modal v-model="showNickname" :custom="true" :showCloseBtn="true">
        <view class="custom-modal-content">
          <view class="">
            <view
              class="tn-text-lg tn-text-bold tn-color-purplered tn-text-center tn-padding"
              >修改昵称</view
            >
            <view
              class="tn-bg-gray--light"
              style="
                border-radius: 10rpx;
                padding: 20rpx 30rpx;
                margin: 50rpx 0 60rpx 0;
              "
            >
              <input
                type="nickname"
                v-model="nickname"
                placeholder-style="color:#AAAAAA"
                maxlength="20"
                placeholder="请输入昵称"
              />
            </view>
          </view>
          <view
            class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center"
          >
            <tn-button
              shape="round"
              backgroundColor="#5677fc"
              padding="40rpx 0"
              width="60%"
              shadow
              fontBold
              @click="showNickname = false"
            >
              <text class="tn-color-white">确定</text>
            </tn-button>
          </view>
        </view>
      </tn-modal>

      <tn-modal v-model="showMobile" :custom="true" :showCloseBtn="true">
        <view class="custom-modal-content">
          <view class="">
            <view
              class="tn-text-lg tn-text-bold tn-color-purplered tn-text-center tn-padding"
              >变更手机号码</view
            >
            <view
              class="tn-bg-gray--light"
              style="
                border-radius: 10rpx;
                padding: 20rpx 30rpx;
                margin: 50rpx 0 60rpx 0;
                display: flex;
              "
            >
              <input
                placeholder="请填写手机号码"
                name="input"
                placeholder-style="color:#AAAAAA"
                v-model="mobile"
                maxlength="11"
                style="width: 65%"
              />
              <!-- #ifdef MP-WEIXIN -->
              <tn-button
                v-show="system && system.auto_bind_mobile"
                open-type="getPhoneNumber"
                @getphonenumber="(e) => getPhoneNumber(e)"
                size="sm"
                backgroundColor="#5677fc"
                fontColor="#fff"
                >自动获取</tn-button
              >
              <!-- #endif -->
            </view>
          </view>
          <view
            class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center"
          >
            <tn-button
              shape="round"
              backgroundColor="#5677fc"
              padding="40rpx 0"
              width="60%"
              shadow
              fontBold
              @click="showMobile = false"
            >
              <text class="tn-color-white">确定</text>
            </tn-button>
          </view>
        </view>
      </tn-modal>

      <!-- <tn-modal v-model="showMobile" :custom="true" :showCloseBtn="true">
				<view class="custom-modal-content">
					<view class="">
						<view class="tn-text-lg tn-text-bold tn-color-purplered tn-text-center tn-padding">变更手机号码</view>
						<view class="tn-bg-gray--light tn-color-gray"
							style="border-radius: 10rpx;padding: 20rpx 30rpx;margin: 50rpx 0 60rpx 0;">
							{{mobile || '尚未设置'}}
						</view>
					</view>
					<view class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center">
						<tn-button shape="round" backgroundColor="#5677fc" padding="40rpx 0" width="60%" shadow fontBold
							open-type="getPhoneNumber" @getphonenumber="getPhoneNumber">
							<text class="tn-color-white">获取手机号</text>
						</tn-button>
					</view>
				</view>
			</tn-modal> -->

      <!-- <tn-modal v-model="showRealName" :custom="true" :showCloseBtn="true">
				<view class="custom-modal-content">
					<view class="">
						<view class="tn-text-lg tn-text-bold tn-color-purplered tn-text-center tn-padding">请输入真实姓名
						</view>
						<view class="tn-bg-gray--light"
							style="border-radius: 10rpx;padding: 20rpx 30rpx;margin: 50rpx 0 60rpx 0;">
							<input placeholder="请填写姓名" name="input" placeholder-style="color:#AAAAAA"
								maxlength="20"></input>
						</view>
					</view>
					<view class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center">
						<tn-button shape="round" backgroundColor="#5677fc" padding="40rpx 0" width="60%" shadow fontBold
							open-type="getMobileNumber">
							<text class="tn-color-white">保 存</text>
						</tn-button>
					</view>
				</view>
			</tn-modal> -->
    </view>

    <!-- 悬浮按钮-->
    <view class="tn-flex tn-footerfixed">
      <view class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center">
        <tn-button
          shape="round"
          backgroundColor="#5677fc"
          padding="40rpx 0"
          width="80%"
          shadow
          fontBold
          @click="changePassword"
        >
          <text class="tn-color-white">修改密码</text>
        </tn-button>
      </view>
      <view class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center">
        <tn-button
          shape="round"
          backgroundColor="#5677fc"
          padding="40rpx 0"
          width="80%"
          shadow
          fontBold
          @click="save"
        >
          <text class="tn-color-white">保存</text>
        </tn-button>
      </view>
    </view>
    <!-- <view class="tn-flex tn-footerfixed">
      <view class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center">
        <tn-button
          shape="round"
          backgroundColor="#5677fc"
          padding="40rpx 0"
          width="60%"
          shadow
          fontBold
          @click="save"
        >
          <text class="tn-color-white">修改密码</text>
        </tn-button>

        <tn-button
          shape="round"
          backgroundColor="#5677fc"
          padding="40rpx 0"
          width="60%"
          shadow
          fontBold
          @click="save"
        >
          <text class="tn-color-white">保存</text>
        </tn-button>
      </view>
    </view> -->

    <login ref="login" v-on:login_success="ajax"></login>
    <kz-wx-privacy-check></kz-wx-privacy-check>
  </view>
</template>

<script>
import userApi from "@/common/api/user.js";

export default {
  data() {
    return {
      showNickname: false,
      showMobile: false,
      showRealName: false,
      user: null,
      nickname: "",
      mobile: "",
      indexGender: 0,
      arrayGender: ["女", "男"],
      birthday: "",
      tempAvatarUrl: "",
      bindMobile: 1, // 0=不绑定，1=可选绑定，2=强制绑定
      system: null,
    };
  },
  computed: {
    startDate() {
      return this.getDate("start");
    },
    endDate() {
      return this.getDate("end");
    },
  },
  onLoad() {
    this.ajax();

    let system = uni.getStorageSync("system");
    this.system = system;
    if (system && system.bind_mobile) {
      this.bindMobile = system.bind_mobile;
    }
  },
  onShow() {
    uni.$on("login_success", (data) => {
      console.log("login_success", data);
      this.setUser(data.user);
    });
  },
  onHide() {
    uni.$off("login_success");
  },
  methods: {
    ajax() {
      console.log("ajax");
      userApi.getUserInfo(this).then((res) => {
        this.setUser(res.data);
      });
    },

    setUser(user) {
      this.user = user;
      uni.setStorageSync("user", this.user);

      this.nickname = this.user.nickname;
      this.mobile = this.user.mobile;
      this.indexGender = this.user.gender;
      this.birthday = this.user.birthday || "2000-01-01";
    },

    // 弹出昵称模态框
    showModalNickname(event) {
      this.showNickname = true;
    },

    // 弹出手机模态框
    showModalMobile(event) {
      this.showMobile = true;
    },

    // 弹出真实姓名模态框
    showModalRealName(event) {
      this.showRealName = true;
    },

    // 选择头像
    async onChooseAvatar(e) {
      console.log("e: ", e);
      // this.tempAvatarUrl = e.detail.avatarUrl
      // this.user.avatar = this.tempAvatarUrl

      // 上传头像
      if (e.detail.avatarUrl) {
        let newAvatar = "";
        let uploadResult = null;
        await this.utils.uploadFile(this, e.detail.avatarUrl).then((res) => {
          if (res.code == 1) {
            newAvatar = res.data.fullurl;
            uploadResult = "";
          } else {
            uploadResult = res.msg;
          }
        });

        if (uploadResult) {
          this.utils.alert(uploadResult);
          return;
        }

        if (newAvatar) {
          this.tempAvatarUrl = newAvatar;
          this.user.avatar = newAvatar;
        }
      }

      // 强制刷新
      // this.$forceUpdate();
    },

    // 获取手机号
    async getPhoneNumber(e) {
      console.log("绑定手机号获取到的参数: ", e);
      uni.showLoading({
        title: "获取中...",
      });

      // 获取微信号绑定的手机号
      userApi.getWechatPhone(this, { code: e.code }).then((res) => {
        console.log("getWechatPhone res", res);
        if (res.code == 1) {
          this.mobile = res.data.mobile;
        } else {
          this.utils.alert(res.msg);
        }
      });
    },

    // 选择性别
    bindGenderChange: function (e) {
      console.log("gender", e);
      this.indexGender = e.detail.value;
    },

    bindPickerChange1: function (e) {
      this.index1 = e.detail.value;
    },

    bindDateChange: function (e) {
      this.birthday = e.detail.value;
    },

    getDate(type) {
      const date = new Date();
      let year = date.getFullYear();
      let month = date.getMonth() + 1;
      let day = date.getDate();

      if (type === "start") {
        year = year - 60;
      } else if (type === "end") {
        year = year + 2;
      }
      month = month > 9 ? month : "0" + month;
      day = day > 9 ? day : "0" + day;
      return `${year}-${month}-${day}`;
    },

    // 获取微信手机号
    // getPhoneNumber(e) {
    //   console.log("getPhoneNumber e", e);

    //   if (!e.iv || !e.encryptedData) {
    //     this.utils.toast("用户取消授权");
    //     return;
    //   }

    //   const data = {
    //     iv: e.iv,
    //     encryptedData: e.encryptedData,
    //   };

    //   // 后端处理后返回解密的手机号码
    //   userApi.getWechatPhone(this, data).then((res) => {
    //     console.log("getWechatPhone res", res);
    //     if (res.code == 1) {
    //       this.mobile = res.data.phoneNumber;
    //     } else {
    //       this.utils.alert(res.msg);
    //     }
    //   });
    // },

    // 修改密码
    changePassword() {
      uni.navigateTo({
        url: "/pages/user/password",
      });
    },

    // 保存设置
    async save() {
      if (this.bindMobile == 2 && !this.mobile) {
        this.utils.alert("请设置手机号码");
        return;
      }

      // let newAvatar = ''
      // let uploadResult = ''

      // 上传头像
      // if (this.tempAvatarUrl) {
      // 	await this.utils.uploadFile(this, this.tempAvatarUrl).then(res => {
      // 		if (res.code == 1) {
      // 			newAvatar = res.data.fullurl
      // 			uploadResult = ''
      // 		} else {
      // 			uploadResult = res.msg
      // 		}
      // 	})
      // }

      // console.log('uploadResult', uploadResult)
      // console.log('newAvatar', newAvatar)
      // if (uploadResult) {
      // 	this.utils.alert(uploadResult)
      // 	return
      // }

      let data = {
        nickname: this.nickname,
        mobile: this.mobile,
        gender: this.indexGender,
        birthday: this.birthday,
      };
      if (this.tempAvatarUrl) {
        data["avatar"] = this.tempAvatarUrl;
      }

      console.log("save user data", data);
      await userApi.save(this, data).then((res) => {
        console.log("user save res", res);
        if (res.code == 1) {
          uni.setStorageSync("user", this.user);
          this.utils.alert("保存成功");
        } else {
          this.utils.alert(res.msg);
        }
      });
    },
  },
};
</script>

<style lang="scss">
page {
  background-color: #fff !important;
}

/* 胶囊*/
.tn-custom-nav-bar__back {
  width: 60%;
  height: 100%;
  position: relative;
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  box-sizing: border-box;
  background-color: rgba(0, 0, 0, 0.15);
  border-radius: 1000rpx;
  border: 1rpx solid rgba(255, 255, 255, 0.5);
  color: #ffffff;
  font-size: 18px;

  .icon {
    display: block;
    flex: 1;
    margin: auto;
    text-align: center;
  }
}

/* 间隔线 start*/
.tn-strip-bottom-min {
  width: 100%;
  border-bottom: 1rpx solid #f8f9fb;
}

.tn-strip-bottom {
  width: 100%;
  border-bottom: 20rpx solid rgba(241, 241, 241, 0.8);
}

/* 间隔线 end*/

/* 用户头像 start */
.logo-image {
  width: 80rpx;
  height: 80rpx;
  position: relative;
}

.logo-pic {
  background-size: cover;
  background-repeat: no-repeat;
  // background-attachment:fixed;
  background-position: top;
  border: 2rpx solid rgba(255, 255, 255, 0.05);
  box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.15);
  border-radius: 50%;
  overflow: hidden;
  // background-color: #FFFFFF;
}

.avatar-wrapper {
  background: none;
}

/* 底部悬浮按钮 start*/
.tn-tabbar-height {
  min-height: 100rpx;
  height: calc(120rpx + env(safe-area-inset-bottom) / 2);
}

.tn-footerfixed {
  position: fixed;
  width: 100%;
  bottom: calc(30rpx + env(safe-area-inset-bottom));
  z-index: 1024;
  box-shadow: 0 1rpx 6rpx rgba(0, 0, 0, 0);
}
/* 底部悬浮按钮 end*/
</style>
