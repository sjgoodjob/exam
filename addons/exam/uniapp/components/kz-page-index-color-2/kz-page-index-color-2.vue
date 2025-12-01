<template>
  <view
    class="page-index-list"
    :style="{ '--custombar-height': vuex_custom_bar_height + 'px' }"
  >
    <!-- 顶部自定义导航 -->
    <tn-nav-bar
      backgroundColor="#5677fc"
      :isBack="false"
      :bottomShadow="false"
      fontColor="#FFFFFF"
      >{{ title }}</tn-nav-bar
    >
    <view :style="{ height: vuex_custom_bar_height + 'px' }"></view>

    <!-- 科目选择 -->
    <view class="tn-flex tn-flex-col-center tn-flex-row-between tn-padding-sm">
      <view
        class="justify-content-item tn-text-xl"
        @click="goPage('/pagesSubject/index?type=subject')"
      >
        <text class="tn-text-bold tn-text-lg">{{ subjectName }}</text>
        <text class="tn-icon-sequence tn-margin-left-sm"></text>
      </view>
      <view
        class="justify-content-item tn-text-xl"
        @click="goPage('notice-list')"
      >
        <text class="tn-icon-notice"></text>
      </view>
    </view>

    <view class="tn-margin-sm">
      <tn-swiper
        :list="banners"
        :height="300"
        :effect3d="false"
        mode="dot"
        @click="clickSwiper"
      ></tn-swiper>
    </view>

    <view class="cu-bar search bg-white">
      <view class="search-form round">
        <text class="cuIcon-search"></text>
				<input @input="inputSearch" @confirm="search()" :adjust-position="false" type="text"
					placeholder="请输入题目关键词进行搜索" confirm-type="search"></input>
      </view>
      <view class="action">
        <button class="cu-btn bg-primary shadow-blur round" @click="search()">
          搜索
        </button>
      </view>
    </view>

    <view
      class="template-course tn-safe-area-inset-bottom"
      style="margin-bottom: 200rpx"
    >
      <view class="">
        <!-- 功能列表 start -->
        <view class="about-shadow tn-flex tn-flex-wrap">
          <view
            class="classify-item-box"
            v-for="(item, index) in moduleList"
            :key="index"
          >
            <view class="tn-padding-sm tn-margin-xs" @click="goPage(item.path)">
              <view
                class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center"
              >
                <view
                  v-if="item.type == 'icon'"
                  class="icon15__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur tn-color-white"
                  :class="[item.color]"
                >
                  <view :class="[item.icon]"></view>
                </view>

                <view
                  v-else-if="item.type == 'image'"
                  class="icon15__item--image tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur tn-color-white"
                  :style="{ backgroundImage: 'url(' + item.image + ')' }"
                >
                  <view
                    :style="{ backgroundImage: 'url(' + item.image + ')' }"
                  ></view>
                </view>

                <view class="tn-text-center">
                  <text class="tn-text-ellipsis" style="font-size: 28rpx">{{
                    item.name
                  }}</text>
                </view>
              </view>
            </view>
          </view>
        </view>
        <!-- 功能列表 end -->

        <!-- 公告 -->
        <view
          class="tui-rolling-news tn-bg-aquablue--light"
          v-if="notices.length"
        >
          <view class="tn-color-aquablue tn-flex tn-flex-col-center">
            <tui-icon
              name="notice-fill"
              :size="20"
              color="tn-color-aquablue"
            ></tui-icon>
            <text class="">公告</text>
            <text class="tn-icon-title tn-text-xl margin-right-xs"></text>
          </view>
          <swiper vertical autoplay circular interval="3000" class="tui-swiper">
            <swiper-item
              v-for="(notice, index) in notices"
              :key="index"
              class="tui-swiper-item"
              @click="clickNotice(notice.id)"
            >
              <view class="">{{ notice.name }}</view>
            </swiper-item>
          </swiper>
        </view>

        <!-- 模拟考试 -->
        <view class="about-shadow tn-margin-bottom" v-if="papers.length > 0">
          <view
            class="tn-flex tn-flex-col-center tn-flex-row-between tn-padding-sm"
          >
            <view class="justify-content-item tn-text-bold tn-text-xl">
              <text class="tn-icon-title"></text>
              <text class="">模拟考试</text>
            </view>
            <view
              class="justify-content-item tn-color-gray"
              @click="goPage('/pages/paper/index')"
            >
              <text class="tn-padding-xs tn-text-md">更多</text>
              <text class="tn-icon-right"></text>
            </view>
          </view>

          <view class="tn-padding-top-xs">
            <view class="skill-sequence-panel-content-wrapper">
              <!-- 试卷列表 start-->
              <view class="tn-flex tn-flex-wrap">
                <block v-for="(item, index) in papers" :key="index">
                  <view style="width: 50%">
                    <view
                      class="tn-blogger-content__wrap card-shadow padding-bottom-sm"
                    >
                      <image
                        class="margin-bottom-sm paper-image"
                        :src="item.cover_image"
                        mode="aspectFill"
                      ></image>
                      <view
                        class="padding-lr-xs margin-bottom-sm text-center text-bold text-cut"
                        style=""
                      >
                        {{ item.title }}
                      </view>
                      <view
                        class="nav-btn shadow bg-index3"
                        @click="goPage('/pages/paper/paper?id=' + item.id)"
                      >
                        立即练习
                      </view>
                    </view>
                  </view>
                </block>
              </view>
              <!-- 试卷列表 end-->
            </view>
          </view>
        </view>

        <!-- 考场列表 -->
        <view class="about-shadow tn-margin-bottom" v-if="rooms.length > 0">
          <view
            class="tn-flex tn-flex-col-center tn-flex-row-between tn-padding-sm"
          >
            <view class="justify-content-item tn-text-bold tn-text-xl">
              <text class="tn-icon-title"></text>
              <text class="">考前模拟</text>
            </view>
            <view
              class="justify-content-item tn-color-gray"
              @click="goPage('/pages/room/index')"
            >
              <text class="tn-padding-xs tn-text-md">更多</text>
              <text class="tn-icon-right"></text>
            </view>
          </view>

          <view class="tn-padding-top-xs">
            <view class="skill-sequence-panel-content-wrapper">
              <!-- 考场列表 start-->
              <view class="tn-flex tn-flex-wrap">
                <block v-for="(item, index) in rooms" :key="index">
                  <view style="width: 50%">
                    <view
                      class="tn-blogger-content__wrap card-shadow padding-bottom-sm"
                    >
                      <image
                        class="margin-bottom-sm paper-image"
                        :src="item.cover_image"
                        mode="aspectFill"
                      ></image>
                      <view
                        class="padding-lr-xs margin-bottom-sm text-center text-bold text-cut"
                        style=""
                      >
                        {{ item.name }}
                      </view>
                      <view
                        class="nav-btn shadow bg-index3"
                        @click="goPage('/pages/room/detail?id=' + item.id)"
                      >
                        立即报名
                      </view>
                    </view>
                  </view>
                </block>
              </view>
              <!-- 考场列表 end-->
            </view>
          </view>
        </view>

        <!-- 学习动态列表 -->
        <view
          class="about-shadow tn-margin-bottom"
          v-if="news.length > 0"
          style="margin-bottom: 200rpx"
        >
          <view
            class="tn-flex tn-flex-col-center tn-flex-row-between tn-padding-sm"
          >
            <view class="justify-content-item tn-text-bold tn-text-xl">
              <text class="tn-icon-title"></text>
              <text class="">学习动态</text>
            </view>
            <view
              class="justify-content-item tn-color-gray"
              @click="goPage('/pages/index/news-list')"
            >
              <text class="tn-padding-xs tn-text-md">更多</text>
              <text class="tn-icon-right"></text>
            </view>
          </view>

          <view class="">
            <view
              class="wallpaper-shadow tn-margin-sm tn-padding"
              v-for="(item, index) in news"
              :key="index"
              @click="goPage('/pages/index/news-detail?id=' + item.id)"
            >
              <view class="tn-flex tn-flex-col-top">
                <!-- 这个是图片形式，自己看需要去使用 -->
                <view class="" v-if="item.cover_image">
                  <view class="logo-pic">
                    <view class="logo-image">
                      <view
                        class=""
                        style="
                          width: 100rpx;
                          height: 100rpx;
                          background-size: cover;
                        "
                        :style="{
                          backgroundImage: 'url(' + item.cover_image + ')',
                        }"
                      >
                      </view>
                    </view>
                  </view>
                </view>
                <view class="tn-padding-left-sm" style="width: 100%">
                  <view class="tn-flex tn-flex-row-between tn-flex-col-between">
                    <view class="justify-content-item">
                      <text
                        class="tn-color-wallpaper tn-text-lg tn-text-bold"
                        >{{ item.name }}</text
                      >
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
        </view>
      </view>
    </view>
  </view>
