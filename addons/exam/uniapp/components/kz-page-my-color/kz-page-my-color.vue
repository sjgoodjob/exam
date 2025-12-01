<template>
	<view class="page">
		<view class="top-backgroup"></view>

		<view class="about__wrap">
			<!-- 用户信息 -->
			<view class="tn-flex margin-bottom-xl user-view">
				<view class="tn-flex-1">
					<!-- 头像 -->
					<view class="logo-pic tn-shadow">
						<view class="logo-image">
							<view v-if="userInfo && userInfo.avatar" class="tn-shadow-blur user-avatar"
								:style="'background-image:url(' + userInfo.avatar + ')'">
							</view>
							<view v-else class="cuIcon-people user-avatar-empty" @tap="login()"></view>
						</view>
					</view>
				</view>
				<view class="tn-flex-3">
					<!-- 昵称 -->
					<view class="tn-color-white">
						<view class="tn-padding-left-sm">
							<block v-if="userInfo && userInfo.nickname">
								<view><text
										class="tn-text-md tn-text-bold tn-text-ellipsis">{{ userInfo.nickname }}</text>
								</view>
								<view><text class="tn-text-md tn-text-bold tn-text-ellipsis">({{userInfo.id}})</text>
								</view>
							</block>
							<text v-else class="tn-text-md tn-text-ellipsis" @tap="login()">点击授权登录</text>
						</view>

					</view>
				</view>
				<!-- 积分 -->
				<view class="tn-flex-2 user-item tn-color-white tn-text-sm" @click="goTo('/pages/score/score-log')">
					<view class="user-item-info">积分</view>
					<view class="text-bold">{{userInfo ? userInfo.info.score : 0}}分</view>
				</view>
				<!-- 会员信息 -->
				<view class="tn-flex-2 user-item tn-text-sm" @click="goTo('member-center')">
					<view class="tn-color-white user-item-info">会员</view>
					<view>
						<view class="" v-if="userInfo && userInfo.info.status == 1">
							<view class='cu-tag radius bg-yellow'>{{userInfo.info.type_text}}</view>
						</view>
						<view class="" v-else>
							<button class="cu-btn round bg-yellow sm" @click="goTo('member-center')">开通会员</button>
						</view>
					</view>

					<!-- <view class="tn-padding-right tn-padding-left-sm tn-text-ellipsis" @click="goTo('member-center')">
                        <view class="" v-if="userInfo && userInfo.info.status == 1">
                            <view class='cu-tag radius bg-yellow'>{{userInfo.info.type_text}}</view>
                        </view>
                      <view class="" v-else>
                          <button class="cu-btn round bg-yellow sm" @click="goTo('member-center')">开通会员</button>
                      </view>
                    </view> -->
				</view>
			</view>

			<!-- 常用功能 start-->
			<view class="tn-flex tn-flex-row-center tn-bg-white course-shadow course-radius">
				<view class="tn-padding-sm tn-margin-xs" v-for="(item, index) in useModule" :key="index">
					<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center"
						@click="goTo(item.url)">
						<view class="icon15__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur tn-color-white"
							:class="[item.color]">
							<view :class="[item.icon]"></view>
						</view>
						<view class="tn-text-center">
							<text class="tn-text-ellipsis tn-text-md">{{item.title}}</text>
						</view>
					</view>
				</view>
			</view>
			<!-- 常用功能 end-->

			<!-- 更多功能 -->
			<view class="about-shadow tn-margin-top-xl tn-padding-top-sm tn-padding-bottom-sm module-list">
				<tn-scroll-list :indicatorWidth="100" :indicatorBarWidth="30" indicatorColor="#cfd2ff"
					indicatorActiveColor="#5677fc">
					<view class="tn-flex tn-margin-left-sm tn-margin-right-sm tn-margin-top">
						<block v-for="(item, index) in moreModule" :key="index">
							<view class="tn-flex-1 tn-padding-sm tn-radius" @click="goTo(item.url)">
								<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
									<view
										class="icon11__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur"
										:class="[`tn-bg-${item.color}--light tn-color-${item.color}`]">
										<view :class="[`${item.icon}`]"></view>
									</view>
									<view class="tn-color-black tn-text-df tn-text-center tn-margin-top-sm">
										<text class="tn-text-ellipsis tn-text-md">{{ item.title }}</text>
									</view>
								</view>
							</view>
						</block>
					</view>
				</tn-scroll-list>
			</view>

			<!-- 更多信息-->
			<view class="about-shadow tn-margin-top-xl tn-padding-top-sm tn-padding-bottom-sm">
				<view class="">
					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/user/set')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-5 tn-color-white">
								<view class="tn-icon-my"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">个人设置</view>
							<!-- <view class="tn-margin-left-sm tn-color-red tn-icon-fire-fill"></view> -->
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/user/my-cate')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-4 tn-color-white">
								<view class="tn-icon-star-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">常用分类</view>
							<!-- <view class="tn-margin-left-sm tn-color-red tn-icon-fire-fill"></view> -->
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/cert/list')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-15 tn-color-white">
								<view class="tn-icon-honor-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">荣誉证书</view>
							<!-- <view class="tn-margin-left-sm tn-color-red tn-icon-fire-fill"></view> -->
						</view>
					</tn-list-cell>

					<!-- <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
            @click="goTo('/pagesCourse/order-list/order-list')">
            <view class="tn-flex tn-flex-col-center">
              <view
                class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-2 tn-color-white">
                <view class="tn-icon-empty-order"></view>
              </view>
              <view class="tn-margin-left-sm tn-flex-1">课程订单</view>
            </view>
          </tn-list-cell>

          <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
            @click="goTo('/pagesSignUp/list/list')">
            <view class="tn-flex tn-flex-col-center">
              <view
                class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-3 tn-color-white">
                <view class="tn-icon-edit-form"></view>
              </view>
              <view class="tn-margin-left-sm tn-flex-1">报名记录</view>
            </view>
          </tn-list-cell> -->

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/score/score-log')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-6 tn-color-white">
								<view class="tn-icon-flower-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">积分明细</view>
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/score/good-list')">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-10 tn-color-white">
								<view class="tn-icon-shop-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">积分商品</view>
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/score/order-list')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-9 tn-color-white">
								<view class="tn-icon-order-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">积分订单</view>
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pages/user/my-correction')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-6 tn-color-white">
								<view class="tn-icon-reply-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">反馈纠错</view>
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('/pagesSubject/index?type=onlySubject')" v-if="userInfo">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-4 tn-color-white">
								<view class="tn-icon-menu-square-select-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">已绑定科目</view>
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" arrow
						@click="goTo('member-protocol')">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-8 tn-color-white">
								<view class="tn-icon-safe-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">会员协议</view>
							<!-- <view class="tn-margin-left-sm tn-color-red tn-icon-fire-fill"></view> -->
						</view>
					</tn-list-cell>

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30" @click="clickFollow()"
						v-show="showFollowBtn">
						<view class="tn-flex tn-flex-col-center">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-7 tn-color-white">
								<view class="tn-icon-message-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">关注我们</view>
							<view class="tn-margin-left-sm tn-color-blue tn-icon-copy-fill"></view>
						</view>
					</tn-list-cell>

					<!-- #ifdef MP-WEIXIN -->
					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30">
						<button class="tn-flex tn-flex-col-center tn-button--clear-style text-left" open-type="contact">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-12 tn-color-white">
								<view class="tn-icon-wechat-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">联系客服</view>
							<view class="tn-margin-left-sm tn-color-green tn-icon-service-fill"></view>
							<button open-type="contact"></button>
						</button>
					</tn-list-cell>
					<!-- #endif -->

					<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30">
						<view class="tn-flex tn-flex-col-center" @tap.stop="clearStorage()">
							<view
								class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-16 tn-color-white">
								<view class="tn-icon-delete-fill"></view>
							</view>
							<view class="tn-margin-left-sm tn-flex-1">清除缓存</view>
							<view class="tn-margin-left-sm tn-color-red tn-icon-clear"></view>
						</view>
					</tn-list-cell>

					<!-- TODO 待定功能 -->
					<!-- <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30">
          <view class="tn-flex tn-flex-col-center">
            <view
              class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-8 tn-color-white">
              <view class="tn-icon-message-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">问题反馈</view>
            <view class="tn-margin-left-sm tn-color-blue tn-icon-copy-fill"></view>
          </view>
        </tn-list-cell>
        
        <tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="30">
          <view class="tn-flex tn-flex-col-center">
            <view
              class="icon1__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-cool-bg-color-16 tn-color-white">
              <view class="tn-icon-safe-fill"></view>
            </view>
            <view class="tn-margin-left-sm tn-flex-1">会员协议</view>
            <view class="tn-margin-left-sm tn-color-red tn-icon-fire-fill"></view>
          </view>
        </tn-list-cell> -->
				</view>
			</view>

			<!-- #ifdef MP-WEIXIN -->
			<!-- 友情链接 -->
			<view class="about-shadow tn-margin-top tn-margin-bottom-lg tn-padding-top-sm tn-padding-bottom-sm" v-show="friendApps.length > 0">
				<view class="tn-flex tn-flex-row-between tn-padding">
					<view class="justify-content-item tn-text-bold tn-text-lg">
						<text class="">友情链接</text>
					</view>
				</view>
				<tn-scroll-list :indicator="false">
					<view class="tn-flex tn-margin-left-sm tn-margin-right-sm tn-margin-top">
						<navigator target="miniProgram" :app-id="item.wx_app_id" :path="item.path ? item.path : '/pages/index/index'" version="release" hover-class="none" v-for="(item, index) in friendApps" :key="index">
							<view class="tn-flex-1 tn-padding-sm tn-radius">
								<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
									<!-- <view class="icon5__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur"
										:style="'background-image:url('+ item.image + ');background-size:100% 100%;'">
									</view> -->
									<image :src="item.image" mode="scaleToFill" style="width: 150rpx; height: 150rpx;"></image>
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

			<view style="height: 200rpx; margin-bottom: 200rpx;" class="margin-bottom-lg"> </view>

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
		</view>
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
			},
		},
		watch: {
			/**
			 * 监听user
			 * @param value
			 */
			user(value) {
				console.log('watch user', value)
				this.userInfo = value

				var page = uni.getStorageSync('page')
				this.showFollowBtn = parseInt(page.page_my_follow_mp_btn) == 1
			},
		},
		data() {
			return {
				userInfo: uni.getStorageSync('user'),
				useModule: [{
						title: '背题模式',
						color: 'tn-bg-orange', //'tn-bg-green',
						icon: 'tn-icon-eye',
						url: '/pages/train/index?page=look',
					},
					{
						title: '答题练习',
						color: 'tn-bg-bluepurple', //'tn-bg-blue',
						icon: 'tn-icon-edit-write',
						url: '/pages/train/index?page=train',
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
					},
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
						title: '题目搜索',
						icon: 'tn-icon-search-list',
						color: 'blue',
						url: '/pages/search/index'
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
					},
				],
				// 关注公众号
				showFollowBtn: false,
				showFollow: false,
				followImage: '',
			};
		},
		methods: {
			login() {
				this.$emit('login')
			},
			goTo(page) {
				this.$emit('goTo', page)
			},
			clearStorage() {
				console.log('emit clearStorage')
				this.$emit('clearStorage')
			},
			clickFollow() {
				var page = uni.getStorageSync('page')
				if (page.page_my_follow_mp_click == 'image') {
					this.followImage = this.appInfo.api_host + page.follow_mp_image
					this.showFollow = true
				} else if (page.page_my_follow_mp_click == 'article') {
					this.goTo('/pages/webview/webview?url=' + page.follow_mp_article_url)
				}
			},
		}
	}
