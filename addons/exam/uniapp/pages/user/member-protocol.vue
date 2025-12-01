<template>
	<view style="height: 100%;overflow-y: auto;">
		<view class="about-shadow tn-margin tn-padding tn-border-solid-bottom">
			<view class="tn-text-bold tn-text-xl tn-text-center">会员协议</view>
			
			<view class="tn-margin-top tn-text-content">
				<rich-text :nodes="contents"></rich-text>
			</view>
		</view>
		
		<view style="width:90%; margin: 10rpx auto;" class="padding-bottom-xl">
			<tui-button shape="circle" shadow bold preventClick @click="goBack">我知道啦，返回吧</tui-button>
		</view>
	</view>
</template>

<script>
	import userApi from "@/common/api/user.js"
	
	export default {
		data() {
			return {
				contents: ''
			}
		},
		onLoad() {
			this.ajax()
		},
		methods: {
			ajax() {
				userApi.getMemberConfigs().then(res => {
					console.log('getMemberConfigs', res)
					this.contents = res.data.member_protocol
				})
			},
			goBack() {
				this.utils.goback()
			}
		}
	}
</script>

<style>
	page {
		background-color: #fff;
	}
</style>