</template>

<script>
import template_page_mixin from "@/libs/mixin/template_page_mixin.js";
// import NavIndexButton from '@/libs/components/nav-index-button.vue'

export default {
  name: "kz-page-index-list1",
  mixins: [template_page_mixin],
  components: {
    // NavIndexButton
  },
  props: {
    // banner图集合
    banners: {
      type: Array,
      default: () => [],
    },
    // 轮播图集合
    slides: {
      type: Array,
      default: () => [],
    },
    // 公告集合
    notices: {
      type: Array,
      default: () => [],
    },
    // 试卷集合
    papers: {
      type: Array,
      default: () => [],
    },
    // 考场集合
    rooms: {
      type: Array,
      default: () => [],
    },
    // 学习动态集合
    news: {
      type: Array,
      default: () => [],
    },
    // 功能集合
    modules: {
      type: Array,
      default: () => [],
    },
    // 科目ID
    subjectId: {
      type: Number,
      default: 0,
    },
    // 科目名称
    subjectName: {
      type: String,
      default: "",
    },
    // 标题
    title: {
      type: String,
      default: "",
    },
  },
  watch: {
    /**
     * 监听banners
     * @param list
     */
    banners(list) {
      console.log("watch banners", list);
      // let banners = []
      // for (var image of list) {
      // 	banners.push({
      // 		image: this.imgUrl + image
      // 	})
      // }
      // this.banner = banners
      this.banner = list;
      console.log("watch banner", this.banner, this.slides);
    },
    /**
     * 监听papers
     * @param list
     */
    papers(list) {
      this.bgColorList = this.utils.shuffle(this.bgColorList);
      // 随机颜色
      console.log("watch bgColorList", this.bgColorList);
    },
    /**
     * 监听rooms
     * @param list
     */
    rooms(list) {},
    /**
     * 监听modules
     * @param list
     */
    // modules(list) {
    // 	console.log('listen1 modules', list, this.moduleList)
    // 	if (list && list.length > 0) {
    // 		this.moduleList = list
    // 	}
    // },
  },
  mounted() {
    let diyIndexButton = uni.getStorageSync("diyIndexButton");
    if (diyIndexButton) {
      this.moduleList = diyIndexButton;
    }
    console.log("mounted diyIndexButton", diyIndexButton, this.moduleList);
  },
  data() {
    return {
      // 图片域名
      imgUrl: this.imgUrl,
      // banner图集
      banner: [],
      // 图鸟颜色列表
      // tuniaoColorList: this.$t.color.getTuniaoColorList(),
      bgColorList: this.utils.shuffle([1, 2, 3, 4, 5, 6]),
      // 搜索关键词
      keyword: "",
      moduleList: [
        {
          name: "背题模式",
          type: "icon",
          image: "",
          color: "tn-bg-green",
          icon: "tn-icon-eye",
          path: "/pages/train/index?page=look",
        },
        {
          name: "答题练习",
          type: "icon",
          image: "",
          color: "tn-bg-blue",
          icon: "tn-icon-edit-write",
          path: "/pages/train/index?page=train",
        },
        {
          name: "模拟考试",
          type: "icon",
          image: "",
          color: "tn-bg-orange",
          icon: "tn-icon-edit-form",
          path: "/pages/paper/index",
        },
        {
          name: "考场报名",
          type: "icon",
          image: "",
          color: "tn-bg-cyan",
          icon: "tn-icon-empty-data",
          path: "/pages/room/index",
        },
      ],
    };
  },
  methods: {
    // 跳转页面
    goPage(page) {
      this.utils.goto(page);
    },
    // 点击公告，跳转公告详情
    clickNotice(id) {
      this.utils.goto("notice-detail?id=" + id);
    },
    // 搜索输入
    inputSearch(e) {
      console.log("input e", e);
      this.keyword = e.detail.value;
    },
    // 搜索
    search() {
      this.utils.goto("/pages/search/index?keyword=" + this.keyword);
    },
		// 点击轮播图
    clickSwiper(index) {
      const slide = this.banners[index];
			console.log("clickSwiper", index, slide);
      if (slide && slide.front_info && slide.front_info != undefined && slide.front_info.full_url) {
				this.utils.goto(slide.front_info.full_url)
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.page-index-list {
  background-color: #ffffff;
}

.classify-item-box {
  width: 25%;

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
        width: 100rpx;
        height: 100rpx;
        font-size: 56rpx;
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

      &--image {
        width: 100rpx;
        height: 100rpx;
        border-radius: 50%;
        margin-bottom: 18rpx;
        position: relative;
        background-position: center;
        background-size: 100% 100%;

        &::after {
          width: 100%;
          height: 100%;
        }
      }
    }
  }
}

/* 自定义导航栏内容 start */
.custom-nav {
  height: 100%;

  &__back {
    margin: auto 5rpx;
    font-size: 40rpx;
    margin-right: 10rpx;
    margin-left: 30rpx;
    flex-basis: 5%;
  }

  &__search {
    flex-basis: 60%;
    width: 100%;
    height: 100%;

    &__box {
      width: 100%;
      height: 70%;
      padding: 10rpx 0;
      margin: 0 30rpx;
      border-radius: 60rpx 60rpx 0 60rpx;
      font-size: 24rpx;
    }

    &__icon {
      padding-right: 10rpx;
      margin-left: 20rpx;
      font-size: 30rpx;
    }

    &__text {
      color: #aaaaaa;
    }
  }
}

/*logo start */
.logo-image {
  width: 65rpx;
  height: 65rpx;
  position: relative;
}

.logo-pic {
  background-size: cover;
  background-repeat: no-repeat;
  // background-attachment:fixed;
  background-position: top;
  border-radius: 50%;
}

/* 自定义导航栏内容 end */

/* 内容布局 start*/
.course-shadow {
  box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.07);
}

.course-radius {
  border-radius: 15rpx;
}

/* 业务展示 start */
.tn-info {
  &__container {
    margin-top: 10rpx;
    margin-bottom: 50rpx;
  }

  &__item {
    width: 48%;
    margin: 15rpx 0rpx;
    padding: 40rpx 30rpx;
    border-radius: 15rpx;

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
      background-image: url(https://tnuiimage.tnkjapp.com/cool_bg_image/3.png);
    }

    &__left {
      &--icon {
        width: 80rpx;
        height: 80rpx;
        border-radius: 30%;
        font-size: 50rpx;
        margin-right: 20rpx;
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
          background-image: url(https://tnuiimage.tnkjapp.com/cool_bg_image/icon_bg5.png);
        }
      }

      &__content {
        font-size: 30rpx;

        &--data {
          margin-top: 5rpx;
          font-weight: bold;
        }
      }
    }

    &__right {
      &--icon {
        position: absolute;
        right: 0upx;
        top: 50upx;
        font-size: 100upx;
        width: 108upx;
        height: 108upx;
        text-align: center;
        line-height: 60upx;
        opacity: 0.15;
      }
    }
  }
}

/* 业务展示 end */

/* 文章内容 start*/
.tn-blogger-content {
  &__wrap {
    box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.07);
    border-radius: 20rpx;
    margin: 15rpx;
  }

  &__info {
    &__btn {
      margin-right: -12rpx;
      opacity: 0.5;
    }
  }

  &__label {
    &__item {
      line-height: 45rpx;
      padding: 0 20rpx;
      margin: 5rpx 18rpx 0 0;

      &--prefix {
        color: #82b2ff;
        padding-right: 10rpx;
      }
    }

    &__desc {
      line-height: 35rpx;
    }
  }

  &__main-image {
    border-radius: 16rpx 16rpx 0 0;

    &--1 {
      max-width: 690rpx;
      min-width: 690rpx;
      max-height: 400rpx;
      min-height: 400rpx;
    }

    &--2 {
      max-width: 260rpx;
      max-height: 260rpx;
    }

    &--3 {
      height: 212rpx;
      width: 100%;
    }
  }

  &__count-icon {
    font-size: 30rpx;
    padding-right: 5rpx;
  }
}

