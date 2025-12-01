<template>
	<view>
		<view class="solids-bottom padding-xs flex align-center">
			<view class="flex-sub text-left">
				<view class="solid-bottom text-sm padding">
					<text class="text-grey">* 可点击记录进行其他操作</text>
				</view>
			</view>
		</view>
		
		<view class="grade-list">
			<view class="grade-card" v-for="(item, index) in list" :key="index" @click="clickGrade(item)">
				<tui-card :title="{text: item.paper ? item.paper.title : '-', size: 30, color: '#7A7A7A'}" :tag="{text: item.cate ? item.cate.name : '-', size: 24}">
					<template v-slot:body>
						<view class="m-lr-20 grade-content text-sm">
							<view class="grid m-t-20">
								<view class="" style="width: 45%;">总分数：{{item.total_score}}分</view>
								<view>得分数：{{item.score}}分</view>
							</view>
							
							<view class="grid">
								<view style="width: 45%;">答对数：{{item.right_count}}题</view>
								<view>答错数：{{item.error_count}}题</view>
							</view>
							
							<view class="m-t-20" :class="is_pass ? ['text-green'] : ['text-red']">{{item.is_pass ? '及格' : '未及格'}}</view>
						</view>
					</template>
					<template v-slot:footer>
						<view class="grade-time">
							<view class="grade-time-item" style="width: 69%;">
								时间：{{item.createtime|format_date}}
							</view>
							<view class="grade-time-item">
								用时：{{item.grade_time|format_second}}
							</view>
						</view>
						
					</template>
				</tui-card>
				
			</view>
			
			<!-- 加载状态条 -->
			<view class="cu-load bg-grey" :class="loadFlag" v-show="has_more || loadFlag == 'over'"></view>
		</view>
		
		<tui-loading v-if="showLoading"></tui-loading>
		<tui-no-data imgUrl="/static/img/img_noorder_3x.png" v-if="showNodata">暂无数据</tui-no-data>
		
		<login ref="login" v-on:succ="getList"></login>
		
		<!-- 操作菜单 -->
		<tn-action-sheet
		  v-model="showAction"
		  :tips="tipsAction"
		  :list="listAction"
		  :borderRadius="23"
		  :cancelBtn="true"
		  :maskCloseable="true"
		  @click="clickActionSheetItem"
		  @close="closedActionSheet"
		>
		</tn-action-sheet>
		
		<tabbar></tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				showLoading: false,
				showNodata: true,
				keyword: '',
				list: [],
				loadFlag: 'loading',
				current_page: 1,
				has_more: false,
				gradeItem: null,
				// sheet action
				showAction: false,
				tipsAction: '',
				listAction: [],
				
			}
		},
		onLoad(e) {
			this.getList()
		},
		async onReachBottom() {
			console.log("onReachBottom")
			if (this.has_more) {
				this.current_page++
				this.getList()
			}
		},
		methods: {
			// 获取记录
			getList() {
				this.loadFlag = 'loading'
				this.has_more = true
				this.http('grade/index', {keyword: this.keyword, page:this.current_page}, 'post').then(res => {
					console.log('grade res', res)
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
						return
					} else {
						this.list = this.list.concat(res.data.list.data)
						
						this.current_page = res.data.list.current_page
						this.has_more = res.data.list.has_more
						this.loadFlag = !this.has_more ? 'over' : ''
						this.showNodata = !this.list.length
					}
				})
			},
			// 查看排行榜
			showRank(paper_id) {
				console.log('paper_id', paper_id)
				this.utils.goto('rank?paper_id=' + paper_id)
			},
			
			// 点击成绩
			clickGrade(item) {
				console.log('grade item', item)
				this.gradeItem = item
				this.tipsAction = {text: item.paper?.title}
				
				let listAction = [
					{text: '查看排行榜'}
				]
				if (item.error_ids) {
					listAction.unshift({text: '查看错题'})
				}
				
				this.listAction = listAction
				this.showAction = true
			},
			
			// 点击菜单选项
			clickActionSheetItem(index) {
				let item = this.listAction[index]
				switch (item.text) {
					case '查看排行榜':
						this.showRank(this.gradeItem.paper_id)
						break
						
					case '查看错题':
						this.utils.goto('/pages/wrong/index?question_ids=' + this.gradeItem.error_ids)
						break
				}
				this.closedActionSheet()
			},
			// 关闭actionSheet
			closedActionSheet() {
			  this.show = false
			}
		}
	}
</script>

<style>
	.grade-list {
		padding-bottom: 20rpx;
	}
	
	.grade-card {
		margin: 20rpx 0;
	}
	
	.options {
		margin: 20rpx 0;
		line-height: 50rpx;
	}
	
	.grade-time {
		margin: 20rpx 0;
		padding: 0rpx 15rpx;
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
	}
	
	.grade-content {
		
	}
	
	
</style>
