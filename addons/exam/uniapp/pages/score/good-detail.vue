<template>
	<view class="template-product tn-safe-area-inset-bottom">
		<!-- 顶部自定义导航 -->
		<!-- <tn-nav-bar fixed alpha customBack>
			<view slot="back" class='tn-custom-nav-bar__back' @click="goBack">
				<text class='icon tn-icon-left'></text>
				<text class='icon tn-icon-home-capsule-fill'></text>
			</view>
		</tn-nav-bar> -->

		<swiper class="card-swiper" :circular="true" :autoplay="true" duration="500" interval="5000"
			@change="cardSwiper">
			<swiper-item v-for="(item,index) in imageList" :key="index" :class="cardCur==index?'cur':''">
				<view class="swiper-item image-banner">
					<image :src="imgHost + item" mode="aspectFill"></image>
				</view>
			</swiper-item>
		</swiper>
		<view class="indication">
			<block v-for="(item,index) in imageList" :key="index">
				<view class="spot" :class="cardCur==index?'active':''"></view>
			</block>
		</view>

		<view class="tn-margin">
			<view class="tn-flex tn-flex-row-between">
				<view class="justify-content-item tn-text-bold tn-text-xxl">
					{{goodInfo.name}}
				</view>
			</view>
			<view class="tn-flex tn-flex-row-between tn-margin-top">
				<view class="justify-content-item tn-text-bold">
					<!-- <text class="tn-text-sm tn-color-purplered">￥</text> -->
					<text class="tn-icon-funds tn-padding-right-xs tn-color-indigo"></text>
					<text class="" style="font-size: 50rpx;">{{goodInfo.price}}</text>
					
				</view>
				<view class="justify-content-item tn-color-gray tn-padding-top-xs">
					<view class="">已兑 {{goodInfo.sell_count}}</view>
				</view>
			</view>
		</view>

		<!-- 边距间隔 -->
		<view class="tn-strip-bottom"></view>


		<!-- 更多信息-->
		<view class="tn-padding-top-sm tn-padding-bottom-sm">
			<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="34">
				<view class="tn-flex tn-flex-col-center">
					<view class="tn-flex-1">商品原价</view>
					<view class="tn-margin-left-sm" style="font-size: 28rpx;">{{goodInfo.origin_price}}元</view>
				</view>
			</tn-list-cell>
			<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="34">
				<view class="tn-flex tn-flex-col-center">
					<view class="tn-flex-1">库存数</view>
					<view class="tn-margin-left-sm" style="font-size: 28rpx;">{{goodInfo.stocks}}</view>
				</view>
			</tn-list-cell>
			<tn-list-cell :hover="true" :unlined="true" :radius="true" :fontSize="34">
				<view class="tn-flex tn-flex-col-center">
					<view class="tn-flex-1">限购数/人</view>
					<view class="tn-margin-left-sm" style="font-size: 28rpx;">{{goodInfo.limit}}</view>
				</view>
			</tn-list-cell>
		</view>


		<!-- 边距间隔 -->
		<view class="tn-strip-bottom"></view>

		<view class="tn-margin">
			<view class="tn-flex tn-flex-row-between">
				<view class="justify-content-item tn-text-bold tn-text-xl">
					兑换说明
				</view>
			</view>
		</view>

		<view class="">
			<view class="tn-tag-content tn-margin tn-text-justify">
				<view class="tn-margin-right tn-round tn-text-sm">
					<rich-text :nodes="notes"></rich-text>
				</view>
			</view>
		</view>

		<!-- 边距间隔 -->
		<view class="tn-strip-bottom"></view>


		<view class="tn-margin">
			<view class="tn-flex tn-flex-row-between">
				<view class="justify-content-item tn-text-bold tn-text-xl">
					内容详情
				</view>
			</view>
		</view>

		<view class="content-backgroup tn-margin">
			<rich-text :nodes="description"></rich-text>
		</view>

		<!-- 边距间隔 -->
		<view class="tn-strip-bottom"></view>

		<view class="footerfixed dd-glass tn-padding-left-sm tn-padding-right tn-padding-top-xs tn-padding-bottom-sm">
			<tn-goods-nav :options="options" :buttonGroups="customButtonGroups" buttonType="round"
				:safeAreaInsetBottom="true" @optionClick="onOptionClick" @buttonClick="onButtonClick"></tn-goods-nav>
		</view>

		<view class='tn-tabbar-height'></view>


		<!-- 兑换确认弹窗 -->
		<!-- <tui-modal :show="showConfirm" @click="confirmExchange" :content="confirmContent" color="#333" :size="32" :button="confirmButton"></tui-modal> -->
		<tui-modal :show="showConfirm" :custom="true">
			<view class="tui-modal-custom">
				<view class="tui-prompt-title">兑换收货信息</view>

				<!-- <form @submit="formSubmit"> -->
				<tn-form :model="exchangeForm" ref="form" :errorType="errorType" :labelPosition="labelPosition" :labelWidth="labelWidth" :labelAlign="labelAlign">
					<tn-form-item label="收货人" prop="user_name" :labelPosition="labelPosition" :labelAlign="labelAlign">
						<tn-input v-model="exchangeForm.user_name" type="text" placeholder="请输入收货人姓名" :border="false">
						</tn-input>
					</tn-form-item>
					<tn-form-item label="手机号码" prop="phone" :labelPosition="labelPosition" :labelAlign="labelAlign">
						<tn-input v-model="exchangeForm.phone" type="number" placeholder="请输入手机号码" :border="false">
						</tn-input>
					</tn-form-item>
					<tn-form-item label="收货地址" prop="address" :labelPosition="labelPosition" :labelAlign="labelAlign">
						<tn-input v-model="exchangeForm.address" type="textarea" placeholder="请输入收货人地址" :border="false">
						</tn-input>
					</tn-form-item>
				</tn-form>

				<!-- <view class="tn-margin-top">
					<view class="content tn-flex tn-flex-direction-row tn-flex-col-center">
						<view class="content__title">收货人</view>
						<view class="content__data tn-flex-1">
							<tn-input v-model="exchangeForm.user_name" name="user_name" type="text" inputAlign="right"
								placeholder="请输入收货人姓名"></tn-input>
						</view>
					</view>

					<view class="content tn-flex tn-flex-direction-row tn-flex-col-center">
						<view class="content__title">手机号码</view>
						<view class="content__data tn-flex-1">
							<tn-input v-model="exchangeForm.phone" name="phone" type="text" inputAlign="right"
								placeholder="请输入收货人手机号码"></tn-input>
						</view>
					</view>

					<view class="content tn-flex tn-flex-direction-row tn-flex-col-center">
						<view class="content__title">收货地址</view>
						<view class="content__data tn-flex-1">
							<tn-input v-model="exchangeForm.address" name="address" type="textarea" inputAlign="right"
								placeholder="请输入收货人完整地址"></tn-input>
						</view>
					</view>
				</view> -->

				<view class="tui-modalBtn-box tn-margin-top">
					<tui-button class="tui-modal-btn" height="72rpx" :size="28" shape="circle" @click="confirmExchange({index:0})" plain>取消</tui-button>
					<tui-button class="tui-modal-btn" height="72rpx" :size="28" shape="circle" @click="confirmExchange({index:1})">确认兑换</tui-button>
				</view>
				<!-- </form> -->
			</view>
		</tui-modal>
		<!-- 兑换结果弹窗 -->
		<tui-modal :show="showResult" @click="payResult" :content="payResultContent" :button="resultButton"></tui-modal>
	</view>
