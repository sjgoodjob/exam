<template>
  <view class="template-login">
    <!-- 顶部自定义导航 -->
    <tn-nav-bar fixed alpha customBack>
      <view slot="back" class="tn-custom-nav-bar__back" @click="goBack">
        <text class="icon tn-icon-left"></text>
        <text class="icon tn-icon-home-capsule-fill"></text>
      </view>
    </tn-nav-bar>

    <view class="login">
      <!-- 顶部背景图片-->
      <view class="login__bg login__bg--top"
        ><image
          class="bg"
          src="/static/user/login_top2.jpg"
          mode="widthFix"
        ></image
      ></view>
      <view class="login__bg login__bg--top">
        <image
          class="rocket rocket-sussuspension"
          src="/static/user/login_top3.png"
          mode="widthFix"
        ></image>
      </view>

      <view
        class="login__wrapper"
        :class="openRegister ? [] : ['margin-top-300']"
      >
        <!-- 登录/注册切换 -->
        <view
          class="login__mode tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-center"
          v-if="openRegister"
        >
          <view
            class="login__mode__item tn-flex-1"
            :class="[{ 'login__mode__item--active': currentModeIndex === 0 }]"
            @tap.stop="modeSwitch(0)"
            >登录</view
          >
          <view
            class="login__mode__item tn-flex-1"
            :class="[{ 'login__mode__item--active': currentModeIndex === 1 }]"
            @tap.stop="modeSwitch(1)"
            >注册</view
          >

          <!-- 滑块-->
          <view
            class="login__mode__slider tn-cool-bg-color-5--reverse"
            :style="[modeSliderStyle]"
          ></view>
        </view>

        <!-- 输入框内容-->
        <view
          class="login__info tn-flex tn-flex-direction-column tn-flex-col-center tn-flex-row-center"
        >
          <!-- 登录 -->
          <block v-if="currentModeIndex === 0">
            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-my"></view
              ></view>
              <view class="login__info__item__input__content">
                <input
                  v-model="form.username"
                  maxlength="20"
                  placeholder-class="input-placeholder"
                  placeholder="请输入登录账号"
                />
              </view>
            </view>

            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-lock"></view
              ></view>
              <view class="login__info__item__input__content">
                <input
                  v-model="form.password"
                  :password="!showPassword"
                  placeholder-class="input-placeholder"
                  placeholder="请输入登录密码"
                />
              </view>
              <view
                class="login__info__item__input__right-icon"
                @click="showPassword = !showPassword"
              >
                <view
                  :class="[showPassword ? 'tn-icon-eye' : 'tn-icon-eye-hide']"
                ></view>
              </view>
            </view>
          </block>

          <!-- 注册 -->
          <block v-if="currentModeIndex === 1">
            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-my"></view
              ></view>
              <view class="login__info__item__input__content">
                <input
                  v-model="form.username"
                  maxlength="20"
                  placeholder-class="input-placeholder"
                  placeholder="请输入用户名"
                />
              </view>
            </view>

            <!-- <view class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left">
							<view class="login__info__item__input__left-icon"><view class="tn-icon-code"></view></view>
							<view class="login__info__item__input__content login__info__item__input__content--verify-code">
								<input placeholder-class="input-placeholder" placeholder="请输入验证码" />
							</view>
							<view class="login__info__item__input__right-verify-code" @tap.stop="getCode">
								<tn-button backgroundColor="#01BEFF" fontColor="#FFFFFF" size="sm" padding="5rpx 10rpx" width="100%" shape="round">{{ tips }}</tn-button>
							</view>
						</view> -->

            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-lock"></view
              ></view>
              <view class="login__info__item__input__content">
                <input
                  v-model="form.password"
                  :password="!showPassword"
                  placeholder-class="input-placeholder"
                  placeholder="请输入登录密码"
                />
              </view>
              <view
                class="login__info__item__input__right-icon"
                @click="showPassword = !showPassword"
              >
                <view
                  :class="[showPassword ? 'tn-icon-eye' : 'tn-icon-eye-hide']"
                ></view>
              </view>
            </view>

            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-lock"></view
              ></view>
              <view class="login__info__item__input__content">
                <input
                  v-model="form.second_password"
                  :password="!showPassword"
                  placeholder-class="input-placeholder"
                  placeholder="再次输入登录密码"
                />
              </view>
              <view
                class="login__info__item__input__right-icon"
                @click="showPassword = !showPassword"
              >
                <view
                  :class="[showPassword ? 'tn-icon-eye' : 'tn-icon-eye-hide']"
                ></view>
              </view>
            </view>

            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-identity"></view
              ></view>
              <view class="login__info__item__input__content">
                <input
                  v-model="form.nickname"
                  maxlength="20"
                  placeholder-class="input-placeholder"
                  placeholder="请输入昵称"
                />
              </view>
            </view>

            <view
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-phone"></view
              ></view>
              <view
                class="login__info__item__input__content flex tn-flex-row-between"
              >
                <input
                  v-model="form.mobile"
                  maxlength="20"
                  placeholder-class="input-placeholder"
                  placeholder="请输入手机号码"
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
              class="login__info__item__input tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-left"
            >
              <view class="login__info__item__input__left-icon"
                ><view class="tn-icon-sex"></view
              ></view>
              <view class="login__info__item__input__content">
                <tn-radio-group v-model="form.sex">
                  <tn-radio name="1">男</tn-radio>
                  <tn-radio name="2">女</tn-radio>
                </tn-radio-group>
              </view>
            </view>
          </block>

          <view
            class="login__info__item__button tn-cool-bg-color-5--reverse"
            hover-class="tn-hover"
            :hover-stay-time="150"
            @click="submit()"
          >
            {{ currentModeIndex === 0 ? "登录" : "注册" }}
          </view>

          <!-- <view v-if="currentModeIndex === 0" class="login__info__item__tips">忘记密码?</view> -->
        </view>

        <!-- 其他登录方式 -->
        <!-- #ifdef MP-WEIXIN -->
        <view
          class="login__way tn-flex tn-flex-col-center tn-flex-row-center"
          v-show="system && system.auto_bind_mobile"
        >
          <view class="tn-padding-sm tn-margin-xs">
            <tn-button
              open-type="getPhoneNumber"
              @getphonenumber="(e) => getPhoneNumber(e, 'regOrLogin')"
              size="sm"
              fontColor="#fff"
            >
              <view
                style="margin-left: 18rpx"
                class="login__way__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur tn-bg-blue tn-color-white"
              >
                <view class="tn-icon-phone-fill"></view>
              </view>
            </tn-button>
            <view class="text-sm tn-margin-top">手机号快捷登录</view>
          </view>
        </view>
        <!-- #endif -->
      </view>

      <!-- 底部背景图片-->
      <view class="login__bg login__bg--bottom"
        ><image src="/static/user/login_bottom_bg.jpg" mode="widthFix"></image
      ></view>
    </view>
	
	<kz-wx-privacy-check ref="kzWxPrivacyCheck"></kz-wx-privacy-check>

    <!-- 验证码倒计时 -->
    <!-- <tn-verification-code ref="code" uniqueKey="login-demo-1" :seconds="60" @change="codeChange"></tn-verification-code> -->
  </view>
