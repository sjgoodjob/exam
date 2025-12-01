<template>
	<view>
		<scroll-view scroll-x class="bg-white nav">
			<view class="flex text-center">
				<view class="cu-item flex-sub" :class="'all'==tabCur?'text-blue cur':''" @tap="tabSelect" data-id="all">
					全部
				</view>

				<view class="cu-item flex-sub" :class="0==tabCur?'text-blue cur':''" @tap="tabSelect" :data-id="0">
					待审核
				</view>

				<view class="cu-item flex-sub" :class="1==tabCur?'text-blue cur':''" @tap="tabSelect" :data-id="1">
					已通过
				</view>

				<view class="cu-item flex-sub" :class="2==tabCur?'text-blue cur':''" @tap="tabSelect" :data-id="2">
					被拒绝
				</view>
			</view>
		</scroll-view>

		<view class="">
			<!-- 报名列表 -->
			<view class="paper-card" v-for="(item, index) in list" :key="index">
				<navigator :url="'detail?id='+item.room_id" hover-class="none">
					<tui-card :title="{text: item.room.name, size: 30, color: '#7A7A7A'}" :header="item.room.name"
						:tag="getTagStyle(item)">
						<template v-slot:body>
							<view class="tui-default">
								<view class="">
									{{item.room.contents}}
								</view>
							</view>
						</template>
						<template v-slot:footer>

							<view class="tui-default">
								<view class="">
									考试时间：{{item.room.start_time|format_date}} - {{item.room.end_time|format_date}}
								</view>

								<!-- <text>报名方式：</text>
								<text class="cu-tag" :class="[getSignupModeClass(item)]">{{item.room.signup_mode_text}}</text>
								
								<view class="cu-capsule round m-l-30" v-if="item.room.is_makeup">
									<view class='cu-tag bg-blue '>
										可补考
									</view>
									<view class="cu-tag line-blue">
										{{item.room.makeup_count}}次
									</view>
								</view>
								<text class="cu-tag m-l-30" :class="[getMakeupModeClass(item)]">{{item.makeup_mode_text}}</text> -->
							</view>
						</template>
					</tui-card>
				</navigator>
			</view>

			<!-- 加载状态条 -->
			<view class="cu-load bg-grey" :class="loadFlag" v-show="!has_more"></view>
		</view>

		<!-- <view class="cu-card article">
			<view class="cu-item shadow borderBottom" v-for="(item, index) in list" :key="index" @click="goDetail(item.id)">
				<view class="title"><view class="text-cut">{{item.title}}</view></view>
				<view class="content">
					<image :src="item.tImg" mode="aspectFit"></image>
					<view class="desc">
						<view class="text-content">{{item.introduceText}}</view>
						<view class="margin-top-xs">
							<view class="text-gray light sm round fl">{{item.time}}</view>
							<view class="text-gray light sm round fr">
								<text class="text-gray cuIcon-mark" style="font-size: 34rpx;"></text>
								<text>{{item.read}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			
			
			<view class="cu-load bg-grey" :class="loadFlag" v-show="!has_more"></view>
		</view> -->

		<login ref="login" v-on:succ="getList"></login>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				tabCur: 'all',
				// scrollLeft: 0

				list: [],
				has_more: false,
				current_page: 1,
				loadFlag: 'loading',
			}
		},
		computed: {
			getTagStyle(item) {
				return function(item) {
					let style = {
						text: item.status_text,
						size: 24
					}
					switch (parseInt(item.status)) {
						case 0:
							style['color'] = '#f37b1d'
							break;
						case 1:
							style['color'] = '#39b54a'
							break;
						case 2:
							style['color'] = '#e54d42'
							break;
					}
					
					console.log('style', style)
					return style
				}
			},
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
		onLoad() {
			this.getList()
		},
		async onReachBottom() {
			console.log("onReachBottom")
			if (this.has_more) {
				this.current_page++
				this.getPaper()
			}
		},
		methods: {
			getList() {
				this.http('room_signup/index', {
					page: this.current_page,
					status: this.tabCur
				}, 'get').then(res => {
					if (res && res.data) {
						if (this.current_page == 1) {
							this.list = res.data.list.data
						} else {
							this.list = this.list.concat(res.data.list.data)
						}

						console.log('list', this.list)
						this.has_more = res.data.list.has_more
						this.current_page = res.data.list.current_page
						this.loadFlag = 'over'
					}
				})
			},
			tabSelect(e) {
				this.tabCur = e.currentTarget.dataset.id
				// this.scrollLeft = (e.currentTarget.dataset.id - 1) * 60
				// 重置页码
				this.current_page = 1
				this.getList()
			},
			goDetail(id) {
				uni.navigateTo({
					url: 'detail?id=' + id,
				})
			}
		}
	}
</script>

<style>
	.paper-contains {
		margin-top: 50px;
	}

	.paper-card {
		margin: 20rpx 0;
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
</style>
