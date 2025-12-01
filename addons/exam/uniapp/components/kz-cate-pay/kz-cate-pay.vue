<template>
	<view>
		<!-- 题库支付弹窗 -->
		<tui-modal :show="showCateConfirm" @cancel="cancel()" :custom="true">
			<view class="tui-modal-custom">
				<view v-show="!showCateCode">
					<image src="/static/img/fail.png" class="tui-tips-img"></image>
					<view class="tui-modal-custom-text">
						<view>{{confirmCateContent}}</view>
							
						<view class="tn-flex tn-flex-wrap tn-margin-top-sm tn-text-sm text-center">
							<view class="tn-flex-basic-full">
								题库开通价<text class="tn-text-bold">￥{{catePrice}}</text>，或使用激活码开通。
							</view>
						</view>
					</view>
							
					<view class="tn-flex tn-flex-wrap justify-between align-center" v-show="!showCateCode">
						<view class="tn-flex-basic-30">
							<tui-button height="72rpx" width="90%" :size="28" type="gray-primary" shape="circle"
								@click="cancel()">取消</tui-button>
						</view>
						
						<!-- #ifdef MP-WEIXIN -->
						<view class="tn-flex-basic-30">
							<tui-button height="72rpx" width="90%" :size="28" type="green" shape="circle"
								@click="confirmCreateCateOrder">支付</tui-button>
						</view>
						<!-- #endif -->
						
						<view class="tn-flex-basic-30">
							<tui-button height="72rpx" width="90%" :size="28" type="primary" shape="circle"
								@click="showCateCode = true">激活码</tui-button>
						</view>
					</view>
				</view>
				
				<!-- 激活码 -->
				<view v-show="showCateCode">
					<view class="tui-modal-custom">
						<view class="tui-prompt-title">激活码</view>
						<view class="tui-input__box">
							<input placeholder="请输入激活码" class="tui-modal-input" v-model="code" />
						</view>
					</view>
					<view class="tn-flex tn-flex-wrap">
						<view class="tn-flex-basic-md">
							<tui-button height="72rpx" width="90%" :size="28" type="gray-primary" shape="circle"
								@click="showCateCode = false">返回</tui-button>
						</view>
							
						<view class="tn-flex-basic-md">
							<tui-button height="72rpx" width="90%" :size="28" type="primary" shape="circle"
								@click="confirmCateCode">激活</tui-button>
						</view>
					</view>
				</view>
				
			</view>
		</tui-modal>
		
		<!-- 支付结果弹窗 -->
		<tui-modal :show="showResult" @click="payResult" :content="payResultContent" :button="resultButton"></tui-modal>
	</view>
</template>

<script>
	import cateApi from "@/common/api/cate.js"
	export default {
		name:"kz-cate-pay",
		props: {
			// 题库ID
			cateId: {
				type: Number,
				default: 0
			},
			// 题库ID
			catePrice: {
				type: Number,
				default: 0
			},
			// 题库ID
			showCateConfirm: {
				type: Boolean,
				default: false
			},
			// 题库ID
			confirmCateContent: {
				type: String,
				default: ""
			},
		},
		data() {
			return {
				// 题库支付弹窗相关
				confirmCateButton: [{
						text: "取消",
						type: "red",
						plain: true
					},
					{
						text: "确认并支付",
						type: "green",
						plain: false
					}
				],
				// 激活码相关
				showCateCode: false,
				code: '',
				// 支付结果弹窗相关
				showResult: false,
				payResultContent: '',
				resultButton: [{
					text: '确定',
					type: 'green'
				}],
			};
		},
		methods: {
			// 创建题库订单并发起支付
			confirmCreateCateOrder() {
				cateApi.createOrder(this, this.cateId).then(res => {
					console.log('createOrder res', res)
			
					if (res.code != 1) {
						this.utils.toast('支付失败：' + res.msg)
						return
					}
			
					// 支付参数
					const payment = res.data.payment
					uni.requestPayment({
						// 微信支付
						timeStamp: payment.timeStamp,
						nonceStr: payment.nonceStr,
						package: payment.package,
						signType: payment.signType,
						paySign: payment.paySign,
						success: (payRes) => {
							console.log('支付成功', payRes)
			
							// 显示支付结果弹窗
							this.showResult = true
							this.payResultContent = '支付成功'
			
							// 自动关闭支付结果弹窗
							setTimeout(() => {
								// 隐藏发起支付弹窗
								// this.showCateConfirm = false
								console.log('paySuccess')
								this.showResult = false
								this.$emit('paySuccess')
								// this.goTrain()
							}, 2000)
						},
						fail: (res) => {
							console.log('支付异常', res)
			
							// 显示支付结果弹窗
							this.showResult = true
							this.payResultContent = '支付失败了'
						},
						complete: (res) => {
							// 隐藏发起支付弹窗
							// this.showCateConfirm = false
						}
					})
			
				})
			},
			// 激活码激活
			confirmCateCode() {
				cateApi.activateCate(this, this.cateId, this.code).then(res => {
					console.log('createOrder res', res)
							
					if (res.code != 1) {
						this.utils.toast('激活失败：' + res.msg)
						return
					}
							
					this.utils.toast('激活成功')
					// this.showCateConfirm = false
					
					// 自动关闭支付结果弹窗
					setTimeout(() => {
						console.log('codeSuccess')
						this.$emit('codeSuccess')
						// this.goTrain()
					}, 1500)
				})
			},
			// 点击支付结果弹窗按钮
			payResult() {
				this.showResult = false
			},
			cancel() {
				// showCateConfirm = false
				console.log('do cancel emit')
				this.$emit('cancel', 1)
			}
		}
	}
</script>

<style>
	.tui-modal-custom {
		text-align: center;
	}
	
	.tui-tips-img {
		width: 80rpx;
		height: 80rpx;
		margin-top: 20rpx;
	}
	
	.tui-modal-custom-text {
		font-size: 30rpx;
		color: #333;
		padding: 30rpx 0 50rpx;
	}
	
	.tn-flex-basic-30 {
		flex-basis: 33%;
	}
	
	.tui-prompt-title {
		padding-bottom: 20rpx;
		font-size: 34rpx;
	}
	
	.tui-input__box {
		width: 80%;
		height: 82rpx;
		margin: 30rpx auto 50rpx;
	}
	
	.tui-modal-input {
		border-bottom: 1rpx solid #e6e6e6;
		height: 80rpx;
		font-size: 28rpx;
		text-align: center;
	}
</style>