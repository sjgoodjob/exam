<template>
	<view style="overflow-x: hidden;">
		<view class="bg bg-black-graw bg-div">
			<!-- <image class="bg-img" src="/static/member/bg.png" mode=""></image> -->
		</view>

		<cu-custom bgColor="bg-black-graw" :isBack="true" :delta="goDelta">
			<block slot="backText">返回</block>
			<block slot="content"><text class="text-df">会员中心</text></block>
		</cu-custom>

		<view class="info" :style="{marginTop: statusBarHeight - 10 + 'px', backgroundImage: `url(${bgImage})`,}" style="background-size: 750rpx;">
			<image class="info-bg" src="@/static/member/info-bg.png" mode=""></image>

			<view class="info-content">
				<view class="info-avatar">
					<image class="info-avatar-pic" :src="user.avatar" mode=""></image>
				</view>
				<view class="info-name">
					<text class="info-name">
						<text>{{ user.nickname }}</text> 
						<text class="tn-color-orange--dark tn-text-bold tn-icon-vip-fill tn-padding-left-sm" v-if="user.info.status == 1"></text>
					</text>

					<text class="info-level">
						<block  v-if="user.info.status == 1">
							<text class="" style="opacity: 0.5; margin-right:10rpx">尊敬的</text>
							<text class="tn-color-orange--dark tn-text-bold">{{user.info.type_text}}</text>
						</block>
						<text class="" v-else>升级会员享额外特权</text>
					</text>
				</view>

				<view class="info-asset tn-flex-3" @click="goTo('/pages/score/score-log')">
					<view class="info-asset-item text-right" style="margin-left: auto;">
						<text class="iai-title">积分</text>
						<text class="iai-value">{{user.info.score}}</text>
					</view>
				</view>
			</view>

			<view class="info-expire-time" v-if="user.info.status == 1">
				<text>会员有效期：</text>
				<text class="tn-text-bold">{{ user.info.expire_time_text }}</text> 
			</view>

		</view>

		<!-- 待定 -->
		<!-- <text class="tip">升级得积分</text> -->

		<view class="tn-text-bold tn-text-lg tn-margin tn-padding-top-sm">
			<text class="">会员特权</text>
		</view>

		<view class="about__wrap tn-margin-top-xl" v-show="false">
			<view class="tn-flex tn-flex-wrap tn-bg-white course-shadow course-radius">
				<view class="tn-padding-sm tn-col-3" v-for="(item, index) in privilegeData" :key="index">
					<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
						<view
							class="icon15__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur tn-color-white"
							:class="[`tn-bg-${item.color}--light tn-color-${item.color}`]">
							<view :class="[`tn-icon-${item.icon}`]"></view>
						</view>
			 		<view class="tn-text-center">
							<text class="tn-text-ellipsis">{{item.title}}</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<view class="about__wrap tn-margin-top-xl">
			<view class="tn-flex tn-flex-wrap tn-bg-white course-shadow course-radius">
				<view class="tn-padding-sm tn-col-3" v-for="(item, index) in privilegeData" :key="index">
					<view class="tn-flex-1 tn-padding-sm tn-radius" @click="goTo(item.url)">
						<view class="tn-flex tn-flex-direction-column tn-flex-row-center tn-flex-col-center">
							<view
								class="icon11__item--icon tn-flex tn-flex-row-center tn-flex-col-center tn-shadow-blur"
								:class="[`tn-bg-${item.color}--light tn-color-${item.color}`]">
								<view :class="[`tn-icon-${item.icon}`]"></view>
							</view>
							<view class="tn-color-black tn-text-df tn-text-center tn-margin-top-sm">
								<text class="tn-text-ellipsis">{{ item.title }}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>


		<view class="tn-text-bold tn-text-lg tn-margin tn-padding-top-sm">
			<text class="">会员开通</text>
		</view>
		<view class="about-shadow tn-margin-top-xl tn-padding-top-sm tn-padding-bottom-sm">
			<scroll-view scroll-x>
				<view class="member">
					<view class="member-item" :class="current == index ? 'member-item-active': ''"
						v-for="(item, index) in memberOptions" :key="index" :style="{marginLeft: !index ? '30rpx': ''}"
						@click="memberChange(index)">
						<view class="member-tag" v-if="item.tag">
							<text class="member-tag-text">{{item.tag}}</text>
						</view>
						<text class="member-item-duration">{{ item.name }}</text>
						<view class="member-item-price">
							<text class="rmb">￥</text>
							<text class="member-item-price-text">{{ item.price }}</text>
						</view>
						<text class="member-item-des">{{ item.desc }}</text>
						<view class="margin-top-xs" @click="seeMemberCateNames(item)">
							<text class="tn-text-sm tn-icon-tips-fill"></text>
							<text class="member-item-des">更多权益</text>
						</view>
					</view>
				</view>
			</scroll-view>
		</view>

		<view class="button update" hover-class="hover" @click="createOrder">
			<text class="button-text" v-if="currentMemberItem && user.info.status == 1 && user.info.member_config_id == currentMemberItem.id">您已成为该会员类型</text>
			<text class="button-text" v-else>立即开通</text>
			<!-- <text class="button-text" v-else-if="user.info.status == 2">重新开通</text> ="user.info.status == 0"-->
		</view>
		<view class="button update" hover-class="hover" @click="showActivate = true" v-if="user.info.status != 1">
			<text class="button-text">我有激活码</text>
		</view>

		<view class="margin-bottom-xl"></view>

		<!-- 开通确认弹窗 -->
		<tui-modal :show="showConfirm" @click="confirmCreateOrder" :content="confirmContent" color="#333" :size="32" :button="confirmButton"></tui-modal>
		<!-- 开通结果弹窗 -->
		<tui-modal :show="showResult" @click="payResult" :content="payResultContent" :button="resultButton"></tui-modal>
		<!-- 激活会员弹窗 -->
		<tn-modal v-model="showActivate" :custom="true" :showCloseBtn="true">
			<view class="custom-modal-content">
				<view class="">
					<view class="tn-text-lg tn-text-bold tn-text-center tn-padding">会员激活码</view>
					<view class="tn-bg-gray--light"
						style="border-radius: 10rpx;padding: 20rpx 30rpx;margin: 50rpx 0 60rpx 0;">
						<input type="text" v-model="memberCode" placeholder-style="color:#AAAAAA" maxlength="20"
							placeholder="请输入会员激活码" />
					</view>
				</view>
				<view class="tn-flex-1 justify-content-item tn-margin-sm tn-text-center">
					<tn-button shape="round" backgroundColor="#5677fc" padding="40rpx 0" width="60%" shadow fontBold @click="activateMember">
						<text class="tn-color-white">激活</text>
					</tn-button>
				</view>
			</view>
		</tn-modal>
		<!-- 查看可用题库弹窗 -->
		<tui-modal :show="showMemberCateNames" custom showCloseBtn>
			<view class="custom-modal-content">
				<view class="">
					<view class="tn-text-lg tn-text-bold tn-text-center tn-padding">可用题库</view>
					<view class="tn-bg-gray--light tn-padding-sm">
						<view class="tn-text-md tn-text-center" v-if="currentMemberItem && currentMemberItem.uses == 'all'">所有题库均可使用</view>
						<view class="" v-else-if="currentMemberItem && currentMemberItem.cate_names.length > 0">
							<view class="margin-bottom-xs tn-text-md tn-text-center" v-for="(name, indexName) in currentMemberItem.cate_names" :key="indexName">{{name}}</view>
						</view>
					</view>
				</view>
				
				<view class="">
					<view class="tn-text-lg tn-text-bold tn-text-center tn-padding">可用试卷分类</view>
					<view class="tn-bg-gray--light tn-padding-sm">
						<view class="tn-text-md tn-text-center" v-if="currentMemberItem && currentMemberItem.paper_uses == 'all'">所有试卷分类均可使用</view>
						<view class="" v-else-if="currentMemberItem && currentMemberItem.paper_cate_names.length > 0">
							<view class="margin-bottom-xs tn-text-md tn-text-center" v-for="(paperCateName, indexPaperCate) in currentMemberItem.paper_cate_names" :key="indexPaperCate">{{paperCateName}}</view>
						</view>
					</view>
				</view>
				
				<view class="tn-flex-1 justify-content-item tn-margin-top tn-text-center">
					<tn-button shape="round" backgroundColor="#5677fc" padding="40rpx 0" width="60%" shadow fontBold @click="showMemberCateNames = false">
						<text class="tn-color-white">我知道了</text>
					</tn-button>
				</view>
			</view>
			<!-- <view class="custom-modal-content">
				<view class="tn-text-center tn-text-bold">可用题库</view>
				<view class="tui-modal-custom-text tn-text-md">
					<view class="" v-if="currentMemberItem && currentMemberItem.cate_names.length > 0">
						<view class="" v-for="(name, indexName) in currentMemberItem.cate_names" :key="indexName">
							<tn-tag backgroundColor="#01BEFF" fontColor="#080808">{{name}}</tn-tag>
						</view>
					</view>
				</view>
			</view> -->
		</tui-modal>
	</view>
