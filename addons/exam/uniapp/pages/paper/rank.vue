<!-- 薪资排行 -->
<template>
	<view>
		<view class="contaier" style="background-color: #FFFFFF;">
			 <!-- style="background: url(/static/rank/rank_bg.png) no-repeat;background-size: 750rpx;position: relative;" -->
			<view class="top_bg" :style="{backgroundImage: `url(${bgImage})`}">
				<view class="one_box">
					<!-- 第二名 -->
					<view class="top3">
						<view class="num_two">
							<image class="huangguan2" src="@/static/rank/two.png"></image>
							<image class="top3_head" :src="getGradeUserAvatar(second)"></image>
							<view class="top_name">{{getGradeUserName(second)}}</view>
							<view class="top_sy">得分：<span>{{second ? second.score : 0}}</span></view>
							<view class="top_sy">用时：<span v-if="second">{{second.grade_time|format_second(second.grade_time)}}</span><span v-else>0</span></view>
						</view>
					</view>
					
					<!-- 第一名 -->
					<view class="top3">
						<view class="num_one">
							<image class="huangguan1" src="@/static/rank/one.png"></image>
							<image class="top3_head" :src="getGradeUserAvatar(first)"></image>
							<view class="top_name text-bold" style="font-size: 30rpx;">{{getGradeUserName(first)}}</view>
							<view class="top_sy">得分：<span>{{first ? first.score : 0}}</span></view>
							<view class="top_sy">用时：<span v-if="first">{{first.grade_time|format_second(first.grade_time)}}</span><span v-else>0</span></view>
						</view>
					</view>
					
					<!-- 第三名 -->
					<view class="top3">
						<view class="num_three">
							<image class="huangguan2" src="@/static/rank/three.png"></image>
							<image class="top3_head" :src="getGradeUserAvatar(third)"></image>
							<view class="top_name">{{getGradeUserName(third)}}</view>
							<view class="top_sy">得分：<span>{{third ? third.score : 0}}</span></view>
							<view class="top_sy">用时：<span v-if="third">{{third.grade_time|format_second(third.grade_time)}}</span><span v-else>0</span></view>
						</view>
					</view>
				</view>


				<view class="number_sy_box">
					<view class="number_sy_box_title">
						<text>统计</text>
						<text style="position: absolute; right: 20rpx;z-index: 9999; font-size: 24rpx;color: #c3c3c3;">
							截止：{{summary.cache_time}}
						</text>
					</view>
					<view class="number_sy_main">
						<view style="width: 50%; text-align: center; border-right: 1px solid #eee;">
							<view class="number_num1">{{summary.grade_count}}人</view>
							<view class="danwei">考试人数</view>
						</view>
						<view style="width: 50%; text-align: center; z-index: 9999;">
							<view class="number_num2">{{summary.pass_rate}}</view>
							<view class="danwei">及格率</view>
						</view>
						<!-- <image mode="widthFix" class="xiaoding_bg" src="@/static/rank/Intersect.png"></image> -->
					</view>
				</view>
			</view>

			<view class="rankList_box">
				<view class="rankItem" v-for="(item,index) in rankList" :key="index">
					<view class="rankIndex">
						<text>{{ index + 4 }}</text>
					</view>
					<view class="HeardBox">
						<image class="rankHeard" :src="item.user.avatar"></image>
					</view>
			
					<view class="NameBox">
						<!-- <view class="userName">{{item.name}}</view> -->
						<view class="userName text-bold">{{item.user.nickname}}</view>
						<view class="userPost text-gray"></view>
						<view class="color_ccc text-sm">
							得分：<span>{{item.score}}</span>分 | 用时：<text class="text-blue">{{item.grade_time|format_second}}</text>
						</view>
					</view>
					<view class="download_box">
						<!-- <image mode="widthFix" src="@/static/rank/download.png"></image> -->
					</view>
				</view>
				
				<tui-no-data imgUrl="/static/img/img_noorder_3x.png" v-if="showNodata">暂无数据</tui-no-data>
			</view>
			
			<view class="">
				
			</view>
		</view>
		
		<!--流量主组件-->
		<kz-ad ref="adRank" kind="BANNER" :config="ad" field="rank_banner"></kz-ad>
		<kz-ad ref="adRank" kind="VIDEO" :config="ad" field="rank_video"></kz-ad>
		<kz-ad ref="adRank" kind="VIDEO_PATCH" :config="ad" field="rank_video_patch"></kz-ad>
	</view>
