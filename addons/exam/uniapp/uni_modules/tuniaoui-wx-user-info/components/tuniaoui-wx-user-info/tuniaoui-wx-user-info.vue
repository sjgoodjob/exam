<template>
  <view v-if="openModal" class="wx-authorization-modal">
    <view class="wam__mask" @touchmove.prevent="" @tap.stop="closeModal"></view>

    <!-- 内容区域 -->
    <view class="wam__wrapper">
      <!-- 关闭按钮 -->
      <view class="wam__close-btn" @tap.stop="closeModal"
        ><text class="tn-icon-close"></text
      ></view>
      <!-- 标题 -->
      <view class="wam__title">获取您的昵称、头像</view>
      <!-- tips -->
      <view class="wam__sub-title">点击设置您的用户头像</view>

      <!-- 头像选择 -->
      <view class="wam__avatar">
        <view class="button-shadow">
          <button
            class="button"
            open-type="chooseAvatar"
            @chooseavatar="chooseAvatarEvent"
          >
            <view v-if="userInfo.avatar" class="avatar__image"
              ><image
                class="image"
                :src="userInfo.avatar"
                mode="aspectFill"
              ></image
            ></view>
            <view v-else class="avatar__empty">
              <!-- <image class="image" src="https://cdn.nlark.com/yuque/0/2022/jpeg/280373/1668928062708-assets/web-upload/764843cf-055a-4cb6-b5d3-dca528b33fd4.jpeg" mode="aspectFill"></image> -->
              <view
                class="cuIcon-people user-avatar-empty"
                style="width: 120rpx; height: 120rpx"
              ></view>
            </view>
            <view class="avatar--icon"
              ><view class="tn-icon-camera-fill"></view
            ></view>
          </button>
        </view>
      </view>

      <!-- 昵称输入 -->
      <view class="wam__nickname">
        <view class="nickname__data"
          ><input
            class="input"
            type="nickname"
            v-model="userInfo.nickname"
            @change="changeNickName"
            placeholder="请输入昵称"
            placeholder-style="color: #AAAAAA;"
        /></view>
      </view>

      <!-- 手机号码输入 -->
      <view class="wam__nickname" v-if="system && system.bind_mobile != 0">
        <view class="nickname__data" style="display: flex">
          <input
            class="input"
            type="text"
            v-model="userInfo.mobile"
            @change="changeMobile"
            placeholder="请输入手机号码"
            placeholder-style="color: #AAAAAA;"
            style="width: 72%"
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

      <!-- 保存按钮 -->
      <view
        class="wam__submit-btn"
        :class="[
          {
            disabled: !userInfo.avatar || !userInfo.nickname,
          },
        ]"
        hover-class="tn-btn-hover-class"
        :hover-stay-time="150"
        @tap.stop="submitUserInfo"
      >
        保 存
      </view>
    </view>
  </view>
</template>

