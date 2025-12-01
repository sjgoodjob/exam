<template>
	<view class="page-index-list">
		<view class="template-course tn-safe-area-inset-bottom">
			<view class="tn-padding-top-xs">
				<!-- 功能列表 start -->
				<view class="about-shadow tn-margin-top tn-padding-top-sm tn-padding-bottom-sm">
					<tn-scroll-list indicatorColor="#cfd2ff" indicatorActiveColor="#5677fc">
						<block v-for="(item, index) in modules" :key="index">
							<view class="tn-padding-sm tn-margin-xs" @click="goPage(item.url)">
								<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
									<view
										class="icon15__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur tn-color-white"
										:class="[item.color]">
										<view :class="[item.icon]"></view>
									</view>
									<view class="tn-text-center">
										<text class="tn-text-ellipsis">{{item.title}}</text>
									</view>
								</view>
							</view>
						</block>

					</tn-scroll-list>
				</view>
				<!-- 功能列表 end -->

				<!-- 试卷列表 -->
				<view class="about-shadow tn-margin-top tn-padding-top-xs tn-padding-bottom-sm"
					v-if="papers.length > 0">
					<view class="tn-flex tn-flex-row-between tn-margin-sm">
						<view class="justify-content-item tn-text-bold tn-text-xl">
							<text class="tn-icon-title "></text>
							<text class="">热门试卷</text>
						</view>
						<view class="justify-content-item tn-text-lg" @click="goPage('/pages/paper/index')">
							<text class="tn-padding-xs">更多</text>
							<text class="tn-icon-right"></text>
						</view>
					</view>

					<view class="tn-margin-top-sm">
						<view class="skill-sequence-panel-content-wrapper">
							<!--左边虚化-->
							<!-- <view class="hide-content-box hide-content-box-left"></view> -->
							<!--右边虚化-->
							<!-- <view class="hide-content-box hide-content-box-right"></view> -->
							<scroll-view scroll-x="true" class="kite-classify-scroll">
								<view class="kite-classify-cell shadow" v-for="(item, index) in papers" :key="index">
									<!-- <view :class="'nav-li bg-index' + (index + 1)"> -->
									<view :class="'nav-li bg-index' + bgColorList[index]">
										<view class="nav-name">{{ item.title }}</view>
									</view>
									<view class="nav-content"> </view>
									<view
										class="tn-flex tn-flex-row-between tn-flex-col-center tn-padding-left-sm tn-padding-right-sm tn-padding-bottom-sm"
										:class="[item.users.length ? '' : 'tn-margin']">
										<view class="justify-content-item tn-flex tn-flex-col-center"
											v-show="item.users.length">
											<view style="margin-right: 10rpx;margin-left: 20rpx;">
												<tn-avatar-group :lists="item.users" size="sm"></tn-avatar-group>
											</view>
											<text class="tn-color-gray">{{ item.join_count }}人</text>
										</view>
									</view>
									<view @click="goPage('/pages/paper/paper?id=' + item.id)" class="nav-btn shadow"
										:class="'bg-index' + bgColorList[index]">立即练习</view>
									<!-- <view @click="goPage('/pages/paper/paper?id=' + item.id)" class="nav-btn shadow" :class="'bg-index' + (index + 1)">立即练习</view> -->
								</view>
							</scroll-view>
						</view>
					</view>
				</view>

				<!-- 考场列表 -->
				<view class="about-shadow tn-margin-top-xl tn-padding-top-xs tn-padding-bottom-sm" v-if="rooms.length > 0" style="margin-bottom: 200rpx;">
					<view class="tn-flex tn-flex-row-between tn-margin-sm">
						<view class="justify-content-item tn-text-bold tn-text-xl">
							<text class="tn-icon-title "></text>
							<text class="">热门考场</text>
						</view>
						<view class="justify-content-item tn-text-lg" @click="goPage('/pages/room/index')">
							<text class="tn-padding-xs">更多</text>
							<text class="tn-icon-right"></text>
						</view>
					</view>

					<view class="tn-margin-top-sm">
						<view class="skill-sequence-panel-content-wrapper">
							<!-- 考场列表 start-->
							<view class="tn-flex tn-flex-wrap tn-margin-sm">
								<block v-for="(item, index) in rooms" :key="index">
									<view class="" style="width: 50%;">
										<view class="tn-blogger-content__wrap">
											<view :class="'nav-li bg-index' + bgColorList[index]">
												<view class="nav-name">{{ item.name }}</view>
											</view>
											
											<!-- <view
												class="tn-flex tn-flex-row-between tn-flex-col-center tn-padding-left-sm tn-padding-right-sm tn-padding-bottom-sm"
												:class="[item.users.length ? '' : 'tn-margin']">
												<view class="justify-content-item tn-flex tn-flex-col-center"
													v-show="item.users.length">
													<view style="margin-right: 10rpx;margin-left: 20rpx;">
														<tn-avatar-group :lists="item.users" size="sm"></tn-avatar-group>
													</view>
													<text class="tn-color-gray">{{ item.grade_count }}人</text>
												</view>
											</view> -->
											
											
											<!-- :class="'bg-index' + bgColorList[index]" -->
											<view @click="goPage('/pages/room/detail?id=' + item.id)" class="nav-btn shadow">立即参加</view>
										</view>
									</view>
								</block>
							</view>
							<!-- 考场列表 end-->
						</view>
					</view>
				</view>
			
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
			// 关注提示
			focusOnTip: {
				type: String,
				default: '点击「添加小程序」，下次访问更便捷'
			},
			// banner图集合
			banners: {
				type: Array,
				default: () => []
			},
			// 公告集合
			headlines: {
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
			rooms(list) {
			},
		},
		data() {
			return {
				// 图片域名
				imgUrl: this.imgUrl,
				// banner图集
				banner: [],
				// 图鸟颜色列表
				// tuniaoColorList: this.$t.color.getTuniaoColorList(),
				bgColorList: this.utils.shuffle([1,2,3,4,5,6]),
			}
		},
		methods: {
			// 跳转页面
			goPage(page) {
				this.utils.goto(page)
			},
		}
	}
</script>

<style lang="scss" scoped>
	.page-index-list {
		background-color: #FFFFFF;
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
				color: #AAAAAA;
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
					color: #82B2FF;
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
		background-color: #FFFFFF;
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
		padding: 10rpx 0 10rpx 10rpx;
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
		background-image: linear-gradient(to left, rgba(255, 255, 255, 0), #f3f3f3 60%);
	}

	.hide-content-box-right {
		right: 0;
		background-image: linear-gradient(to right, rgba(255, 255, 255, 0), #f3f3f3 60%);
	}

	.kite-classify-scroll {
		width: 100%;
		height: 380rpx;
		overflow: hidden;
		white-space: nowrap;
		padding-top: 15rpx;
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
		content: '';
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
		content: '';
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
</style>
