<template>
	<view>
		<block v-if="style === 'glass'">
			<view class="tabbar footerfixed dd-glass" style="border-radius: 100rpx;">
				<view class="action" v-for="(item, index) in tabbarArray" :key="index">
					<navigator :url="item.path" hover-class="none" open-type="reLaunch"
						:class="[clickClass(item.path)]">
						<view class="bar-icon">
							<view :class="[item.icon]">
							</view>
						</view>
						<view class="">{{item.name}}</view>
					</navigator>
				</view>
			</view>
		</block>


		<block v-if="style === 'simple'">
			<view style="height: 120rpx;"></view>
			<view class="disFix index-bottom radius20">
				<navigator url="/pages/index/index" hover-class="none" open-type="reLaunch">
					<image
						:src="route == '/pages/index/index' ? '/static/tabbar/index1.png' : '/static/tabbar/index.png'">
					</image>
					<text :style="'color:' + (route == '/pages/index/index' ? '#5677fc' : '#9b9b9b')">首页</text>
				</navigator>

				<navigator url="/pages/paper/index" hover-class="none" open-type="reLaunch">
					<image
						:src="route == '/pages/paper/index' ? '/static/tabbar/kaoshi1.png' : '/static/tabbar/kaoshi.png'">
					</image>
					<text :style="'color:' + (route == '/pages/paper/index' ? '#5677fc' : '#9b9b9b')">模拟考试</text>
				</navigator>

				<navigator class="index-b-center" url="/pages/index/news-list" hover-class="none" open-type="reLaunch">
					<image
						:src="route == '/pages/index/news-list' ? '/static/tabbar/zixun1.png' : '/static/tabbar/zixun.png'">
					</image>
					<text :style="'color:' + (route == '/pages/index/news-list' ? '#5677fc' : '#9b9b9b')">学习动态</text>
				</navigator>

				<navigator url="/pages/room/index" hover-class="none" open-type="reLaunch">
					<image :src="route == '/pages/room/index' ? '/static/tabbar/tiku1.png' : '/static/tabbar/tiku.png'">
					</image>
					<text :style="'color:' + (route == '/pages/room/index' ? '#5677fc' : '#9b9b9b')">考场列表</text>
				</navigator>

				<navigator url="/pages/user/user" hover-class="none" open-type="reLaunch">
					<image :src="route == '/pages/user/user' ? '/static/tabbar/user1.png' : '/static/tabbar/user.png'">
					</image>
					<text :style="'color:' + (route == '/pages/user/user' ? '#5677fc' : '#9b9b9b')">我的</text>
				</navigator>
			</view>
		</block>


	</view>
</template>

<script>
	export default {
		name: 'tabbar',
		props: {
			// 样式风格
			theme: {
				type: String,
				default: 'glass'
			},
			// 自定义tabbar
			tabbar: {
				type: Array,
				default: () => [{
						name: '首页',
						icon: 'tn-icon-home',
						path: '/pages/index/index'
					},
					{
						name: '模拟考试',
						icon: 'tn-icon-edit-form',
						path: '/pages/paper/index',
					},
					{
						name: '学习动态',
						icon: 'tn-icon-inventory',
						path: '/pages/index/news-list',
					},
					{
						name: '考场列表',
						icon: 'tn-icon-menu-list',
						path: '/pages/room/index',
					},
					{
						name: '我的',
						icon: 'tn-icon-my',
						path: '/pages/user/user',
					}
				],
			},
		},
		data() {
			return {
				navbar: false,
				route: '',
				style: 'glass',
				page: null,
				tabbarArray: [{
						name: '首页',
						icon: 'tn-icon-home',
						path: '/pages/index/index'
					},
					{
						name: '模拟考试',
						icon: 'tn-icon-edit-form',
						path: '/pages/paper/index',
					},
					{
						name: '学习动态',
						icon: 'tn-icon-inventory',
						path: '/pages/index/news-list',
					},
					{
						name: '考场列表',
						icon: 'tn-icon-menu-list',
						path: '/pages/room/index',
					},
					{
						name: '我的',
						icon: 'tn-icon-my',
						path: '/pages/user/user',
					}
				],
			};
		},
		watch: {
			/**
			 * 监听user
			 * @param value
			 */
			theme(value) {
				if (value && value != undefined) {
					this.style = value
				}
			},
			/**
			 * 监听user
			 * @param value
			 */
			tabbar(value) {
				if (value && value != undefined && value.length > 0) {
					this.tabbarArray = value
				}
				console.log('this.tabbarArray', this.tabbarArray)
			},
		},
		mounted() {
			var that = this;
			var pages = getCurrentPages();
			var currentPage = pages[pages.length - 1];
			const url = currentPage.route;
			const options = currentPage.options;
			let urlWithArgs = `/${url}?`;
			for (let key in options) {
				const value = options[key];
				urlWithArgs += `${key}=${value}&`;
			}
			that.route = urlWithArgs.substring(0, urlWithArgs.length - 1);

			// tabbar风格
			this.page = uni.getStorageSync('page')
			if (this.page?.page_tabbar_style) {
				this.style = this.page.page_tabbar_style
			}
			// console.log('tabbar style', this.style)

			let tabbar = uni.getStorageSync('tabbar')
			if (tabbar) {
				this.tabbarArray = tabbar
			}
			console.log('mounted tabbar', tabbar, this.tabbarArray)
		},
		methods: {
			// 选中样式
			clickClass(url) {
				return this.route == url ? 'text-primary' : 'tn-color-grey'
				// return this.route == url ? 'tn-color-grey--dark' : 'tn-color-grey'
			},
		}
	};
