<template>
	<view>
		<tui-navigation-bar splitLine @init="initNavigation" @change="opacityChange" :scrollTop="scrollTop" :title="title" :backgroundColor="backgroundColor" :color="color">
			<view class="tui-header-icon" :style="{ marginTop: top + 'px' }"><tui-icon name="arrowleft" :color="opacity > 0.85 ? color : backgroundColor" @click="back"></tui-icon></view>
		</tui-navigation-bar>
	</view>
</template>

<script>
	export default {
		name:"topbar",
		props: {
			title: {
				type: String,
				default: ''
			},
			color: {
				type: String,
				default: '#333'
			},
			backgroundColor: {
				type: String,
				default: '#fff'
			},
			//滚动条滚动距离
			scrollTop: {
				type: [Number, String],
				default: 0
			},
		},
		data() {
			return {
				top: 0, //标题图标距离顶部距离
				opacity: 0,
				scrollTop: 0.5,
			};
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		methods: {
			initNavigation(e) {
				this.opacity = e.opacity;
				this.top = e.top;
			},
			opacityChange(e) {
				this.opacity = e.opacity;
			},
			back() {
				uni.navigateBack()
			},
		}
	}
</script>

<style>
.tui-header-icon {
	width: 100%;
	position: fixed;
	top: 0;
	padding: 0 12rpx;
	display: flex;
	align-items: center;
	height: 32px;
	transform: translateZ(0);
	z-index: 99999;
	box-sizing: border-box;
}
</style>