</script>

<style lang="scss" scoped>
	.page {
		background-color: #fff;
		height: 100vh;
	}

	/* 顶部背景图 start */
	.top-backgroup {
		height: 400rpx;
		z-index: -1;
		background: #5677fc;
		border-radius: 0 0 10% 10%;

		.backgroud-image {
			width: 100%;
			height: 450rpx;
			// z-index: -1;
		}
	}

	/* 顶部背景图 end */

	/* 用户头像 start */
	.logo-image {
		width: 120rpx;
		height: 120rpx;
		position: relative;
	}

	.logo-pic {
		background-size: cover;
		background-repeat: no-repeat;
		// background-attachment:fixed;
		background-position: top;
		border-radius: 50%;
		overflow: hidden;
		background-color: #FFFFFF;
	}

	/* 页面 start*/
	.about-shadow {
		border-radius: 15rpx;
		box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
	}

	.about {

		&__wrap {
			position: relative;
			z-index: 1;
			margin: 20rpx 30rpx;
			margin-top: -230rpx;
		}
	}

	/* 页面 end*/

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
				font-size: 28rpx;
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
					// background-image: url(https://tnuiimage.tnkjapp.com/cool_bg_image/icon_bg.png);
				}
			}
		}
	}
	
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
	        width: 100rpx;
	        height: 100rpx;
	        border-radius: 50%;
	        margin-bottom: 18rpx;
	        position: relative;
	        z-index: 1;
	      }
	    }
	  }

	/* 图标容器1 end */

	.my-shadow {
		box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.07);
		border-radius: 20rpx;
	}

	/* 图标容器7 start */
	.icon7 {
		&__item {
			width: 30%;
			background-color: #FFFFFF;
			border-radius: 10rpx;
			padding: 10rpx;
			margin: 20rpx 10rpx;
			transform: scale(1);
			transition: transform 0.3s linear;
			transform-origin: center center;

			&--icon {
				width: 100rpx;
				height: 100rpx;
				font-size: 60rpx;
				border-radius: 0;
				margin-bottom: 18rpx;
				position: relative;
				z-index: 1;
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
				width: 100rpx;
				height: 100rpx;
				font-size: 60rpx;
				// border-radius: 50%;
				margin-bottom: 0rpx;
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
					// background-image: url(https://tnuiimage.tnkjapp.com/cool_bg_image/icon_bg.png);


				}
			}
		}
	}

	/* 图标容器11 start */
	.icon11 {
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
					// background-image: url(https://tnuiimage.tnkjapp.com/cool_bg_image/icon_bg.png);
				}
			}
		}
	}

	.course-shadow {
		box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.07);
	}

	.course-radius {
		border-radius: 15rpx;
	}

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
				font-size: 50rpx;
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

	.avatar-eye {
		position: absolute;
		top: -10%;
		width: 65px;
		height: 65px;
		background: linear-gradient(105deg, white, #cb87f4);
		border-radius: 100%;
		box-shadow: 4px 8px 5px rgba(0, 0, 0, 0.2);
		margin: 3px;
		-webkit-transform: translateX(-50%);
		transform: translateX(-50%);
	}


	.avatar-eye--green {
		background: linear-gradient(to bottom, #fdfdfd, #c3efea);
	}

	.avatar-eye--violet {
		background: linear-gradient(to bottom, #fdfdfd, #e6d6f6);
	}


	.eye--left {
		left: 10%;
	}

	.eye--right {
		left: 85%;
	}

	.eye--center {
		left: 45%;
		top: 10%;
	}

	.avatar-eye-pupil {
		position: absolute;
		width: 55%;
		height: 55%;
		left: 50%;
		top: 25%;
		-webkit-transform: translate(-50%);
		transform: translate(-50%);
		z-index: 100;
		border-radius: 100%;
	}


	.pupil--green {
		background: linear-gradient(135deg, rgba(188, 248, 177, 0.7), #2fa38c 75%);
	}

	.pupil--pink {
		background: linear-gradient(135deg, #f1a183, #8535cd);
	}


	.avatar-eye-pupil-blackThing {
		position: absolute;
		width: 55%;
		height: 55%;
		left: 50%;
		top: 25%;
		background: #2c2f32;
		-webkit-transform: translate(-50%);
		transform: translate(-50%);
		border-radius: 100%;
		box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
	}

	.avatar-eye-pupil-lightReflection {
		position: absolute;
		width: 7px;
		height: 7px;
		left: 25%;
		top: 10%;
		background: #ebebeb;
		-webkit-transform: translate(-50%);
		transform: translate(-50%);
		border-radius: 100%;
		box-shadow: 10px 10px 10px rgba(255, 255, 255, 0.2);
	}





	/**/
	@keyframes t {

		0%,
		10%,
		80%,
		100% {
			top: -30px;
		}

		20% {
			top: 0px;
		}

		30% {
			top: -20px;
		}

		40% {
			top: -0px;
		}

		50% {
			top: -25px;
		}

		70% {
			top: 0px;
		}
	}

	@keyframes b {

		0%,
		10%,
		80%,
		100% {
			bottom: -30px;
		}

		20% {
			bottom: 0px;
		}

		30% {
			bottom: -20px;
		}

		40% {
			bottom: -0px;
		}

		50% {
			bottom: -25px;
		}

		70% {
			bottom: 0px;
		}
	}

	@keyframes mouth {

		0%,
		10%,
		100% {
			width: 100%;
			height: 0%;
		}

		15% {
			width: 90%;
			height: 30%;
		}

		20% {
			width: 50%;
			height: 70%;
		}

		25% {
			width: 70%;
			height: 70%;
		}

		30% {
			width: 80%;
			height: 60%;
		}

		35% {
			width: 60%;
			height: 70%;
		}

		40% {
			width: 55%;
			height: 75%;
		}

		45% {
			width: 50%;
			height: 90%;
		}

		50% {
			width: 40%;
			height: 70%;
		}

		55% {
			width: 70%;
			height: 95%;
		}

		60% {
			width: 40%;
			height: 50%;
		}

		65% {
			width: 100%;
			height: 60%;
		}

		70% {
			width: 100%;
			height: 70%;
		}

		75% {
			width: 90%;
			height: 70%;
		}

		80% {
			width: 50%;
			height: 70%;
		}

		85% {
			width: 90%;
			height: 50%;
		}

		85% {
			width: 40%;
			height: 70%;
		}

		90% {
			width: 90%;
			height: 30%;
		}

		95% {
			width: 100%;
			height: 10%;
		}
	}

	@keyframes tongue {

		0%,
		20%,
		100% {
			bottom: -80px;
		}

		30%,
		90% {
			bottom: -40px;
		}

		40% {
			bottom: -45px;
		}

		50% {
			bottom: -50px;
		}

		70% {
			bottom: -80px;
		}

		90% {
			bottom: -40px;
		}
	}

	.module-list {
		background-color: #fff;
	}

	.user-avatar {
		width: 120rpx;
		height: 120rpx;
		background-size: cover;
	}

	.user-avatar-empty {
		width: 120rpx;
		height: 120rpx;
		line-height: 120rpx;
		text-align: center;
		font-size: 80rpx;
		color: #fff;
		background-color: #ccc;
		box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
	}
</style>