</script>

<style>
	.page {
		background: transparent;
	}

	button {
		overflow: inherit;
	}

	.disFix {
		display: flex;
		justify-content: space-between;
		z-index: 999;
	}

	.index-bottom {
		position: fixed;
		/* left: 0;
  bottom: 1;
  width: 100%; */
		height: 90rpx;
		background: #ffffff;
		box-shadow: 0 0 2px 0 rgba(134, 133, 133, 0.5);
		font-size: 0;
		box-sizing: border-box;
		/* padding-bottom: 0;
  padding-bottom: constant(safe-area-inset-bottom);
  padding-bottom: env(safe-area-inset-bottom); */

		bottom: 30rpx;
		width: 95%;
		position: fixed;
		margin: 0 auto;
		left: 0;
		right: 0;
	}

	.index-bottom>navigator,
	.index-bottom>view,
	.index-bottom>button {
		/* width: 20%; */
		flex: 1;
		display: inline-block;
		text-align: center;
	}

	.index-bottom>button image {
		display: block;
	}

	.index-bottom>button {
		border-radius: 0px;
		padding: 0px;
		line-height: 15px;
		background: transparent;
		font-size: 20rpx;
		margin: 0 auto;
	}

	.index-bottom>button::after {
		border: none;
	}

	.index-bottom text {
		font-size: 20rpx;
	}

	.index-bottom image {
		display: block;
		width: 38rpx;
		height: 38rpx;
		margin: 14rpx auto 6rpx;
	}

	.index-b-center {
		position: relative;
		width: 90rpx;
		height: 90rpx;
		margin: -40rpx 0 0;
	}

	.index-bottom .index-b-center image {
		width: 76rpx;
		height: 76rpx;
	}


	/* 底部tabbar 毛玻璃风格 start*/
	.footerfixed {
		position: fixed;
		// margin: 20rpx;
		margin: 40rpx 30rpx;
		width: 690rpx;
		bottom: calc(env(safe-area-inset-bottom) / 2);
		z-index: 999;
		background-color: rgba(255, 255, 255, 0.5);
		box-shadow: 0rpx 0rpx 30rpx 0rpx rgba(0, 0, 0, 0.07);
	}

	.tabbar {
		display: flex;
		align-items: center;
		min-height: 110rpx;
		justify-content: space-between;
		padding: 0;
		height: calc(110rpx + env(safe-area-inset-bottom) / 2);
		// padding-bottom: calc(env(safe-area-inset-bottom) / 2);
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

	/* 毛玻璃*/
	.dd-glass {
		width: 690rpx;
		backdrop-filter: blur(20rpx);
		-webkit-backdrop-filter: blur(20rpx);
	}
</style>