.image-music {
  padding: 100rpx 0rpx;
  font-size: 16rpx;
  font-weight: 300;
  position: relative;
}

.image-pic {
  background-size: cover;
  background-repeat: no-repeat;
  // background-attachment:fixed;
  background-position: top;
  border-radius: 20rpx 20rpx 0 0;
}

/* 文章内容 end*/

/* 底部tabbar start*/
.footerfixed {
  position: fixed;
  width: 100%;
  bottom: 0;
  z-index: 999;
  background-color: #ffffff;
  box-shadow: 0rpx 0rpx 30rpx 0rpx rgba(0, 0, 0, 0.07);
}

.tabbar {
  display: flex;
  align-items: center;
  min-height: 110rpx;
  justify-content: space-between;
  padding: 0;
  height: calc(110rpx + env(safe-area-inset-bottom) / 2);
  padding-bottom: calc(env(safe-area-inset-bottom) / 2);
}

.tabbar .action {
  font-size: 22rpx;
  position: relative;
  flex: 1;
  text-align: center;
  padding: 0;
  display: block;
  height: auto;
  line-height: 1;
  margin: 0;
  overflow: initial;
}

.tabbar .action .bar-icon {
  width: 100rpx;
  position: relative;
  display: block;
  height: auto;
  margin: 0 auto 10rpx;
  text-align: center;
  font-size: 42rpx;
}

