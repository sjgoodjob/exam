<template>
	<view class="container">

		<view class="bg-top bg-black">
			<view class="top-box shadow">
				<!-- <view class="qh-pic cu-avatar xl" style="background-image:url(https://zhoukaiwen.com/img/kevinLogo.png)"></view> -->
				<view class="qh-title text-bold text-primary text-xl">
					<text>{{detail.name}}</text>
				</view>

				<view class="bxBox" style="border-top: 1rpx solid #eee;">
					<text class="bxImg cuIcon-titles text-pink"></text>
					<view class="title text-black">考场说明</view>
				</view>
				<view class='padding-bottom-sm padding-left-sm bg-white'>
					{{detail.contents}}
				</view>

				<!-- :class="[menuBorder?'sm-border':'',menuCard?'card-menu margin-top':'']" -->
				<view class="cu-list menu">
					<view class="cu-item padding-0">
						<view class="content margin-top" style="display: flex; justify-content: space-around;">
							<text class="text-black text-bold"
								style="min-width: 290rpx; max-width: 290rpx; text-align: left;">{{detail.start_time|format_date}}</text>
							<text class="cuIcon-forwardfill text-primary margin-lr-sm"></text>
							<text class="text-black text-bold"
								style="min-width: 290rpx; max-width: 290rpx; text-align: left;">{{detail.end_time|format_date}}</text>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class="content">
							<text class="cuIcon-filter text-green"></text>
							<text class="text-black">考场分类</text>
						</view>
						<view class="action">
							<view class="text-black text-sm">{{detail.cates ? detail.cates.name : ''}}</view>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class="content">
							<text class="cuIcon-list text-primary"></text>
							<text class="text-black">报名方式</text>
						</view>
						<view class="action">
							<view class="cu-tag round light" :class="[signupClass]">{{detail.signup_mode_text}}</view>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class="content">
							<text class="cuIcon-writefill text-pink"></text>
							<text class="text-black">补考方式</text>
						</view>

						<view class="cu-capsule radius" v-if="detail && detail.is_makeup">
							<view class='cu-tag bg-green'>
								<text class="cuIcon-check text-lg"></text>
							</view>
							<view class="cu-tag line-green">
								{{detail.is_makeup_text + detail.makeup_count}}次
							</view>
						</view>

						<view class="cu-capsule radius" v-else>
							<view class='cu-tag bg-red'>
								<text class="cuIcon-close text-lg"></text>
							</view>
							<view class="cu-tag line-red">
								不可补考
							</view>
						</view>
					</view>

				</view>

			</view>


			<!-- 试卷信息 -->
			<view class="center-box shadow">
				<view class="cu-list menu">
					<view class="cu-bar bg-white margin-top-xs u-border-bottom">
						<view class="action sub-title">
							<text class="text-xl text-bold text-primary text-shadow">试卷信息</text>
							<text class="text-ABC text-primary">Paper</text>
						</view>
					</view>
					<view class="cu-item padding-0">
						<view class="content">
							<text class="cuIcon-formfill text-primary"></text>
							<text class="text-lg">考卷</text>
						</view>
						<view class="action">
							<text class="text-grey text-sm">{{detail && detail.paper && detail.paper.title}}</text>
						</view>
					</view>

					<view class="cu-item padding-0" v-if="detail && detail.paper && detail.paper.limit_time">
						<view class='content'>
							<text class="cuIcon-timefill text-primary"></text>
							<text class='text-lg'>考试限时</text>
						</view>
						<view class="action">
							<view class="cu-tag round bg-blue light">
								{{detail.paper.limit_time|format_second}}
							</view>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class='content'>
							<text class="cuIcon-activityfill text-primary"></text>
							<text class='text-lg'>总分数</text>
						</view>
						<view class="action">
							<view class="cu-tag round bg-blue light">
								{{detail && detail.paper && detail.paper.total_score}}分
							</view>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class='content'>
							<text class="cuIcon-upstagefill text-primary"></text>
							<text class='text-lg'>及格分数</text>
						</view>
						<view class="action">
							<view class="cu-tag round bg-red light">
								{{detail && detail.paper && detail.paper.pass_score}}分
							</view>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class='content'>
							<text class="cuIcon-btn text-primary"></text>
							<text class='text-lg'>题目数量</text>
						</view>
						<view class="action">
							<view class="text-sm">
								{{detail && detail.paper && detail.paper.quantity}}题
							</view>
						</view>
					</view>

				</view>
			</view>

			<!-- 报名信息 -->
			<view class="center-box shadow" v-if="signupLog">
				<view class="cu-list menu">
					<view class="cu-bar bg-white margin-top-xs u-border-bottom">
						<view class="action sub-title">
							<text class="text-xl text-bold text-primary text-shadow">报名信息</text>
							<text class="text-ABC text-primary">Signup</text>
						</view>
					</view>
					<view class="cu-item padding-0">
						<view class="content">
							<text class="cuIcon-myfill text-primary"></text>
							<text class="text-lg">姓名</text>
						</view>
						<view class="action">
							<text class="text-grey text-sm">{{signupLog.real_name}}</text>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class='content'>
							<text class="cuIcon-mobilefill text-primary"></text>
							<text class='text-lg'>联系方式</text>
						</view>
						<view class="action">
							<view class="action">
								<text class="text-grey text-sm">{{signupLog.phone}}</text>
							</view>
						</view>
					</view>

					<view class="cu-item padding-0">
						<view class='content'>
							<text class="cuIcon-title text-primary"></text>
							<text class='text-lg'>报名状态</text>
						</view>
						<view class="action">
							<view class="cu-tag round light" :class="[signupStatusClass]">{{signupLog.status_text}}</view>
						</view>
					</view>

				</view>
			</view>

			<view class="top-box shadow" v-if="signupLog && signupLog.status == 2">
				<view class="bxBox" style="border-top: 1rpx solid #eee;">
					<text class="bxImg cuIcon-wenzi text-red"></text>
					<view class="title text-black">驳回说明</view>
				</view>
				<view class='padding-bottom-sm padding-left-sm bg-white text-red'>
					{{signupLog.message || '无'}}
				</view>
			</view>

			<!-- 考试记录 -->
			<view class="center-box shadow" v-if="examLogs.length > 0">
				<view class="cu-list menu">
					<view class="cu-bar bg-white margin-top-xs u-border-bottom">
						<view class="action sub-title">
							<text class="text-xl text-bold text-primary text-shadow">考试记录</text>
							<text class="text-ABC text-primary">Exam</text>
						</view>
					</view>

					<view class="cu-item arrow padding-left-0" v-for="(examLog, index) in examLogs" :key="index">
						<button class="cu-btn content" @click="viewExamLogModal(examLog)">
							<view class="content">
								<text class="cuIcon-myfill text-primary"></text>
								<text class="text-lg">{{examLog.createtime|format_date}}</text>
							</view>
							<view class="action">
								<view class="cu-tag round light" :class="[examLog.is_pass ? 'bg-green' : 'bg-red']">{{examLog.score}}分</view>
							</view>
						</button>
					</view>

				</view>
			</view>

			<view class="padding-lr" style="margin-top: 15rpx; margin-bottom: 30rpx;">
				<text class="text-grey text-sm">{{signupTips}}</text>
			</view>
			
			<!-- 报名、考试按钮 -->
			<view class="padding-xl">
				<button class="cu-btn round block margin-tb-sm lg" :class="[signupBtnClass]" @tap="signup" v-if="canSignup">{{signupBtnText}}</button>
				<button class="cu-btn round block margin-tb-sm lg bg-green" @tap="startPaper" v-if="canStart">{{canStart == 1 ? '开始考试' : '开始补考'}}</button>
			</view>

		</view>

		<!-- 报名对话框 -->
		<view class="cu-modal" :class="showApplyModal">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">请填写相关信息</view>
					<view class="action" @tap="hideModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="">
					<view class="cu-form-group">
						<view class="title">学校</view>
						<picker @change="schoolChange" :value="schoolIndex" :range="schoolList" range-key="name">
							<view class="picker" style="text-align: center;">
								{{schoolList[schoolIndex]?schoolList[schoolIndex]['name']:''}}
							</view>
						</picker>
					</view>
					<view class="cu-form-group">
						<view class="title">班级</view>
						<input placeholder="请输入班级" v-model="formData.class_name"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">姓名</view>
						<input placeholder="请输入姓名" v-model="formData.real_name"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">手机</view>
						<input placeholder="请输入手机号码" v-model="formData.phone"></input>
					</view>

					<view class="cu-form-group" v-if="detail.signup_mode == 'PASSWORD'">
						<view class="title">密码</view>
						<input type="password" placeholder="请输入该考场密码" v-model="formData.password"></input>
					</view>

				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-blue text-primary" @tap="hideModal">取消</button>
						<button class="cu-btn bg-blue margin-left" @tap="submitSignup">提交报名</button>
					</view>
				</view>
			</view>
		</view>

		<!-- 开始考试对话框 -->
		<view class="cu-modal" :class="showStartModal">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">提示</view>
					<view class="action" @tap="hideModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding-xl">
					确认进入考场开始考试吗？{{makeupText}}
				</view>
				<view class="cu-bar bg-white">
					<view class="action margin-0 flex-sub text-green " @tap="hideModal" v-if="false"><text class="cuIcon-moneybag"></text>微信支付</view>
					<view class="action margin-0 flex-sub solid-left" @tap="hideModal">取消</view>
					<view class="action margin-0 flex-sub text-green solid-left" @tap="goExam()">准备好了，开始吧</view>
				</view>
			</view>
		</view>

		<!-- 考试记录弹框 -->
		<view class="cu-modal" :class="showExamLogModal">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">考试记录</view>
					<view class="action" @tap="hideModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="center-box ">

					<view class="cu-list menu">
						<view class="cu-item text-left">
							<view class="content">
								<text class="cuIcon-time text-primary"></text>
								<text class="text-black">考试时间</text>
							</view>
							<view class="action">
								<text class="text-grey text-sm">{{examLogItem.createtime|format_date}}</text>
							</view>
						</view>

						<view class="cu-item text-left">
							<view class="content">
								<text class="cuIcon-countdown text-primary"></text>
								<text class="text-black">考试用时</text>
							</view>
							<view class="action">
								<text class="text-grey text-sm">{{examLogItem.grade_time|format_second}}</text>
							</view>
						</view>

						<view class="cu-item text-left">
							<view class="content">
								<text class="cuIcon-write text-primary"></text>
								<text class="text-black">是否补考</text>
							</view>
							<view class="action">
								<view class="cu-tag round light" :class="[detail && detail.is_makeup?'bg-yellow':'bg-blue']">{{examLogItem.is_makeup?'是':'否'}}</view>
							</view>
						</view>

						<view class="cu-item text-left">
							<view class="content">
								<text class="cuIcon-punch text-primary"></text>
								<text class="text-black">考试成绩</text>
							</view>
							<view class="action">
								<view class="cu-tag round light" :class="[examLogItem.is_pass?'bg-green':'bg-red']">{{examLogItem.score}}分</view>
								<view class="cu-tag round light" :class="[examLogItem.is_pass?'bg-green':'bg-red']">{{examLogItem.is_pass?'及格':'不及格'}}</view>
							</view>
						</view>

						<view class="cu-item text-left">
							<view class="content">
								<text class="cuIcon-read text-primary"></text>
								<text class="text-black">考试情况</text>
							</view>
							<view class="action">
								<view class="cu-tag round light bg-green">正确{{examLogItem.right_count}}题</view>
								<view class="cu-tag round light bg-red">错误{{examLogItem.error_count}}题</view>
							</view>
						</view>
					</view>

				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-blue text-primary" @tap="hideModal">关闭</button>
					</view>
				</view>
			</view>
		</view>

		<login ref="login" v-on:succ="getDetail"></login>
	</view>
