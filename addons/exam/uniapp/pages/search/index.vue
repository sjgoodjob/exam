<template>
	<view>
		<!-- #ifdef H5 -->
		<!-- 顶部自定义导航 -->
		<tn-nav-bar fixed :bottomShadow="false" backTitle=" ">
		  <view class="">
		    <text class="tn-text-lg">题目搜索</text>
		    <text class="tn-text-xl tn-padding-left-sm tn-icon-group-circle"></text>
		  </view>
		</tn-nav-bar>
		<!-- #endif -->
		
		<view class="cu-bar search bg-white">
			<view class="search-form round">
				<text class="cuIcon-search"></text>
				<input v-model="keyword" @input="inputSearch" @confirm="search()" :adjust-position="false" type="text"
					placeholder="请输入题目关键词进行搜索" confirm-type="search"></input>
			</view>
			<view class="action">
				<button class="cu-btn bg-primary shadow-blur round" @click="search()">搜索</button>
			</view>
		</view>

		<view class="question-list">
			<view class="question-card" v-for="(item, index) in list" :key="index">
				<tui-card :title="{text: item.cates.name, size: 30, color: '#7A7A7A'}"
					:tag="{text: item.kind_text, size: 24}" :header="item.title">
					<template v-slot:body>
						<view class="m-lr-20">
							<view class="m-t-20" v-if="item.is_material_child && item.material_question_id && item.material_parent">
								<text>材料题干：</text>
								<!-- <rich-text :nodes="formatRichtext(item.material_parent.title)" style="font-size: 36rpx; font-weight: bold;"></rich-text> -->
								<mp-html :content="formatRichtext(item.material_parent.title)" style="font-size: 36rpx; font-weight: bold;"></mp-html>
							</view>
							
							<view class="m-t-20">
								<text>{{item.kind_text}}：</text>
								<!-- <rich-text :nodes="formatRichtext(item.title)" style="font-size: 36rpx; font-weight: bold;"></rich-text> -->
								<mp-html :content="formatRichtext(item.title)" style="font-size: 36rpx; font-weight: bold;"></mp-html>
							</view>
							<view class="options">
								<view v-if="item.kind == 'FILL'" v-html="getFillAnswer(item)"></view>
								
								<view v-for="(option, i) in item.options_json" 
									:key="i"
									:class="item.answer.indexOf(option.key) > -1 ? ['text-green'] : []"
									v-else
								>
									<mp-html :content="option.key+':'+option.value"></mp-html>
									<!-- {{ option.key }}:<mp-html :content="option.value"></mp-html> -->
									<!-- {{ option.value }} -->
									<block v-if="item.options_img && item.options_img[i]">
										<image :src="item.options_img[i].value" @click="previewOptionImage(item.options_img[i].value)" class="option-img"></image>
									</block>
									
								</view>
							</view>
						</view>
					</template>
					<template v-slot:footer>
						<text class="margin-left-sm">解析：</text>
						<view class="explain" v-if="item.explain">
							<!-- <rich-text :nodes="formatRichtext(item.explain)" style="border-top: solid 1px #ccc;width: 100%;display: block;" v-if="item.explain"></rich-text> -->
							<mp-html :content="formatRichtext(item.explain)" style="border-top: solid 1px #ccc;width: 100%;display: block;" v-if="item.explain"></mp-html>
						</view>
						<view class="explain" v-else>无</view>
					</template>
				</tui-card>

			</view>

			<!-- 加载状态条 -->
			<view class="cu-load bg-grey" :class="loadFlag" v-show="has_more || loadFlag == 'over'"></view>
		</view>

		<tui-loading v-if="showLoading"></tui-loading>
		<tui-no-data imgUrl="/static/img/img_noorder_3x.png" v-if="showNodata">暂无数据</tui-no-data>

		<!--流量主组件-->
		<!-- #ifdef MP-WEIXIN -->
		<view :class="showNodata ? ['margin-top-100'] : ['margin-top-10']" v-show="false">
			<kz-ad ref="adSearch" kind="BANNER" :config="ad" field="question_search_banner"></kz-ad>
			<kz-ad ref="adSearch" kind="VIDEO" :config="ad" field="question_search_video"></kz-ad>
			<kz-ad ref="adSearch" kind="VIDEO_PATCH" :config="ad" field="question_search_video_patch"></kz-ad>
		</view>
		<!-- #endif -->

		<login ref="login"></login>
		<tabbar></tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				showLoading: false,
				showNodata: true,
				keyword: '',
				list: [],
				loadFlag: 'loading',
				current_page: 1,
				has_more: false,
				ad: {}
			}
		},
		computed: {
			// 显示填空题答案
			getFillAnswer(item) {
				return function(item) {
					let answer = ''
					if (item.answer && item.kind == 'FILL') {
						try {
							answer = '';
							for (let i = 0; i < item.answer.length; i++) {
								answer += '填空位' + (i + 1) + '：' + item.answer[i].answers.join('、') + '<br>';
							}
						} catch (e) {
							console.log('answer', answer, e);
							return value;
						}
					}
					return answer
				}
			},
		},
		onLoad(e) {
			this.ad = this.utils.getData('ad')
			console.log('ad data', this.ad)
			// 插屏广告
			if (this.ad.question_search_cp_open == 1) {
				this.adUtils.interstitial.load(this.ad.question_search_cp)
				this.adUtils.interstitial.show()
			}
			if (e.keyword) {
				this.keyword = e.keyword
				this.search()
			}
		},
		async onReachBottom() {
			console.log("onReachBottom")
			if (this.has_more) {
				this.current_page++
				this.search()
			}
		},
		methods: {
			search() {
				if (!this.keyword) {
					uni.showToast({
						title: '请输入关键词',
						icon: 'none'
					})
					return
				}

				this.loadFlag = 'loading'
				this.has_more = true
				this.http('question/search', {
					keyword: this.keyword,
					page: this.current_page
				}, 'post').then(res => {
					console.log('search res', res)
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
						return
					} else {
						this.list = res.data.list.data //this.list.concat(res.data.list.data)

						this.current_page = res.data.list.current_page
						this.has_more = res.data.list.has_more
						this.loadFlag = !this.has_more ? 'over' : ''
						this.showNodata = !this.list.length
					}
				})
			},
			inputSearch(e) {
				console.log('input e', e)
				this.keyword = e.detail.value
			},
			// 试题标题
			formatRichtext(text) {
				if (text) {
					return this.utils.formatRichText(text)
				}
				return '无'
			},
			// 选项图片yulan
			previewOptionImage(src) {
				if (!src) {
					return
				}
				
				uni.previewImage({
					current: 0,
					urls: [src]
				});
			}
			
		}
	}
</script>

<style>
	.question-list {
		padding-bottom: 20rpx;
	}

	.question-card {
		margin: 20rpx 0;
	}

	.options {
		margin: 20rpx 0;
		line-height: 50rpx;
	}

	.explain {
		margin: 20rpx 0;
		padding: 15rpx;
		padding-top: 0rpx;
	}

	.margin-top-10 {
		margin-top: 10px;
	}

	.margin-top-100 {
		margin-top: 100%;
	}
	
	.option-img {
		width: 100rpx;
		height: 100rpx;
	}
</style>