</template>

<script>
	import CuCustom from '@/colorui/components/cu-custom.vue'
	import userApi from "@/common/api/user.js"
	import bgImage from "@/static/member/info-bg.png"

	const statusBarHeight = uni.getSystemInfoSync().statusBarHeight
	export default {
		components: {
			CuCustom
		},
		data() {
			return {
				bgImage: bgImage,
				imgUrl: this.url,
				user: null,
				memberConfigs: [],
				statusBarHeight,
				current: 0,
				memberOptions: [],
				showConfirm: false,
				confirmContent: '',
				confirmButton: [{
						text: "取消",
						type: "red",
						plain: true
					},
					{
						text: "确定",
						type: "green",
						plain: false
					}
				],
				showResult: false,
				payResultContent: '',
				resultButton: [{
					text: '确定',
					type: 'green'
				}],
				privilegeData: [{
						title: '积分功能',
						icon: 'flower',
						color: 'orange',
					},
					{
						title: '限用题库',
						icon: 'empty-order',
						color: 'orange',
					},
					{
						title: '限用试卷',
						icon: 'edit-form',
						color: 'orange',
					},
					{
						title: '免费考场',
						icon: 'order',
						color: 'orange',
					},
				],
				
				// 会员激活
				showActivate: false,
				memberCode: '',
				
				// 查看可用题库
				showMemberCateNames: false,
				currentMemberItem: null,
				goDelta: 1,
			}
		},
		onShow() {
			this.ajax()
		},
		onLoad(e) {
			if (e.from_train) {
				const pages = getCurrentPages()
				if (pages.length > 1) {
					this.goDelta = 2
				}
			}
		},
		methods: {
			goTo(page) {
				this.utils.goto(page)
			},
			/**
			 * 获取用户信息和会员配置
			 */
			ajax() {
				userApi.getUserInfo().then(res => {
					console.log('user info res', res)
					this.user = res.data
					uni.setStorageSync('user', this.user)
				})

				userApi.getMemberOpenConfig().then(res => {
					console.log('getMemberOpenConfig res', res)
					this.memberOptions = res.data
				})
			},
			/**
			 * 选择开通会员类型
			 * @param {Object} index
			 */
			memberChange(index) {
				this.current = index
				this.currentMemberItem = this.memberOptions[index]
			},
			/**
			 * 点击开通会员按钮
			 */
			createOrder() {
				if (this.user.info.status == 1 && this.memberOptions[this.current].id == this.user.info.member_config_id) {
					this.utils.alert('您当前已经是会员了，无须重复开通', 'error')
					return
				}
				if (!this.memberOptions || this.memberOptions.length == 0) {
					this.utils.alert('未设置会员开通配置，请先到后台配置', 'error')
					return
				}
				if (!this.memberOptions[this.current]) {
					this.utils.alert('请选择要开通的会员类型', 'error')
					return
				}

				this.confirmContent = `确定开通「${this.memberOptions[this.current].name}」吗？`
				this.showConfirm = true
			},
			/**
			 * 确认开通，创建订单
			 */
			confirmCreateOrder(e) {
				
				if (e.index == 0) {
					this.showConfirm = false
				} else {
					const member_config_id = this.memberOptions[this.current].id
					userApi.createMemberOrder(this, member_config_id).then(res => {
						console.log('createMemberOrder res', res)
						
						if (res.code != 1) {
							this.utils.toast('支付失败：' + res.msg)
							return
						}
					
						// 开通需要费用时
						if (res.data.type == 'orderPay') {
							// 支付参数
							const payment = res.data.payment
							uni.requestPayment({
								// 微信支付
								timeStamp: payment.timeStamp,
								nonceStr: payment.nonceStr,
								package: payment.package,
								signType: payment.signType,
								paySign: payment.paySign,
								success: (payRes) => {
									console.log('支付成功', payRes)
					
									// 显示支付结果弹窗
									this.showResult = true
									this.payResultContent = '支付成功，恭喜您成为会员啦~'
								},
								fail: (res) => {
									console.log('支付异常', res)
					
									// 显示支付结果弹窗
									this.showResult = true
									this.payResultContent = '支付失败了'
								},
								complete: (res) => {
									// 隐藏开通会员弹窗
									this.showConfirm = false
					
									// 刷新会员信息
									this.ajax()
								}
							})
						} else {
							// 免费开通
							// this.utils.goto('member-result?type=' + res.data.type)
					
							// 显示支付结果弹窗
							this.showResult = true
							this.payResultContent = '支付成功，恭喜您成为会员啦~'
					
							// 刷新会员信息
							this.ajax()
						}
					
					})
				}
			},
			/**
			 * 点击支付结果弹窗按钮
			 */
			payResult() {
				this.showResult = false
			},
			/**
			 * 会员激活
			 */
			activateMember() {
				if (!this.memberCode) {
					this.utils.toast('请输入会员激活码')
					return
				}
				
				userApi.activateMember(this, this.memberCode).then(res => {
					if (res.code) {
						this.utils.alert('激活成功')
						
						// 清空并关闭弹窗
						this.memberCode = ''
						this.showActivate = false
						
						// 刷新会员信息
						this.ajax()
					} else {
						this.utils.alert(res.msg)
					}
				})
			},
			// 查看可用题库
			seeMemberCateNames(item) {
				this.currentMemberItem = item
				this.showMemberCateNames = true
			},
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}

	.about {

		&__wrap {
			position: relative;
			z-index: 1;
			margin: 20rpx 30rpx;
			// margin-top: -230rpx;
		}
	}

	.bg {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 420rpx;
		z-index: -2;

		&-img {
			width: 100%;
			height: 100%;
		}
	}

	.bg-div {
		z-index: -1;
		border-radius: 0 0 10% 10%;
	}

	.bg-black-graw {
		background-image: linear-gradient(45deg, #101528, #252c45);
		color: #ffffff;
	}

	.main-title {
		padding: 30rpx 30rpx;
		font-size: 34rpx;
		color: #1C1C1C;
	}

	.tip {
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: center;
		font-size: 24rpx;
		color: #A5A3A2;
	}

	.rmb {
		font-size: 26rpx;
		color: #E3BE83;
	}

	.hover {
		opacity: 0.7;
	}

	.nav {
		position: fixed;
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: space-between;
		width: 750rpx;
		height: 44px;
		padding: 0 30rpx;
		background-image: url('~@/static/member/nav-bg.png');
		background-size: 100%;
		z-index: 99;

		&-back {
			width: 20rpx;
			height: 40rpx;
		}

		&-title {
			color: #FFFFFF;
			font-size: 42rpx;
		}

		&-space {
			width: 70rpx;
			height: 40rpx;
		}
	}

	.info {
		position: relative;
		padding: 0 15rpx;
		width: 730rpx;
		height: 360rpx;

		&-bg {
			position: absolute;
			width: 730rpx;
			height: 360rpx;
			z-index: -1;
		}

		&-content {
			padding: 110rpx 50rpx 0 50rpx;
			margin-bottom: 50rpx;
			display: flex;
			flex-direction: row;
			align-items: center;
		}
		
		&-expire-time {
			padding: 0rpx 50rpx 0 50rpx;
			opacity:0.5;
			color: #fff;
		}

		&-avatar {
			margin-right: 30rpx;
			display: flex;
			flex-direction: row;
			align-items: center;
			justify-content: center;
			width: 85rpx;
			height: 85rpx;
			background-color: #fff;
			padding: 0rpx;
			border-radius: 50%;

			&-pic {
				width: 85rpx;
				height: 85rpx;
				border-radius: 50%;
			}
		}

		&-name {
			display: flex;
			flex-direction: column;
			color: #FFFFFF;
			font-size: 35rpx;
		}

		&-level {
			margin-top: 15rpx;
			font-size: 24rpx;
		}

		&-asset {
			// padding: 0 50rpx;
			display: flex;
			flex-direction: row;
			align-items: center;

			&-item {
				display: flex;
				flex-direction: column;
			}
		}
	}

	.iai {
		&-title {
			margin-bottom: 20rpx;
			// margin-right: 100rpx;
			font-size: 24rpx;
			color: #CFB386;
		}

		&-value {
			font-size: 35rpx;
			color: #FFFFFF;
		}
	}

	.member {
		position: relative;
		margin-bottom: 10rpx;
		display: flex;
		flex-direction: row;
		align-items: center;

		&-tag {
			position: absolute;
			top: -2rpx;
			left: -2rpx;
			width: 170rpx;
			height: 36rpx;
			display: flex;
			flex-direction: row;
			align-items: center;
			justify-content: center;
			background-image: url('~@/static/member/tag.png');
			background-size: 100%;

			&-text {
				font-size: 20rpx;
				color: #FFFFFF;
				text-align: center;
			}
		}

		&-item {
			position: relative;
			padding: 40rpx 0;
			margin-left: 15rpx;
			width: 200rpx;
			height: 280rpx;
			flex-shrink: 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			border: solid 2rpx #F2F2F3;
			border-radius: 12rpx;

			&-active {
				border-color: #EDD2A9;
				background-color: #FBF1E5;
			}

			&-duration {
				margin-bottom: 30rpx;
				font-size: 26rpx;
				color: #1C1C1C;
			}

			&-price {
				margin-bottom: 20rpx;
				display: flex;
				flex-direction: row;
				align-items: baseline;

				&-text {
					font-size: 48rpx;
					color: #E3BE83;
				}
			}

			&-des {
				font-size: 22rpx;
				color: #A5A3A2;
			}
		}
	}

	.button {
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: center;
		height: 85rpx;
		border-radius: 50rpx;
		background-image: linear-gradient(#EFCF9E, #E4BF85);

		&-text {
			font-size: 30rpx;
			color: #504b4b;
		}
	}

	.update {
		margin: 35rpx 30rpx 20rpx 30rpx;
	}

	.privilege {
		padding: 0 30rpx;
		margin-bottom: 40rpx;
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: space-around;
		flex-wrap: wrap;

		&-item {
			margin: 0 23rpx;
			margin-bottom: 25rpx;
			display: flex;
			flex-direction: column;
			align-items: center;

			&-pic {
				width: 80rpx;
				height: 80rpx;
				margin-bottom: 20rpx;
			}

			&-text {
				font-size: 24rpx;
				color: #383738;
			}
		}
	}

	.level {
		position: relative;
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: center;
		width: 750rpx;
		height: 466rpx;
		background-image: url('~@/static/member/vip-bg.png');
		background-size: 100%;

		&-rate {
			position: absolute;
			top: 43rpx;
			left: 33rpx;
			display: flex;
			flex-direction: row;
			align-items: center;

			&-text1 {
				margin-right: 20rpx;
				color: #1C1C1C;
				font-size: 34rpx;
			}

			&-text2 {
				font-size: 34rpx;
				color: #C07C07;
			}
		}

		&-info {
			// position: absolute;
			// top: 188rpx;
			// left: 350rpx;
			margin-top: 70rpx;
			display: flex;
			flex-direction: column;
			align-items: center;

			&-icon {
				margin-bottom: 20rpx;
				width: 48rpx;
				height: 38rpx;
			}

			&-text {
				margin-bottom: 20rpx;
				font-size: 26rpx;
				color: #292929;
			}

			&-integral {
				font-size: 40rpx;
				color: #C07C07;
			}
		}

		&-distance {
			position: absolute;
			left: 30rpx;
			right: 30rpx;
			bottom: 35rpx;
			display: flex;
			flex-direction: row;
			align-items: center;
			justify-content: space-between;

			&-text {
				font-size: 26rpx;
				color: #292929;
			}

			&-button {
				padding: 0 30rpx;
				height: 60rpx;
			}
		}
	}

	.course-shadow {
		box-shadow: 0rpx 0rpx 80rpx 0rpx rgba(0, 0, 0, 0.07);
	}

	.course-radius {
		border-radius: 15rpx;
	}

	/* 图标容器11 start */
	.icon11 {
		&__item {
			width: 30%;
			background-color: #FFFFFF;
			border-radius: 10rpx;
			padding: 30rpx;
			margin: 20rpx 10rpx;
			transform: scale(1);
			transition: transform 0.3s linear;
			transform-origin: center center;

			&--icon {
				width: 100rpx;
				height: 100rpx;
				font-size: 60rpx;
				border-radius: 50%;
				margin-bottom: 18rpx;
				position: relative;
				z-index: 1;

				&::after {
					content: " ";
					position: absolute;
					z-index: -1;
					width: 100%;
					height: 100%;
					left: 0;
					bottom: 0;
					border-radius: inherit;
					opacity: 1;
					transform: scale(1, 1);
					background-size: 100% 100%;
					// background-image: url(https://tnuiimage.tnkjapp.com/cool_bg_image/icon_bg.png);
				}
			}
		}
	}

	/* 图标容器15 start */
	.icon15 {
		&__item {
			width: 30%;
			background-color: #FFFFFF;
			border-radius: 10rpx;
			padding: 30rpx;
			margin: 20rpx 10rpx;
			transform: scale(1);
			transition: transform 0.3s linear;
			transform-origin: center center;

			&--icon {
				width: 100rpx;
				height: 100rpx;
				font-size: 60rpx;
				border-radius: 50%;
				margin-bottom: 18rpx;
				position: relative;
				z-index: 1;

				&::after {
					content: " ";
					position: absolute;
					z-index: -1;
					width: 100%;
					height: 100%;
					left: 0;
					bottom: 0;
					border-radius: inherit;
					opacity: 1;
					transform: scale(1, 1);
					background-size: 100% 100%;


				}
			}
		}
	}
</style>
