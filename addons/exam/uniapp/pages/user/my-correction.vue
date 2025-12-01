<template>
	<view>

		<view class="list-contains">
			<!-- 列表 -->
			<view class="">
				<block v-for="(item, index) in list" :key="index">
					<view class="article-shadow tn-margin">
						<view class="tn-flex">
							<view class="tn-margin-sm tn-padding-top-xs" style="width: 100%;">
								<view class="tn-flex">
									<view class="tn-text-lg tn-text-bold clamp-text-1 tn-text-justify tn-flex-basic-lg">
										<text class="">{{ item.createtime_text }}</text>
									</view>
									<view class="tn-flex-basic-sm text-right">
										<view
											style="transform: translate(0rpx,6rpx);"
											class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-blue--light tn-color-blue"
										>
											<text class="tn-tag-content__item--prefix">#</text>
											{{ item.type_names }}
										</view>
									</view>
								</view>
								<view class="tn-padding-top-xs" style="min-height: 90rpx; line-height: 50rpx;">
									<view class="text-content tn-color-gray clamp-text-2 tn-text-justify">试题ID：{{ item.question_id }}</view>
									<view class="text-content tn-color-gray clamp-text-2 tn-text-justify">试题题目：{{ item.question ? item.question.title : '试题可能被删除了' }}</view>
									<view class="text-content tn-color-gray clamp-text-2 tn-text-justify">所属题库：{{ item.question && item.question.cates ? item.question.cates.name : '题库可能被删除了' }}</view>
									<view class="text-content tn-color-gray clamp-text-2 tn-text-justify">反馈内容：{{ item.remark }}</view>
									
									<view class="text-content text-red" v-if="item.message">处理说明：{{ item.message }}</view>
								</view>
								<view class="tn-flex tn-flex-row-between tn-flex-col-between margin-top">
									<view v-if="item.status == 0"
										style="transform: translate(0rpx,6rpx);"
										class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-orange--light tn-color-orange"
									>未处理</view>
									<view v-if="item.status == 1"
										style="transform: translate(0rpx,6rpx);"
										class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-cyan--light tn-color-cyan"
									>已处理</view>
									<view v-if="item.status == 2"
										style="transform: translate(0rpx,6rpx);"
										class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-red--light tn-color-red"
									>忽略</view>

									<view class="justify-content-item tn-color-gray tn-text-center" style="padding-top: 15rpx;">
										<block>
											<view v-if="item.message">处理时间：{{ item.updatetime | format_date }}</view>
										</block>
									</view>
								</view>
							</view>
						</view>
					</view>
				</block>
			</view>

			<!-- 加载状态条 -->
			<view class="cu-load bg-grey" :class="loadFlag" v-show="!has_more"></view>
		</view>

		<tabbar></tabbar>
	</view>
</template>

<script>

import TuiCard from '@/components/tui-card/tui-card.vue';
import correctionApi from "@/common/api/correction.js"

export default {
	components: {
		'tui-card': TuiCard
	},
	data() {
		return {
			list: [],
			has_more: false,
			current_page: 1,
			loadFlag: 'loading',
		};
	},
	onLoad(e) {
		this.ajax();
	},
	onReachBottom() {
		console.log('onReachBottom');
		if (this.has_more) {
			this.current_page++;
			this.getData();
		}
	},
	methods: {
		ajax() {
			this.getData();
		},

		// 获取试卷
		getData() {
			this.loadFlag = 'loading';

			let params = {
				page: this.current_page
			};
			
			correctionApi.getCorrectionList(this, {}).then(res => {
				if (res && res.code == 1) {
					this.list = this.list.concat(res.data.data);
					console.log('list', this.list);
					this.has_more = res.data.has_more;
					this.current_page = res.data.current_page;
					this.loadFlag = 'over';
				} else {
					this.uitls.toast('获取数据失败')
				}
			})
		},
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}

.list-contains {
	margin-top: 20px;
	padding-bottom: 100px;
}

.list-card {
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

.article-shadow {
	border-radius: 15rpx;
	box-shadow: 0rpx 0rpx 50rpx 0rpx rgba(0, 0, 0, 0.07);
}

/* 标签内容 start*/
.tn-tag-content {
	&__item {
		display: inline-block;
		line-height: 35rpx;
		padding: 7rpx 25rpx 5rpx 25rpx;

		&--prefix {
			padding-right: 10rpx;
		}
	}
}

/* 标签内容 end*/

.text-del-line {
	text-decoration: line-through;
}

.text-content {
	word-break: break-all;
	word-wrap: break-word;
	white-space: pre-wrap;
}
</style>
