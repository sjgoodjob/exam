<template>
	<view>
		<view class="cu-card article">
			<view class="cu-item shadow">
				<view class="title">
					<text class="text-green">{{tips}}</text>
				</view>

				<view class="tui-progress-box">
					<tui-circular-progress
						:fontShow="false"
						:percentage="score / 2"
						:sAngle="sAngle"
						:diam="240"
						:height="130"
						:lineWidth="12"
						:progressColor="color"
						:fontColor="color"
						defaultColor="rgba(25,190,107,0.1)"
						@change="change"
					>
						<view class="tui-progress-text">
							<view>分数</view>
							<view class="tui-progress-num">{{ score }} 分</view>
						</view>
					</tui-circular-progress>
				</view>
			</view>

			<view class="cu-list menu card-menu margin-top sm-border" v-if="gradeResult">
				<view class="cu-item">
					<view class="content">
						<text class="cuIcon-my text-green"></text>
						<text class="text-grey">考生昵称</text>
					</view>
					<view class="action">
						<text class="text-grey">{{user.nickname}}</text>
					</view>
				</view>

				<view class="cu-item">
					<view class="content">
						<text class="cuIcon-time text-green"></text>
						<text class="text-grey">考试时长</text>
					</view>
					<view class="action">
						<text class="text-grey">{{gradeResult.grade_time|format_second}}</text>
					</view>
				</view>

				<view class="cu-item">
					<view class="content">
						<text class="cuIcon-info text-green"></text>
						<text class="text-grey">试卷总分</text>
					</view>
					<view class="action">
						<text class="text-grey">{{gradeResult.total_score}}</text>
					</view>
				</view>

				<view class="cu-item">
					<view class="content">
						<text class="cuIcon-filter text-green"></text>
						<text class="text-grey">及格分数</text>
					</view>
					<view class="action">
						<text class="text-grey">{{gradeResult.pass_score}}</text>
					</view>
				</view>

				<view class="cu-item">
					<view class="content">
						<text class="cuIcon-text text-green"></text>
						<text class="text-grey">题目数量</text>
					</view>
					<view class="action">
						<text class="text-grey">{{gradeResult.total_count}}</text>
					</view>
				</view>

				<view class="cu-item" v-if="gradeResult && gradeResult.error_count > 0">
					<view class="content">
						<text class="cuIcon-warn text-yellow"></text>
						<text class="text-grey">错误题数</text>
					</view>
					<view class="action">
						<button class="cu-btn round bg-yellow shadow" @click="goWrong()">去查看</button>
					</view>
				</view>
				
				<view class="cu-item" v-if="gradeResult && gradeResult.cert">
					<view class="content">
						<text class="cuIcon-pic text-yellow"></text>
						<text class="text-grey">获得证书</text>
					</view>
					<view class="action">
						<button class="cu-btn round bg-yellow shadow" @click="goCert()">去查看</button>
					</view>
				</view>
			</view>

			<view class="padding flex flex-direction">
				<button class="cu-btn block bg-green margin-tb-sm lg radius10" @click="goIndex()">返回首页</button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"kz-grade-result",
		props: {
			score: {
				type: Number,
				default: null
			},
			gradeResult: {
				type: Object,
				default: null
			},
			examMode: {
				type: String,
				default: 'PAPER'
			},
		},
		data() {
			return {
				sAngle: -Math.PI,
				color: '#19be6b',
				tips: '',
				// score: this.gradeScore,
				user: this.utils.getData('user')
			};
		},
		watch: {
			// gradeScore: function() {
			// 	console.log('watch gradeScore', this.gradeScore)
			// 	this.score = this.gradeScore
			// },
			gradeResult: function() {
				console.log('watch gradeResult', this.gradeResult)
				if (this.gradeResult) {
					// this.score = this.gradeResult.score
					// setTimeout(() => {this.score = this.gradeResult.score}, 500)

					if (this.gradeResult.is_pass) {
						this.tips = '恭喜您，考试通过啦！'
						this.color = '#19be6b'
					} else {
						this.tips = '很遗憾，考试没通过，请再接再厉！'
						this.color = '#ff6667'
					}
				}
			},
		},
		methods: {
			change(e) {
				//半圆 进度 * 2
				// this.score = e.percentage * 2;
			},
			goIndex() {
				this.utils.gotore('/pages/index/index')
			},
			goWrong() {
				this.utils.goto('/pages/wrong/index?question_ids=' + this.gradeResult.error_ids)
			},
			goCert() {
				this.utils.goto('/pages/cert/index?grade_id=')
			}
		}
	}
</script>

<style>
	.title {
		font-size: 36upx;
		font-weight: 900;
		line-height: 100upx;
		padding: 0 30upx;
		text-align: center;
	}
	.tui-progress-box {
		width: 100%;
		padding: 0 30rpx;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.tui-progress-text {
		width: 100%;
		height: 130px;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		padding-top: 40rpx;
		position: absolute;
		left: 0;
		top: 0;
	}
	.tui-progress-num {
		font-size: 60rpx;
		padding-top: 20rpx;
	}
</style>
