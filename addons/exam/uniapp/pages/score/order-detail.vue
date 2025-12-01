<template>
	<view class="page">
		<view class="top-backgroup">
		</view>

		<view class="about__wrap">
			<view class="tn-flex margin-bottom-lg">
				<view
					class="tn-col-2 tn-padding-sm tn-margin-xs tn-radius bg-flex-shadow tn-shadow-blur tn-avatar-class tn-avatar tn-avatar--circle"
					style="padding: 25px;border: 6rpx solid #fff;">
					<view class="tn-color-white tn-text-bold tn-text-lg tn-avatar">
						<view class="tn-icon-shopbag" style="font-size: 45rpx;"></view>
					</view>
				</view>
				<view class="tn-flex-2 tn-col-10 tn-padding-left">
					<view class="tn-color-white tn-text-bold tn-text-lg">
						{{orderInfo.status_text}}
					</view>
					<view class="tn-color-white tn-padding-top-sm">
						<text>{{orderInfo.order_no}}</text>
						<button class="cu-btn round sm bg-white shadow margin-left-sm text-black" @click="copyText(orderInfo.order_no)">复制</button>
					</view>
				</view>
			</view>

			<!-- 步骤条 start-->
			<view class="tn-flex tn-flex-row-center tn-bg-white course-shadow course-radius tn-margin-top-lg" v-if="orderInfo != null">
				<view class="steps-view">
					<view class="cu-steps">
						<view class="cu-item" :class="(orderInfo.status >= (index * 10)) ? 'text-primary' : ''" v-for="(item, index) in statusList" :key="index">
							<text :class="'cuIcon-' + (orderInfo.status >= (index * 10) ? 'radioboxfill' : 'radiobox')"></text>
							{{item.name}}
						</view>
					</view>
				</view>
			</view>
			<!-- 步骤条 end-->

			<!-- 收货信息 start-->
			<view class="tn-flex tn-flex-row-center tn-bg-white course-shadow course-radius tn-margin-top-xs">
				<view class="address-view">
					<view class="tn-text-bold">
						<text class="tn-margin-right-sm">{{orderInfo.user_name}}</text>
						<text class="">{{orderInfo.phone}}</text>
					</view>

					<view class="tn-margin-top-xs">
						<text class="text-grey">{{orderInfo.address}}</text>
					</view>
				</view>
			</view>
			<!-- 收货信息 end-->

			<!-- 商品信息 start-->
			<view class="order__item tn-margin-top-xs" @click="goDetail()">
				<!-- <view class="order__item__head tn-flex tn-flex-direction-row tn-flex-col-center tn-flex-row-between">
					<view class="order__item__head__title">共{{orderInfo.quantity}}件商品</view>
					<view class="order__item__head__status">{{orderInfo.status_text}}</view>
				</view> -->

				<view
					class="order__item__content tn-flex tn-flex-direction-row tn-flex-nowrap tn-flex-col-center tn-flex-row-center">
					<view class="order__item__content__image tn-col-3">
						<image :src="imgHost + orderInfo.first_image" mode="scaleToFill">
						</image>
					</view>
					<view class="order__item__content__title tn-col-6">
						{{orderInfo.name}}
					</view>
					<view
						class="order__item__content__info tn-flex tn-flex-direction-column tn-flex-col-center tn-flex-row-center tn-col-3">
						<view class="order__item__content__info__price">
							<!-- <text class="order__item__content__info__price--unit">￥</text> -->
							<text class="order__item__content__info__price__value--integer">{{orderInfo.price}}</text>
							<text class="order__item__content__info__price__value--decimal">积分</text>
						</view>
						<view class="order__item__content__info__count">
							<text>共{{orderInfo.quantity}}件</text>
						</view>
					</view>
				</view>
			</view>
			<!-- 商品信息 end-->

			<!-- 订单信息 start-->
			<view class="cu-list menu card-menu tn-margin-top-xs">
				<!-- <view class="cu-item">
					<view class="content">
						<text class="text-grey">订单编号</text>
					</view>
					<view class="action">
						<button class="cu-btn round sm bg-green shadow margin-right-sm" @click="copyText(orderInfo.order_no)">复制</button>
						<text class="text-grey text-sm">{{orderInfo.order_no}}</text>
					</view>
				</view> -->
				<view class="cu-item">
					<view class="content">
						<!-- <text class="cuIcon-warn text-green"></text> -->
						<text class="text-grey">兑换时间</text>
					</view>
					<view class="action">
						<text class="text-grey text-sm">{{orderInfo.createtime_text}}</text>
					</view>
				</view>
				<block v-if="orderInfo.status >= 20">
					<view class="cu-item">
						<view class="content">
							<!-- <text class="cuIcon-warn text-green"></text> -->
							<text class="text-grey">发货单号</text>
						</view>
						<view class="action">
							<button class="cu-btn round sm bg-green shadow margin-right-sm" @click="copyText(orderInfo.ship_no)">复制</button>
							<text class="text-grey text-sm">{{orderInfo.ship_no}}</text>
						</view>
					</view>
					
					<view class="cu-item">
						<view class="content">
							<!-- <text class="cuIcon-warn text-green"></text> -->
							<text class="text-grey">发货说明</text>
						</view>
						<view class="action">
							<text class="text-grey text-sm" :user-select="true">{{orderInfo.ship_remark}}</text>
						</view>
					</view>
					
					<view class="cu-item">
						<view class="content">
							<!-- <text class="cuIcon-warn text-green"></text> -->
							<text class="text-grey">发货时间</text>
						</view>
						<view class="action">
							<text class="text-grey text-sm">{{orderInfo.ship_time_text}}</text>
						</view>
					</view>
				</block>
				
				<view class="cu-item" v-if="orderInfo.status == 30">
					<view class="content">
						<!-- <text class="cuIcon-warn text-green"></text> -->
						<text class="text-grey">完成时间</text>
					</view>
					<view class="action">
						<text class="text-grey text-sm">{{orderInfo.complete_time_text}}</text>
					</view>
				</view>
				
			</view>
			<!-- 订单信息 end-->
		</view>

		<view style="width:90%;" class="margin padding-bottom-xl">
			<view class="" v-if="orderInfo.status == 20">
				<tui-button shape="circle" shadow bold preventClick @click="showCompleteConfirm" height="84rpx" type="green" class="padding-bottom">订单完成</tui-button>
			</view>
			
			<view class="margin-top-sm">
				<tui-button shape="circle" shadow bold preventClick @click="goList" height="84rpx">返回列表</tui-button>
			</view>
		</view>
		<view class="tn-padding-bottom"></view>
		
		<!-- 订单确认完成弹窗 -->
		<tui-modal :show="showConfirm" @click="orderComplete" content="确认已收到积分商品并完成订单吗？" color="#333" :size="32" :button="confirmButton"></tui-modal>
	</view>