</template>

<script>
	// import request from '@/common/request.js';
	export default {
		data() {
			return {
				roomId: 0,
				detail: {},
				signupLog: {},
				lastSignupLog: {},
				examLogs: [],
				examLogItem: {},
				canSignup: true,
				canStart: false,
				signupBtnText: '立即报名',
				signupBtnClass: 'bg-blue',
				signupClass: 'bg-green',
				signupStatusClass: 'bg-green',
				signupTips: '',
				showApplyModal: [''],
				showStartModal: [''],
				showExamLogModal: [''],
				makeupText: '',
				formData: {
					school_id: 0,
					class_name: '',
					real_name: '',
					phone: '',
					password: '',
				},
				schoolList: [],
				schoolIndex: 0,
				schoolValue: 0,
			}
		},

		onLoad(e) {
			this.roomId = e.id

			if (!this.roomId) {
				uni.showToast({
					title: '缺少考场ID参数',
					icon: 'error'
				})

				this.utils.goback()
				return
			}

			this.getDetail()
		},
		onShow() {
		  uni.$on('login_success', (data) => {
			console.log('login_success', data)
			this.getDetail()
		  })

		  this.getDetail()
		  this.getSchool()
		},
		computed: {

		},
		methods: {
			// 获取详情
			getDetail() {
				this.http('room/detail', {
					room_id: this.roomId
				}, 'get').then(res => {
					if (res.code == 0) {
						this.utils.toast(res.msg)

						setTimeout(() => {
							uni.navigateBack()
						}, 2000)

						return
					}

					this.detail = res.data.room
					if (this.detail.signup_mode == 'NORMAL') {
						this.signupClass = 'bg-green'
						this.signupTips = '当前报名方式为【直接参加】，点击按钮报名即表示参加成功'
					} else if (this.detail.signup_mode == 'PASSWORD') {
						this.signupClass = 'bg-red'
						this.signupTips = '当前报名方式需要输入正确密码才能参加'
					} else if (this.detail.signup_mode == 'AUDIT') {
						this.signupClass = 'bg-purple'
						this.signupTips = '当前报名方式提交申请后需要后台人员审核'
					}

					this.signupLog = res.data.signup_log
					// 已报名
					if (this.signupLog) {
						// 被拒绝情况可以修改信息重新提交报名
						this.canSignup = this.signupLog.status == 2
						this.signupBtnText = '修改信息重新提交'
						this.signupBtnClass = 'bg-yellow'

						// 符合开始考试条件
						this.canStart = this.signupLog.can_start

						// 之前的报名信息
						this.formData.school_id = this.signupLog.school_id
						this.formData.class_name = this.signupLog.class_name
						this.formData.real_name = this.signupLog.real_name
						this.formData.phone = this.signupLog.phone

						switch (parseInt(this.signupLog.status)) {
							case 0:
								this.signupStatusClass = 'bg-yellow'
								break
							case 1:
								this.signupStatusClass = 'bg-green'
								break
							case 2:
								this.signupStatusClass = 'bg-red'
								break
						}

						// 考试记录
						this.examLogs = res.data.exam_logs
					}
					
					this.lastSignupLog = res.data.last_signup
					if (this.lastSignupLog) {
						// 之前的报名信息
						if (!this.formData.school_id) {
							this.formData.school_id = this.lastSignupLog.school_id
						}
						if (!this.formData.class_name) {
							this.formData.class_name = this.lastSignupLog.class_name
						}
						if (!this.formData.real_name) {
							this.formData.real_name = this.lastSignupLog.real_name
						}
						if (!this.formData.phone) {
							this.formData.phone = this.lastSignupLog.phone
						}
					}
					
					let timestampInSeconds = Math.floor(Date.now() / 1000);
					this.canSignup = timestampInSeconds > this.detail.start_time && timestampInSeconds < this.detail.end_time
				})
			},
			
			// 获取学校列表
			getSchool() {
				this.http('school/index', {}, 'post').then(res => {
					if (res.code == 1) {
						let schoolList = res.data.list
						schoolList.unshift({
							id: 0,
							name: '--请选择学校--'
						})
						this.schoolList = schoolList
					} else {
						this.schoolList = []
					}
				})
			},
			
			// 选择学校
			schoolChange(e) {
				console.log('schoolChange', e)
				this.schoolIndex = e.detail.value
				// this.schoolValue = e.detail.value
			},

			// 报名
			signup() {
				let user = this.utils.getData('user')
				if (!user) {
					this.$refs.login.modal = true
					return
				}
				this.showApplyModal = ['show']
			},
			// 隐藏报名对话框
			hideModal(e) {
				this.showApplyModal = ['']
				this.showStartModal = ['']
				this.showExamLogModal = ['']
			},
			// 清除输入框值
			clearInput() {
				this.formData.school_id = 0;
				this.schoolIndex = 0;
				this.formData.class_name = '';
				this.formData.real_name = ''
				this.formData.phone = ''
				this.formData.password = ''
			},
			// 密码输入
			// inputPassword(e) {
			// 	console.log('input e', e)
			// 	this.password = e.detail.value
			// },
			// 提交报名
			submitSignup() {
				if (this.detail.signup_mode == 'PASSWORD' && !this.formData.password) {
					this.utils.toast('请输入考场密码')
					return
				}
				
				let school_id = 0
				if (this.schoolIndex) {
					school_id = this.schoolList[this.schoolIndex].id
				}

				this.formData['room_id'] = this.roomId
				this.formData['school_id'] = school_id//this.schoolValue
				console.log('formData', this.formData)
				this.http('room/signup', this.formData, 'post').then(res => {
					// 有错误
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})

						return
					} else {
						// 清除输入框
						this.clearInput()
						// 隐藏弹框
						this.hideModal()
						// 刷新
						this.getDetail()
					}

					this.utils.toast(res.msg, 'success')
				})
			},
			// 开始考试弹窗
			startPaper() {
				this.showStartModal = ['show']
			},
			// 进入考场
			goExam() {
				console.log('go', '../paper?id=' + this.detail.paper_id + '&room_id=' + this.roomId)
				// uni.reLaunch({
				// 	url: '/pages/paper/paper?id=' + this.detail.paper_id + '&room_id=' + this.roomId
				// })
				this.showStartModal = ['hide']
				this.utils.goto('/pages/paper/paper?id=' + this.detail.paper_id + '&room_id=' + this.roomId)
			},
			// 查看考试记录
			viewExamLogModal(examLog) {
				this.examLogItem = examLog
				this.showExamLogModal = ['show']
			},
		}
	}
