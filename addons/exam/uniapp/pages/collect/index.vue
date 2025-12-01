<template>
	<view>
		<!-- 答题组件 -->
		<kz-question mode="TRAINING" title="收藏试题" :questions="list" v-show="!showNodata"></kz-question>
		
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
				showNodata: false,
			}
		},
		onLoad() {
			this.ajax()
		},
		methods: {
			ajax() {
				this.http('question/collectList', {page:this.page}, 'get').then(res => {
					this.total = res.data.total;
					// this.choice[2] = res.data.total;
					var list = res.data.list.data;
					for (var i in list) {
						list[i]['log_id'] = list[i]['id']
						list[i]['collected'] = true
						// list[i]['code2'] = false;
						// list[i] = list[i].concat(list[i]['question'])
						list[i] = Object.assign(list[i], list[i]['question']);
					}
					
					this.list = this.list || [];
					this.list = this.list.concat(list);
					this.page++;
					
					if (!res.data.list.has_more) {
						this.over = 1;
					}
					
					this.showNodata = this.list.length == 0
					
					console.log('collect list', this.list)
				});
			},
		}
	}
</script>

<style>

</style>
