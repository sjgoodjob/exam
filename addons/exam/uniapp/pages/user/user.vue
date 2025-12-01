<template>
  <view>
    <!-- 简约风格 -->
    <!-- <kz-page-my-simple :user="user" @login="login" @goTo="goTo" @clearStorage="clearStorage"
			v-if="pageStyle == 'simple'"></kz-page-my-simple> -->

    <!-- 多彩风格1 -->
    <kz-page-my-color
      :user="user"
      :friendApps="friendApps"
      @login="login"
      @goTo="goTo"
      @clearStorage="clearStorage"
      v-if="pageStyle == 'color'"
    ></kz-page-my-color>

    <!-- 多彩风格2 -->
    <kz-page-my-color-1
      :user="user"
      :friendApps="friendApps"
      @login="login"
      @goTo="goTo"
      @clearStorage="clearStorage"
      v-else-if="pageStyle == 'color2'"
    ></kz-page-my-color-1>

    <!-- 流量主组件 -->
    <!-- #ifdef MP-WEIXIN -->
    <kz-ad ref="adMy" kind="BANNER" :config="ad" field="my_banner"></kz-ad>
    <kz-ad ref="adMy" kind="VIDEO" :config="ad" field="my_video"></kz-ad>
    <kz-ad
      ref="adMy"
      kind="VIDEO_PATCH"
      :config="ad"
      field="my_video_patch"
    ></kz-ad>
    <!-- #endif -->

    <!-- 大嘴鸟组件 -->
    <monster v-if="showMonster"></monster>

    <login ref="login" v-on:succ="ajax"></login>
    <tabbar></tabbar>
  </view>
</template>

<script>
import userApi from "@/common/api/user.js";
import commonApi from "@/common/api/common.js";
import { nextTick } from "vue";

export default {
  data() {
    return {
      imgUrl: this.url,
      user: null,
      ad: {},
      pageStyle: "",
      showMonster: false,
      isLogin: false,
      friendApps: [],
    };
  },
  onLoad() {
    this.pageStyle = "color2";
  },
  onShow() {
    uni.$on("login_success", (data) => {
      console.log("login_success", data);
      this.user = data.user;
      this.ajax();
    });

    if (uni.getStorageSync("token")) {
      this.isLogin = true;

      this.ajax();
    } else {
      this.$nextTick(() => {
        console.log(this.$refs.login);
        this.$refs.login.modal = true;
      });
    }
  },
  onHide() {
    uni.$off("login_success");
  },
  methods: {
    ajax() {
      // 获取配置
      this.getSetting();

      if (uni.getStorageSync("token")) {
        userApi.getUserInfo(this).then((res) => {
          if (res.code == 1) {
            this.user = { ...res.data };
            uni.setStorageSync("user", this.user);
          } else {
            uni.$emit("uniLogin");
          }
        });
      } else {
        uni.$emit("uniLogin");
      }
    },

    loadSetting() {
      let page = this.utils.getData("page");
      if (page) {
        // 页面风格
        this.pageStyle = page.page_my_style ? page.page_my_style : "color2";
        // this.pageStyle = 'color'
        // 大嘴鸟
        this.showMonster = page.page_my_monster_btn == 1;
      }

      // #ifdef MP-WEIXIN
      this.ad = this.utils.getData("ad");
      // 插屏广告
      if (this.ad.my_cp_open == 1) {
        this.adUtils.interstitial.load(this.ad.my_cp);
        this.adUtils.interstitial.show();
      }

      this.getFriendAppsList();
      // #endif
    },

    getSetting() {
      this.http("common/index", "", "get").then((res) => {
        if (!res.code) {
          uni.showToast({
            title: "获取数据失败，请刷新重试",
            icon: "error",
          });
          return;
        }

        // 系统配置数据
        let system = res.data.system;
        if (system) {
          this.system = system;
          uni.setStorageSync("system", system);
        }

        // 页面配置数据
        let page = res.data.page;
        if (page) {
          this.page = page;
          uni.setStorageSync("page", page);
        }

        // #ifdef MP-WEIXIN
        // 流量主数据
        let ad = res.data.ad;
        if (ad) {
          this.ad = ad;
          uni.setStorageSync("ad", ad);

          // 插屏广告
          if (this.ad.index_cp_open == 1) {
            this.adUtils.interstitial.load(this.ad.index_cp);
            this.adUtils.interstitial.show();
          }
        }
        // #endif

        this.loadSetting();
      });
    },

    login() {
      console.log("on login");
      if (!uni.getStorageSync("token")) {
        this.$refs.login.modal = true;
        return;
      } else {
        uni.navigateTo({
          url: "set",
        });
      }
    },
    goTo(page) {
      console.log("page", page);
      if (!uni.getStorageSync("token")) {
        this.$refs.login.modal = true;
        return;
      } else {
        this.utils.goto(page);
        // uni.navigateTo({
        // 	url: page
        // 	// url: e.currentTarget.dataset.url
        // });
      }
    },
    clearStorage() {
      console.log("on clearStorage");
      uni.showModal({
        title: "提醒",
        content:
          "是否确认要退出登录吗？（将清理用户登录信息及练题记忆模式等数据）",
        confirmColor: "#4A90E2",
        success: (t) => {
          t.confirm
            ? (uni.showLoading({
                title: "正在退出登录...",
                mask: !0,
              }),
              uni.clearStorage({
                success: () => {
                  this.user = null;
                  uni.setStorageSync("user", "");
                  uni.setStorageSync("user_id", "");
                  uni.setStorageSync("openid", "");
                  uni.setStorageSync("token", "");
                },
                fail: (t) => {
                  console.log("fail");
                },
                complete: function (t) {
                  setTimeout(function () {
                    uni.hideLoading();
                  }, 1000);
                },
              }),
              this.getSetting())
            : t.cancel && console.log("取消");
        },
      });
    },
    tel() {
      uni.makePhoneCall({
        phoneNumber: this.copyright_mobile,
      });
    },
    // 获取友情链接小程序
    getFriendAppsList() {
      commonApi.getFriendAppsList(this).then((res) => {
        if (res && res.data) {
          this.friendApps = res.data;
        }
      });
    },
  },
};
</script>

<style>
page {
  background-color: #fff;
}
</style>
