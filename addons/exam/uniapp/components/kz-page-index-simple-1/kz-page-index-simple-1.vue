<template>
	<view class="page-index-list" :style="{'--custombar-height': vuex_custom_bar_height+'px'}">
		<!-- 顶部自定义导航 -->
		<tn-nav-bar backgroundColor="#81C0FF" :isBack="false" :bottomShadow="false" fontColor="#FFFFFF">{{title}}</tn-nav-bar>
		<view :style="{height: vuex_custom_bar_height + 'px'}"></view>
		<view class="page-index-list_content template-course tn-safe-area-inset-bottom">
			<!-- 用户信息 -->
			<view class="user-info-box">
				<view class="user-info-box_left flex align-center" @click="clickUserInfo">
					<view class="image-box">
						<image class="image" :src="userInfo.avatar" mode="" v-if="userInfo && userInfo.avatar"></image>
						<view class="no-image" v-else></view>
					</view>
					<view class="info-box">
						<view class="name">
							{{userInfo && userInfo.nickname ? userInfo.nickname : '请登录'}}
						</view>
						<view class="user-uid">
							ID：{{userInfo && userInfo.id ? userInfo.id : '请登录'}}
						</view>
					</view>
				</view>
				<view class="user-info-box_right">
					<!-- #ifdef MP-WEIXIN -->
					<tn-button shape="round" open-type="contact" size="sm" backgroundColor="tn-main-gradient-orangeyellow">人工客服</tn-button>
					<!-- #endif -->
				</view>
			</view>
			
			<!-- 科目 -->
			<view class="subject-box" @click="clickSubjectChang">
				<view class="subject-box-content tn-flex tn-flex-col-center tn-flex-row-between">
					<view class="tn-margin-left-sm tn-text-md">
						<text class="">{{subjectName}}</text>
					</view>
					<view class="tn-text-xl">
						<text class="tn-icon-sequence"></text>
					</view>
				</view>
			</view>
			
			<!-- 会员 -->
			<view class="subject-box" @click="goPage('/pages/user/member-center')">
				<view class="subject-box-content tn-flex tn-flex-col-center tn-flex-row-between">
					<view class="tn-margin-left-sm tn-text-md">
						<text class="">{{ userInfo && userInfo.info.status == 1 ? '成为会员，享受更多权益' : '查看更多会员权益' }}</text>
					</view>
					<view>
						<tn-button shape="round" size="sm" backgroundColor="tn-main-gradient-orangeyellow" @click="goPage('/pages/user/member-center')">开通VIP</tn-button>
					</view>
				</view>
			</view>
			
			<!-- 公告 -->
			<view class="subject-box">
				<view class="subject-box-content tui-rolling-news" v-if="notices.length">
					<view class="tn-flex tn-flex-col-center tn-color-aquablue">
						<tui-icon name="notice-fill" :size='20' color='tn-color-aquablue'></tui-icon>
						<text class="tn-text-md tn-color-aquablue">公告</text>
						<text class="tn-icon-title tn-text-xl margin-right-xs"></text>
					</view>
					<swiper vertical autoplay circular interval="3000" class="tui-swiper">
						<swiper-item v-for="(notice,index) in notices" :key="index" class="tui-swiper-item"
							@click="clickNotice(notice.id)">
							<view class="">{{ notice.name }}</view>
						</swiper-item>
					</swiper>
				</view>
			</view>
			
			<view class="tn-info__container tn-flex tn-flex-wrap tn-flex-col-center tn-flex-row-between tn-margin-left tn-margin-right">
				<block v-for="(item, index) in diyIndexButton" :key="index">
					<view class="tn-info__item tn-flex tn-flex-direction-row tn-flex-col-center tn-flex-row-between tn-color-white"
						:class="[item.bg_color]" @click="goPage(item.path)">
						<view class="tn-info__item__left tn-flex tn-flex-direction-row tn-flex-col-center tn-flex-row-left">
						  <!-- <view class="tn-info__item__left--icon tn-flex tn-flex-col-center tn-flex-row-center">
						    <view :class="[`tn-icon-${item.icon}`]"></view>
						  </view> -->
						  <view class="tn-info__item__left__content">
						    <view class="tn-info__item__left__content--title tn-text-bold" style="font-size: 32rpx;color: #080808;">{{ item.name }}</view>
						    <view class="tn-info__item__left__content--data tn-padding-top-xs tn-color-grey">
						      前往 <text class="tn-icon-right tn-padding-left-xs"></text>
						    </view>
						  </view>
						</view>
						<view class="tn-info__item__right">
						  <view class="tn-info__item__right--icon" :class="[item.color]" v-if="item.type == 'icon'">
						    <view :class="[item.icon]" style="text-shadow: 5rpx 8rpx 10rpx rgba(0,0,0,0.08);"></view>
						  </view>
						  
						  <view class="tn-info__item__right--image" v-else-if="item.type == 'image'">
						    <image class="image" :src="item.image" mode=""></image>
						  </view>
						</view>
						<view class="tn-info__item__bottom">
						  <view class='name tn-text-sm tn-color-gray' style="margin-left: -10rpx;">
						    <text class="tn-icon-code tn-padding-right-xs" style="opacity: 0;"></text>
						  </view>
						</view>
					</view>
				</block>
			</view>

		</view>
	</view>
