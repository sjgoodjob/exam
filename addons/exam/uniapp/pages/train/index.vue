<template>
	
	<view>
		<watermark text="JASIC CLOUD · Internal Use Only" />

		<view class="card-view">
			<image :src="bannerImage" mode="aspectFill" style="width: 100%; height: 200rpx;"></image>
		</view>

		<view style="height: calc(99vh - 200px);">
			<view class="card-view">
				<!-- <tui-divider width="80%" gradual>选择要练习的题目类型</tui-divider> -->
			
				<view class="margin">
					<tn-radio-group activeColor="#5677fc" v-model="mode">
						<tn-radio name="normal">正常模式</tn-radio>
						<tn-radio name="memory">记忆模式</tn-radio>
						<tn-radio name="random">随机模式</tn-radio>
					</tn-radio-group>
				</view>
				
				<view class="margin-left text-sm tn-color-gray">
					<view v-if="mode == 'normal'">* 按顺序出题，不记忆当前做题题标</view>
					<view v-if="mode == 'memory'">* 按顺序出题，记忆当前做题题标，下次进入直接跳至题标</view>
					<view v-if="mode == 'random'">* 随机出题，不记忆当前做题题标</view>
				</view>
			
				<tui-cascade-selection height="800rpx" :itemList="cateList" @complete="complete" text="请选择题库"
					:defaultItemList="defaultCateList"></tui-cascade-selection>
			</view>
			
			<view style="width:90%; margin: 10rpx auto;" class="padding-bottom-xl">
				<tui-button shape="circle" shadow bold preventClick :disabled="questionCount == 0"
					@click="checkPay">{{btnText}}</tui-button>
			</view>
		</view>

		<login ref="login" v-on:succ="getCate"></login>

		<!-- 题库支付、激活弹窗组件 -->
		<kz-cate-pay ref="cate_pay" :cateId="cateId" :catePrice="catePrice" :showCateConfirm="showCateConfirm"
			:confirmCateContent="confirmCateContent" v-on:paySuccess="goTrain()" v-on:codeSuccess="goTrain()"
			v-on:cancel="closeDialog">
		</kz-cate-pay>
	</view>
</template>