</script>
<style lang="scss" scoped>
	.container {
		width: 750rpx;
		color: #333333;

		.bg-top {
			margin-top: -1rpx;
			width: 750rpx;
			height: 220rpx;
			padding-top: 50rpx;
			border-radius: 0 0 20% 20%;

			.top-box {
				width: 700rpx;
				background-color: #FFFFFF;
				margin: 0 auto;
				border-radius: 20rpx;
				padding: 20rpx 30rpx 0rpx;
				position: relative;

				.qh-pic {
					position: absolute;
					right: 64rpx;
					top: -50rpx;
					border-radius: 12rpx;
				}

				.qh-title {
					width: 100%;
					height: 60rpx;
					line-height: 65rpx;
					padding-right: 190rpx;
				}

				.bxBox {
					position: relative;
					display: flex;
					/* padding: 0 30rpx; */
					min-height: 100rpx;
					/* background-color: #ffffff; */
					/* justify-content: space-between; */
					align-items: center;
					font-size: 30rpx;
					line-height: 1.6em;
					flex: 1;

					.bxImg {
						display: inline-block;
						margin-right: 10rpx;
						width: 1.6em;
						text-align: center;
					}
				}

			}
		}

		.center-box {
			color: #333333;
			width: 700rpx;
			background-color: #FFFFFF;
			margin: 0 auto;
			border-radius: 20rpx;
			padding: 0rpx 30rpx 0rpx;
			position: relative;
			margin-top: 20rpx;
		}

		.bg-black {
			background-color: #5677fc;
		}
	}

	.cu-list.menu>.cu-item.arrow {
		padding-right: 70rpx;
	}

	.padding-0 {
		padding: 0px !important;
	}

	.padding-left-0 {
		padding-left: 0px !important;
	}
</style>