</template>

<script>
	import template_page_mixin from '@/libs/mixin/template_page_mixin.js'
	// import NavIndexButton from '@/libs/components/nav-index-button.vue'

	export default {
		name: "kz-page-index-list1",
		mixins: [template_page_mixin],
		components: {
			// NavIndexButton
		},
		props: {
			// 标题
			title: {
				type: String,
				default: ''
			},
			userInfo: {
				type: Object,
				default: () => {}
			},
			// 关注提示
			subjectName: {
				type: String,
				default: '请选择科目'
			},
			// banner图集合
			diyIndexButton: {
				type: Array,
				default: () => []
			},
			// 公告集合
			notices: {
				type: Array,
				default: () => []
			},
			// 试卷集合
			papers: {
				type: Array,
				default: () => []
			},
			// 考场集合
			rooms: {
				type: Array,
				default: () => []
			},
			// 功能集合
			modules: {
				type: Array,
				default: () => [{
						title: '看题模式',
						color: 'tn-bg-green',
						icon: 'tn-icon-eye',
						url: '/pages/train/index?page=look',
					},
					{
						title: '答题练习',
						color: 'tn-bg-blue',
						icon: 'tn-icon-edit-write',
						url: '/pages/train/index?page=train',
					},
					{
						title: '模拟考试',
						color: 'tn-bg-orange',
						icon: 'tn-icon-edit-form',
						url: '/pages/paper/index'
					},
					{
						title: '我的错题',
						color: 'tn-bg-red',
						icon: 'tn-icon-close-circle',
						url: '/pages/wrong/index'
					},
					{
						title: '考场报名',
						color: 'tn-bg-cyan',
						icon: 'tn-icon-empty-data',
						url: '/pages/room/index'
					},
					{
						title: '题目搜索',
						color: 'tn-bg-indigo',
						icon: 'tn-icon-search-list',
						url: '/pages/search/index'
					},
					{
						title: '题目收藏',
						color: 'tn-bg-purple',
						icon: 'tn-icon-like-lack',
						url: '/pages/collect/index'
					},
					// {
					// 	title: '报名记录',
					// 	color: 'orange',
					// 	icon: 'tn-icon-empty-coupon',
					// 	url: '/pages/room/signup-index'
					// },
				]
			},

		},
		watch: {
			/**
			 * 监听banners
			 * @param list
			 */
			banners(list) {
				console.log('watch banners', list)
				let banners = []
				for (var image of list) {
					banners.push({
						image: this.imgUrl + image
					})
				}
				this.banner = banners
				console.log('watch banner', this.banner)
			},
			/**
			 * 监听papers
			 * @param list
			 */
			papers(list) {
				this.bgColorList = this.utils.shuffle(this.bgColorList)
				// 随机颜色
				console.log('watch bgColorList', this.bgColorList)
			},
			/**
			 * 监听rooms
			 * @param list
			 */
			rooms(list) {},
		},
		data() {
			return {}
		},
		methods: {
			// 跳转页面
			goPage(page) {
				this.utils.goto(page)
			},
			// 点击科目，跳转科目切换
			clickSubjectChang() {
				this.utils.goto('/pagesSubject/index?type=subject')
			},
			clickUserInfo() {
				this.$emit('clickUserInfo')
			}
		}
	}
