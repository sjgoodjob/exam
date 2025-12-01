<script>
	import Vue from 'vue'
	import store from './store/index.js'
	import updateCustomBarInfo from './tuniao-ui/libs/function/updateCustomBarInfo.js'
	
	export default {
		onLaunch: function() {
			uni.getSystemInfo({
				success: function(e) {
					// #ifndef MP
					Vue.prototype.StatusBar = e.statusBarHeight;
					if (e.platform == 'android') {
						Vue.prototype.CustomBar = e.statusBarHeight + 50;
					} else {
						Vue.prototype.CustomBar = e.statusBarHeight + 45;
					};
					// #endif

					// #ifdef MP-WEIXIN
					Vue.prototype.StatusBar = e.statusBarHeight;
					let custom = wx.getMenuButtonBoundingClientRect();
					Vue.prototype.Custom = custom;
					Vue.prototype.CustomBar = custom.bottom + custom.top - e.statusBarHeight;
					// #endif		

					// #ifdef MP-ALIPAY
					Vue.prototype.StatusBar = e.statusBarHeight;
					Vue.prototype.CustomBar = e.statusBarHeight + e.titleBarHeight;
					// #endif
				}
			})
			
			// uni.getSystemInfo({
			//   success: function(e) {
			//     // #ifndef H5
			//     // 获取手机系统版本
			//     const system = e.system.toLowerCase()
			//     const platform = e.platform.toLowerCase()
			//     // 判断是否为ios设备
			//     if (platform.indexOf('ios') != -1 && (system.indexOf('ios') != -1 || system.indexOf('macos') != -1)) {
			//       Vue.prototype.SystemPlatform = 'apple'
			//     } else if (platform.indexOf('android') != -1 && (system.indexOf('android') != -1)) {
			//       Vue.prototype.SystemPlatform = 'android'
			//     } else {
			//       Vue.prototype.SystemPlatform = 'devtools'
			//     }
			//     // #endif
			//   }
			// })
			
			// 获取设备的状态栏信息和自定义顶栏信息
			// store.dispatch('updateCustomBarInfo')
			updateCustomBarInfo().then((res) => {
			  store.commit('$tStore', {
			    name: 'vuex_status_bar_height',
			    value: res.statusBarHeight
			  })
			  store.commit('$tStore', {
			    name: 'vuex_custom_bar_height',
			    value: res.customBarHeight
			  })
			})

		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		}

	}
</script>

<style lang="scss">
	@import "colorui/main.css";
	@import "colorui/icon.css";
	@import 'thorui/app.css';
	@import 'tuniao-ui/index.scss';
	@import 'tuniao-ui/iconfont.css';
	
	body {
	    font-family: "Maple Mono NF CN";
	    font-weight: normal;
	}
	
	page {
		background: #EDEDED;
		font-family: "Maple Mono NF CN";
		font-weight: normal;
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
	
	.fix-bottom {
		width: 100%;
		height: 100rpx;
	}
	
	.m-t-20 {
		margin-top: 20rpx;
	}
	
	.m-lr-10 {
		margin: 0 10rpx;
	}
	
	.m-lr-20 {
		margin: 0 20rpx;
	}
	
	.m-l-30 {
		margin: 30rpx;
	}
	
	.text-primary {
		color: #5677fc;
	}
	
	.bg-primary {
	    background-color: #5677fc;
	    color: #ffffff;
	}
	
	.line-primary::after,
	.lines-primary::after {
		border-color: #5677fc;
	}
	
	.card-view {
		width: 90%;
		padding: 20rpx;
		background: #fff;
		margin: 30rpx auto;
		border-radius: 15rpx;
		box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
	}
	
	.card-shadow {
		border-radius: 15rpx;
		box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
	}
	
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
</style>
