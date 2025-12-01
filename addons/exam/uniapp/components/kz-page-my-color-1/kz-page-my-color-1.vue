<template>
  <view class="page-e tn-safe-area-inset-bottom">
    <view class="top-backgroup" v-if="userInfo">
      <image :src="imgHost + '/assets/addons/exam/img/my/my-bg4.png'" mode="widthFix" class="backgroud-image">
      </image>
    </view>

    <view class="tn-margin-left tn-margin-right" :style="{paddingTop: vuex_custom_bar_height + 'px'}">
      <!-- 图标logo/头像 -->
      <view class="tn-flex tn-flex-row-between tn-flex-col-center tn-margin-bottom" style="margin-top: -450rpx;"
            v-if="userInfo && userInfo.nickname">
        <view class="justify-content-item">
          <view class="tn-flex tn-flex-col-center tn-flex-row-left">
            <view class="logo-pic tn-shadow" @click="goTo('/pages/user/set')">
              <view class="logo-image">
                <view class="tn-shadow-blur" :style="'background-image:url(' + userInfo.avatar + ')'"
                      style="width: 110rpx;height: 110rpx;background-size: cover;overflow: hidden;">
                </view>
              </view>
            </view>
            <view class="tn-padding-right">
              <view class="tn-padding-right tn-padding-left-sm">
                <block>
                  <text
                    class="tn-color-wallpaper tn-text-xl tn-text-bold">{{ userInfo.nickname }}
                  </text>
                  <text class=" tn-round tn-text-xs tn-bg-red tn-color-white tn-margin-left-sm"
                        style="padding: 10rpx 20rpx;"
                        v-if="userInfo.info && userInfo.info.type_text">{{ userInfo.info.type_text }}
                  </text>
                </block>
              </view>
              <view
                class="tn-padding-right tn-padding-top-xs tn-padding-left-sm tn-text-ellipsis tn-text-sm"
                @click="goTo('/pages/score/score-log')">
                <text class="tn-color-gray">积分: {{ userInfo ? userInfo.info.score : 0 }}分</text>
                <text class="tn-color-blue--disabled tn-padding-left-xs tn-icon-share-square"></text>
              </view>
            </view>

          </view>
        </view>
        <view class="justify-content-item" @click="goTo('/pages/user/set')">
          <view class="tn-icon-install tn-color-gray" style="font-size: 50rpx;opacity: 0.5;">
          </view>
        </view>
      </view>

      <!-- 没有授权，则显示这个授权按钮-->
      <view class="tn-flex tn-flex-row-between" @click="login()" v-else>
        <view class="tn-flex-1 justify-content-item tn-margin-xs tn-text-center">
          <tn-button shape="round" backgroundColor="#1D2541" fontColor="#ffffff" padding="20rpx 0" width="40%"
                     shadow>
            <text class="tn-icon-login tn-padding-right-xs tn-text-xl"></text>
            <text class="">授权登录</text>
          </tn-button>
        </view>
      </view>

      <view class="tn-margin-top-xl">
        <view class="button-number tn-flex tn-flex-row-between tn-flex-col-center tn-shadow-blur"
              style="background: linear-gradient(-120deg, #3E445A, #31374A, #2B3042, #262B3C);">

          <view class="tn-margin-left" @click="tn('/homePages/member')">
            <view class="tn-flex tn-flex-col-center" style="color: #F1C68E;margin-top: -20rpx;">
              <text class="tn-text-bold tn-text-xl">会员</text>
              <text class="tn-icon-vip-text tn-text-center" style="font-size: 92rpx;"></text>
            </view>
            <view class="tn-color-white tn-text-sm">成为会员，享受更多权益</view>
          </view>
          <view class="tn-margin-right">
            <tn-button shape="round" backgroundColor="#F1C68E" fontColor="#634738" padding="10rpx 0"
                       width="160rpx" shadow @click="goTo('member-center')">
              <!-- <text class="tn-icon-vip tn-padding-right-sm tn-text-lg"></text> -->
              <text class="tn-text-bold">权 益</text>
            </tn-button>
          </view>

          <view class="tnwave waveAnimation">
            <view class="waveWrapperInner bgTop">
              <view class="wave waveTop"
                    :style="'background-image:url(' + imgHost + '/assets/addons/exam/img/wave/wave-2.png)'">
              </view>
            </view>
            <view class="waveWrapperInner bgMiddle">
              <view class="wave waveMiddle"
                    :style="'background-image:url(' + imgHost + '/assets/addons/exam/img/wave/wave-2.png)'">
              </view>
            </view>
            <view class="waveWrapperInner bgBottom">
              <view class="wave waveBottom"
                    :style="'background-image:url(' + imgHost + '/assets/addons/exam/img/wave/wave-1.png)'">
              </view>
            </view>
          </view>

        </view>
      </view>


      <!-- 更多信息-->
      <view class="about-shadow tn-margin-top-xl tn-padding-top-sm tn-padding-bottom-sm tn-bg-white">

        <!-- 方式12 start-->
        <view class="tn-flex tn-flex-row-center tn-radius tn-padding-top">
          <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/wrong/index')">
            <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
              <view
                class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                <view class="tn-color-wallpaper tn-icon-ticket"></view>
              </view>
              <view class="tn-text-center" style="font-size: 28rpx;">
                <text class="tn-text-ellipsis">我的错题</text>
              </view>
            </view>
          </view>

          <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/collect/index')">
            <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
              <view
                class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                <view class="tn-color-wallpaper tn-icon-close-circle"></view>
              </view>
              <view class="tn-text-center" style="font-size: 28rpx;">
                <text class="tn-text-ellipsis">题目收藏</text>
              </view>
            </view>
          </view>

          <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/room/signup-index')">
            <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
              <view
                class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                <view class="tn-color-wallpaper tn-icon-ticket"></view>
              </view>
              <view class="tn-text-center" style="font-size: 28rpx;">
                <text class="tn-text-ellipsis">考场报名</text>
              </view>
            </view>
          </view>

          <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/room/grade')">
            <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
              <view
                class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                <view class="tn-color-wallpaper tn-icon-identity"></view>
              </view>
              <view class="tn-text-center" style="font-size: 28rpx;">
                <text class="tn-text-ellipsis">考场成绩</text>
              </view>
            </view>
          </view>
        </view>
        <!-- 方式12 end-->

        <!-- 方式12 start-->
        <view class="tn-flex tn-flex-row-center tn-radius tn-padding-top">
          <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/paper/grade')">
            <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
              <view
                class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                <view class="tn-color-wallpaper tn-icon-order"></view>
              </view>
              <view class="tn-text-center" style="font-size: 28rpx;">
                <text class="tn-text-ellipsis">考卷成绩</text>
              </view>
            </view>
          </view>

          <block v-if="userInfo">

            <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/cert/list')">
              <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
                <view
                  class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                  <view class="tn-color-wallpaper tn-icon-honor"></view>
                </view>
                <view class="tn-text-center" style="font-size: 28rpx;">
                  <text class="tn-text-ellipsis">荣誉证书</text>
                </view>
              </view>
            </view>

            <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pages/user/my-cate')">
              <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
                <view
                  class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                  <view class="tn-color-wallpaper tn-icon-star"></view>
                </view>
                <view class="tn-text-center" style="font-size: 28rpx;">
                  <text class="tn-text-ellipsis">常用分类</text>
                </view>
              </view>
            </view>

            <view class="tn-padding-sm tn-margin-xs tn-radius" @click="goTo('/pagesSubject/index?type=onlySubject')">
              <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
                <view
                  class="icon12__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-bg-blue--light">
                  <view class="tn-color-wallpaper tn-icon-menu-square-select"></view>
                </view>
                <view class="tn-text-center" style="font-size: 28rpx;">
                  <text class="tn-text-ellipsis">已绑科目</text>
                </view>
              </view>
            </view>
          </block>
        </view>
        <!-- 方式12 end-->
      </view>


      <!-- 用户相关 -->
      <!-- <view class="wallpaper-shadow tn-margin-top-lg tn-padding-top-sm tn-padding-bottom-sm" v-if="userInfo">
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
          @click="goTo('/pages/user/my-cate')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
              style="color: #7C8191;">
              <view class="tn-icon-star-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">常用分类</view>
            <view class="tn-color-gray tn-icon-right"></view>
          </view>
        </tn-list-cell>
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
          @click="goTo('/pages/cert/index')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
              style="color: #7C8191;">
              <view class="tn-icon-honor-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">证书列表</view>
            <view class="tn-color-gray tn-icon-right"></view>
          </view>
        </tn-list-cell>
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
          @click="goTo('/pages/user/my-correction')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
              style="color: #7C8191;">
              <view class="tn-icon-reply-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">反馈纠错</view>
            <view class="tn-color-gray tn-icon-right"></view>
          </view>
        </tn-list-cell>
      </view> -->


      <!-- 积分相关 -->
      <view class="wallpaper-shadow tn-margin-top-lg tn-padding-top-sm tn-padding-bottom-sm" v-if="userInfo">
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
                      @click="goTo('/pages/score/score-log')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-search-list-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">积分明细</view>
            <view class="tn-color-gray tn-icon-right"></view>
          </view>
        </tn-list-cell>
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
                      @click="goTo('/pages/score/good-list')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-shop-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">积分商品</view>
            <view class="tn-color-gray tn-icon-right"></view>
          </view>
        </tn-list-cell>
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
                      @click="goTo('/pages/score/order-list')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-order-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">积分订单</view>
            <view class="tn-color-gray tn-icon-right"></view>
          </view>
        </tn-list-cell>
      </view>


      <!-- 更多信息 -->
      <view class="wallpaper-shadow tn-margin-top-lg tn-margin-bottom-lg tn-padding-top-sm tn-padding-bottom-sm">
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30"
                      @click="goTo('member-protocol')">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-safe-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">会员协议</view>
            <view
              class="tn-margin-left-sm tn-color-wallpaper tn-text-sm tn-padding-left-xs tn-padding-right-xs tn-bg-gray--light tn-round">
            </view>
          </view>
        </tn-list-cell>

        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" @click="clickFollow()"
                      v-show="showFollowBtn">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-message-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">关注我们</view>
            <view
              class="tn-margin-left-sm tn-color-wallpaper tn-text-sm tn-padding-left-xs tn-padding-right-xs tn-bg-gray--light tn-round">
            </view>
          </view>
        </tn-list-cell>

        <!-- #ifdef MP-WEIXIN -->
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30">
          <button class="tn-flex tn-flex-col-center tn-button--clear-style" open-type="contact">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-service-fill"></view>
            </view>
            <view class="tn-flex tn-flex-row-between" style="width: 100%;">
              <view class="tn-margin-left-sm">联系客服</view>
              <view class="tn-color-gray tn-icon-right"></view>
            </view>
          </button>
        </tn-list-cell>
        <!-- #endif -->

        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" @click="clearStorage()">
          <view class="tn-flex tn-flex-col-center">
            <view class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center"
                  style="color: #7C8191;">
              <view class="tn-icon-delete-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">退出登录</view>
            <view
              class="tn-margin-left-sm tn-color-wallpaper tn-text-sm tn-padding-left-xs tn-padding-right-xs tn-bg-gray--light tn-round">
            </view>
          </view>
        </tn-list-cell>
      </view>
    </view>

    <!-- #ifdef MP-WEIXIN -->
    <!-- 友情链接 -->
    <view class="about-shadow tn-margin-top tn-margin-bottom-lg tn-padding-top-sm tn-padding-bottom-sm"
          v-show="friendApps.length > 0">
      <view class="tn-flex tn-flex-row-between tn-padding">
        <view class="justify-content-item tn-text-bold tn-text-lg">
          <text class="">友情链接</text>
        </view>
      </view>
      <tn-scroll-list :indicator="false">
        <view class="tn-flex tn-margin-left-sm tn-margin-right-sm tn-margin-top">
          <navigator target="miniProgram" :app-id="item.wx_app_id" :path="item.path ? item.path : '/pages/index/index'" version="release"
                     hover-class="none" v-for="(item, index) in friendApps" :key="index">
            <view class="tn-flex-1 tn-padding-sm tn-radius">
              <view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
                <!-- <view class="icon5__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur"
                  :style="'background-image:url('+ item.image + ');background-size:100% 100%;'">
                </view> -->
                <image :src="item.image" mode="scaleToFill" style="width: 150rpx; height: 150rpx;">
                </image>
                <view class="tn-color-black tn-text-df tn-text-center tn-margin-top-sm">
                  <text class="tn-text-ellipsis">{{ item.name }}</text>
                </view>
              </view>
            </view>
          </navigator>
        </view>
      </tn-scroll-list>
    </view>
    <!-- #endif -->


    <!-- 关注我们 -->
    <tui-modal :show="showFollow" @cancel="showFollow = false" :custom="true">
      <view class="tui-modal-custom">
        <image :src="followImage" mode="widthFix"></image>
        <view class="tui-modal-custom-text">

        </view>
        <tui-button height="72rpx" :size="28" type="primary" shape="circle" @click="showFollow = false">确定
        </tui-button>
      </view>
    </tui-modal>

    <view class="tn-tabbar-height"></view>

  </view>
