<template>
	<view>
		<watermark text="JASIC CLOUD · Internal Use Only" />

		<HMfilterDropdown :filterData="filterData" :defaultSelected="filterDropdownValue" @confirm="confirmCate"></HMfilterDropdown>

		<view class="paper-contains">
			<!-- 试卷列表 -->
			<view class="">
				<block v-for="(item, index) in list" :key="index">
					<navigator :url="'paper?id=' + item.id" hover-class="none">
						<view class="article-shadow tn-margin">
							<view class="tn-flex">
								<view class="tn-margin-sm tn-padding-top-xs" style="width: 100%;">
									<view class="tn-flex">
										<view class="tn-text-lg tn-text-bold clamp-text-1 tn-text-justify tn-flex-basic-lg">
											<text class="">{{ item.title }}</text>
										</view>
										<view class="tn-flex-basic-sm text-right">
											<view
												style="transform: translate(0rpx,6rpx);"
												class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-blue--light tn-color-blue"
											>
												<text class="tn-tag-content__item--prefix">#</text>
												{{ item.cates.name }}
											</view>
										</view>
									</view>
									<view class="tn-padding-top-xs" style="min-height: 90rpx;">
										<text class=" tn-color-gray clamp-text-2 tn-text-justify">考试限时：{{ item.limit_time | format_second }}</text>
										<view class="text-red" v-if="item.end_time > 0">截止时间：{{ item.end_time | format_date }}</view>
									</view>
									<view class="tn-flex tn-flex-row-between tn-flex-col-between">
										<!-- <view v-if="item.uses == 'ALL'" style="transform: translate(0rpx,6rpx);"
                      class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-yellowgreen tn-color-white">
                      {{ item.uses_text }}</view> -->
										<view
											v-if="item.uses == 'ONLY_MEMBER'"
											style="transform: translate(0rpx,6rpx);"
											class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-red--light tn-color-red"
										>
											{{ item.uses_text }}
										</view>
										<view
											v-else-if="item.uses == 'ONLY_PAY'"
											style="transform: translate(0rpx,6rpx);"
											class="justify-content-item tn-tag-content__item tn-margin-right-xs tn-round tn-text-sm tn-text-bold tn-bg-orange--light tn-color-orange"
										>
											{{ item.uses_text }}
										</view>

										<view class="justify-content-item tn-color-gray tn-text-center" style="padding-top: 15rpx;" v-if="item.uses != 'ALL'">
											<!-- 普通用户价 -->
											<block v-if="item.uses == 'ONLY_PAY'">
												<text class="tn-icon-my-fill tn-padding-right-xs tn-text-lg"></text>
												<text class="tn-padding-right" :class="item.member_price < item.price ? ['text-del-line'] : []">￥{{ item.price }}</text>
											</block>

											<!-- 会员价 -->
											<block v-if="item.uses == 'ONLY_MEMBER' || item.uses == 'ONLY_PAY'">
												<text class="tn-icon-vip-fill tn-padding-right-xs tn-text-lg tn-color-orange"></text>
												<text class="tn-color-orange">￥{{ item.member_price }}</text>
											</block>
										</view>
									</view>
								</view>
								<!-- <view class="image-pic tn-margin-sm" :style="'background-image:url(' + item.userAvatar + ')'">
                  <view class="image-article">
                  </view>
                </view> -->
							</view>
						</view>
					</navigator>
				</block>
			</view>

			<!-- 旧版试卷列表 -->
			<!-- <view class="paper-card" v-for="(item, index) in list" :key="index">
        <navigator :url="'paper?id='+item.id" hover-class="none">
          <tui-card :title="{text: item.cates.name, size: 30, color: '#7A7A7A'}" :tag="{text: item.uses_text, size: 24}" :header="item.title">
            <template v-slot:body>
              <view class="tui-default">
                {{item.title}}
              </view>
              <view class="tn-flex tui-default" v-if="item.uses != 'ALL'">
                <view class="tn-flex-1">费用：</view>
                <view class="tn-flex-1 text-right" v-if="item.price > 0">￥{{item.price}}</view>
                <view class="tn-flex-1 text-right text-red" v-if="item.member_price > 0">￥{{item.member_price}}</view>
              </view>
            </template>
            <template v-slot:footer>
              <view class="tui-default">
                考试限时：{{item.limit_time|format_second}}
              </view>
            </template>
          </tui-card>
        </navigator>
      </view> -->

			<!-- 加载状态条 -->
			<view class="cu-load" :class="loadFlag" v-show="!has_more"></view>
		</view>

		<tabbar></tabbar>
	</view>
</template>

<script>
import HMfilterDropdown from '@/components/HM-filterDropdown/HM-filterDropdown.vue';
import TuiCard from '@/components/tui-card/tui-card.vue';
import Watermark from "@/components/watermark/watermark.vue";

export default {
	components: {
		HMfilterDropdown: HMfilterDropdown,
		'tui-card': TuiCard,
		Watermark,
	},
	data() {
		return {
			cate_id: null,
			sort: null,
			list: [],
			has_more: false,
			current_page: 1,
			loadFlag: 'loading',
			filterData: [],
			filterDropdownValue: [],
			isMember: false,
			subjectId: 0,
		};
	},
	onLoad(e) {
		if (e.cate_id) {
			this.cate_id = e.cate_id
		}
		this.subjectId = this.utils.getSubjectId()
		this.ajax();
	},
	async onReachBottom() {
		console.log('onReachBottom');
		if (this.has_more) {
			this.current_page++;
			this.getPaper();
		}
	},
	methods: {
		async ajax() {
			await this.getCate('init');
			await this.getPaper();
		},

		// 获取试卷
		async getPaper() {
			this.loadFlag = 'loading';

			let params = {
				page: this.current_page
			};

			if (this.cate_id) {
				params['cate_id'] = this.cate_id;
			}
			if (this.sort) {
				params['sort'] = this.sort;
			}
			if (this.subjectId) {
				params['subject_id'] = this.subjectId
			}

			await this.http('paper/index', params).then(res => {
				if (res && res.data) {
					this.list = this.list.concat(res.data.list.data);
					console.log('list', this.list);
					this.has_more = res.data.list.has_more;
					this.current_page = res.data.list.current_page;
					this.loadFlag = 'over';
				}
			});
		},
		// 获取题库
		async getCate(type = '') {
			await this.http('cate/filter', { kind: 'PAPER', subject_id: this.subjectId }).then(res => {
				this.filterData = res.data;
			});
		},
		// 选择题库
		confirmCate(event) {
			console.log('confirm cate', event);
			let cate_id = 0;
			for (let i = 0; i < event.value[0].length; i++) {
				cate_id = event.value[0].pop();
				if (cate_id) {
					break;
				}
			}

      if (!cate_id && event.value[0][0]) {
        cate_id = event.value[0][0]
      }

			let sort = '';
			if (event?.value[1]) {
				sort = event?.value[1][0];
			}

			if (cate_id != this.cate_id || sort !== this.sort) {
				console.log('confirmCate', [cate_id, this.cate_id, sort, this.sort]);

				this.cate_id = cate_id;
				this.sort = sort;

				this.list = [];
				this.getPaper();
			}
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}

.paper-contains {
	margin-top: 50px;
	padding-bottom: 100px;
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
</style>
