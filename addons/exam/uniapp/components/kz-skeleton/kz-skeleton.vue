<template>
	<view>
		<tui-skeleton v-if="showSkeleton" backgroundColor="#fafafa" borderRadius="10rpx"></tui-skeleton>
		<view class="container tui-skeleton">
			<image src="/static/img/train-banner1.png" mode="widthFix" class="tui-banner tui-skeleton-rect"></image>
			<view class="tui-text">
				<text class=" tui-skeleton-rect"> </text>
			</view>
		
			<view class="tui-view">
				<view class="tui-cell">
					<view class="tui-title tui-skeleton-rect"> </view>
					<view class="tui-link tui-skeleton-fillet" > </view>
				</view>
				<view class="tui-cell">
					<view class="tui-title tui-skeleton-rect"> </view>
					<view class="tui-link tui-skeleton-fillet" > </view>
				</view>
				<view class="tui-cell">
					<view class="tui-title tui-skeleton-rect"> </view>
					<view class="tui-link tui-skeleton-fillet" > </view>
				</view>
				<view class="tui-cell">
					<view class="tui-title tui-skeleton-rect"> </view>
					<view class="tui-link tui-skeleton-fillet" > </view>
				</view>
				<view class="tui-cell">
					<view class="tui-title tui-skeleton-rect"> </view>
					<view class="tui-link tui-skeleton-fillet" > </view>
				</view>
				<view class="tui-cell">
					<view class="tui-title tui-skeleton-rect"> </view>
					<view class="tui-link tui-skeleton-fillet" > </view>
				</view>
			</view>
		
		</view>
	</view>
</template>

<script>
	export default {
		name:"kz-skeleton",
		props: {
			// 显示开关
			showSkeleton: {
				type: Boolean,
				default: true
			},
		},
		data() {
			return {
				
			};
		}
	}
</script>

<style>
	.tui-banner {
		width: 100%;
		height: 375rpx;
	}
	
	.tui-text {
		width: 100%;
		padding: 12rpx 30rpx 20rpx;
		box-sizing: border-box;
		color: #B3B3B3;
		font-size: 26rpx;
		text-align: right;
		margin-top: 8rpx
	}
	
	.tui-view {
		width: 100%;
		padding: 20rpx 30rpx;
		box-sizing: border-box;
	}
	
	
	.tui-cell {
		padding: 24rpx 0;
		color: #555;
	}
	
	.tui-title {
		padding: 0 8rpx;
		box-sizing: border-box;
		display: inline-block;
	}
	
	.tui-link {
		width: 100%;
		padding: 30rpx;
		box-sizing: border-box;
		background: #fff;
		box-shadow: 0px 3rpx 20rpx rgba(183, 183, 183, 0.1);
		border-radius: 10rpx;
		color: #06c;
		margin-top: 20rpx;
		word-break: break-all;
	}
</style>
