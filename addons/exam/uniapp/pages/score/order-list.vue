<template>
	<view>
		<!-- nav -->
		<scroll-view scroll-x class="bg-white nav">
			<view class="flex text-center">
				<view class="cu-item flex-sub" :class="index==tabCur?'text-primary cur':''"
					v-for="(item,index) in statusList" :key="index" @tap="tabSelect" :data-id="index" :data-status="item.status">
					{{item.name}}
				</view>
			</view>
		</scroll-view>

		<view class="">
			<scroll-view scroll-y>
				<view v-for="(item,index) in list" :key="index" class="order__item" @tap.stop="goDetail(item.id)">
					<view class="order__item__head tn-flex tn-flex-direction-row tn-flex-col-center tn-flex-row-between">
						<view class="order__item__head__title">{{item.createtime_text}}</view>
						<view class="order__item__head__status" :class="[getStatusClass(item.status)]">{{item.status_text}}</view>
					</view>

					<view class="order__item__content tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-center">
						<view class="order__item__content__image tn-col-3">
							<image :src="imgHost + item.first_image" mode="scaleToFill">
							</image>
						</view>
						<view class="order__item__content__title tn-col-6">
							{{item.name}}
						</view>
						<view class="order__item__content__info tn-flex tn-flex-direction-column tn-flex-col-center tn-flex-row-center tn-col-3">
							<view class="order__item__content__info__price">
								<!-- <text class="order__item__content__info__price--unit">￥</text> -->
								<text class="order__item__content__info__price__value--integer">{{item.price}}</text>
								<text class="order__item__content__info__price__value--decimal">积分</text>
							</view>
							<view class="order__item__content__info__count">
								<text>共{{item.quantity}}件</text>
							</view>
						</view>
					</view>

					<view class="order__item__operation tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-between">
						<view class="order__item__operaation__left">
							<!-- <text class="order__item__operaation__left--text">更多</text> -->
						</view>
						<view class="order__item__operation__right tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-right">
							<view class="order__item__operaation__right__button">
								<tn-button shadow shape="round" fontColor="#080808" backgroundColor="#E6E6E6"
									:fontSize="24" height="auto" padding="10rpx 18rpx" @tap.stop="goDetail(item.id)">查看详情</tn-button>
							</view>
							<view class="order__item__operation__right__button">
								<tn-button shadow shape="round" fontColor="#FFFFFF" backgroundColor="#5677fc" :fontSize="24" height="auto" padding="10rpx 18rpx" @tap.stop="goGoodList()">再次兑换</tn-button>
							</view>
						</view>
					</view>
				</view>
				<view class="tn-padding-bottom"></view>

			</scroll-view>
			
			<!-- 没有更多 -->
			<view class="tn-margin-top " v-if="list.length > 0">
				<tn-load-more class="tn-margin-top" :status="loadStatus" :loadText="loadText"></tn-load-more>
			</view>
			
			<!-- 无数据 -->
			<tui-no-data imgUrl="/static/img/img_noorder_3x.png" v-else>暂无数据</tui-no-data>
			
			<view class="tn-padding-bottom"></view>
		</view>	
	</view>
</template>

