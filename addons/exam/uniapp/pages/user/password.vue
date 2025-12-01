<template>
  <view class="container">
    <tn-form
      ref="form"
      class="tn-padding"
      :model="form"
      :errorType="errorType"
      :labelPosition="labelPosition"
      :labelWidth="labelWidth"
      :labelAlign="labelAlign"
      :labelStyle="{ fontSize: '32rpx', fontWeight: 'bold' }"
    >
      <tn-form-item label="旧密码" prop="oldPassword">
        <tn-input
          v-model="form.oldPassword"
          type="password"
          placeholder="请输入密码"
          :border="border"
          :passwordIcon="true"
          :placeholderStyle="placeholderStyle"
        />
      </tn-form-item>
      <tn-form-item label="新密码" prop="newPassword">
        <tn-input
          v-model="form.newPassword"
          type="password"
          placeholder="请输入密码"
          :border="border"
          :passwordIcon="true"
          :placeholderStyle="placeholderStyle"
        />
      </tn-form-item>
      <tn-form-item label="确认密码" prop="confirmPassword">
        <tn-input
          v-model="form.confirmPassword"
          type="password"
          placeholder="请输入密码"
          :border="border"
          :passwordIcon="true"
          :placeholderStyle="placeholderStyle"
        />
      </tn-form-item>
    </tn-form>

    <!-- 悬浮按钮-->
    <view class="tn-flex tn-footerfixed">
      <view
        class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center"
        @click="handleSubmit"
      >
        <tn-button
          shape="round"
          backgroundColor="#5677fc"
          padding="40rpx 0"
          width="60%"
          shadow
          fontBold
        >
          <!-- <text class="tn-icon-light tn-padding-right-xs tn-color-black"></text> -->
          <text class="tn-color-white">保存</text>
          <!-- <text class="tn-icon-light tn-padding-left-xs tn-color-black"></text> -->
        </tn-button>
      </view>
    </view>
  </view>
</template>

<script>
import { changePassword } from "@/common/api/user.js";

export default {
  data() {
    return {
      errorType: ["message", "border-bottom", "toast"],
      labelPosition: "top",
      labelAlign: "right",
      labelWidth: 140,
      border: false,
      placeholderStyle: "color: #AAAAAA;fontSize: 32rpx,",
      form: {
        oldPassword: "",
        newPassword: "",
        confirmPassword: "",
      },
      rules: {
        oldPassword: [
          {
            required: true,
            message: "请输入旧密码",
            trigger: ["change", "blur"],
          },
        ],
        newPassword: [
          {
            required: true,
            message: "请输入新密码",
            trigger: ["change", "blur"],
          },
          {
            pattern: /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]+\S{5,12}$/,
            message: "需同时含有字母和数字，长度在6-12之间",
            trigger: ["change", "blur"],
          },
        ],
        confirmPassword: [
          {
            required: true,
            message: "请再次输入密码",
            trigger: ["change", "blur"],
          },
          {
            validator: (rule, value, callback) => {
              return value === this.form.newPassword;
            },
            message: "两次输入的密码不相等",
            trigger: ["change", "blur"],
          },
        ],
      },
    };
  },
  methods: {
    onReady() {
      this.$refs.form.setRules(this.rules);
    },
    handleSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          // 验证通过
          changePassword(this, this.form).then((res) => {
            if (res.code === 1) {
              // 替换新token
              uni.setStorageSync("token", res.data.token);

              this.utils.alert("修改成功", () => {
                // uni.navigateBack();
                uni.launch({
                  url: "/pages/user/user",
                });
              });
            } else {
              this.utils.alert(res.msg);
            }
          });
        } else {
          // 验证失败
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
page {
  background-color: #fff !important;
}

.container {
  background-color: #fff !important;
  // 高度vh(100);
  height: 100vh;
}
.input-group {
  margin-bottom: 20px;
}
.input {
  width: 100%;
  padding: 10px;
  border: none;
  border-bottom: 2px solid #ccc;
  outline: none;
}
.input:focus {
  border-bottom-color: #5677fc;
}
.submit-button {
  width: 100%;
  padding: 10px;
  background-color: #5677fc;
  color: #fff;
  border: none;
  border-radius: 5px;
  text-align: center;
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

.tn-border-solid-bottom {
  border-bottom: 1px solid #f8f9fb;
}
</style>
