<template>
  <view>
    <!-- 骨架屏 -->
    <kz-skeleton
      v-if="showSkeleton"
      :showSkeleton="showSkeleton"
      backgroundColor="#fafafa"
      borderRadius="10rpx"
    ></kz-skeleton>

    <!-- 添加小程序提示组件 -->
    <!-- #ifdef MP-WEIXIN -->
    <add-tip :tip="focusOnTip" :duration="3" />
    <!-- #endif -->

    <!-- 首页多彩列表风格组件1 -->
    <kz-page-index-color-1
      :title="title"
      :banners="banners"
      :slides="slides"
      :papers="papers"
      :rooms="rooms"
      :news="news"
      :notices="notices"
      :modules="diyIndexButton"
      :subjectId="subjectId"
      :subjectName="subjectName"
      v-if="pageStyle == 'color'"
    ></kz-page-index-color-1>

    <!-- 首页多彩列表风格组件2 -->
    <kz-page-index-color-2
      :title="title"
      :banners="banners"
      :slides="slides"
      :papers="papers"
      :rooms="rooms"
      :news="news"
      :notices="notices"
      :modules="diyIndexButton"
      :subjectId="subjectId"
      :subjectName="subjectName"
      v-else-if="pageStyle == 'color2'"
    ></kz-page-index-color-2>

    <!-- 首页简约风格组件 -->
    <kz-page-index-simple-1
      :title="title"
      :notices="notices"
      :subjectName="subjectName"
      :userInfo="userInfo"
      :diyIndexButton="diyIndexButton"
      v-else-if="pageStyle == 'simple'"
      @clickUserInfo="clickUserInfo"
    ></kz-page-index-simple-1>
    <!-- <kz-page-index-simple :banners="banners" v-if="pageStyle == 'simple'"></kz-page-index-simple> -->

    <!-- 悬浮组件 -->
    <tui-scroll-top
      :scrollTop="scrollTop"
      :isIndex="false"
      :isHideAd="showAdBtn"
      :isShare="showShareBtn"
      :customShare="false"
      @hideAd="
        () => {
          showAd = !showAd;
        }
      "
      @goNotice="goNoticeList"
    ></tui-scroll-top>

    <!-- 流量主组件 -->
    <!-- #ifdef MP-WEIXIN -->
    <view v-show="showAd">
      <kz-ad
        ref="adIndex"
        kind="BANNER"
        :config="ad"
        field="index_banner"
      ></kz-ad>
      <kz-ad
        ref="adIndex"
        kind="VIDEO"
        :config="ad"
        field="index_video"
      ></kz-ad>
      <kz-ad
        ref="adIndex"
        kind="VIDEO_PATCH"
        :config="ad"
        field="index_video_patch"
      ></kz-ad>
    </view>
    <!-- #endif -->

    <!-- toast提示 -->
    <tui-toast ref="toast"></tui-toast>

    <!-- 登录组件 -->
    <login ref="login"></login>

    <!-- 底部导航栏组件 -->
    <tabbar :theme="tabbarStyle" :tabbar="tabbar"></tabbar>

    <view class="margin-bottom-xl">
      <tn-load-more class="tn-margin-top" status="nomore" dot></tn-load-more>
    </view>
  </view>
</template>

<script>
// import HeadLine from '@/components/headline/headline.vue'
import AddTip from "@/components/struggler-uniapp-add-tip/struggler-uniapp-add-tip.vue";
import subjectApi from "@/common/api/subject.js";

var interstitialAd = null;

