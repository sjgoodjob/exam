<template>
	<view>
		<!-- 答题组件 -->
		<kz-question mode="VIEW" :title="cateName" :questions="questions" :questionCount="questionCount"
			:pageCount="pageCount" :currentPage="currentPage" :viewMode="mode" @loadQuestion="getQuestion">
		</kz-question>

		<!-- toast提示 -->
		<tui-toast ref="toast"></tui-toast>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				cateId: 0,
				cateName: '',
				questions: [],
				questionCount: 0,
				pageCount: 100,
				currentPage: 0,
				lastPage: 0,
				mode: 'normal',
			}
		},
		onLoad(e) {
			this.cateId = e.cateId
			this.cateName = e?.cateName
			this.mode = e.mode

			console.log('mode', this.mode)
			this.getQuestion()
		},
		methods: {
			// 获取试题（包括延迟获取）
			getQuestion(page = 1, callback = null) {
				console.log('getQuestion', page, this.currentPage, this.lastPage)
				// 避免重复获取
				if (page <= this.currentPage || (this.lastPage && page > this.lastPage)) {
					return
				}

				let params = {
					page: page,
					page_count: this.pageCount,
					cate_id: this.cateId,
					mode: this.mode
				}

				this.http('question/train', params).then(res => {
					if (res.code == 1) {
						// 默认分页方式
						if (this.mode == 'normal') {
							this.questionCount = res.data.total
							this.currentPage = res.data.current_page
							this.lastPage = res.data.last_page
							this.questions = this.questions.concat(res.data.data)
						} else {
							// 记忆、随机模式暂时只能获取全部题目了
							this.questions = res.data.data
						}

						// 积分提示
						let point = res.data.point
						console.log('point', point)
						if (point?.get_point > 0) {
							this.$refs.toast.show({
								title: "积分+" + point.get_point,
								content: point.type,
								imgUrl: "/static/toast/info-circle.png",
								icon: true,
								duration: 4000,
							})
						}

						if (callback) {
							callback()
						}
					} else {
						if (res.data.need_open_member) {
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
						} else {
							this.utils.alert(res.msg, () => {
								this.utils.goback()
							})
						}
					}
				})
			}
		}
	}
</script>

<style>

</style>
