<template>
	<view class="container">
		<!-- 顶部导航 -->
		<!-- <topbar :title="paper_name" :scrollTop="scrollTop"></topbar> -->

		<!-- <tui-navigation-bar splitLine @init="initNavigation" @change="opacityChange" :scrollTop="scrollTop" title="NavBar自定义导航栏" backgroundColor="#fff" color="#333">
      <view class="tui-header-icon" :style="{ marginTop: top + 'px' }"><tui-icon name="arrowleft" :color="opacity > 0.85 ? '#333' : '#fff'" @click="back"></tui-icon></view>
    </tui-navigation-bar> -->
		<!-- <view class="tui-header-bg"><image src="/static/componentBg.png" class="tui-header-img"></image></view> -->
		<!-- <tui-navigation-bar  @init="initNavigation" :title="paper_name" @change="opacityChange" :scrollTop="scrollTop" :isOpacity="false" backgroundColor="#fff" color="#333">
      <view class="tui-header-icon" :style="{ marginTop: top + 'px' }"><tui-icon name="arrowleft" color="#333" @click="back"></tui-icon></view>
    </tui-navigation-bar> -->

		<!-- 倒计时 -->
		<!-- <tui-countdown :time="limit_time" borderColor="transparent" :isColon="false" @end="endOfTime"></tui-countdown> -->

		<!-- 答题组件 -->
		<kz-question v-show="result == null" 
			ref="comQuestion"
			mode="EXAM" 
			:questionMode="paper ? paper.mode : 'RANDOM'"
			:title="paper_name" 
			:questions="questions" 
			:configs="configs" 
			:limit_time="limit_time"
			:isPreventSwitchScreen="isPreventSwitchScreen"
			:switchScreenCount="switchScreenCount"
			@submitQuestion="submitQuestion">
		</kz-question>

		<!-- 成绩组件 -->
		<kz-grade-result v-show="result != null" :score="score" :gradeResult="result" ref="gradeResult"
			:examMode="exam_mode"></kz-grade-result>

		<!-- toast提示 -->
		<tui-toast ref="toast"></tui-toast>

		<!-- 登录组件 -->
		<login ref="login" v-on:succ="ajax()"></login>

		<!-- 试卷支付弹窗 -->
		<tui-modal :show="showConfirm" @cancel="goBack()" :custom="true">
			<view class="tui-modal-custom">
				<image src="/static/img/fail.png" class="tui-tips-img"></image>
				<view class="tui-modal-custom-text">
					<view>{{confirmContent}}</view>

					<view class="tn-flex tn-flex-wrap tn-margin-top-sm tn-text-sm">
						<view class="tn-flex-basic-md">
							普通用户价<text class="tn-text-bold">￥{{price}}</text>
						</view>
						<view class="tn-flex-basic-md">
							会员价<text class="tn-text-bold text-red">￥{{memberPrice}}</text>
						</view>
					</view>
				</view>

				<view class="tn-flex tn-flex-wrap">
					<view class="tn-flex-basic-md">
						<tui-button height="72rpx" width="90%" :size="28" type="gray-primary" shape="circle"
							@click="goBack()">取消</tui-button>
					</view>

					<view class="tn-flex-basic-md">
						<tui-button height="72rpx" width="90%" :size="28" type="primary" shape="circle"
							@click="confirmCreateOrder">确认并支付</tui-button>
					</view>
				</view>
			</view>
		</tui-modal>

		<!-- 题库支付、激活弹窗组件 -->
		<kz-cate-pay ref="cate_pay" 
			:cateId="cateId"
			:catePrice="catePrice"
			:showCateConfirm="showCateConfirm"
			:confirmCateContent="confirmCateContent"
			v-on:paySuccess="cateSuccess()" 
			v-on:codeSuccess="cateSuccess()"
			v-on:cancel="closeCateDialog"
			>
		</kz-cate-pay>
	</view>
</template>

