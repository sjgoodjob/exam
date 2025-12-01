<!-- 新闻 详情 -->
<template>
  <view class="container">

    <!-- #ifdef H5 -->
    <!-- 顶部自定义导航 -->
    <tn-nav-bar fixed :bottomShadow="false" backTitle=" ">
      <view class="">
        <text class="tn-text-lg">学习动态</text>
        <text class="tn-text-xl tn-padding-left-sm tn-icon-group-circle"></text>
      </view>
    </tn-nav-bar>
    <!-- #endif -->

    <view>
      <view style="background-color: #FFFFFF;padding: 30rpx 30rpx 30rpx 30rpx;">
        <view class="titleBox text-xl text-black text-bold">{{ newsData.name }}</view>

        <view class="flex justify-between text-df text-gray margin-top-sm margin-bottom-sm">
          <text>{{ newsData.create_time_text }}</text>
          <!-- <text>{{newsData.type | typeF}}</text> -->
        </view>
        <!-- <image mode="widthFix" :src="newsData.cover_image" @click="previewImage(0)"></image> -->

        <!-- 中间文章区域 -->
        <view class="contentBox text-lg text-black margin-top-sm margin-tb-lg">
          <view class="margin-top-sm" v-html="newsData.contents">
          </view>
          <!-- <view class="margin-top-sm" v-for="(item, index) in newsData.content.split('&')" v-html="item">
          </view> -->
        </view>
      </view>

      <!-- <view class="cu-bar justify-left bg-white margin-top-sm">
        <view class="action border-title">
          <text class="text-lg text-bold text-blue">图片展示</text>
          <text class="bg-gradual-blue" style="width:3rem"></text>
        </view>
      </view> -->
      <!-- <view style="background-color: #FFFFFF;padding: 0rpx 30rpx 30rpx 30rpx;">

        <image @click="previewImage(index)" v-for="(item, index) in newsData.images" :key="index"
          mode="widthFix" :src="item"></image>

        <view class="text-right text-df text-gray margin-top-sm margin-bottom-sm">
          <text class="text-gray cuIcon-attentionfill text-df" style="margin-right: 6rpx;"></text>
          <text class="text-df margin-right-sm" style="margin-top: 2rpx;">{{newsData.seeNum}}</text>
          <text class="text-gray cuIcon-appreciatefill text-df" style="margin-right: 6rpx;"></text>
          <text class="text-df margin-right-sm" style="margin-top: 2rpx;">{{newsData.likesNum}}</text>
          <text class="text-gray cuIcon-share text-df" style="margin-right: 6rpx;"></text>
          <text class="text-df" style="margin-top: 2rpx;">{{newsData.commentNum}}</text>
        </view>
      </view> -->
    </view>

    <view class="cu-bar bg-white tabbar border shop bottomBox">
      <view class="btn-group">
        <tn-button shape="round" backgroundColor="#5677fc" padding="30rpx 0" width="200rpx" shadow @click="handleBack()"
                   fontBold plain>
          <text class="primary-color">返回列表</text>
        </tn-button>

        <tn-button shape="round" backgroundColor="#5677fc" padding="30rpx 0" width="200rpx" shadow @click="handleGo()"
                   v-if="newsData && newsData.front_info && newsData.front_info.url" fontBold>
          <text class="tn-color-white">点击前往</text>
        </tn-button>
      </view>
    </view>
    <view class="safe-area-inset-bottom"></view>
  </view>
</template>

<script>
import newsApi from "@/common/api/news.js"

export default {
  data() {
    return {
      newsData: [],
      requestStatus: false // 事件防抖
    }
  },
  onLoad(option) {
    console.log(option)
    this.getData(option.id);
  },
  methods: {
    // 获取数据
    getData(id) {
      console.log(id);

      newsApi.getNewsDetail(this, {id: id}).then(res => {
        if (res && res.data) {
          this.newsData = res.data
        }
      })
    },
    handleBack() {
      this.utils.goto('news-list')
    },
    handleGo() {
      if (this.newsData && this.newsData.front_info) {
        this.utils.goto(this.newsData.front_info.full_url)
      }
    },
    previewImage(index) {
      // 预览功能需要数组格式，具体查看uniapp文档：previewImage
      const seeImgList = this.newsData.images
      uni.previewImage({
        current: index, //预览图片的下标
        urls: seeImgList //预览图片的地址，必须要数组形式，如果不是数组形式就转换成数组形式就可以
      })
    },

  },
}
</script>

<style lang="scss" scoped>
button::after {
  border: none;
  background: transparent;
}

uni-button {
  background: transparent;
}

.container {
  background-color: #f2f2f2;
  width: 750rpx;

}

.solid {
  border-radius: 50rpx;
  text-indent: 12rpx;
}

image {
  width: 750rpx;
}

.bottomBox {
  width: 750rpx;
  position: fixed;
  left: 0;
  bottom: 0rpx;
}
</style>