</template>

<script>
	import scoreApi from "@/common/api/score.js"

	export default {
		data() {
			return {
				imgHost: '',//this.imgUrl,
				orderId: 0,
				orderInfo: null,
				statusList: [],
				
				showConfirm: false,
				confirmButton: [{
						text: "取消",
						type: "red",
						plain: true
					},
					{
						text: "确定",
						type: "green",
						plain: false
					}
				],
			}
		},
		onLoad(e) {
			this.orderId = e.id
			this.ajax()
		},
		methods: {
			ajax(from_pull_down = false) {
				scoreApi.getOrderDetail(this, {
					id: this.orderId
				}).then(res => {
					console.log('score log res', res)
					if (res && res.code == 1) {
						this.statusList = res.data.status_list
						// 订单信息
						this.orderInfo = res.data.order

						console.log('notes desc', this.notes, this.description)
					} else {
						this.utils.toast(res.msg)
						setTimeout(() => {
							this.utils.goback()
						}, 2000)
					}
				})
			},

			// 查看详情
			goDetail() {
				this.utils.goto('good-detail?id=' + this.orderInfo.good_id)
			},

			// 复制文本
			copyText(value) {
				//提示模板
				uni.setClipboardData({
					data: value, //要被复制的内容
					success: () => { //复制成功的回调函数
						uni.showToast({ //提示
							title: "复制成功"
						})
					}
				});
			},
			
			// 显示订单完成确认弹窗
			showCompleteConfirm() {
				this.showConfirm = true
			},
			
			// 订单完成
			orderComplete(e) {
				if (e.index == 0) {
					this.showConfirm = false
				} else {
					scoreApi.orderComplete(this, {
						id: this.orderId
					}).then(res => {
						console.log('score log res', res)
						this.utils.toast(res.msg)
						if (res && res.code == 1) {
							this.showConfirm = false
							this.ajax()
						}
					})
				}
			},
			
			// 返回订单列表
			goList() {
				this.utils.goto('order-list')
			},
		},
	}