</template>

<script>
	// import form from "@/components/common/tui-validation/tui-validation.js"
	import template_page_mixin from '@/libs/mixin/template_page_mixin.js'
	import scoreApi from "@/common/api/score.js"

	export default {
		name: 'TemplateProduct',
		mixins: [template_page_mixin],
		data() {
			return {
				imgHost: '',//this.imgUrl,
				goodId: 0,
				goodInfo: null,
				imageList: [],
				notes: '',
				description: '',
				cardCur: 0,
				options: [
					// {
					// 	icon: 'share-circle',
					// 	text: '分享',
					// },
				],
				customButtonGroups: [{
					text: '立即兑换',
					backgroundColor: '#5677fc',
					color: '#FFFFFF'
				}],

				// 兑换表单
				errorType: ['message', 'toast'],//'border-bottom', 
				labelPosition: 'left',
				labelAlign: 'right',
				labelWidth: 140,
				exchangeForm: {
					user_name: '',
					phone: '',
					address: '',
				},
				formRules: {
					user_name: [{
							required: true,
							message: '请输入用户名',
							trigger: 'blur'
						},
						{
							// 此为同步验证，可以直接返回true或者false，如果是异步验证，稍微不同，见下方说明
							validator: (rule, value, callback) => {
								return this.$t.test.chinese(value);
							},
							message: '姓名必须为中文',
							// 触发器可以同时用blur和change，二者之间用英文逗号隔开
							trigger: ['change', 'blur'],
						},
					],
					phone: [
						{
							required: true,
							message: '请输入手机号码',
							trigger: 'change'
						},
						{
							// 自定义验证函数，见上说明
							validator: (rule, value, callback) => {
								// 上面有说，返回true表示校验通过，返回false表示不通过
								// this.$t.test.mobile()就是返回true或者false的
								return this.$t.test.mobile(value);
							},
							message: '手机号码不正确',
							// 触发器可以同时用blur和change
							trigger: ['change','blur'],
						}
					],
					address: [
						{
							required: true,
							message: '请输入收货人地址',
							trigger: 'blur'
						},{
							min: 5,
							message: '收货人地址不能少于5个字',
							trigger: 'change'
						},
					],
				},

				showConfirm: false,
				confirmContent: '确认兑换此商品吗？',
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
				showResult: false,
				payResultContent: '',
				resultButton: [{
					text: '确定',
					type: 'primary'
				}],
				exchangeResult: false,
			}
		},
		onLoad(e) {
			this.goodId = e.id
			this.ajax()
		},
		onReady() {
			this.$refs.form.setRules(this.formRules)
		},
		methods: {
			ajax(from_pull_down = false) {
				scoreApi.getGoodsDetail(this, {
					id: this.goodId
				}).then(res => {
					console.log('score log res', res)
					if (res && res.code == 1) {
						// 商品信息
						this.goodInfo = res.data.good
						this.imageList = this.goodInfo.images.split(',')
						this.notes = this.utils.formatRichText(this.goodInfo.notes)
						this.description = this.utils.formatRichText(this.goodInfo.description)
						
						// 上次提交的收货信息
						this.exchangeForm = {...res.data.receiver_info}

						console.log('notes desc', this.notes, this.description)
					} else {
						this.utils.toast(res.msg)
						setTimeout(() => {
							this.utils.goback()
						}, 2000)
					}
				})
			},

			// cardSwiper
			cardSwiper(e) {
				this.cardCur = e.detail.current
			},

			onOptionClick(e) {
				console.log('onOptionClick', e)
			},

			onButtonClick(e) {
				console.log('onButtonClick', e)
				this.showConfirm = true
			},
			/**
			 * 确认兑换 - 表单验证
			 */
			formSubmit: function(e) {
				// 表单规则
				let rules = [{
					name: "user_name",
					rule: ["required", "isChinese"],
					msg: ["请输入姓名", "姓名必须全部为中文"]
				}, {
					name: "phone",
					rule: ["required", "isMobile"],
					msg: ["请输入手机号", "请输入正确的手机号"]
				}, {
					name: "address",
					rule: ["required"],
					msg: ["请输入收货地址"],
				}];

				// 进行表单检查
				let formData = e.detail.value;
				let checkRes = form.validation(formData, rules);
				if (!checkRes) {
					// 通过，提交订单
					this.confirmExchange({
						index: 1
					})
				} else {
					uni.showToast({
						title: checkRes,
						icon: "none"
					});
				}
			},
			/**
			 * 确认兑换 - 提交订单
			 */
			confirmExchange(e) {
				console.log('confirmExchange', e)

				if (e.index == 0) {
					this.showConfirm = false
				} else {
					this.$refs.form.validate(valid => {
						console.log('form valid', valid)
						
						// 验证通过
						if (valid) {
							
							let data = {
								...this.exchangeForm,
								id: this.goodId,
								quantity: 1,// 默认数量1
							}
							
							scoreApi.goodExchange(this, data).then(res => {
								console.log('confirmExchange res', res)
							
								// 显示兑换结果弹窗
								this.showResult = true
								this.payResultContent = res.msg
								this.exchangeResult = res.code == 1
							
								// 刷新信息
								this.ajax()
							})
							
						} else {
							// 验证失败
						}
					})

					
				}
			},
			/**
			 * 点击兑换结果弹窗按钮
			 */
			payResult() {
				this.showResult = false
				this.showConfirm = false
				
				// 兑换成功，跳订单列表
				if (this.exchangeResult) {
					this.utils.goto('order-list')
				}
			},
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #FFFFFF;
	}

	.template-product {}

	.tn-tabbar-height {
		min-height: 120rpx;
		height: calc(140rpx + env(safe-area-inset-bottom) / 2);
	}

	/* 用户头像 start */
	.logo-image {
		width: 110rpx;
		height: 110rpx;
		position: relative;
	}

	.logo-pic {
		background-size: cover;
		background-repeat: no-repeat;
		// background-attachment:fixed;
		background-position: top;
		box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.15);
		border-radius: 10rpx;
		overflow: hidden;
		// background-color: #FFFFFF;
	}

	/* 胶囊*/
	.tn-custom-nav-bar__back {
		width: 100%;
		height: 100%;
		position: relative;
		display: flex;
		justify-content: space-evenly;
		align-items: center;
		box-sizing: border-box;
		background-color: rgba(0, 0, 0, 0.15);
		border-radius: 1000rpx;
		border: 1rpx solid rgba(255, 255, 255, 0.5);
		color: #FFFFFF;
		font-size: 18px;

		.icon {
			display: block;
			flex: 1;
			margin: auto;
			text-align: center;
		}

		&:before {
			content: " ";
			width: 1rpx;
			height: 110%;
			position: absolute;
			top: 22.5%;
			left: 0;
			right: 0;
			margin: auto;
			transform: scale(0.5);
			transform-origin: 0 0;
			pointer-events: none;
			box-sizing: border-box;
			opacity: 0.7;
			background-color: #FFFFFF;
		}
	}

	/* 轮播视觉差 start */
	.card-swiper {
		height: 750rpx !important;
	}

	.card-swiper swiper-item {
		width: 750rpx !important;
		left: 0rpx;
		box-sizing: border-box;
		padding: 0;
		padding-bottom: 20rpx;
		// padding: 0rpx 30rpx 90rpx 30rpx;
		overflow: initial;
	}

	.card-swiper swiper-item .swiper-item {
		width: 100%;
		display: block;
		height: 100%;
		transform: scale(1);
		transition: all 0.2s ease-in 0s;
		overflow: hidden;
	}

	.card-swiper swiper-item.cur .swiper-item {
		transform: none;
		transition: all 0.2s ease-in 0s;
	}

	.image-banner {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.image-banner image {
		width: 100%;
		height: 100%;
	}

	/* 轮播指示点 start*/
	.indication {
		z-index: 9999;
		width: 100%;
		height: 36rpx;
		position: absolute;
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: center;
	}

	.spot {
		background-color: #FFFFFF;
		opacity: 0.6;
		width: 10rpx;
		height: 10rpx;
		border-radius: 20rpx;
		top: -60rpx;
		margin: 0 8rpx !important;
		position: relative;
	}

	.spot.active {
		opacity: 1;
		width: 30rpx;
		background-color: #FFFFFF;
	}

	/* 间隔线 start*/
	.tn-strip-bottom {
		width: 100%;
		border-bottom: 20rpx solid rgba(241, 241, 241, 0.8);
	}

	/* 间隔线 end*/
	/* 标题 start */
	.nav_title {
		-webkit-background-clip: text;
		color: transparent;

		&--wrap {
			position: relative;
			display: flex;
			height: 120rpx;
			font-size: 46rpx;
			align-items: center;
			justify-content: center;
			font-weight: bold;
			background-image: url(https://tnuiimage.tnkjapp.com/title_bg/title44.png);
			background-size: cover;
		}
	}

	/* 标题 end */

	/* 底部tabbar start*/
	.footerfixed {
		position: fixed;
		width: 100%;
		bottom: 0;
		z-index: 999;
		background-color: #FFFFFF;
		box-shadow: 0rpx 0rpx 30rpx 0rpx rgba(0, 0, 0, 0.07);
	}

	/* 标签内容 start*/
	.tn-tag-content {
		&__item {
			display: inline-block;
			line-height: 45rpx;
			padding: 10rpx 30rpx;
			margin: 20rpx 20rpx 5rpx 0rpx;

			&--prefix {
				padding-right: 10rpx;
			}
		}
	}

	/* 标签内容 end*/

	/* 内容图 start */
	.content-backgroup {
		z-index: -1;

		.backgroud-image {
			border-radius: 15rpx;
			width: 100%;
		}
	}

	/* 内容图 end */

	/* 商家商品 start*/
	.tn-blogger-content {
		&__wrap {
			box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
			border-radius: 20rpx;
			margin: 15rpx;
		}

		&__info {
			&__btn {
				margin-right: -12rpx;
				opacity: 0.5;
			}
		}

		&__label {
			&__item {
				line-height: 45rpx;
				padding: 0 10rpx;
				margin: 5rpx 18rpx 0 0;

				&--prefix {
					color: #E83A30;
					padding-right: 10rpx;
				}
			}

			&__desc {
				line-height: 35rpx;
			}
		}

		&__main-image {
			border-radius: 16rpx 16rpx 0 0;

			&--1 {
				max-width: 690rpx;
				min-width: 690rpx;
				max-height: 400rpx;
				min-height: 400rpx;
			}

			&--2 {
				max-width: 260rpx;
				max-height: 260rpx;
			}

			&--3 {
				height: 212rpx;
				width: 100%;
			}
		}

		&__count-icon {
			font-size: 24rpx;
			padding-right: 5rpx;
		}
	}

	.image-book {
		padding: 150rpx 0rpx;
		font-size: 16rpx;
		font-weight: 300;
		position: relative;
	}

	.image-picbook {
		background-size: cover;
		background-repeat: no-repeat;
		// background-attachment:fixed;
		background-position: top;
		border-radius: 15rpx 15rpx 0 0;
	}

	/* 毛玻璃*/
	.dd-glass {
		width: 100%;
		backdrop-filter: blur(20rpx);
		-webkit-backdrop-filter: blur(20rpx);
	}

	.tui-modal-custom {
		text-align: center;
	}

	.tui-prompt-title {
		padding-bottom: 20rpx;
		font-size: 34rpx;
	}

	.tui-input__box {
		width: 80%;
		height: 82rpx;
		margin: 10rpx auto;
	}

	.tui-title {
		line-height: 32rpx;
		min-width: 120rpx;
		flex-shrink: 0;
	}

	.tui-input {
		font-size: 32rpx;
		color: #333;
		padding-left: 20rpx;
		flex: 1;
		overflow: visible;
	}

	.tui-modal-input {

		border-bottom: 1rpx solid #e6e6e6;
		height: 80rpx;
		font-size: 28rpx;
		text-align: center;
	}

	.tui-modalBtn-box {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-flex-column {
		flex-direction: column;
	}

	.tui-modal-btn {
		width: 46%;
		height: 68rpx;
		line-height: 68rpx;
		position: relative;
		border-radius: 10rpx;
		font-size: 26rpx;
		overflow: visible;
		margin-left: 0;
		margin-right: 0;
	}

	// .tui-modal-btn::after {
	// 	content: ' ';
	// 	position: absolute;
	// 	width: 200%;
	// 	height: 200%;
	// 	-webkit-transform-origin: 0 0;
	// 	transform-origin: 0 0;
	// 	transform: scale(0.5, 0.5) translateZ(0);
	// 	left: 0;
	// 	top: 0;
	// 	border-radius: 20rpx;
	// 	z-index: 2;
	// }

	.content {
		background-color: #FFFFFF;
		border-bottom: 1px solid #e6e6e6;

		&__title {
			padding: 5rpx 20rpx;
		}

		&__data {
			margin: 5rpx 0;
			margin-right: 30rpx;
		}
	}

	.indication {
		z-index: 999 !important;
	}
</style>
