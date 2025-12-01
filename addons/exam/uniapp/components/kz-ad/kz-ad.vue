<template>
	<view>
		<view v-if="kind && config_data && config_data[field] && config_data[field + '_open'] == 1">
			<!-- banner广告 -->
			<view v-if="kind == 'BANNER'">
				<ad :unit-id="config_data[field]"></ad>
			</view>

			<!-- 视频广告 -->
			<view v-else-if="kind == 'VIDEO'">
				<ad :unit-id="config_data[field]" ad-type="video" ad-theme="white"></ad>
			</view>

			<!-- 视频贴片广告 -->
			<view v-else-if="kind == 'VIDEO_PATCH'">
				<video :unit-id="config_data[field]" style="width: 100%"></video>
			</view>

			<!-- 其他忽略 -->
			<view v-else></view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "kz-ad",
		props: {
			/**
			 * 广告种类
			 * BANNER：banner广告
			 * VIDEO：视频广告
			 * VIDEO_PATCH：视频贴片广告
			 */
			kind: {
				type: String,
				default: 'BANNER'
			},
			// 广告配置集合
			config: {
				type: Object,
				default: () => {}
			},
			// 配置字段
			field: {
				type: String,
				default: ''
			},
		},
		data() {
			return {
				config_data: [],
			};
		},
		watch: {
			/**
			 * 监听广告配置
			 * @param val
			 */
			config(val) {
				console.log('kz-ad watch val', val)
				this.config_data = val
			}
		},
		methods: {
			/**
			 * 手动刷新广告配置
			 * @param val
			 */
			refresh(val) {
				this.config_data = val
				console.log('kz-ad refresh', val, this.config_data)
				// this.$forceUpdate()
			}
		}
	}
</script>

<style>

</style>