</script>

<style lang="scss" scoped>
	// page {
	// 	background-color: #fff;
	// }

	// .page {
	// 	background-color: #fff;
	// }

	/* 顶部背景 start */
	.top-backgroup {
		height: 300rpx;
		z-index: -1;
		background: #5677fc;

		.backgroud-image {
			width: 100%;
			height: 450rpx;
			// z-index: -1;
		}
	}

	/* 顶部背景 end */

	.about {
		&__wrap {
			position: relative;
			z-index: 1;
			margin: 20rpx 30rpx;
			margin-top: -230rpx;
		}
	}

	.course-shadow {
		box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.07);
	}

	.course-radius {
		border-radius: 20rpx;
	}

	.steps-view {
		width: 100%;
		background-color: #fff;
		padding: 20rpx 0;
		border-radius: 20rpx;
	}

	.address-view {
		width: 100%;
		background-color: #fff;
		padding: 30rpx;
		border-radius: 20rpx;
	}

	.cu-steps .cu-item [class*="cuIcon-"],
	.cu-steps .cu-item .num {
		display: block;
		font-size: 30rpx;
		line-height: 80rpx;
	}

	.tn-avatar {
		/* #ifndef APP-NVUE */
		display: inline-flex;
		/* #endif */
		margin: 0;
		padding: 0;
		text-align: center;
		align-items: center;
		justify-content: center;
		// background-color: $tn-font-holder-color;
		// color: #FFFFFF;
		white-space: nowrap;
		position: relative;
		width: 64rpx;
		height: 64rpx;
		z-index: 1;

		&--sm {
			width: 48rpx;
			height: 48rpx;
		}

		&--lg {
			width: 96rpx;
			height: 96rpx;
		}

		&--xl {
			width: 128rpx;
			height: 128rpx;
		}

		&--square {
			border-radius: 10rpx;
		}

		&--circle {
			border-radius: 5000rpx;
		}

		&--shadow {
			position: relative;

			&::after {
				content: " ";
				display: block;
				background: inherit;
				filter: blur(10rpx);
				position: absolute;
				width: 100%;
				height: 100%;
				top: 10rpx;
				left: 10rpx;
				z-index: -1;
				opacity: 0.4;
				transform-origin: 0 0;
				border-radius: inherit;
				transform: scale(1, 1);
			}
		}

		&__img {
			width: 100%;
			height: 100%;

			&--square {
				border-radius: 10rpx;
			}

			&--circle {
				border-radius: 5000rpx;
			}
		}

		&__text {
			display: flex;
			flex-direction: row;
			align-items: center;
			justify-content: center;
		}
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
			// margin: 20rpx;
			padding: 36rpx 30rpx;
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
				border-bottom: 1px solid #d1d1d1;
				padding-bottom: 15rpx;

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
					color: #AAAAAA;
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
							margin-left: 5rpx;
						}

						&__value--decimal {
							font-size: 20rpx;
						}
					}

					&__count {
						color: #AAAAAA;
						font-size: 24rpx;
						margin-top: 10rpx;
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

	.cu-list.card-menu {
		margin-right: 0rpx;
		margin-left: 0rpx;
	}

	.cu-list.menu>.cu-item .content {
		font-size: 28rpx;
	}
	
	.cu-list.menu>.cu-item .action {
		max-width: 70%;
		padding: 20rpx 0;
	}
</style>