<script>
	import paperApi from "@/common/api/paper.js"
	import cateApi from "@/common/api/cate.js"

	export default {
		data() {
			return {
				// 顶部栏
				scrollTop: 0,
				// 试卷
				user: this.utils.getData('user'),
				paper_id: 0,
				paper: null,
				paper_name: '试卷考试中',
				questions: [],
				configs: {},
				start_time: 0,
				limit_time: 3600,
				total: 0,
				// 考试成绩
				result: null,
				score: 0,
				// 考场
				room_id: 0,
				room_grade_id: 0,
				exam_mode: 'PAPER',
				paper_mode: 'RANDOM',
				// 题库
				cateId: 0,
				cate: null,

				// 支付信息相关
				price: 0,
				memberPrice: 0.00,
				catePrice: 0.00,
				// 试卷支付弹窗相关
				showConfirm: false,
				confirmContent: '',
				confirmButton: [{
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
				// 题库支付弹窗相关
				showCateConfirm: false,
				confirmCateContent: '',
				// 支付结果弹窗相关
				showResult: false,
				payResultContent: '',
				resultButton: [{
					text: '确定',
					type: 'green'
				}],

				isSubmit: false,
				
				isFirstShow: true,
				// 是否防切屏
				isPreventSwitchScreen: false,
				switchScreenCount: 0,
				switchScreenSecond: 0,
				curretSwitchScreenCount: 0,
				// currentSwitchScreenSecond: 0,
				switchScreenTimestamp: 0,
			}
		},
		onShow() {
			if (this.isFirstShow) {
				this.isFirstShow = false
			} else {
				if (this.isPreventSwitchScreen) {
					// 切屏时间
					let switchScreenSecond = parseInt((Date.now() - this.switchScreenTimestamp) / 1000)
					// 超过切屏认定时间
					if (switchScreenSecond >= this.switchScreenSecond) {
						this.curretSwitchScreenCount++
						// 超过切屏次数
						if (this.curretSwitchScreenCount >= this.switchScreenCount) {
							// 结束考试，强制交卷
							this.utils.toast('切屏超过次数，考试结束', 'error', 5000)
							this.$refs.comQuestion.submit()
							// this.submitQuestion([])
						}
					}
				}
			}

			console.log('onShow', this.curretSwitchScreenCount)
		},
		onHide() {
			if (this.isPreventSwitchScreen) {
				// 当前时间戳
				this.switchScreenTimestamp = Date.now()
			}

			console.log('onHide', this.switchScreenTimestamp)
		},
		onLoad(options) {
			this.paper_id = options.id
			this.room_id = options.room_id ? options.room_id : 0

			this.ajax()
			if (this.room_id) {
				this.getQuestion()
			}
		},
		onBackPress(e) {
			console.log('onBackPress e', e)
			if (!this.isSubmit) {
				if (e.from == "backbutton") {
					let message = '您尚未交卷，离开本页面将没有成绩哦'
					if (this.room_id) {
						message = '您尚未交卷，离开本页面将没有成绩且失去本次考试资格哦'
					}

					uni.showModal({
						title: '提示',
						content: message,
						success: (res) => {
							if (res.confirm) {
								uni.navigateBack({
									delta: 1
								});
							}
						}
					});

					return true; //阻止默认返回行为
				}
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop
		},
		methods: {
			// 请求数据
			ajax() {
				paperApi.checkPay(this, {
					paper_id: this.paper_id,
					room_id: this.room_id
				}).then(res => {
					console.log('xxx', res.data.type, res.data)
					if (res.code == 1) {
						if (res.data.type == 'OPEN_CATE') {
							// 1：可以参加，2：需要支付
							switch (res.data.status) {
								case 0:
									this.utils.toast(res.data.msg ? res.data.msg : res.msg)
									setTimeout(() => {
										if (res.data.url) {
											this.utils.goto(res.data.url)
										} else {
											this.goBack()
										}
									}, 3000)
									break
								case 1:
									this.getQuestion()
									break
							
								case 2:
									// this.utils.toast(res.data.msg)
									// this.showCateConfirm = true
									// this.confirmCateContent = res.data.msg
									// this.catePrice = res.data.price
									this.cate = res.data.cate
									this.cateId = res.data.cate.id
									
									this.catePrice = parseFloat(res.data.price)
									this.confirmCateContent = res.data.msg
									this.showCateConfirm = true
									break
									
								default:
									this.utils.toast('检测支付返回不支持的结果')
									break
							}
						} else if (res.data.type == 'PAPER_PAY') {
							// 0：不能参加，1：可以参加，2：需要支付
							switch (res.data.status) {
								case 0:
									this.utils.toast(res.data.msg, 'error')
									setTimeout(() => {
										if (res.data.url) {
											this.utils.goto(res.data.url)
										} else {
											this.goBack()
										}
									}, 3000)
									break
							
								case 1:
									this.getQuestion()
									break
							
								case 2:
									this.utils.toast(res.data.msg)
									this.showConfirm = true
									this.confirmContent = '参加该试卷考试需要付费才能进行'
									this.price = res.data.price
									this.memberPrice = res.data.member_price
									break
									
								default:
									this.utils.toast('检测支付返回不支持的结果')
									break
							}
						} else if (res.data.type == 'OPEN_MEMBER') {
							// 1：可以参加，2：需要支付
							switch (res.data.status) {
								case 0:
									this.utils.toast(res.data.msg ? res.data.msg : '未知错误')
									setTimeout(() => {
										if (res.data.url) {
											this.utils.goto(res.data.url)
										} else {
											this.goBack()
										}
									}, 3000)
									break
								case 1:
									this.getQuestion()
									break
									
								default:
									this.utils.toast('检测支付返回不支持的结果')
									break
							}
						}
					} else {
						this.utils.toast(res.msg ? res.msg : '未知错误')
						if (this.room_id) {
							setTimeout(() => {
								this.utils.goto('/pages/room/detail?id=' + this.room_id)
							})
						}
					}
				})
			},

			// 获取试卷试题
			getQuestion() {
				this.http('paper/getExamQuestion', {
					paper_id: this.paper_id,
					room_id: this.room_id
				}, 'get').then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
						
						setTimeout(() => {
							uni.navigateBack()
						}, 2000)
						// if (this.room_id) {
						// 	setTimeout(() => {
						// 		this.utils.goto('/pages/room/detail?id=' + this.room_id)
						// 	}, 2000)
						// } else {
						// 	setTimeout(() => {
						// 		uni.navigateBack()
						// 	}, 2000)
						// }

						return
					}

					this.paper = res.data.paper
					this.paper_mode = this.paper.mode
					this.paper_name = this.paper.title
					this.limit_time = this.paper.limit_time
					this.start_time = res.data.start_time
					this.room_grade_id = res.data.room_grade_id

					let questions = res.data.questions
					for (let i in questions) {
						questions[i]['code2'] = false
						console.log('questions[i][score]', questions[i]['score'])
						questions[i]['score'] = this.getSingleScore(questions[i])//, questions[i].kind, questions[i].difficulty
						console.log('questions[i][score]', questions[i]['score'])
					}

					this.questions = questions
					this.total = questions.length
					this.configs = this.paper.configs					
					
					// 防切屏
					if (this.paper.is_prevent_switch_screen && this.paper.switch_screen_count && this.paper.switch_screen_second) {
						this.isPreventSwitchScreen = true
						this.switchScreenCount = this.paper.switch_screen_count
						this.switchScreenSecond = this.paper.switch_screen_second
					}
				})
			},
			// 交卷
			submitQuestion(paperData) {
				console.log('paperData', paperData)

				if (this.isSubmit) {
					return
				}

				uni.showLoading({
					title: '交卷中'
				})

				let data = {
					paper_id: this.paper.id,
					start_time: this.start_time,
					questions: paperData.questions,
					room_id: this.room_id,
					room_grade_id: this.room_grade_id,
				}

				this.http('paper/submit', data, 'post').then(res => {
					console.log('paper submit res', res)
					this.result = res
					this.score = res.score
					this.isSubmit = true

					// 积分提示
					let point = res.point
					if (point?.get_point) {
						this.$refs.toast.show({
							title: "积分+" + point.get_point,
							content: point.type,
							imgUrl: "/static/toast/info-circle.png",
							icon: true,
							duration: 4000,
						})
					}

				})
			},
			// 计算试题分数
			getSingleScore(question) {
				console.log('paper_mode', this.paper_mode)
				if (this.paper_mode == 'FIX') {
					return question.score
				}

				let kind = question.kind
				let difficulty = question.difficulty
				const configs = this.paper.configs[kind.toLowerCase()]
				if (configs && configs['use_difficulty']) {
					return configs['difficulty'][difficulty.toLowerCase()]['score']
				}
				return configs['score']
			},

			/**
			 * 创建试卷订单并发起支付
			 */
			confirmCreateOrder() {
				paperApi.createOrder(this, {
					paper_id: this.paper_id
				}).then(res => {
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

							// 加载试题
							this.getQuestion()

							// 显示支付结果弹窗
							this.showResult = true
							this.payResultContent = '支付成功，请开始考试吧~'

							// 自动关闭支付结果弹窗
							setTimeout(() => {
								this.showResult = false
							}, 5000)
						},
						fail: (res) => {
							console.log('支付异常', res)

							// 显示支付结果弹窗
							this.showResult = true
							this.payResultContent = '支付失败了'
						},
						complete: (res) => {
							// 隐藏发起支付弹窗
							this.showConfirm = false
						}
					})

				})
			},
			/**
			 * 点击支付结果弹窗按钮
			 */
			payResult() {
				this.showResult = false
			},
			/**
			 * 返回上一页
			 */
			goBack() {
				const pages = getCurrentPages();
				if (pages && pages.length > 0) {
					const firstPage = pages[0];
					if (pages.length == 1 && (!firstPage.route || firstPage.route != "pages/index/index")) {
						uni.reLaunch({
							url: "/pages/index/index",
						});
					} else {
						uni.navigateBack({
							delta: 1,
						});
					}
				} else {
					uni.reLaunch({
						url: "/pages/index/index",
					});
				}
			},
			cateSuccess() {
				this.showCateConfirm = false
				// this.getQuestion()
				this.ajax()
			},
			closeCateDialog() {
				this.showCateConfirm = false
				this.utils.goback()
			},
		}
	}
</script>

<style>
	page {
		height: 100%;
	}

	.container {
		padding-bottom: 120rpx;
		box-sizing: border-box;
	}

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
</style>