export default {
  components: {
    // HeadLine,
    AddTip,
  },
  data() {
    return {
      focusOnTip: "点击「添加小程序」，下次访问更便捷",
      imgUrl: this.imgUrl,
      banners: [],
      slides: [],
      system: null,
      ad: null,
      showAd: true,
      showSkeleton: true,
      headlines: [],
      notices: [],
      scrollTop: 0,
      showAdBtn: false,
      showShareBtn: false,
      hideTop: false,
      pageStyle: "color",
      papers: [],
      rooms: [],
      news: [],
      tabbarStyle: "",
      tabbar: [],
      diyIndexButton: [],
      userInfo: {},
      subjectId: 0,
      subjectName: "请选择科目",
      title: "答题考试系统",
    };
  },
  onLoad(e) {
    this.getUserSubject();
    this.getSetting();
    let userInfo = uni.getStorageSync("user");
    this.userInfo = userInfo ? userInfo : {};
    uni.$once("login_success", (data) => {
      let userInfo = uni.getStorageSync("user");
      this.userInfo = userInfo ? userInfo : {};
    });
  },
  onShow() {
    this.getUserSubject();

    // 监听科目选择事件
    uni.$on("event_subject_choose", (e) => {
      console.log("event_subject_choose", e);
      let subject_id = e.subject_id;
      if (subject_id != this.subjectId) {
        this.subjectId = subject_id;
        this.getSetting();
      }
    });
  },
  onPageScroll(e) {
    if (!this.hideTop) {
      this.scrollTop = e.scrollTop;
    }
  },
  methods: {
    clickUserInfo() {
      if (!uni.getStorageSync("token")) {
        this.$refs.login.modal = true;
      }
    },
    getUserSubject() {
      let user_subject = uni.getStorageSync("user_subject");
      if (user_subject && user_subject.subject2 && user_subject.subject2.id) {
        this.subjectId = user_subject.subject2.id;
        this.subjectName = user_subject.subject2.name;
      } else {
        this.subjectId = 0;
        this.subjectName = "请选择科目";
      }
    },

    getSetting() {
      this.http("common/index", { subject_id: this.subjectId }, "get").then(
        (res) => {
          if (!res.code) {
            uni.showToast({
              title: "获取数据失败，请刷新重试",
              icon: "error",
            });
            return;
          }

          // 积分提示
          let point = res.data.point;
          if (point?.get_point) {
            this.$refs.toast.show({
              title: "积分+" + point.get_point,
              content: point.type,
              imgUrl: "/static/toast/info-circle.png",
              icon: true,
              duration: 4000,
            });
          }

          // 公告
          this.notices = res.data.notices;
          // this.headlines = res.data.notice
          // 考卷
          this.papers = res.data.papers;
          // 考场
          this.rooms = res.data.rooms;
          // 学习动态
          this.news = res.data.news;

          // 系统配置数据
          let system = res.data.system;
          if (system) {
            this.system = system;
            uni.setStorageSync("system", system);

            // 轮播图
            if (system.banner) {
              let banners = [];
              for (let bannerImage of system.banner.split(",")) {
                console.log(
                  "bannerImage",
                  bannerImage,
                  bannerImage.indexOf("http")
                );
                if (bannerImage.indexOf("http") < 0) {
                  bannerImage = this.imgUrl + bannerImage;
                }

                banners.push({
                  image: bannerImage,
                });
              }
              this.banners = banners;
              console.log("banners", this.banners);

              // 延迟加载：v-if导致组件未完全渲染
              // setTimeout(() => {
              // 	this.banners = system.banner.split(',')
              // 	console.log('banners', this.banners)
              // }, 200)
            }
          }

          // 轮播图
          this.slides = res.data.slides;
          if (this.slides && this.slides.length > 0) {
            console.log("slides", res.data.slides);
            let slides = [];
            for (let slideImage of res.data.slides) {
              if (slideImage.image.indexOf("http") < 0) {
                slideImage.image = this.imgUrl + slideImage.image;
              }
              slides.push(slideImage);
            }
            this.banners = slides;
          }

          // 页面配置数据
          let page = res.data.page;
          if (page) {
            this.page = page;
            uni.setStorageSync("page", page);

            if (page.index_title) {
              this.title = page.index_title;
            }

            // 页面风格
            this.pageStyle = page.page_index_style
              ? page.page_index_style
              : "color";
            // this.pageStyle = 'color'
            // 底部栏风格
            this.tabbarStyle = page.page_tabbar_style
              ? page.page_tabbar_style
              : "glass";
            // 悬浮按钮
            this.showAdBtn = parseInt(page.page_index_ad_btn) == 1;
            this.showShareBtn = parseInt(page.page_index_share_btn) == 1;
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

            // // 激励广告
            // this.adUtils.rewarded.load('adunit-69d6a49d4c5999c5', () => {
            //       //这里写你的任意奖励事件
            // });
            // this.adUtils.rewarded.show();
          }
          // #endif

          // cdn域名
          if (res.data.cdn_url) {
            uni.setStorageSync("cdn_url", res.data.cdn_url);
          }

          // 自定义tabbar
          if (res.data.tabbar) {
            this.tabbar = res.data.tabbar;
            uni.setStorageSync("tabbar", this.tabbar);
          }

          // 自定义首页
          if (res.data.diy_index_button) {
            this.diyIndexButton = res.data.diy_index_button;
            uni.setStorageSync("diyIndexButton", this.diyIndexButton);
          }

          // 隐藏骨架屏
          this.showSkeleton = false;
          this.$forceUpdate();
        }
      );

      let user_subject = uni.getStorageSync("user_subject");
      if (user_subject && user_subject.subject2 && user_subject.subject2.id) {
        subjectApi
          .getSubjectDetail(this, { subject_id: user_subject.subject2.id })
          .then((res) => {
            console.log("res", res);
            if (res && res.code == 1) {
              let subject = res.data;
              uni.setStorageSync("user_subject", {
                subject1: {
                  id: subject.parent.id,
                  name: subject.parent.name,
                },
                subject2: {
                  id: subject.id,
                  name: subject.name,
                },
              });

              this.subjectName = subject.name;
            } else {
              uni.setStorageSync("user_subject", null);
              this.subjectName = "请选择";
            }
          });
      }
    },

    // 点击悬浮按钮
    clickFabItem(e) {
      console.log("clickFabItem", e);
    },

    // 跳转公告列表
    goNoticeList() {
      this.utils.goto("notice-list");
    },

    // 点击公告，跳转公告详情
    clickNotice(id) {
      this.utils.goto("notice-detail?id=" + id);
    },

    // 点击科目，跳转科目切换
    clickSubjectChang() {
      this.utils.goto("/pagesSubject/index?type=subject");
    },
  },
};
</script>

