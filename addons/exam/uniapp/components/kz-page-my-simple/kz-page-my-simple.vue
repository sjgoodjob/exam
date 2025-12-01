<template>
	<view class="page">
		<view class="bg-user user-info ">
			<view class="user" @tap="login">
				<view class="cu-avatar xl round user-avatar" v-if="userInfo" :style="'background-image:url(' + userInfo.avatar + ')'">
					<text class="cuIcon-people" v-if="userInfo && !userInfo.avatar"></text>
				</view>
				<view class="user-name" v-if="userInfo && userInfo.nickname">{{ userInfo.nickname }}</view>
				<view class="user-name" v-else>点击授权登录</view>
				<navigator class="merchant_num" url="score" hover-class="none"></navigator>
			</view>
		</view>

		<view class="cu-card case" style="margin-top: -50px;z-index: 11;position: relative;">
			<view class="cu-item shadow">
				<view class="cu-list grid col-3">
					<view class="cu-item" @tap="goTo" data-url="../user/member-center">
						<view class="cuIcon-crown text-brown"></view>
						<text>会员中心</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../user/member-protocol">
						<view class="cuIcon-copy text-mauve"></view>
						<text>会员协议</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../score/score-log">
						<view class="cuIcon-text text-pink"></view>
						<text>会员积分</text>
					</view>
				</view>
			</view>
		</view>

		<!-- <view class="cu-card case" style="margin-top: -50px;z-index: 9999;position: relative;">
			<view class="cu-item shadow">
				<view class="cu-list grid col-3">
					<view class="cu-item" @tap="goTo" data-url="../room/index">
						<view class="cuIcon-list text-blue"></view>
						<text>考场</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../room/signup-index">
						<view class="cuIcon-edit text-olive"></view>
						<text>报名记录</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../room/grade">
						<view class="cuIcon-formfill text-yellow"></view>
						<text>考场成绩</text>
					</view>
				</view>
			</view>
		</view> -->

		<view class="cu-card case">
			<view class="cu-item shadow">
				<view class="cu-list grid col-3">
					<view class="cu-item" @tap="goTo" data-url="../score/good-list">
						<view class="cuIcon-shop text-red"></view>
						<text>积分商城</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../room/index">
						<view class="cuIcon-list text-blue"></view>
						<text>考场</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../room/signup-index">
						<view class="cuIcon-edit text-olive"></view>
						<text>报名记录</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../room/grade">
						<view class="cuIcon-formfill text-yellow"></view>
						<text>考场成绩</text>
					</view>

					<view class="cu-item" @tap="goTo" data-url="../search/index">
						<view class="cuIcon-searchlist text-purple"></view>
						<text>试题查询</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../collect/index">
						<view class="cuIcon-likefill text-orange"></view>
						<text>试题收藏</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../wrong/index">
						<view class="cuIcon-roundclose text-blue"></view>
						<text>我的错题</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../paper/grade">
						<view class="cuIcon-newshotfill text-cyan"></view>
						<text>考卷成绩</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../user/set">
						<view class="cuIcon-my text-mauve"></view>
						<text>个人设置</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../user/my-cate">
						<view class="cuIcon-favor text-green"></view>
						<text>常用题库</text>
					</view>
					<view class="cu-item" @tap="goTo" data-url="../user/my-correction">
						<view class="cuIcon-wenzi text-red"></view>
						<text>纠错反馈</text>
					</view>
					<view class="cu-item" @tap="clickFollow()" v-show="showFollowBtn">
						<view class="cuIcon-weixin tn-color-green"></view>
						<text>关注我们</text>
					</view>
					<!-- #ifdef MP-WEIXIN -->
					<view class="cu-item">
						<button class="content" style="color: #333333;" open-type="contact">
							<view class="cuIcon-weixin text-green"></view>
							<text>联系客服</text>
						</button>
					</view>
					<!-- #endif -->
					<view class="cu-item" @tap="clearStorage">
						<view class="cuIcon-deletefill text-grey"></view>
						<text>清除缓存</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 关注我们 -->
		<tui-modal :show="showFollow" @cancel="showFollow = false" :custom="true">
			<view class="tui-modal-custom">
				<image :src="followImage" mode="widthFix"></image>
				<view class="tui-modal-custom-text">

				</view>
				<tui-button height="72rpx" :size="28" type="primary" shape="circle" @click="showFollow = false">确定</tui-button>
			</view>
		</tui-modal>
	</view>
</template>

<script>
	export default {
		name:"kz-page-my-simple",
		// emits: ['login', 'goTo', 'clearStorage'],
		props: {
			// 用户信息
			user: {
				type: Object,
				default: () => null
			},
		},
		watch: {
			/**
			 * 监听user
			 * @param value
			 */
			user(value) {
				console.log('watch user', value)
				this.userInfo = value

				var page = uni.getStorageSync('page')
				this.showFollowBtn = parseInt(page.page_my_follow_mp_btn) == 1
			},
		},
		data() {
			return {
				userInfo: null,
				// 关注公众号
				showFollowBtn: false,
				showFollow: false,
				followImage: '',
			};
		},
		methods: {
			login() {
				this.$emit('login')
			},
			goTo(e) {
				let page = e.currentTarget.dataset.url
				this.$emit('goTo', page)
			},
			clearStorage() {
				this.$emit('clearStorage')
			},
			clickFollow() {
				var page = uni.getStorageSync('page')
				if (page.page_my_follow_mp_click == 'image') {
					this.followImage = this.appInfo.api_host + page.follow_mp_image
					this.showFollow = true
				} else if (page.page_my_follow_mp_click == 'article') {
					this.goTo('/pages/webview/webview?url=' + page.follow_mp_article_url)
				}

			},
		}
	}
</script>

<style>
	.page {
		height: 100vh;
	}

	.bg-user {
		background: #5677fc;
		color: #fff;
	}

	.user-info {
		width: 100%;
		height: 150px;
		position: relative;
		border-radius: 0 0 10% 10%;
	}

	.user {
		width: 100%;
		height: 80px;
	}

	.avatar {
		width: 80px;
		height: 80px;
		border: 1px solid #fff;
		margin-left: 5%;
		float: left;
	}

	.avatar image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.user-avatar {
		margin-left: 50rpx;
		margin-top: 50rpx;
	}

	.user-name {
		font-size: 18px;
		width: calc(95% - 100px);
		float: right;
		margin-top: 80rpx;
		color: #fff;
	}

	button.contact::after {
		border: none;
	}

	button.content {
		background-color: #fff;

		padding-left: 0;

		padding-right: 0;

		line-height: inherit;
		border-radius: 0;
	}
</style>