<script>
	import cateApi from "@/common/api/cate.js"
	import Watermark from "@/components/watermark/watermark.vue";

	export default {
		data() {
			return {
				page: '',
				cateList: [],
				cateId: 0,
				cateName: '',
				questionCount: 0,
				btnText: '开始练习',
				mode: 'normal',
				defaultCateList: [],

				// 题库支付弹窗相关
				catePrice: 0.00,
				showCateConfirm: false,
				confirmCateContent: '',
				
				subjectId: 0,
				
				bannerImage: '',
			}
		},
		onLoad(e) {
			this.page = e?.page
			this.getPageCache()
			this.subjectId = this.utils.getSubjectId()
			this.getCate()
		},
		methods: {
			// 获取缓存
			getPageCache() {
				let cdn_url = this.utils.getData('cdn_url')
				cdn_url = cdn_url ? cdn_url : ''
				
				let pageCache = this.utils.getData('page')
				console.log('pageCache', pageCache)
				if (pageCache && pageCache.cate_banner_image) {
					this.bannerImage = cdn_url + pageCache.cate_banner_image
					console.log('this.bannerImage', this.bannerImage)
				}
			},
			// 获取题库
			getCate() {
				this.http('cate/getThree', {
					kind: 'QUESTION',
					page: this.page,
					subject_id: this.subjectId
				}).then(res => {
					this.cateList = res.data

					// 模拟题库点击事件
					if (this.cateId) {
						this.complete({
							value: this.cateId,
							result: [{
								text: this.cateName
							}]
						})
					} else {
						let defaultCate = this.utils.getData('default_cate-QUESTION')
						console.log('defaultCate', defaultCate)
						if (defaultCate) {
							let lastCate = [...defaultCate.result].pop()
							console.log('lastCate', lastCate)
							
							let hasCate = false
							for (let j = 0; j < this.cateList.length; j++) {
								let cate = this.cateList[j]
								if (hasCate || cate.value == lastCate.value) {
									hasCate = true
									break
								}
								
								if (cate.children && cate.children.length > 0) {
									for (let k = 0; k < cate.children.length; k++) {
										let child_cate = cate.children[k]
										if (child_cate.value == lastCate.value) {
											hasCate = true
											break
										}
									}
								}
							}

							if (hasCate) {
								let defaultCateList = []
								for (let i = 0; i < defaultCate.result.length; i++) {
									defaultCateList.push(defaultCate.result[i].text)
								}
								this.defaultCateList = defaultCateList
								
								this.cateId = lastCate.value
								this.cateName = lastCate.text
								
								this.complete({
									value: this.cateId,
									result: [{
										text: this.cateName
									}]
								})
							}
						}

						// let user = this.utils.getData('user')
						// if (user?.info && user.info?.default_cate_ids) {
						// 	this.defaultCateList = [...user.info.default_cate_names]

						// 	this.cateId = [...user.info.default_cate_ids].pop()
						// 	this.cateName = [...user.info.default_cate_names].pop()

						// 	this.complete({
						// 		value: this.cateId,
						// 		result: [
						// 			{
						// 				text: this.cateName
						// 			}
						// 		]
						// 	})
						// }
					}

					// this.defaultCateList = ["消防","灭火救援常识",]
				})

				// 模拟题库点击事件
				if (this.cateId) {
					this.complete({
						value: this.cateId,
						result: [{
							text: this.cateName
						}]
					})
				}
			},
			// 选择题库
			complete(e) {
				console.log(e);
				console.log('您选择的数据为：', e);

				this.cateId = e.value
				this.cateName = e.result[e.result.length - 1].text

				let params = {
					cate_id: this.cateId,
					just_get_count: 1,
				}

				this.$refs.login.afterMethod = () => {
					this.complete(e)
				}
				console.log('afterMethod', this.$refs.login.afterMethod)
				this.http('question/train', params).then(res => {
					if (res.code == 1) {
						this.questionCount = res.data.total
						if (this.questionCount) {
							this.btnText = '开始练习（' + this.questionCount + '题）'
						} else {
							this.btnText = '当前分类无试题'
						}
					} else {
						this.utils.toast(res.msg)
					}
				})

			},
			// 检测题库是否已开通
			checkPay() {
				if (!this.cateId) {
					this.utils.toast('请先选择练习的题库')
					return
				}

				// 检测题库是否已开通
				cateApi.checkPay(this, this.cateId).then(res => {
					console.log('checkPay res', res)

					if (res.code != 1) {
						// this.utils.toast(res.msg)
						// if (res.data.need_open_member) {
						// 	this.utils.goto('/pages/user/member-center')
						// }
						uni.showModal({
							title: '提示',
							content: res.msg,
							confirmText: '前往开通',
							success: (res) => {
								if (res.confirm) {
									this.utils.goto('/pages/user/member-center?from_train=1')
								} else if (res.cancel) {
									this.utils.goback()
								}
							}
						});
						return
					}

					// 无须支付
					if (res.data.status == 1) {
						this.goTrain()
						return
					} else if (res.data.status == 2) {
						this.catePrice = parseFloat(res.data.price)
						this.confirmCateContent = res.data.msg
						this.showCateConfirm = true
					}
				})
			},
			// 开始练习
			goTrain() {
				this.showCateConfirm = false
				this.showResult = false
				this.confirmCateContent = ''
				this.payResultContent = ''

				this.page = this.page == 'train' ? 'train' : 'look'
				this.utils.goto(this.page + '?cateId=' + this.cateId + '&cateName=' + this.cateName + '&mode=' + this.mode)
			},
			closeDialog() {
				console.log('get emit closeDialog')
				this.showCateConfirm = false
			}
		},
		components: {
			Watermark,
		}

	}
</script>

<style>
	.tn-radio__label {
		color: #333 !important;
		font-size: 15px !important;
	}
</style>