</template>

<script>
import template_page_mixin from "@/libs/mixin/template_page_mixin.js";
import userApi from "@/common/api/user.js";

export default {
  mixins: [template_page_mixin],
  data() {
    return {
      // 当前选中的模式
      currentModeIndex: 0,
      // 模式选中滑块
      modeSliderStyle: {
        left: 0,
      },
      // 是否显示密码
      showPassword: false,
      // 倒计时提示文字
      tips: "获取验证码",
      form: {
        username: "",
        password: "",
        second_password: "",
        nickname: "",
        mobile: "",
        gender: "1",
        bio: "",
      },
      openRegister: true,
      system: null,
    };
  },
  watch: {
    currentModeIndex(value) {
      const sliderWidth = uni.upx2px(476 / 2);
      this.modeSliderStyle.left = `${sliderWidth * value}px`;
    },
  },
  onLoad() {
    let config = uni.getStorageSync("system");
    if (
      config.open_user_register != undefined &&
      parseInt(config.open_user_register) === 0
    ) {
      this.openRegister = false;
    }

    this.system = config;
  },
  methods: {
    // 切换模式
    modeSwitch(index) {
      this.currentModeIndex = index;
      this.showPassword = false;
    },

    submit() {
      if (this.currentModeIndex === 0) {
        this.login();
      } else {
        this.register();
      }
    },

    // 注册
    register() {
      if (!this.form.username) {
        this.utils.toast("请填写登录账号");
        return;
      }
      if (!this.form.password) {
        this.utils.toast("请填写登录密码");
        return;
      }
      if (this.form.password != this.form.second_password) {
        this.utils.toast("两次输入密码不一致");
        return;
      }
      if (!this.form.nickname) {
        this.utils.toast("请填写昵称");
        return;
      }
      if (!this.form.mobile) {
        this.utils.toast("请填写手机号码");
        return;
      }

      let data = this.form;
      userApi.register(this, data).then((res) => {
        console.log("user register res", res);
        if (res.code == 1) {
          uni.setStorageSync("user", res.data.user);
          uni.setStorageSync("token", res.data.token);
          uni.$emit("login_success", res.data);
          this.utils.alert("注册成功", () => {
            this.utils.goto("/pages/user/user");
          });
        } else {
          this.utils.alert(res.msg);
        }
      });
    },

    // 登录
    login() {
      let data = {
        username: this.form.username,
        password: this.form.password,
      };
      userApi.login(this, data).then((res) => {
        console.log("user login res", res);
        if (res.code == 1) {
          uni.setStorageSync("user", res.data.user);
          uni.setStorageSync("token", res.data.token);
          uni.$emit("login_success", res.data);

          let loginJumpPage = this.utils.getData("loginJumpPage");
          if (loginJumpPage) {
            this.utils.goto(loginJumpPage);
          } else {
            this.utils.goto("/pages/user/user");
          }
        } else {
          this.utils.alert(res.msg);
        }
      });
    },

    // 获取手机号快速获取
    async getPhoneNumber(e, type = "") {
		let isPrivacySetting =
		  await this.$refs.kzWxPrivacyCheck.getPrivacySetting("showPrivacy");
		console.log("isPrivacySetting", isPrivacySetting);
		
		if (!isPrivacySetting) {
			return
		}
		
      console.log("绑定手机号获取到的参数: ", e);
      uni.showLoading({
        title: "获取中...",
      });

      let kind = this.currentModeIndex == 0 ? "login" : "register";
      // 获取微信号绑定的手机号
      userApi
        .getWechatPhone(this, { code: e.code, type: type, kind: kind })
        .then((res) => {
          console.log("getWechatPhone res", res);
          if (res.code == 1) {
            if (type == "regOrLogin") {
              if (res.data.token) {
                uni.setStorageSync("token", res.data.token);
              }
              if (res.data.user) {
                uni.setStorageSync("user", res.data.user);
              }

              if (res.data.operate == "register") {
                // 注册成功，弹窗提示密码，跳转首页
                this.utils.alert("注册成功，请保存好密码：" + res.data.password, () => {
                  this.utils.goto("/pages/user/user");
                });
              } else {
                // 登录成功，跳转首页
                this.utils.goto("/pages/user/user");
              }
            } else {
              this.form.mobile = res.data.mobile;
            }
          } else {
            this.utils.alert(res.msg);
          }
        });
    },

    // 获取验证码
    // getCode() {
    // 	if (this.$refs.code.canGetCode) {
    // 		this.$tn.message.loading('正在获取验证码');
    // 		setTimeout(() => {
    // 			this.$tn.message.closeLoading();
    // 			this.$tn.message.toast('验证码已经发送');
    // 			// 通知组件开始计时
    // 			this.$refs.code.start();
    // 		}, 2000);
    // 	} else {
    // 		this.$tn.message.toast(this.$refs.code.secNum + '秒后再重试');
    // 	}
    // },
    // // 获取验证码倒计时被修改
    // codeChange(event) {
    // 	this.tips = event;
    // }
  },
};
</script>

