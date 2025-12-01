<template>
	<view>
		<!-- 顶部自定义导航 -->
		<!-- <tn-nav-bar fixed :bottomShadow="false" backTitle=" ">
			<view class="">
				<text class="tn-text-lg">考场列表</text>
				<text class="tn-text-xl tn-padding-left-sm tn-icon-group-circle"></text>
			</view>
		</tn-nav-bar> -->
		
		<HMfilterDropdown :filterData="filterData" :defaultSelected="filterDropdownValue" @confirm="confirmCate">
		</HMfilterDropdown>

		<view class="paper-contains">
			<!-- 考场列表 -->
			<view class="paper-card" v-for="(item, index) in list" :key="index">
				<navigator :url="'detail?id='+item.id" hover-class="none">
					<tui-card :title="{text: item.name, size: 30, color: '#7A7A7A'}"
						:tag="{text: item.cates ? item.cates.name : '-', size: 24}" class="article-shadow">
						<template v-slot:body>
							<view class="tui-default">
								<view class="">
									{{item.contents}}
								</view>
							</view>
						</template>
						<template v-slot:footer>
							
							<view class="tui-default">
								<view class="">
									考试时间：{{item.start_time|format_date}} - {{item.end_time|format_date}}
								</view>
								
								<text>报名方式：</text>
								<text class="cu-tag" :class="[getSignupModeClass(item)]">{{item.signup_mode_text}}</text>
								
								<view class="cu-capsule round m-l-30" v-if="item.is_makeup">
									<view class='cu-tag bg-blue '>
										可补考
									</view>
									<view class="cu-tag line-blue">
										{{item.makeup_count}}次
									</view>
								</view>
								<!-- <text class="cu-tag m-l-30" :class="[getMakeupModeClass(item)]">{{item.makeup_mode_text}}</text> -->
							</view>
						</template>
					</tui-card>
				</navigator>
			</view>

			<!-- 加载状态条 -->
			<view class="cu-load" :class="loadFlag" v-show="!has_more"></view>
		</view>
		
		<view class="tn-tabbar-height"></view>

		<tabbar></tabbar>
		<!-- <scroll-view scroll-x class="bg-white nav" scroll-with-animation :scroll-left="scrollLeft">
			<view class="cu-item" :class="index==tabCur?'text-green cur':''" v-for="(item,index) in 10" :key="index" @tap="tabSelect" :data-id="index">
				Tab{{index}}
			</view>
		</scroll-view> -->
	</view>
</template>