</script>

<style lang="scss" scoped>
	.page-index-list {
		min-height: 100vh;
		background: linear-gradient(180deg, #81C0FF var(--custombar-height), #FFFFFF 60vh);
	}

	.page-index-list_content {

		// background: linear-gradient(0deg, #f8f8f8 0%, #81C0FF 100%);
		.user-info-box {
			padding: 30rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;

			.user-info-box_left {
				.image-box {
					width: 150rpx;
					height: 150rpx;
					border-radius: 50%;
					padding: 3rpx;
					background-color: #FFFFFF;

					.image {
						width: 100%;
						height: 100%;
						border-radius: 50%;
					}

					.no-image {
						width: 100%;
						height: 100%;
						background-color: #F1F1F1;
						border-radius: 50%;
					}
				}

				.info-box {
					color: #FFFFFF;
					margin-left: 20rpx;

					.name {
						font-size: 32rpx;
						font-weight: bold;
					}

					.user-uid {
						margin-top: 16rpx;
						font-size: 28rpx;
					}
				}
			}
		}

		.subject-box {
			padding: 0 30rpx 30rpx;

			.subject-box-content {
				border-radius: 20rpx;
				background-color: #FFFFFF;
				padding: 20rpx;
			}
		}

	}

	/* 工作区展示 start */
	.tn-info {

		&__container {
			margin-bottom: 30rpx;
		}

		&__item {
			width: 47.7%;
			margin-bottom: 30rpx;
			padding: 40rpx 30rpx;
			border-radius: 10rpx;

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
				// background-image: url(https://resource.tuniaokj.com/images/cool_bg_image/2.png);
			}

			&__left {

				&--icon {
					width: 80rpx;
					height: 80rpx;
					border-radius: 50%;
					font-size: 40rpx;
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
						background-image: url(https://resource.tuniaokj.com/images/cool_bg_image/icon_bg5.png);
					}

					.image {
						width: 100%;
						height: 100%;
						border-radius: 50%;
					}
				}

				&__content {
					font-size: 25rpx;

					&--data {
						color: rgba(255, 255, 255, 0.5);
						margin-top: 0rpx;
						// font-weight: bold;
					}
				}
			}

			&__right {
				&--icon {
					position: absolute;
					right: 0rpx;
					top: 50rpx;
					font-size: 80rpx;
					width: 108rpx;
					height: 108rpx;
					text-align: center;
					line-height: 60rpx;
					opacity: 0.6;
				}
				
				&--image {
					position: absolute;
					right: -10rpx;
					top: 40rpx;
					width: 108rpx;
					height: 108rpx;
					
					.image {
						width: 80rpx;
						height: 80rpx;
					}
				}
			}

			// &__bottom {
			//   box-shadow: 0rpx 0rpx 30rpx 0rpx rgba(0, 0, 0, 0.12);
			//   border-radius: 0 0 10rpx 10rpx;
			//   position: absolute;
			//   width: 85%;
			//   line-height: 15rpx;
			//   left: 50%;
			//   bottom: -15rpx;
			//   transform: translateX(-50%);
			//   z-index: -1;
			//   text-align: center;
			// }
		}
	}

	/* 工作区展示 end */
	
	/*公告*/
	.tui-rolling-news {
		// width: calc(100% - 40rpx);
		// padding: 12rpx 20rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-wrap: nowrap;
		background: #fff;
		border-radius: 10rpx;
		// margin: 0 20rpx 30rpx;
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
		align-items: center
	}
	
	.tui-news-item {
		line-height: 24rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	
	/*公告*/
</style>