<template>
	<view>
		
		<!-- banner -->
		<!-- <view class="tui-banner-box">
			<swiper
				:indicator-dots="true"
				:autoplay="true"
				:interval="5000"
				:duration="150"
				class="tui-banner-swiper"
				:circular="true"
				previous-margin="60rpx"
				next-margin="60rpx"
			>
				<swiper-item v-for="(item, index) in banners" :key="index" class="tui-banner-item">
				  <image :src="imgUrl + item" class="tui-slide-image" :class="[current != index ? 'tui-banner-scale' : '']" mode="scaleToFill" lazy-load></image>
				</swiper-item>
			</swiper>
		</view> -->
		<!-- color-ui样式 -->
		<!-- 
		<swiper class="swiper" indicator-dots="true" circular="true" autoplay="true" interval="5000" duration="500"
			style="height: 220px;">
			<swiper-item v-for="(item, index) in banner" :key="index">
				<image :src="imgUrl + item" style="width: 100%;height: 100%;"></image>
			</swiper-item>
		</swiper>
		-->
		<!-- banner end -->
		
		<!-- 公告 -->
		<!-- <view class="tui-rolling-news">
			<tui-icon name="news-fill" :size='24' color='#5677fc'></tui-icon>
			<swiper vertical autoplay circular interval="3000" class="tui-swiper">
				<swiper-item v-for="(item,index) in headlines" :key="index" class="tui-swiper-item">
					<view class="tui-news-item">{{item}}</view>
				</swiper-item>
			</swiper>
		</view> -->
		
		<!-- 菜单 -->
		<view class="nav-list margin-top-xl">
			<navigator hover-class="none" :url="item.url" class="nav-li" navigateTo :class="'bg-'+item.color"
				:style="[{animation: 'show ' + ((index+1)*0.2+1) + 's 1'}]" v-for="(item,index) in modules"
				:key="index">
				<view class="nav-title">{{ item.title }}</view>
				<view class="nav-name">{{ item.name }}</view>
				<text :class="'cuIcon-' + item.cuIcon"></text>
			</navigator>
		</view>
	</view>
</template>

<script>
	export default {
		name:"kz-page-index-simple",
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
			// 功能集合
			modules: {
				type: Array,
				default: () => [
					{
						title: '看题模式',
						name: 'train',
						color: 'green',
						cuIcon: 'creative',
						url: '/pages/train/index?page=look',
					},
					{
						title: '答题练习',
						name: 'train',
						color: 'olive',
						cuIcon: 'copy',
						url: '/pages/train/index?page=train',
					},
					{
						title: '模拟考试',
						name: 'exam',
						color: 'cyan',
						cuIcon: 'newsfill',
						url: '/pages/paper/index'
					},
					{
						title: '我的收藏',
						name: 'collect',
						color: 'blue',
						cuIcon: 'colorlens',
						url: '/pages/collect/index'
					},
					{
						title: '我的错题',
						name: 'wrong',
						color: 'purple',
						cuIcon: 'font',
						url: '/pages/wrong/index'
					},
					{
						title: '题目搜索',
						name: 'search',
						color: 'mauve',
						cuIcon: 'cuIcon',
						url: '/pages/search/index'
					},
					{
						title: '考场报名',
						name: 'signup',
						color: 'green',
						cuIcon: 'btn',
						url: '/pages/room/index'
					},
					{
						title: '报名记录',
						name: 'signupLog',
						color: 'orange',
						cuIcon: 'tagfill',
						url: '/pages/room/signup-index'
					},
				]
			},
		},
		watch: {
			/**
			 * 监听headlines
			 * @param val
			 */
			headlines(val) {
				console.log('watch headlines', val)
			}
		},
		data() {
			return {
				imgUrl: this.imgUrl,
			};
		}
	}
</script>

<style>
	.nav-list {
		display: flex;
		flex-wrap: wrap;
		padding: 0px 40 upx 0px;
		justify-content: space-between;
		padding-bottom: 100px;
	}

	.nav-li {
		padding: 30 upx;
		border-radius: 12 upx;
		width: 45%;
		margin: 0 2.5% 40 upx;
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
		border-radius: 10 upx;
		opacity: 0.2;
		transform: scale(0.9, 0.9);
	}

	.nav-li.cur {
		color: #fff;
		background: rgb(94, 185, 94);
		box-shadow: 4 upx 4 upx 6 upx rgba(94, 185, 94, 0.4);
	}

	.nav-title {
		font-size: 32 upx;
		font-weight: 300;
	}

	.nav-title::first-letter {
		font-size: 40 upx;
		margin-right: 4 upx;
	}

	.nav-name {
		font-size: 28 upx;
		text-transform: Capitalize;
		margin-top: 20 upx;
		position: relative;
	}

	.nav-name::before {
		content: "";
		position: absolute;
		display: block;
		width: 40 upx;
		height: 6 upx;
		background: #fff;
		bottom: 0;
		right: 0;
		opacity: 0.5;
	}

	.nav-name::after {
		content: "";
		position: absolute;
		display: block;
		width: 100 upx;
		height: 1px;
		background: #fff;
		bottom: 0;
		right: 40 upx;
		opacity: 0.3;
	}

	.nav-name::first-letter {
		font-weight: bold;
		font-size: 36 upx;
		margin-right: 1px;
	}

	.nav-li text {
		position: absolute;
		right: 30 upx;
		top: 30 upx;
		font-size: 52 upx;
		width: 60 upx;
		height: 60 upx;
		text-align: center;
		line-height: 60 upx;
	}

	.text-light {
		font-weight: 300;
	}

	@keyframes show {
		0% {
			transform: translateY(-50px);
		}

		60% {
			transform: translateY(40 upx);
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
			transform: translateY(40 upx);
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
		content: '';
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

	/* #ifndef MP-WEIXIN */
	>>>.tui-banner-swiper .uni-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		display: inline-flex;
		background: none;
		justify-content: space-between;
	}

	>>>.tui-banner-swiper .uni-swiper-dot::before {
		content: '';
		flex-grow: 1;
		background: rgba(255, 255, 255, 0.8);
		border-radius: 16rpx;
		overflow: hidden;
	}

	>>>.tui-banner-swiper .uni-swiper-dot-active::before {
		background: #fff;
	}

	>>>.tui-banner-swiper .uni-swiper-dot.uni-swiper-dot-active {
		width: 16rpx;
	}

	/* #endif */
	/*banner*/
	
	.tui-rolling-news {
		width: 100%;
		padding: 12rpx 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-wrap: nowrap;
		background: #fff;
	}
	
	.tui-swiper {
		font-size: 24rpx;
		height: 50rpx;
		flex: 1;
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
</style>