</template>

<script>
export default {
  name: "kz-page-my-color",
  emits: ['login', 'goTo', 'clearStorage'],
  props: {
    // 用户信息
    user: {
      type: Object,
      default: () => null
    },
    // 友情链接
    friendApps: {
      type: Array,
      default: () => []
    }
  },
  watch: {
    /**
     * 监听user
     * @param value
     */
    user (value) {
      console.log('watch user', value)
      this.userInfo = value

      var page = uni.getStorageSync('page')
      this.showFollowBtn = parseInt(page.page_my_follow_mp_btn) == 1
    }
  },
  data () {
    return {
      show1: false,
      imgHost: this.imgUrl,

      userInfo: uni.getStorageSync('user'),
      useModule: [{
        title: '背题模式',
        color: 'tn-bg-orange', //'tn-bg-green',
        icon: 'tn-icon-eye',
        url: '/pages/train/index?page=look'
      },
        {
          title: '答题练习',
          color: 'tn-bg-bluepurple', //'tn-bg-blue',
          icon: 'tn-icon-edit-write',
          url: '/pages/train/index?page=train'
        },
        {
          title: '模拟考试',
          color: 'tn-bg-indigo', //'tn-bg-orange',
          icon: 'tn-icon-edit-form',
          url: '/pages/paper/index'
        },
        {
          title: '考场报名',
          color: 'tn-bg-purplered', //'tn-bg-cyan',
          icon: 'tn-icon-empty-data',
          url: '/pages/room/index'
        }
      ],
      moreModule: [{
        title: '题目收藏',
        icon: 'tn-icon-like-lack',
        color: 'orange',
        url: '/pages/collect/index'
      },
        {
          title: '我的错题',
          icon: 'tn-icon-close-circle',
          color: 'purple',
          url: '/pages/wrong/index'
        },
        {
          title: '考场记录',
          icon: 'tn-icon-ticket',
          color: 'purplered',
          url: '/pages/room/signup-index'
        },
        {
          title: '考场成绩',
          icon: 'tn-icon-identity',
          color: 'teal',
          url: '/pages/room/grade'
        },
        {
          title: '考卷成绩',
          icon: 'tn-icon-order',
          color: 'orangered',
          url: '/pages/paper/grade'
        }

      ],
      // 关注公众号
      showFollowBtn: true,
      showFollow: false,
      followImage: ''
    }
  },
  methods: {
    // 跳转
    tn (e) {
      uni.navigateTo({
        url: e
      });
    },

    // 跳转到图鸟UI
    navTuniaoUI () {
      uni.navigateToMiniProgram({
        appId: 'wxf3d81a452b88ff4b'
      })
    },

    // 收货地址
    navAddress () {
      uni.chooseAddress({})
    },

    // 单纯短震动
    short () {
      wx.vibrateShort();
    },

    //拨打固定电话
    callPhoneNumber () {
      uni.makePhoneCall({
        phoneNumber: "18219128888"
      });
    },
    // 复制id
    copyUid () {
      wx.vibrateShort();
      uni.setClipboardData({
        data: "10262008"
      })
    },
    // 复制开源地址
    copySource () {
      wx.vibrateShort();
      uni.setClipboardData({
        data: "https://ext.dcloud.net.cn/publisher?id=356088"
      })
    },


    login () {
      this.$emit('login')
    },
    goTo (page) {
      this.$emit('goTo', page)
    },
    clearStorage () {
      console.log('emit clearStorage')
      this.$emit('clearStorage')
    },
    clickFollow () {
      var page = uni.getStorageSync('page')
      if (page.page_my_follow_mp_click == 'image') {
        this.followImage = this.appInfo.api_host + page.follow_mp_image
        this.showFollow = true
      } else if (page.page_my_follow_mp_click == 'article') {
        this.goTo('/pages/webview/webview?url=' + page.follow_mp_article_url)
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.page-e {
  max-height: 100vh;
}

/* 底部安全边距 start*/
.tn-tabbar-height {
  min-height: 120rpx;
  height: calc(140rpx + env(safe-area-inset-bottom) / 2);
  height: calc(140rpx + constant(safe-area-inset-bottom));
}

.tn-color-wallpaper {
  color: #1D2541;
}


/* 自定义导航栏内容 start */
.custom-nav {
  height: 100%;

  &__back {
    margin: auto 5rpx;
    font-size: 40rpx;
    margin-right: 10rpx;
    flex-basis: 5%;
    width: 100rpx;
    position: absolute;
  }
}

/* 自定义导航栏内容 end */

/* 顶部背景图 start */
.top-backgroup {
  height: 450rpx;
  z-index: -1;

  .backgroud-image {
    width: 100%;
    height: 450rpx;
    z-index: -1;
  }
}

/* 顶部背景图 end */


/* 图标容器5 start */
.icon5 {
  &__item {
    // width: 30%;
    background-color: #FFFFFF;
    border-radius: 10rpx;
    padding: 0rpx;
    margin: 0rpx;
    transform: scale(1);
    transition: transform 0.3s linear;
    transform-origin: center center;

    &--icon {
      width: 96rpx;
      height: 96rpx;
      border-radius: 50%;
      margin-bottom: 18rpx;
      position: relative;
      z-index: 1;
    }
  }
}

/* 用户头像 start */
.logo-image {
  width: 110rpx;
  height: 110rpx;
  position: relative;
  overflow: hidden;
  border-radius: 50%;
}

.logo-pic {
  background-size: cover;
  background-repeat: no-repeat;
  // background-attachment:fixed;
  background-position: top;
  border: 8rpx solid rgba(255, 255, 255, 0.05);
  box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.15);
  border-radius: 50%;
  overflow: hidden;
  // background-color: #FFFFFF;
}

/* 页面阴影 start*/
.wallpaper-shadow {
  border-radius: 15rpx;
  box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
}

/* 页面阴影 end*/

/* 图标容器15 start */
.icon15 {
  &__item {
    width: 30%;
    background-color: #FFFFFF;
    border-radius: 10rpx;
    padding: 30rpx;
    margin: 20rpx 10rpx;
    transform: scale(1);
    transition: transform 0.3s linear;
    transform-origin: center center;

    &--icon {
      width: 100rpx;
      height: 100rpx;
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


/* 图标容器12 start */
.icon12 {
  &__item {
    width: 30%;
    background-color: #FFFFFF;
    border-radius: 10rpx;
    padding: 30rpx;
    margin: 20rpx 10rpx;
    transform: scale(1);
    transition: transform 0.3s linear;
    transform-origin: center center;

    &--icon {
      width: 15rpx;
      height: 15rpx;
      font-size: 50rpx;
      border-radius: 50%;
      margin-bottom: 38rpx;
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

/* 图标容器1 start */
.icon1 {
  &__item {
    // width: 30%;
    background-color: #FFFFFF;
    border-radius: 10rpx;
    padding: 30rpx;
    margin: 20rpx 10rpx;
    transform: scale(1);
    transition: transform 0.3s linear;
    transform-origin: center center;

    &--icon {
      width: 40rpx;
      height: 40rpx;
      font-size: 40rpx;
      border-radius: 50%;
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
        background-image: url(https://resource.tuniaokj.com/images/cool_bg_image/icon_bg.png);
      }
    }
  }
}

/* 图标容器1 end */


.button-shake {
  animation: shake 4s infinite;
}

@keyframes shake {

  5%,
  50% {
    transform: scale(1);
  }

  10% {
    transform: scale(0.9);
  }

  15% {
    transform: scale(1.15);
  }

  20% {
    transform: scale(1.15) rotate(-5deg);
  }

  25% {
    transform: scale(1.15) rotate(5deg);
  }

  30% {
    transform: scale(1.15) rotate(-3deg);
  }

  35% {
    transform: scale(1.15) rotate(2deg);
  }

  40% {
    transform: scale(1.15) rotate(0);
  }
}


/* 背景波浪高度 */
.button-number {
  width: 100%;
  height: 150rpx;
  border-radius: 15rpx;
  position: relative;
  z-index: 1;
}

/* 动态背景波浪*/
@keyframes move_wave {
  0% {
    transform: translateX(0) translateZ(0) scaleY(1)
  }

  50% {
    transform: translateX(-25%) translateZ(0) scaleY(1)
  }

  100% {
    transform: translateX(-50%) translateZ(0) scaleY(1)
  }
}

.tnwave {
  overflow: hidden;
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  top: 0;
  margin: auto;
  z-index: -1;
  border-radius: 15rpx;
}

.waveWrapperInner {
  position: absolute;
  width: 100%;
  overflow: hidden;
  height: 100%;
}

.wave {
  position: absolute;
  left: 0;
  width: 200%;
  height: 100%;
  background-repeat: repeat no-repeat;
  background-position: 0 bottom;
  transform-origin: center bottom;
}

.bgTop {
  opacity: 0.1;
}

.waveTop {
  background-size: 50% 45px;
}

.waveAnimation .waveTop {
  animation: move_wave 4s linear infinite;
}

.bgMiddle {
  opacity: 0.2;
}

.waveMiddle {
  background-size: 50% 40px;
}

.waveAnimation .waveMiddle {
  animation: move_wave 3.5s linear infinite;
}

.bgBottom {
  opacity: 0.3;
}

.waveBottom {
  background-size: 50% 35px;
}

.waveAnimation .waveBottom {
  animation: move_wave 2s linear infinite;
}
</style>
