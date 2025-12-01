<template>
	<view>
		<!-- 答题组件 -->
		<kz-question mode="VIEW" pageType="WRONG" title="我的错题" :questions="list" v-show="!showNodata" :canDeleteWrong="true" v-on:refresh="refresh"></kz-question>
		
		<!-- 暂无数据 -->
		<tui-no-data imgUrl="/static/img/img_noorder_3x.png" v-if="showNodata">暂无数据</tui-no-data>
		
		<login ref="login" v-on:succ="ajax"></login>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				total: 0,
				page: 1,
				over: 0,
				questionIds: [],
				showNodata: false,
			}
		},
		onLoad(e) {
			this.questionIds = e.question_ids
			this.ajax()
		},
		methods: {
			ajax() {
				this.http('question/wrongList', {page:this.page, question_ids: this.questionIds}, 'post').then(res => {
					if (res.code == 0) {
						this.utils.toast(res.msg)
						setTimeout(() => {
							this.utils.goto('/pages/user/member-center')
						}, 2000)
						// this.utils.confirm()
						return
					}
					
					this.total = res.data.total;
					// this.choice[2] = res.data.total;
					var list = res.data.list.data;
					for (var i in list) {
						list[i]['log_id'] = list[i]['id']
						list[i] = Object.assign(list[i], list[i]['question']);
					}
					
					this.list = this.list || [];
					this.list = this.list.concat(list);
					this.page++;
					
					if (!res.data.list.has_more) {
						this.over = 1;
					}
					
					this.showNodata = this.list.length == 0
				});
			},
			refresh() {
				this.page = 1
				this.list = []
				this.ajax()
			}
		}
	}
</script>

<style>

</style>