<style>
page {
  background-color: #fff;
}

.nav-list {
  display: flex;
  flex-wrap: wrap;
  padding: 0px 40upx 0px;
  justify-content: space-between;
}

.nav-li {
  padding: 30upx;
  border-radius: 12upx;
  width: 45%;
  margin: 0 2.5% 40upx;
  background-image: url(https://cdn.nlark.com/yuque/0/2019/png/280374/1552996358352-assets/web-upload/cc3b1807-c684-4b83-8f80-80e5b8a6b975.png);
  background-size: cover;
  background-position: center;
  position: relative;
  z-index: 1;
}

.nav-li::after {
  content: "";
  position: absolute;
  z-index: -1;
  background-color: inherit;
  width: 100%;
  height: 100%;
  left: 0;
  bottom: -10%;
  border-radius: 10upx;
  opacity: 0.2;
  transform: scale(0.9, 0.9);
}

.nav-li.cur {
  color: #fff;
  background: rgb(94, 185, 94);
  box-shadow: 4upx 4upx 6upx rgba(94, 185, 94, 0.4);
}

.nav-title {
  font-size: 32upx;
  font-weight: 300;
}

.nav-title::first-letter {
  font-size: 40upx;
  margin-right: 4upx;
}

.nav-name {
  font-size: 28upx;
  text-transform: Capitalize;
  margin-top: 20upx;
  position: relative;
}

.nav-name::before {
  content: "";
  position: absolute;
  display: block;
  width: 40upx;
  height: 6upx;
  background: #fff;
  bottom: 0;
  right: 0;
  opacity: 0.5;
}

.nav-name::after {
  content: "";
  position: absolute;
  display: block;
  width: 100upx;
  height: 1px;
  background: #fff;
  bottom: 0;
  right: 40upx;
  opacity: 0.3;
}

.nav-name::first-letter {
  font-weight: bold;
  font-size: 36upx;
  margin-right: 1px;
}

.nav-li text {
  position: absolute;
  right: 30upx;
  top: 30upx;
  font-size: 52upx;
  width: 60upx;
  height: 60upx;
  text-align: center;
  line-height: 60upx;
}

.text-light {
  font-weight: 300;
}

@keyframes show {
  0% {
    transform: translateY(-50px);
  }

  60% {
    transform: translateY(40upx);
  }

  100% {
    transform: translateY(0px);
  }
}

@-webkit-keyframes show {
  0% {
    transform: translateY(-50px);
  }

  60% {
    transform: translateY(40upx);
  }

  100% {
    transform: translateY(0px);
  }
}

/*banner*/

.tui-banner-box {
  width: 100%;
  padding-top: 20rpx;
  box-sizing: border-box;
  background: #fff;
}

.tui-banner-swiper {
  width: 100%;
  height: 320rpx;
}

.tui-banner-item {
  padding: 0 16rpx;
  box-sizing: border-box;
}

.tui-slide-image {
  width: 100%;
  height: 320rpx;
  display: block;
  border-radius: 12rpx;
  /* transition: all 0.1s linear; */
}

.tui-banner-scale {
  transform: scaleY(0.9);
  transform-origin: center center;
}

/* #ifdef MP-WEIXIN */
.tui-banner-swiper .wx-swiper-dot {
  width: 8rpx;
  height: 8rpx;
  display: inline-flex;
  background: none;
  justify-content: space-between;
}

.tui-banner-swiper .wx-swiper-dot::before {
  content: "";
  flex-grow: 1;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 16rpx;
  overflow: hidden;
}

.tui-banner-swiper .wx-swiper-dot-active::before {
  background: #fff;
}

.tui-banner-swiper .wx-swiper-dot.wx-swiper-dot-active {
  width: 16rpx;
}

/* #endif */
/*banner*/
</style>