<script>
	import HMfilterDropdown from '@/components/HM-filterDropdown/HM-filterDropdown.vue';

	export default {
		components: {
			'HMfilterDropdown': HMfilterDropdown,
		},
		data() {
			return {
				// scrollLeft: 0,
				// tabCur: 0,
				filterData: [],
				filterDropdownValue: '',
				cate_id: null,
				sort: null,
				list: [],
				has_more: false,
				current_page: 1,
				loadFlag: 'loading',
				subjectId: 0,
			}
		},
		onLoad(e) {
			this.subjectId = this.utils.getSubjectId()
			this.getCate()
			this.getRoom()
		},
		async onReachBottom() {
			console.log("onReachBottom")
			if (this.has_more) {
				this.current_page++
				this.getRoom()
			}
		},
		computed: {
			// 报名方式样式
			getSignupModeClass(item) {
				return function(item) {
					switch (item.signup_mode) {
						case 'NORMAL':
							return 'bg-green'
						case 'PASSWORD':
							return 'bg-bule'
						case 'AUDIT':
							return 'bg-purple'
					}
				}
			},
			// 补考方式样式
			getMakeupModeClass(item) {
				return function(item) {
					if (item.is_makeup) {
						return 'bg-green'
					}
					
					return 'bg-bule'
				}
			},
		},
		methods: {
			// 获取分类
			getCate() {
				this.http('cate/filter', {
					kind: 'ROOM',
					subject_id: this.subjectId,
				}).then(res => {
					this.filterData = res.data
				})
			},

			// 获取考场
			getRoom() {
				this.loadFlag = 'loading'
				
				let params = {
					page: this.current_page
				}
				
				if (this.subjectId) {
					params['subject_id'] = this.subjectId
				}
				if (this.cate_id) {
					params['cate_id'] = this.cate_id
				}
				if (this.sort) {
					params['sort'] = this.sort
				}
				
				this.http('room/index', params).then(res => {
					if (res && res.data) {
						this.list = this.list.concat(res.data.list.data)
						console.log('list', this.list)
						this.has_more = res.data.list.has_more
						this.current_page = res.data.list.current_page
						this.loadFlag = 'over'
					}
				})
			},

			// 选择分类
			confirmCate(event) {
				console.log('confirm cate', event)
				let cate_id = 0
				for (let i = 0; i < event.value[0].length; i++) {
					cate_id = event.value[0].pop()
					if (cate_id) {
						break
					}
				}
				
				if (!cate_id && event.value[0][0]) {
					cate_id = event.value[0][0]
				}
				
				let sort = ''
				if (event?.value[1]) {
					sort = event?.value[1][0]
				}

				if (cate_id != this.cate_id || sort != this.sort) {
					this.cate_id = cate_id
					this.sort = sort
					
					this.list = []
					this.getRoom()
				}
			},


			// tabSelect(e) {
			// 	this.tabCur = e.currentTarget.dataset.id;
			// 	this.scrollLeft = (e.currentTarget.dataset.id - 1) * 60
			// }
		}
	}
</script>

<style>
	page {
		background-color: #fff;
	}
	
	.paper-contains {
		margin-top: 50px;
	}

	.paper-card {
		margin: 30rpx 0;
	}

	.tui-title {
		width: 100%;
		padding: 70rpx 30rpx 30rpx 30rpx;
		box-sizing: border-box;
		font-size: 30rpx;
		line-height: 30rpx;
		color: #666;
	}

	.tui-default {
		padding: 20rpx 30rpx;
	}

	.tui-article {
		position: relative;
	}

	.tui-article-img {
		width: 100%;
		height: 300rpx;
		display: block;
	}

	.tui-article-title {
		position: absolute;
		left: 0;
		bottom: 0;
		color: #fff;
		font-size: 32rpx;
		font-weight: 500;
		box-sizing: border-box;
		padding: 20rpx 30rpx;
		background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.6));
	}

	.tui-cell-title {
		font-size: 32rpx;
		font-weight: 500;
		padding: 0 10rpx;
		word-break: break-all;
		word-wrap: break-word;
	}

	.tui-cell-img {
		height: 160rpx;
		width: 160rpx;
	}

	.tui-flex {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.tui-mr {
		margin-right: 20rpx;
	}


	.tui-flex-pic {
		display: flex;
		display: -webkit-flex;
		justify-content: space-between;
		flex-direction: row;
		flex-wrap: wrap;
		box-sizing: border-box;
		background: #fff;
		padding: 0 120rpx;
	}

	.tui-flex-pic image {
		width: 32%;
		margin-bottom: 2%;
	}

	.tui-content {
		padding: 0rpx 30rpx 20rpx 120rpx;
		box-sizing: border-box;
		font-size: 34rpx;
		font-weight: 400;
		color: #333;
	}

	.tui-color-gray {
		color: #ccc;
	}

	.tui-pleft {
		padding-left: 120rpx;
	}
	
	.article-shadow {
		border-radius: 15rpx;
		box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
	}
	
	/* 底部安全边距 start*/
	.tn-tabbar-height {
		min-height: 60rpx;
		height: calc(40rpx + env(safe-area-inset-bottom) / 2);
		height: calc(40rpx + constant(safe-area-inset-bottom));
	}
</style>