<style lang="scss">
@import "@/static/css/templatePage/custom_nav_bar.scss";
/* 悬浮 */
.rocket-sussuspension {
  animation: suspension 3s ease-in-out infinite;
}

@keyframes suspension {
  0%,
  100% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(-0.8rem, 1rem);
  }
}

page {
  background: #fff;
}

.login {
  position: relative;
  height: 100%;
  z-index: 1;

  /* 背景图片 start */
  &__bg {
    z-index: -1;
    position: fixed;

    &--top {
      top: 0;
      left: 0;
      right: 0;
      width: 100%;

      .bg {
        width: 750rpx;
        will-change: transform;
      }
      .rocket {
        margin: 50rpx 28%;
        width: 400rpx;
        will-change: transform;
      }
    }

    &--bottom {
      bottom: -10rpx;
      left: 0;
      right: 0;
      width: 100%;
      // height: 144px;
      margin-bottom: env(safe-area-inset-bottom);

      image {
        width: 750rpx;
        will-change: transform;
      }
    }
  }
  /* 背景图片 end */

  /* 内容 start */
  &__wrapper {
    margin-top: 303rpx;
    width: 100%;
  }

  /* 切换 start */
  &__mode {
    position: relative;
    margin: 0 auto;
    width: 476rpx;
    height: 77rpx;
    background-color: #ffffff;
    box-shadow: 0rpx 10rpx 50rpx 0rpx rgba(0, 3, 72, 0.1);
    border-radius: 39rpx;

    &__item {
      height: 77rpx;
      width: 100%;
      line-height: 77rpx;
      text-align: center;
      font-size: 31rpx;
      color: #908f8f;
      letter-spacing: 1em;
      text-indent: 1em;
      z-index: 2;
      transition: all 0.4s;

      &--active {
        font-weight: bold;
        color: #ffffff;
      }
    }

    &__slider {
      position: absolute;
      height: inherit;
      width: calc(476rpx / 2);
      border-radius: inherit;
      box-shadow: 0rpx 18rpx 72rpx 18rpx rgba(0, 195, 255, 0.1);
      z-index: 1;
      transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
  }
  /* 切换 end */

  /* 登录注册信息 start */
  &__info {
    margin: 0 30rpx;
    margin-top: 40rpx;
    padding: 30rpx 51rpx;
    padding-bottom: 0;
    border-radius: 20rpx;
    background-color: #ffff;
    box-shadow: 0rpx 10rpx 50rpx 0rpx rgba(0, 3, 72, 0.1);

    &__item {
      &__input {
        margin-top: 29rpx;
        width: 100%;
        height: 77rpx;
        border: 1rpx solid #e6e6e6;
        border-radius: 39rpx;

        &__left-icon {
          width: 10%;
          font-size: 44rpx;
          margin-left: 20rpx;
          color: #aaaaaa;
        }

        &__content {
          width: 80%;
          padding-left: 10rpx;

          &--verify-code {
            width: 56%;
          }

          input {
            font-size: 24rpx;
            // letter-spacing: 0.1em;
          }
        }

        &__right-icon {
          width: 10%;
          font-size: 44rpx;
          margin-right: 20rpx;
          color: #aaaaaa;
        }

        &__right-verify-code {
          width: 34%;
          margin-right: 20rpx;
        }
      }

      &__button {
        margin-top: 75rpx;
        margin-bottom: 39rpx;
        width: 100%;
        height: 77rpx;
        text-align: center;
        font-size: 31rpx;
        font-weight: bold;
        line-height: 77rpx;
        letter-spacing: 1em;
        text-indent: 1em;
        border-radius: 39rpx;
        box-shadow: 1rpx 10rpx 24rpx 0rpx rgba(60, 129, 254, 0.35);
      }

      &__tips {
        margin: 30rpx 0;
        color: #aaaaaa;
      }
    }
  }
  /* 登录注册信息 end */

  /* 登录方式切换 start */
  &__way {
    margin: 0 auto;
    margin-top: 70rpx;

    &__item {
      &--icon {
        width: 77rpx;
        height: 77rpx;
        font-size: 50rpx;
        border-radius: 100rpx;
        margin-bottom: 18rpx;
        position: relative;
        z-index: 1;

        &::after {
          content: " ";
          position: absolute;
          z-index: -1;
          width: 100%;
          height: 100%;
          left: 0;
          bottom: 0;
          border-radius: inherit;
          opacity: 1;
          transform: scale(1, 1);
          background-size: 100% 100%;
          background-image: url(/static/user/icon_bg5.png);
        }
      }
    }
  }
  /* 登录方式切换 end */
  /* 内容 end */
}

.input-placeholder {
  font-size: 24rpx;
  color: #e6e6e6;
}

.margin-top-300 {
  margin-top: 300px;
}
</style>
