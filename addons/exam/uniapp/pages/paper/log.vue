<template>
	<view class="content">
		<scroll-view id="scroll" scroll-y="true">
			<view v-for="(item,key) in list" :key="key" class="cu-list menu card-menu margin-top-sm">
				<!-- <ad :unit-id="ad.log_banner" v-if="ad.log_type == 1"></ad>
				<ad :unit-id="ad.log_gezi" ad-type="grid" grid-opacity="0.8" grid-count="5" ad-theme="white" v-else-if="ad.log_type == 2"></ad>
				<ad :unit-id="ad.log_video" ad-type="video" ad-theme="white" v-else-if="ad.log_type == 3"></ad> -->
				
				<view class="cu-item">
					<view class="content padding-tb-sm">
						<view style="display: flex;flex-direction: row;">
							<view>分数：{{item.score}}</view>
						</view>
						<view class="text-gray">考试用时：{{item.grade_time}}秒</view>
						<view class="text-gray">考试时间：{{item.createtime|format_date}}</view>
					</view>
					<view class="action" v-if="false">
						<template v-if="key==0">
							<image class="rankIcon" src="/static/img/rank-01.png" mode=""></image>
						</template>
						<template v-if="key==1">
							<image class="rankIcon" src="/static/img/rank-02.png" mode=""></image>
						</template>
						<template v-if="key==2">
							<image class="rankIcon" src="/static/img/rank-03.png" mode=""></image>
						</template>
					</view>
				</view>
			</view>
			
			<!-- 加载状态条 -->
			<tui-nomore></tui-nomore>
			
			<view class="fix-bottom"></view>
		</scroll-view>
	</view>
</template>

<script>
	let interstitialAd = null
	export default {
		data() {
			return {
				list: [],
				page: 1,
				over: 0,
				ad: this.utils.getData('ad')
			}
		},
		onLoad() {
			this.getList()
			if (wx.createInterstitialAd && this.ad && this.ad.log_cp) {
				interstitialAd = wx.createInterstitialAd({
					adUnitId: this.ad.log_cp
				});
				interstitialAd.onLoad(() => {});
				interstitialAd.onError(err => {});
				interstitialAd.onClose(() => {});
				if (interstitialAd) {
					interstitialAd.show().catch(err => {
						console.error(err);
					});
				}
			}
		},
		onReachBottom() {
			if (this.over == 0) {
				this.getList()
			}
		},
		onShareAppMessage() {
			return {
				title: '在线考试',
				path: '/pages/index/index',
			};
		},
		onShareTimeline() {
			return {
				title: '在线考试',
				// query: '/pages/index/index',
				// imageUrl: '',
			};
		},
		methods: {
			getList(pageNum){
				this.http('grade/index', {user_id:this.utils.getData('user_id'),page:this.page}, 'get').then(res => {
					var list = res.data.list.data;
					this.list = this.list || [];
					this.list = this.list.concat(list);
					this.page++;
					if (!res.data.list.has_more) {
						this.over = 1;
					}
				});
			}
		}
	}
</script>

<style>
.rankIcon{
	width: 70upx;
	height: 90upx;
}

</style>