</template>

<script>
	import bgImage from "@/static/rank/rank_bg.png"
	
	export default {
		data() {
			return {
				bgImage: bgImage,
				paper_id: 0,
				nowTime: '',
				defaultAvatar: '/static/user/avatar.png',
				first: null,
				second: null,
				third: null,
				rankList: [
					
				],
				summary: {},
				showNodata: false,
				ad: {}
			}
		},
		// computed: {
		// 	getGradeUserAvatar(grade) {
		// 		return grade?.user?.avatar ?? this.defaultAvatar
		// 	},
		// },
		onLoad(e) {
			this.ad = this.utils.getData('ad')
			console.log('ad data', this.ad)
			// 插屏广告
			if (this.ad.rank_cp_open == 1) {
				this.adUtils.interstitial.load(this.ad.rank_cp)
				this.adUtils.interstitial.show()
			}
			
			this.paper_id = e.paper_id
			this.getRank()
			this.getTime();
		},
		methods: {
			getRank() {
				// this.paper_id = 66
				
				this.http('grade/rank', {paper_id: this.paper_id}, 'post').then(res => {
					console.log('grade res', res)
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
						return
					} else {
						let list = res.data.list
						
						let getTop3 = (list) => {
							if (list?.length > 0) {
								return list.shift()
							}
						}
						
						this.first = getTop3(list)
						this.second = getTop3(list)
						this.third = getTop3(list)
						this.rankList = list
						this.summary = res.data.summary
						this.showNodata = list.length == 0
					}
				})
			},
			// 获取头像
			getGradeUserAvatar(grade) {
				let avatar = grade?.user?.avatar ?? this.defaultAvatar
				return avatar ? avatar : this.defaultAvatar
			},
			// 获取姓名
			getGradeUserName(grade) {
				return grade?.user?.nickname ?? '暂无'
			},
			getTime: function() {

				var date = new Date(),
					year = date.getFullYear(),
					month = date.getMonth() + 1,
					day = date.getDate(),
					hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(),
					minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
					second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
				month >= 1 && month <= 9 ? (month = "0" + month) : "";
				day >= 0 && day <= 9 ? (day = "0" + day) : "";
				var timer = year + '-' + month + '-' + day + ' ' + hour + ':00';
				this.nowTime = timer
				console.log(this.nowTime);
			},
		},
		filters: {

		}
	}
</script>