<script>
	import scoreApi from "@/common/api/score.js"

	export default {
		data() {
			return {
				imgHost: '',//this.imgUrl,
				tabCur: 0,
				scrollLeft: 0,

				statusList: [],
				currentStatus: -1, // 默认全部

				list: [],
				currentPage: 1,
				hasMore: false,
				loadText: {
					loadmore: '下拉加载',
					loading: '加载中',
					nomore: '没有数据啦'
				},
				loadStatus: '下拉加载',

			};
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
			ajax() {
				scoreApi.getOrdersList(this, {
					page: this.currentPage,
					status: this.currentStatus
				}).then(res => {
					console.log('score log res', res)
					if (res && res.code == 1) {
						// // 状态列表
						// let statusList = res.data.status_list
						// console.log('this.currentStatus', this.currentStatus)
						// // 订单列表
						// for (var i = 0; i < statusList.length; i++) {
						// 	if (this.currentStatus == statusList[i].status) {
						// 		statusList[i].list = res.data.list
						// 		break
						// 	}
						// }
						// this.statusList = statusList
						
						this.statusList = res.data.status_list
						this.list = this.list.concat(res.data.list.data)
						this.currentPage = res.data.list.current_page
						this.hasMore = res.data.list.current_page < res.data.list.last_page
						this.loadStatus = this.hasMore ? 'loadmore' : 'nomore'
					} else {
						this.utils.toast(res.msg)
						setTimeout(() => {
							this.utils.goback()
						}, 2000)
					}
				})
			},

			// tab切换
			tabSelect(e) {
				if (this.currentStatus != e.currentTarget.dataset.status) {
					this.list = []
				}
				
				this.tabCur = e.currentTarget.dataset.id
				this.currentStatus = e.currentTarget.dataset.status
				this.scrollLeft = (e.currentTarget.dataset.id - 1) * 60
				
				console.log('currentStatus', this.currentStatus)
				this.ajax()
			},
			
			// 订单状态不同颜色
			getStatusClass(status) {
				switch (parseInt(status)) {
					case 10:
						return 'text-blue'
					case 20:
						return 'text-orange'
					case 30:
						return 'text-green'
				}
			},
			
			// 查看详情
			goDetail(id) {
				this.utils.goto('order-detail?id=' + id)
			},
			
			// 商品列表
			goGoodList() {
				this.utils.goto('good-list')
			},
		}
	}
</script>

<style lang="scss" scoped>
	page {
		background-color: #F7F7F7CC;
	}
	
	.template-order {
		background-color: #F7F7F7CC;
	}

	.order {
		&--wrap {
			position: fixed;
			left: 0;
			right: 0;
			width: 100%;
			background-color: inherit;
		}

		/* 导航栏 start */
		&__tabs {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			background-color: inherit;
		}

		/* 导航栏 end */

		/* swiper start */
		&__swiper {
			position: absolute;
			left: 0;
			right: 0;
			width: 100%;
			background-color: inherit;
			// padding: 0 16rpx;
		}

		/* swiper end */

		/* 订单内容 start */
		&__item {
			margin: 20rpx;
			padding: 36rpx 26rpx;
			background-color: #FFFFFF;
			border-radius: 18rpx;

			&:first-child {
				margin-top: 40rpx;
			}

			&:last-child {
				margin-bottom: 0;
			}

			/* 头部 start */
			&__head {

				&__title {
					// font-weight: bold;
					line-height: normal;

					&--right-icon {
						font-size: 24rpx;
						margin-left: 8rpx;
					}
				}

				&__status {
					font-size: 22rpx;
					// color: #AAAAAA;
				}
			}

			/* 头部 end */

			/* 内容 start */
			&__content {

				margin-top: 20rpx;

				&__image {
					margin-right: 20rpx;

					image {
						width: 140rpx;
						height: 140rpx;
						border-radius: 10rpx;
					}
				}

				&__title {
					padding-right: 40rpx;
					display: -webkit-box;
					overflow: hidden;
					white-space: normal !important;
					text-overflow: ellipsis;
					word-wrap: break-word;
					-webkit-line-clamp: 2;
					-webkit-box-orient: vertical;
				}

				&__info {

					&__price {
						&--unit {
							font-size: 20rpx;
						}

						&__value--integer,
						&__value--decimal {
							font-weight: bold;
						}

						&__value--decimal {
							font-size: 20rpx;
						}
					}

					&__count {
						color: #AAAAAA;
						font-size: 24rpx;
					}
				}
			}

			/* 内容 end */

			/* 操作按钮 start */
			&__operation {
				margin-top: 30rpx;

				&__right {
					&__button {
						margin-left: 10rpx;
					}
				}
			}

			/* 操作按钮 end */
		}

		/* 订单内容 end */
	}
</style>

