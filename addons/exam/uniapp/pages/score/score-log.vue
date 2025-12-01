<template>
	<view class="template-message">
		<!-- 顶部自定义导航 -->
		<!-- <tn-nav-bar fixed :bottomShadow="false" backTitle=" ">
      <view class="">
        <text class="tn-text-lg">积分记录</text>
        <text class="tn-text-xl tn-padding-left-sm tn-icon-group-circle"></text>
      </view>
    </tn-nav-bar> -->

		<!-- 积分统计 start-->
		<view class="tn-flex tn-message-fixed" :style="{paddingTop: vuex_custom_bar_height - 60 + 'px'}">
			<view class="tn-width-full">
				<view class="tn-flex">
					<view class="tn-flex-1 tn-col-4 tn-padding-sm tn-margin-xs">
						<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
							<view class="">
								<view class="tn-text-xxl tn-color-orange">{{user && user.info ? user.info.score : 0}}</view>
							</view>
							<view class="tn-margin-top-xs tn-color-gray tn-text-df tn-text-center">
								<text class="tn-icon-flower"></text>
								<text class="tn-padding-left-xs">积分值</text>
							</view>
						</view>
					</view>
					<view class="tn-flex-1 tn-col-4 tn-padding-sm tn-margin-xs">
						<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
							<view class="">
								<view class="tn-text-xxl tn-color-blue">{{user && user.info ? user.info.score_inc : 0}}</view>
							</view>
							<view class="tn-margin-top-xs tn-color-gray tn-text-df tn-text-center">
								<text class="tn-icon-add-circle"></text>
								<text class="tn-padding-left-xs">累计获得</text>
							</view>
						</view>
					</view>
					<view class="tn-flex-1 tn-col-4 tn-padding-sm tn-margin-xs">
						<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
							<view class="">
								<view class="tn-text-xxl tn-color-red">{{user && user.info ? user.info.score_dec : 0}}</view>
							</view>
							<view class="tn-margin-top-xs tn-color-gray tn-text-df tn-text-center">
								<text class="tn-icon-reduce-circle"></text>
								<text class="tn-padding-left-xs">累计支出</text>
							</view>
						</view>
					</view>
				</view>
			</view>


		</view>
		<!-- 积分统计 end-->

		<!-- 积分记录 start-->
		<view class="tn-safe-area-inset-bottom" style="margin-top: 60rpx;"
			:style="{paddingTop: vuex_custom_bar_height + 'px'}">
			<view class="tn-margin-top-lg">
				<view class="tn-flex tn-flex-row-between tn-strip-bottom-min tn-padding" v-for="(item,index) in list"
					:key="index">
					<view class="justify-content-item">
						<view class="tn-color-gray--dark tn-text-lg">
							{{item.type_text}}
						</view>
						<view class="tn-color-gray tn-padding-top-xs">
							{{item.createtime_text}}
						</view>
					</view>
					<view class="justify-content-item tn-text-xl tn-padding-top">
						<view :class="'tn-color-' + (item.kind == 'INC' ? 'blue--dark' : 'purplered') + ';'">
							{{(item.kind == 'INC' ? '+' : '-') + item.score}}
						</view>
					</view>
				</view>
			</view>
		</view>
		<!-- 积分记录 end-->

		<!-- 没有更多 -->
		<view class="tn-margin-top" v-if="list.length > 0">
			<tn-load-more class="tn-margin-top" :status="loadStatus" :loadText="loadText"></tn-load-more>
		</view>

		<!-- 无数据 -->
		<tui-no-data imgUrl="/static/img/img_noorder_3x.png" v-else>暂无数据</tui-no-data>

	</view>
</template>

<script>
	import template_page_mixin from '@/libs/mixin/template_page_mixin.js'
	import userApi from "@/common/api/user.js"
	import scoreApi from "@/common/api/score.js"

	export default {
		name: 'TemplateMessage',
		mixins: [template_page_mixin],
		data() {
			return {
				user: null,
				list: [],
				currentPage: 1,
				hasMore: false,
				loadText: {
					loadmore: '下拉加载',
					loading: '加载中',
					nomore: '没有数据啦'
				},
				loadStatus: '下拉加载',
			}
		},
		onLoad() {
			this.ajax()
		},
		onPullDownRefresh() {
			console.log('refresh');
			this.list = []
			this.currentPage = 1

			this.ajax()
		},
		async onReachBottom() {
			console.log("onReachBottom")
			if (this.hasMore) {
				this.currentPage++
				this.ajax()
			}
		},
		methods: {
			ajax(from_pull_down = false) {
				this.loadStatus = 'loading'

				userApi.getUserInfo(this).then(res => {
					console.log('user info res', res)
					this.user = res.data
					uni.setStorageSync('user', this.user)
				})

				scoreApi.getUserScoreLog(this, {
					page: this.currentPage
				}).then(res => {
					console.log('score log res', res)
					if (res && res.data) {
						this.list = this.list.concat(res.data.list.data)
						this.currentPage = res.data.list.current_page
						this.hasMore = res.data.list.current_page < res.data.list.last_page
						this.loadStatus = this.hasMore ? 'loadmore' : 'nomore'
					}

					if (from_pull_down) {
						uni.stopPullDownRefresh()
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #FFFFFF !important;
		height: 100%;
	}

	.template-message {
		background-color: #FFFFFF;
		height: 100%;
	}

	.tn-message-fixed {
		position: fixed;
		background-color: rgba(255, 255, 255, 1);
		box-shadow: 0rpx 0rpx 30rpx 0rpx rgba(0, 0, 0, 0.07);
		top: 0;
		width: 100%;
		transition: all 0.25s ease-out;
		z-index: 100;
	}
</style>
