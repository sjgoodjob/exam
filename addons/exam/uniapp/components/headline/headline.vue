<template>
	<view class="tui-rolling-news list-item">
		<text class="cuIcon-notification"></text>
		<swiper :vertical="true" :autoplay="true" :circular="true" :interval="4000" class="tui-swiper">
			<swiper-item v-for="(item, index) in list" :key="index" class="tui-swiper-item">
				<view class="tui-news-item">{{ item }}</view>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	export default {
		name: "headline",
		props: {
			list: {
				type: Array,
				default: function() {
					return [];
				}
			},
		},
		data() {
			return {

			};
		}
	}
</script>

<style>
	/*headlines*/

	.tui-rolling-news {
		width: 100%;
		padding: 0 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-wrap: nowrap;
		background: #fff;
	}

	.tui-rolling-news::after {
		left: 0;
	}

	.tui-swiper {
		margin-left: 8rpx;
		font-size: 28rpx;
		height: 80rpx;
		flex: 1;
	}

	.tui-swiper-item {
		display: flex;
		align-items: center;
	}

	.tui-news-item {
		line-height: 28rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		color: #555;
	}

	/*headlines*/
</style>