.tabbar .action .bar-icon image {
  width: 50rpx;
  height: 50rpx;
  display: inline-block;
}

/*scroll-view外层*/
.skill-sequence-panel-content-wrapper {
  position: relative;
  white-space: nowrap;
  padding: 0 20rpx 20rpx;
  // background-color: #f2f5f9;
}

/*左右渐变遮罩*/
.hide-content-box {
  position: absolute;
  top: 0;
  height: 100%;
  width: 10px;
  z-index: 2;
}

.hide-content-box-left {
  left: 0;
  background-image: linear-gradient(
    to left,
    rgba(255, 255, 255, 0),
    #f3f3f3 60%
  );
}

.hide-content-box-right {
  right: 0;
  background-image: linear-gradient(
    to right,
    rgba(255, 255, 255, 0),
    #f3f3f3 60%
  );
}

.kite-classify-scroll {
  width: 100%;
  height: 360rpx;
  overflow: hidden;
  white-space: nowrap;
}

.kite-classify-cell {
  display: inline-block;
  width: 266rpx;
  height: 350rpx;
  margin-right: 20rpx;
  background-color: #ffffff;
  border-radius: 20rpx;
  overflow: hidden;
  box-shadow: 2px 2px 3px rgba(26, 26, 26, 0.2);
}

