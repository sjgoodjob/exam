<template>
	<view>
		<tn-landscape
		  :show="showFollow"
		  :closeBtn="true"
		  closePosition="bottom"
		  :mask="true"
		  :maskCloseable="true"
		  @close="() => showFollow = false"
		>
			<view class="card_test">
				<view class="card_test__content">
					123123123
					<image :src="followImage" mode="widthFix"></image>
				</view>
				<view class="blob">123</view>
				<view class="blob">33</view>
				<view class="blob">22</view>
			</view>
			
		</tn-landscape>
	</view>
</template>

<script>
	export default {
		name:"kz-follow-mp",
		props: {
			// 显示关注
			showFollow: {
				type: Boolean,
				default: false
			},
		},
		data() {
			return {
				
			};
		}
	}
</script>

<style>
	.card_mp {
	  position: relative;
	  width: 190px;
	  height: 254px;
	  background: lightgrey;
	  box-shadow: #d11bff42 0 15px 40px -5px;
	  z-index: 1;
	  border-radius: 21px;
	  overflow: hidden;
	}
	
	.card_mp__content {
	  background: linear-gradient(rgba(255, 255, 255, 0.473), rgba(150, 150, 150, 0.25));
	  z-index: 1;
	  backdrop-filter: blur(20px);
	  -webkit-backdrop-filter: blur(20px);
	  position: absolute;
	  left: 0;
	  top: 0;
	  width: 100%;
	  height: 100%;
	  border-radius: 21px;
	}
	
	.card_mp .blob {
	  position: absolute;
	  z-index: -1;
	  border-radius: 5em;
	  width: 200px;
	  height: 200px;
	}
	
	.card_mp .blob:nth-child(2) {
	  left: -50px;
	  top: -90px;
	  background: #ff930f;
	}
	
	.card_mp .blob:nth-child(3) {
	  left: 110px;
	  top: -20px;
	  z-index: -1;
	  background: #bf0fff;
	}
	
	.card_mp .blob:nth-child(4) {
	  left: -40px;
	  top: 100px;
	  background: #ff1b6b;
	}
	
	.card_mp .blob:nth-child(5) {
	  left: 100px;
	  top: 180px;
	  background: #0061ff;
	}
</style>