<script>
import userApi from "@/common/api/user.js";
export default {
  options: {
    // 在微信小程序中将组件节点渲染为虚拟节点，更加接近Vue组件的表现(不会出现shadow节点下再去创建元素)
    virtualHost: true,
  },
  props: {
    value: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      openModal: false,
      userInfo: {
        avatar: "",
        nickname: "",
        mobile: "",
      },
      system: null,
      bindMobile: false,
    };
  },
  watch: {
    value: {
      handler(val) {
        this.openModal = val;
        if (val == true) {
          this.loadUser();
        }
      },
      immediate: true,
    },
  },
  methods: {
    // 头像选择
    chooseAvatarEvent(e) {
      this.userInfo.avatar = e.detail.avatarUrl;
    },
    // 修改用户昵称
    changeNickName(e) {
      console.log("changeNickName e", e);
      let name = e.detail.value;
      if (name.length === 0) return;
      this.userInfo.nickname = name;
    },
    // 修改用户手机
    changeMobile(e) {
      console.log("changeNickName e", e);
      let mobile = e.detail.value;
      if (mobile.length === 0) return;
      if (!this.validMobile(this.userInfo.mobile)) return;

      this.userInfo.mobile = mobile;
    },
    // 更新用户信息
    submitUserInfo() {
      // 判断是否已经选择了用户头像和输入了用户昵称
      if (!this.userInfo.avatar || !this.userInfo.nickname) {
        return uni.showToast({
          icon: "none",
          title: "请选择头像和输入用户信息",
        });
      }

      if (this.system.bind_mobile && this.system.bind_mobile == 2) {
        if (!this.userInfo.mobile) {
          return uni.showToast({
            icon: "none",
            title: "请选输入手机号码",
          });
        }
        if (!this.validMobile(this.userInfo.mobile)) {
          return false;
        }
      }

      // 更新完成事件
      this.$emit("updated", this.userInfo);
    },

    // 关闭弹框
    closeModal() {
      if (this.bindMobile) {
        return;
      }
      this.$emit("input", false);
    },

    // 加载缓存用户信息
    loadUser() {
      let user = uni.getStorageSync("user");
      if (user) {
        this.userInfo.avatar = user.avatar;
        this.userInfo.nickname = user.nickname;
      }

      let system = uni.getStorageSync("system");
      this.system = system;
      if (system && system.bind_mobile) {
        this.bindMobile = true;
      }
    },
    // 验证手机号
    validMobile(mobile) {
      const pattern = /^1[3456789]\d{9}$/;
      let result = pattern.test(mobile);

      if (!result) {
        uni.showToast({
          icon: "none",
          title: "手机号码格式不正确",
        });
      }

      return result;
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
          this.userInfo.mobile = res.data.mobile;
        } else {
          this.utils.alert(res.msg);
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "../../static/iconfont.css";

.wx-authorization-modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  z-index: 99998;

  view {
    box-sizing: border-box;
  }

  .image {
    width: 100%;
    height: 100%;
    border-radius: inherit;
  }

  .wam {
    /* mask */
    &__mask {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      opacity: 0;
      animation: showMask 0.25s ease 0.1s forwards;
    }

    /* close-btn */
    &__close-btn {
      position: absolute;
      top: 30rpx;
      right: 30rpx;
      z-index: 99999;
    }

    /* wrapper */
    &__wrapper {
      position: absolute;
      left: 0;
      bottom: 0;
      width: 100%;
      background-color: #ffffff;
      border-radius: 20rpx 20rpx 0rpx 0rpx;
      padding: 40rpx;
      padding-top: 60rpx;
      padding-bottom: 40rpx;
      padding-bottom: calc(constant(safe-area-inset-bottom) + 40rpx);
      padding-bottom: calc(env(safe-area-inset-bottom) + 40rpx);
      transform-origin: center bottom;
      transform: scaleY(0);
      animation: showWrapper 0.25s ease 0.1s forwards;
      z-index: 99999;
    }

    /* title */
    &__title {
      font-size: 34rpx;
    }
    /* sub-title */
    &__sub-title {
      font-size: 26rpx;
      color: #aaaaaa;
      margin-top: 16rpx;
      padding-bottom: 30rpx;
    }

    /* 头像选择 */
    &__avatar {
      width: 100%;
      margin-top: 30rpx;
      display: flex;
      align-items: center;
      justify-content: center;

      .button-shadow {
        border: 8rpx solid rgba(255, 255, 255, 0.05);
        box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.15);
        border-radius: 50%;
      }

      .button {
        position: relative;
        width: 160rpx;
        height: 160rpx;
        border-radius: 50%;
        overflow: visible;
        background-image: repeating-linear-gradient(45deg, #e4e9ec, #f8f7f8);
        color: #ffffff;
        background-color: transparent;
        padding: 0;
        margin: 0;
        font-size: inherit;
        line-height: inherit;
        border: none;
        &::after {
          border: none;
        }
      }

      .avatar {
        &__empty,
        &__image {
          width: 100%;
          height: 100%;
          border-radius: inherit;
        }

        &--icon {
          position: absolute;
          right: -10rpx;
          bottom: -6rpx;
          width: 60rpx;
          height: 60rpx;
          // transform: translate(50%, 50%);
          background-color: #1d2541;
          color: #ffffff;
          border-radius: 50%;
          border: 6rpx solid #ffffff;
          line-height: 1;
          font-size: 36rpx;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }
    }

    /* 昵称 */
    &__nickname {
      margin-top: 40rpx;

      .nickname {
        &__data {
          margin-top: 16rpx;
          width: 100%;
          padding: 26rpx 20rpx;
          border-radius: 10rpx;
          background-color: #f8f7f8;

          .input {
            color: #080808;
          }
        }
      }
    }

    /* 保存按钮 */
    &__submit-btn {
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

      &.disabled {
        background-color: #e6e6e6;
      }
    }
  }
}

.tn-btn-hover-class {
  box-shadow: inset 10rpx 2rpx 40rpx 0rpx rgba(0, 0, 0, 0.05);
}

@keyframes showMask {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}
@keyframes showWrapper {
  0% {
    transform: scaleY(0);
  }
  100% {
    transform: scaleY(1);
  }
}
</style>