.nav-li {
  padding: 40rpx 30rpx;
  width: 100%;
  background-image: url(https://s1.ax1x.com/2020/06/27/NyU04x.png);
  background-size: cover;
  background-position: center;
  position: relative;
  z-index: 1;
  margin: 0 !important;
}

.nav-name {
  font-size: 28upx;
  text-transform: Capitalize;
  margin-top: 20upx;
  position: relative;
  text-overflow: ellipsis;
  overflow: hidden;
}

.nav-name::before {
  content: "";
  position: absolute;
  display: block;
  width: 40rpx;
  height: 6rpx;
  background: #fff;
  bottom: 0;
  right: 0;
  opacity: 0.5;
}

.nav-name::after {
  content: "";
  position: absolute;
  display: block;
  width: 100rpx;
  height: 1px;
  background: #fff;
  bottom: 0;
  right: 40rpx;
  opacity: 0.3;
}

.nav-content {
  width: 100%;
  padding: 15rpx;
  display: inline-block;
  overflow-wrap: break-word;
  white-space: normal;
}

.nav-btn {
  width: 200rpx;
  height: 60rpx;
  margin: 8rpx auto;
  text-align: center;
  line-height: 60rpx;
  border-radius: 10rpx;
  font-size: 28rpx;
}

.bg-index1 {
  background-color: #19cf8a;
  color: #fff;
}

.bg-index2 {
  background-color: #954ff6;
  color: #fff;
}

.bg-index3 {
  background-color: #5177ee;
  color: #fff;
}

.bg-index4 {
  background-color: #f49a02;
  color: #fff;
}

.bg-index5 {
  background-color: #ff4f94;
  color: #fff;
}

.bg-index6 {
  background-color: #7fd02b;
  color: #fff;
}

/* 页面阴影 start*/
.wallpaper-shadow {
  border-radius: 15rpx;
  box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
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

/*公告*/
.tui-rolling-news {
  width: calc(100% - 40rpx);
  padding: 12rpx 20rpx;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: nowrap;
  background: #fff;
  border-radius: 10rpx;
  margin: 0 20rpx 30rpx;
}

.tui-swiper {
  font-size: 28rpx;
  height: 50rpx;
  flex: 1;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.tui-swiper-item {
  display: flex;
  align-items: center;
}

.tui-news-item {
  line-height: 24rpx;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/*公告*/

.paper-image {
  width: 100%;
  height: 200rpx;
  border-radius: 10rpx 10rpx 0 0;
}
</style>