<style lang="scss">
	.tui-nodata-fixed {
		top: 80% !important;
	}
	.top_bg {
		width: 750rpx;
		height: 650rpx;
		// background: url(@/static/rank/rank_bg.png) no-repeat;
		background-size: 750rpx;
		position: relative;
		margin-top: -50rpx;
	}

	.one_box {
		width: 750rpx;
		height: 260rpx;
		position: absolute;
		left: 0;
		bottom: 110rpx;
		display: flex;
		justify-content: space-around;
	}

	.one_box .top3 {
		width: 210rpx;
		height: 280rpx;
	}

	.top_name {
		width: 100%;
		height: 45rpx;
		// line-height: 60rpx;
		color: #f2f2f2;
		font-size: 26rpx;
		text-align: center;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.top_sy {
		width: 100%;
		height: 40rpx;
		line-height: 40rpx;
		font-size: 24rpx;
		color: rgba(255, 255, 255, .7);
		text-align: center;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.top_sy span {
		font-size: 20rpx !important;
	}

	.num_one {
		position: relative;
	}

	.huangguan1 {
		width: 60px;
		height: 60px;
		position: absolute;
		right: -10rpx;
		top: -18*2rpx;
	}

	.num_one .top3_head {
		width: 150rpx;
		height: 150rpx;
		border-radius: 100%;
		margin: 15rpx 0 0 30rpx;
		border: 4rpx solid #ffdd3c;
	}

	.num_two {
		position: relative;
	}

	.huangguan2 {
		width: 100rpx;
		height: 100rpx;
		position: absolute;
		right: 15rpx;
	}

	.num_two .top3_head {
		width: 120rpx;
		height: 120rpx;
		border-radius: 100%;
		margin: 45rpx 0 0 45rpx;
		border: 4rpx solid #bcdcdf;
	}

	.num_three {
		position: relative;
	}

	.huangguan2 {
		width: 100rpx;
		height: 100rpx;
		position: absolute;
		right: 15rpx;
	}

	.num_three .top3_head {
		width: 120rpx;
		height: 120rpx;
		border-radius: 100%;
		margin: 45rpx 0 0 45rpx;
		border: 4rpx solid #e29d85;
	}

	// 第二块
	.number_sy_box {
		width: 700rpx;
		height: 210rpx;
		background-color: #FFFFFF;
		position: absolute;
		left: 25rpx;
		border-radius: 20rpx;
		bottom: -140rpx;
		z-index: 999;
		padding: 22rpx;
		box-shadow: 3px 3px 15px 3px rgba(0, 0, 0, 0.1)
	}

	.number_sy_box_title {
		color: #888888;
		// font-weight: 500;
		font-size: 28rpx;
		display: flex;
		z-index: 9999;
		justify-content: space-between;
	}

	.number_sy_main {
		width: 100%;
		height: 124rpx;
		padding-top: 20rpx;
		line-height: 52rpx;
		// background: red;
		display: flex;
		justify-content: space-around;
		position: relative;
	}

	.xiaoding_bg {
		position: absolute;
		right: -22rpx;
		bottom: -30rpx;
		width: 180rpx;
	}

	.number_num1 {
		font-size: 40rpx;
		font-weight: 500;
		color: #2fc04f;
	}

	.number_num2 {
		font-size: 40rpx;
		font-weight: 500;
		color: #4bac7f;
	}

	.danwei {
		height: 60rpx;
		line-height: 60rpx;
		font-size: 26rpx;
		color: #c9c9c9;
	}

	// 列表
	.rankList_box {
		width: 750rpx;
		min-height: 420rpx;
		margin-top: 160rpx;
	}

	.rankItem:last-child {
		border: none;
	}

	.rankItem {
		width: 700rpx;
		height: 140rpx;
		margin: 0px auto;
		border-bottom: 1px solid #e9e9e9;
	}

	.rankIndex {
		width: 60rpx;
		height: 140rpx;
		line-height: 140rpx;
		text-align: center;
		color: #CCCCCC;
		font-size: 40rpx;
		float: left;
	}

	.HeardBox {
		width: 100rpx;
		height: 100rpx;
		margin: 20rpx;
		border-radius: 100%;
		overflow: hidden;
		float: left;
		margin-right: 25rpx;
		margin-left: 10rpx !important;
	}

	.HeardBox image {
		width: 100%;
		height: 100%;
	}

	.NameBox {
		width: 400rpx;
		height: 140rpx;
		float: left;
		padding-top: 10rpx;
		box-sizing: border-box;
	}

	.NameBox view {
		height: 50rpx;
		line-height: 70rpx;
	}

	.userName {
		min-width: 90rpx;
		font-size: 30rpx;
		float: left;
		margin-right: 20rpx;
	}

	.download_box {
		width: 80rpx;
		height: 140rpx;
		// background-color: red;
		float: right;

	}

	.download_box image {
		width: 45rpx;
		margin: 50rpx auto;
		display: block;
	}
</style>
