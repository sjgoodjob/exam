<template>
  <view class="message tn-safe-area-inset-bottom">
    <!-- #ifdef H5 -->
    <!-- 顶部自定义导航 -->
    <tn-nav-bar fixed :bottomShadow="false" backTitle=" ">
      <view class="">
        <text class="tn-text-lg">学习动态列表</text>
        <text class="tn-text-xl tn-padding-left-sm tn-icon-group-circle"></text>
      </view>
    </tn-nav-bar>
    <!-- #endif -->

    <view v-if="list.length > 0">
      <view
        class="wallpaper-shadow tn-margin tn-padding"
        v-for="(item, index) in list"
        :key="index"
        @click="goDetail(item.id)"
      >
        <view class="tn-flex tn-flex-col-top">
          <!-- 这个是图片形式，自己看需要去使用 -->
          <view class="" v-if="item.cover_image">
            <view class="logo-pic">
              <view class="logo-image">
                <view
                  class=""
                  style="width: 100rpx; height: 100rpx; background-size: cover"
                  :style="{ backgroundImage: 'url(' + item.cover_image + ')' }"
                >
                </view>
              </view>
            </view>
          </view>
          <view class="tn-padding-left-sm" style="width: 100%">
            <view class="tn-flex tn-flex-row-between tn-flex-col-between">
              <view class="justify-content-item">
                <text class="tn-color-wallpaper tn-text-lg tn-text-bold">{{
                  item.name
                }}</text>
              </view>
            </view>
            <view class="tn-padding-top-xs tn-text-ellipsis-2">
              <!-- <text class="tn-color-gray">副标题</text> -->
            </view>
          </view>
        </view>
        <view
          class="tn-flex tn-flex-row-between tn-flex-col-between tn-margin-top-sm"
        >
          <view
            class="justify-content-item tn-color-gray tn-text-center tn-color-gray"
          >
            <text
              class="tn-icon-time tn-padding-right-xs tn-padding-left-xs tn-text-df"
            ></text>
            <text class="tn-text-sm">{{ item.create_time_text }}</text>
          </view>
          <!-- <view class="justify-content-item tn-round tn-text-xs tn-bg-blue--light tn-color-blue"
            style="padding: 5rpx 15rpx;">
            <text class="tn-padding-right-xs">#</text> 官 方
          </view> -->
        </view>
      </view>
    </view>

    <view v-else>
      <tui-no-data imgUrl="/static/img/img_noorder_3x.png"
        >暂无数据</tui-no-data
      >
    </view>

    <view class="tn-tabbar-height"></view>

    <!-- 底部导航栏组件 -->
    <tabbar :theme="tabbarStyle"></tabbar>
  </view>
</template>

<script>
import newsApi from "@/common/api/news.js";

export default {
  name: "Message",
  data() {
    return {
      list: [],
      hasMore: false,
      currentPage: 1,
      loadFlag: "loading",
    };
  },
  onLoad() {
    this.getData();
  },
  async onReachBottom() {
    console.log("onReachBottom");
    if (this.hasMore) {
      this.currentPage++;
      this.getData();
    }
  },
  methods: {
    getData() {
      this.loadFlag = "loading";

      let params = {
        page: this.currentPage,
      };

      newsApi.getNewsList(this, params).then((res) => {
        if (res && res.data) {
          this.list = this.list.concat(res.data.list.data);
          this.hasMore = res.data.list.has_more;
          this.currentPage = res.data.list.current_page;
          this.loadFlag = "over";
        }
      });
    },
    // 跳转详情
    goDetail(id) {
      this.utils.goto("news-detail?id=" + id);
    },
  },
};
</script>

<style lang="scss">
page {
  background-color: #fff;
}

.message {
  max-height: 100vh;
}

/* 自定义导航栏内容 start */
.custom-nav {
  height: 100%;

  &__back {
    margin: auto 30rpx;
    font-size: 40rpx;
    margin-right: 10rpx;
    flex-basis: 5%;
    width: 100rpx;
    position: absolute;
  }
}

/* 自定义导航栏内容 end */

/* 底部安全边距 start*/
.tn-tabbar-height {
  min-height: 20rpx;
  height: calc(40rpx + env(safe-area-inset-bottom) / 2);
  height: calc(40rpx + constant(safe-area-inset-bottom));
}

.tn-color-wallpaper {
  color: #1d2541;
}

/* 页面阴影 start*/
.wallpaper-shadow {
  border-radius: 15rpx;
  box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
}

/* 图标容器15 start */
.icon15 {
  &__item {
    width: 30%;
    background-color: #ffffff;
    border-radius: 10rpx;
    padding: 30rpx;
    margin: 20rpx 10rpx;
    transform: scale(1);
    transition: transform 0.3s linear;
    transform-origin: center center;

    &--icon {
      width: 105rpx;
      height: 105rpx;
      font-size: 60rpx;
      border-radius: 50%;
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
      }
    }
  }
}

/* 用户头像 start */
.logo-image {
  width: 100rpx;
  height: 100rpx;
  position: relative;
}

.logo-pic {
  background-size: cover;
  background-repeat: no-repeat;
  // background-attachment:fixed;
  background-position: top;
  border: 1rpx solid rgba(255, 255, 255, 0.05);
  box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.05);
  border-radius: 50%;
  overflow: hidden;
  // background-color: #FFFFFF;
}
</style>
