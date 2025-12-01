<!-- 新闻 详情 -->
<template>
	<view class="container">
		
		<!-- #ifdef H5 -->
		<!-- 顶部自定义导航 -->
		<tn-nav-bar fixed :bottomShadow="false" backTitle=" ">
			<view class="">
				<text class="tn-text-lg">公告</text>
				<text class="tn-text-xl tn-padding-left-sm tn-icon-group-circle"></text>
			</view>
		</tn-nav-bar>
		<!-- #endif -->

		<view>
			<view style="background-color: #FFFFFF;padding: 30rpx 30rpx 30rpx 30rpx;">
				<view class="titleBox text-xl text-black text-bold">{{newsData.name}}</view>
				
				<view class="flex justify-between text-df text-gray margin-top-sm margin-bottom-sm">
					<text>{{newsData.create_time_text}}</text>
					<!-- <text>{{newsData.type | typeF}}</text> -->
				</view>
				<!-- <image mode="widthFix" :src="newsData.img"></image> -->

				<!-- 中间文章区域 -->
				<view class="contentBox text-lg text-black margin-top-sm margin-tb-lg">
					<view class="margin-top-sm" v-html="newsData.contents">
					</view>
					
					<!-- <view class="margin-top-sm" v-for="(item, index) in newsData.contents.split('&')" v-html="item">
					</view> -->
				</view>
			</view>
		</view>

		<view class="cu-bar bg-white tabbar border shop bottomBox">
			<!-- <button class="action" open-type="share">
				<view class="cuIcon-share text-green">
					<view class="cu-tag badge">{{newsData.commentNum}}</view>
				</view>
				返回
			</button> -->
			<!-- <button class="action" open-type="contact">
				<view class="cuIcon-service text-green">
					<view class="cu-tag badge"></view>
				</view>
				联系我们
			</button> -->
			<view class="btn-group">
				<!-- <button style="width: 80%;height: 70rpx;" @click="handleClick()"
					class="cu-btn bg-gradual-blue round shadow-blur">
					<view class="cuIcon-appreciatefill text-white margin-right-xs"></view>
					前往
				</button> -->
				
				<tn-button shape="round" backgroundColor="#5677fc" padding="30rpx 0" width="200rpx" shadow @click="handleBack()" fontBold plain>
					<text class="primary-color">返回列表</text>
				</tn-button>
				
				<tn-button shape="round" backgroundColor="#5677fc" padding="30rpx 0" width="200rpx" shadow @click="handleGo()" v-if="newsData && newsData.front_info" fontBold>
					<text class="tn-color-white">点击前往</text>
				</tn-button>
			</view>
		</view>
		<view class="safe-area-inset-bottom"></view>
	</view>
</template>

<script>
	import noticeApi from "@/common/api/notice.js"
	export default {
		data() {
			return {
				newsData: [],
				requestStatus: false // 事件防抖
			}
		},
		onLoad(option) {
			console.log(option)
			this.getData(option.id);
		},
		methods: {
			// 获取详情
			getData(id) {
				console.log(id);
				
				noticeApi.getNoticeDetail(this, {id: id}).then(res => {
					if (res && res.data) {
						this.newsData = res.data
					}
				})
			},
			handleBack() {
				this.utils.goto('notice-list')
			},
			handleGo() {
				if (this.newsData && this.newsData.front_info) {
					this.utils.goto(this.newsData.front_info.full_url)
				}
			},
		},
	}
</script>

<style lang="scss">
	button::after {
		border: none;
		background: transparent;
	}

	uni-button {
		background: transparent;
	}

	.container {
		background-color: #f2f2f2;
		width: 750rpx;

	}

	.solid {
		border-radius: 50rpx;
		text-indent: 12rpx;
	}

	image {
		width: 750rpx;
	}

	.bottomBox {
		width: 750rpx;
		position: fixed;
		left: 0;
		bottom: 0rpx;
	}
	
	.primary-color {
		color: #5677fc;
	}
</style>

