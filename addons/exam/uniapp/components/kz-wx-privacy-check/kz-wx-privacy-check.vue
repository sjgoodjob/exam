<template>
	<tn-modal v-model="showPrivacy" :custom="true" title="隐私保护指引" :maskCloseable="false">
		<view class="content">
			<view class="title">隐私保护指引</view>
			<view class="des">
				在使用当前小程序服务之前，请仔细阅读
				<text class="link text-primary" @tap="handleOpenPrivacyContract">{{privacyContractName}}</text>
				。如你同意{{privacyContractName}}，请点击“同意”开始使用。
			</view>
			<view class="btns">
				<button class="item reject" @tap="handleDisagree">拒绝</button>
				<button id="agree-btn" class="item agree bg-primary" open-type="agreePrivacyAuthorization"
					@agreeprivacyauthorization="handleAgreePrivacyAuthorization">同意</button>
			</view>
		</view>
	</tn-modal>
</template>

<script>
	export default {
		name: 'KzWxPrivacyCheck',
		data() {
			return {
				privacyContractName: '',
				showPrivacy: false,
				resolvePrivacyAuthorization: null,
			};
		},
		mounted() {
			// #ifdef MP-WEIXIN
			this.getPrivacySetting()
			if (wx.onNeedPrivacyAuthorization) {
				wx.onNeedPrivacyAuthorization(resolve => {
					console.log('触发 onNeedPrivacyAuthorization', resolve)
					this.showPrivacy = true
					this.resolvePrivacyAuthorization = resolve
				})
			}
			// #endif
		},

		methods: {
			getPrivacySetting(type) {
				return new Promise((resolve, reject) => {
					// #ifdef APP-PLUS
					resolve(true)
					// #endif

					// #ifdef MP-WEIXIN
					if (wx.getPrivacySetting) {
						wx.getPrivacySetting({
							success: res => {
								// 返回结果为: res = { needAuthorization: true/false, privacyContractName: '《xxx隐私保护指引》' }
								console.log(res)
								if (res.needAuthorization) {
									// 需要弹出隐私协议
									if (type == "showPrivacy") {
										this.showPrivacy = true
									}
									this.privacyContractName = res.privacyContractName
									resolve(false)
								} else {
									// 用户已经同意过隐私协议，所以不需要再弹出隐私协议，也能调用已声明过的隐私接口
									resolve(true)
								}
							}
						})
					}else{
						resolve(true)
					}
					
					// #endif
				})
			},

			async handleAgreePrivacyAuthorization() {
				// 用户点击了同意，之后所有已声明过的隐私接口和组件都可以调用了
				this.showPrivacy = false
				if (typeof this.resolvePrivacyAuthorization === 'function') {
					this.resolvePrivacyAuthorization({
						buttonId: 'agree-btn',
						event: 'agree'
					})
				}
			},

			handleDisagree() {
				this.showPrivacy = false
				if (typeof this.resolvePrivacyAuthorization === 'function') {
					this.resolvePrivacyAuthorization({
						event: 'disagree'
					})
				}
			},

			handleOpenPrivacyContract() {
				// 打开隐私协议页面
				wx.openPrivacyContract({
					success: () => {}, // 打开成功
					fail: () => {}, // 打开失败
					complete: () => {}
				})
			}
		},
	};
</script>

<style>

	.content .title {
		text-align: center;
		color: #333;
		font-weight: bold;
		font-size: 32rpx;
	}

	.content .des {
		font-size: 26rpx;
		color: #666;
		margin-top: 40rpx;
		text-align: justify;
		line-height: 1.6;
	}

	.content .des .link {
		color: #07c160;
		text-decoration: underline;
	}

	.btns {
		margin-top: 48rpx;
		display: flex;
	}

	.btns .item {
		width: 180rpx;
		height: 70rpx;
		overflow: visible;
		display: flex;
		align-items: center;
		margin: 0 12px;
		justify-content: center;
		box-sizing: border-box;
		border: none !important;
		font-size: 26rpx;
	}

	.btns .reject {
		background: #f4f4f5;
		color: #909399;
	}
</style>