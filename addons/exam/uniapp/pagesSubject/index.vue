<template>
	<view class="page-b">

		<!-- 顶部自定义导航 -->
		<!-- <tn-nav-bar :isBack="true" :bottomShadow="true" backgroundColor="none">
			<view class="custom-nav tn-flex tn-flex-col-center tn-flex-row-left">
				<view class="custom-nav__back">
					<text class="tn-text-bold tn-text-xl tn-color-black">图片分类</text>
				</view>
			</view>
		</tn-nav-bar> -->
		
		<view class="" v-if="isEmpty">
			<tui-no-data imgUrl="/static/img/img_noorder_3x.png">暂无数据</tui-no-data>
		</view>

		<view class="tn-classify__wrap" v-show="!isEmpty">

			<!-- 搜索框  style="padding-bottom: 20rpx;" -->
			<view class="tn-classify__search--wrap">
				<!-- <view class="tn-color-gray--dark"
					style="margin: 20rpx 30rpx 0rpx 30rpx;border-radius: 100rpx;padding-left: 6rpx;background-color: rgba(248, 247, 248, 0.9);"
					@click="tn('/pageA/search/search')">
					<tn-notice-bar :list="searlist" mode="vertical" leftIconName="search"
						:duration="6000"></tn-notice-bar>
				</view> -->
			</view>

			<!-- 分类列表和内容 -->
			<view class="tn-classify__container">
				<view class="tn-classify__container__wrap tn-flex tn-flex-nowrap tn-flex-row-around tn-bg-white">
					<!-- 左边容器 -->
					<scroll-view class="tn-classify__left-box left-width" :scroll-top="leftScrollViewTop" scroll-y
						scroll-with-animation :style="{height: scrollViewHeight + 'px'}">
						<view v-for="(item, index) in tabbar" :key="index" :id="`tabbar_item_${index}`"
							class="tn-classify__tabbar__item tn-flex tn-flex-col-center tn-flex-row-center"
							:class="[tabbarItemClass(index)]" @tap.stop="clickClassifyNav(index)">
							<view class="tn-classify__tabbar__item__title">{{ item.name }}</view>
						</view>
					</scroll-view>

					<!-- 右边容器 -->
					<scroll-view class="tn-classify__right-box" scroll-y :scroll-top="rightScrollViewTop"
						:style="{height: scrollViewHeight + 'px'}">
						<block v-if="classifyContent.subClassify && classifyContent.subClassify.length > 0">
							<view class="tn-classify__content" @click="tn('/preferredPages/product')">

								<!-- 推荐壁纸轮播图，有需要直接显示出来即可 -->
								<!-- <view class="tn-classify__content__recomm">
                  <tn-swiper v-if="classifyContent.recommendProduct.length > 0" class="tn-classify__content__recomm__swiper" :list="classifyContent.recommendProduct" :height="100" :effect3d="true" mode=""></tn-swiper>
                </view> -->

								<!-- 分类内容子栏目 -->
								<view v-for="(item,index) in classifyContent.subClassify" :key="index" class="tn-classify__content__sub-classify">
									<!-- <view class="tn-classify__content__sub-classify--title tn-text-lg tn-padding-top-sm">{{ item.title }}</view> -->

									<view class="tn-classify__content__sub-classify__content">
										<view @click="clickSubject(sub_item)" v-for="(sub_item,sub_index) in item.classify" :key="sub_index" class="tn-classify__content__sub-classify__content__item tn-flex tn-flex-col-center tn-flex-row-between">
											<view class="justify-content-item">{{sub_item.name}}</view>
											<view class="justify-content-item tn-text-right text-primary" v-if="sub_item.id == userSubjectId2"><text class="tn-icon-check"></text></view>
											<!-- <view
												class="tn-classify__content__sub-classify__content__image tn-flex tn-flex-col-center tn-flex-row-center"
												@click="tn('/pageA/photo/photo')">
												<image :src="sub_item.image" mode="aspectFill"></image>
											</view> -->
											
											<!-- 标题，有需要可以显示出来 -->
											<!-- <view class="tn-classify__content__sub-classify__content__title tn-margin-top-xs tn-margin-bottom-sm">{{ sub_item.name }}</view> -->
										</view>
									</view>
								</view>
							</view>

						</block>
					</scroll-view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import subjectApi from "@/common/api/subject.js"
	
	export default {
		name: 'templateShopClassify',
		data() {
			return {
				// searlist: [
				// 	'手机屏保壁纸'
				// ],
				// 侧边栏分类数据
				tabbar: [],
				// 分类里面的内容信息
				classifyContent: {
					// 推荐商品
					/* recommendProduct: [{
							image: 'https://cdn.nlark.com/yuque/0/2022/jpeg/280373/1664015047023-assets/web-upload/147b0b7f-8253-4b92-bc1d-e28db7f54096.jpeg',
							title: '壁纸1'
						},
					], */
					/* // 子栏目
					subClassifyTabbar: [],
					// 显示子栏目分类选项
					showSubClassifyTabbar: false, */
					// 每个子栏目下的内容
					subClassify: [
						{
							title: '推荐',
							classify: [
								{
									image: '',
									name: ''
								},
							]
						},
					]
				},
				// 分类菜单item的信息
				tabbarItemInfo: [],
				// scrollView的top值
				scrollViewBasicTop: 0,
				// scrollView的高度
				scrollViewHeight: 0,
				// 左边scrollView的滚动高度
				leftScrollViewTop: 0,
				// 右边scrollView的滚动高度
				rightScrollViewTop: 0,
				// 当前选中的tabbar序号
				currentTabbarIndex: 0,
				// 科目列表
				subjectList: [],
				// 用户一级科目ID
				userSubjectId1: 1,
				userSubjectIndex1: 0,
				// 用户二级科目ID
				userSubjectId2: 8,
				userSubjectIndex2: 5,
				// 类型
				type: '',
				isEmpty: false,
			}
		},
		computed: {
			tabbarItemClass() {
				return index => {
					if (index === this.currentTabbarIndex) {
						return 'tn-classify__tabbar__item--active tn-bg-white'
					} else {
						let clazz = 'tn-bg-gray--light'
						if (this.currentTabbarIndex > 0 && index === this.currentTabbarIndex - 1) {
							clazz += ' tn-classify__tabbar__item--active--prev'
						}
						if (this.currentTabbarIndex < this.tabbar.length && index === this.currentTabbarIndex + 1) {
							clazz += ' tn-classify__tabbar__item--active--next'
						}
						return clazz
					}
				}
			}
		},
		created(e) {
			
		},
		onLoad(e) {
			console.log('subject e', e)
			if (e?.type) {
				this.type = e.type
				if (this.type == 'onlySubject') {
					uni.setNavigationBarTitle({
						title: '已绑定科目'
					});
				}
			}
			
			this.getUserSubject()
			this.ajax()
		},
		mounted() {
			// this.$nextTick(() => {
			// 	this.getScrollViewInfo()
			// 	this.getTabbarItemRect()
			// })
		},
		methods: {
			getUserSubject() {
				let userSubject = uni.getStorageSync('user_subject')
				if (userSubject) {
					console.log()
					this.userSubjectId1 = userSubject.subject1.id
					this.userSubjectId2 = userSubject.subject2.id
				}
			},
			ajax() {
				let callback = (res) => {
					console.log('res', res)
					if (res && res.code == 1) {
						if (res.data.length > 0) {
							this.isEmpty = false
							
							// this.subjectData = res.data
							this.tabbar = res.data
							
							for (let i = 0; i < res.data.length; i++) {
								if (res.data[i].id == this.userSubjectId1) {
									this.userSubjectIndex1 = i
									
									for (let j = 0; j < res.data[i].children.length; j++) {
										let child = res.data[i].children
										if (child[j].id == this.userSubjectId2) {
											this.userSubjectIndex2 = j
											break
										}
									}
									break
								}
							}
							
							setTimeout(() => {
								this.getScrollViewInfo()
								this.getTabbarItemRect()
								
								this.clickClassifyNav(this.userSubjectIndex1)
								this.switchClassifyContent()
							}, 100)
						} else {
							this.isEmpty = true
						}
					} else {
						this.utils.alert(res?.msg)
						return
					}
				}
				
				if (this.type == 'onlySubject') {
					subjectApi.getOnlySubjectList(this, {}).then(res => {
						callback(res)
					})
				} else {
					subjectApi.getSubjectList(this, {}).then(res => {
						callback(res)
					})
				}
			},
			
			// 跳转
			tn(e) {
				uni.navigateTo({
					url: e,
				});
			},

			// 获取scrollView的高度信息
			getScrollViewInfo() {
				// 获取搜索栏的bottom信息，然后用整个屏幕的可用高度减去bottom的值即可获取scrollView的高度(防止双重滚动)
				this._tGetRect('.tn-classify__search--wrap').then((rect) => {
					// 如果获取失败重新获取
					if (!rect) {
						setTimeout(() => {
							this.getScrollViewInfo()
						}, 10)
						return
					}
					// 获取当前屏幕的可用高度
					const systemInfo = uni.getSystemInfoSync()
					this.scrollViewBasicTop = systemInfo.statusBarHeight + rect.bottom + uni.upx2px(10)
					this.scrollViewHeight = systemInfo.safeArea.height + systemInfo.statusBarHeight - rect.bottom -
						uni.upx2px(10) - 49
				})
			},
			// 获取分类菜单每个item的信息
			getTabbarItemRect() {
				let view = uni.createSelectorQuery().in(this)
				for (let i = 0; i < this.tabbar.length; i++) {
					view.select('#tabbar_item_' + i).boundingClientRect()
				}
				view.exec(res => {
					if (!res.length) {
						setTimeout(() => {
							this.getTabbarItemRect()
						}, 10)
						return
					}

					// 将每个分类item的相关信息
					res.map((item) => {
						this.tabbarItemInfo.push({
							top: item.top,
							height: item.height
						})
					})
				})
			},
			// 点击了分类导航
			clickClassifyNav(index) {
				if (this.currentTabbarIndex === index) {
					return
				}
				this.currentTabbarIndex = index

				this.handleLeftScrollView(index)
				this.switchClassifyContent()
			},
			// 点击分类后，处理scrollView滚动到居中位置
			handleLeftScrollView(index) {
				if (!this.tabbarItemInfo[index]) {
					return
				}
				console.log('handleLeftScrollView', this.tabbarItemInfo, this.tabbarItemInfo[index])
				
				const tabbarItemTop = this.tabbarItemInfo[index].top - this.scrollViewBasicTop
				if (tabbarItemTop > this.scrollViewHeight / 2) {
					this.leftScrollViewTop = tabbarItemTop - (this.scrollViewHeight / 2) + this.tabbarItemInfo[index]
						.height
				} else {
					this.leftScrollViewTop = 0
				}
			},
			// 切换对应分类的数据
			switchClassifyContent() {
				this.rightScrollViewTop = 1
				this.$nextTick(() => {
					this.rightScrollViewTop = 0
				})
				// this.classifyContent.subClassify[0].title = this.tabbar[this.currentTabbarIndex]
				
				this.classifyContent.subClassify[0].title = this.tabbar[this.currentTabbarIndex].name
				this.classifyContent.subClassify[0].classify = this.tabbar[this.currentTabbarIndex].children
			},
			clickSubject(subItem) {
				console.log('clickSubject', subItem, this.tabbar[this.currentTabbarIndex])
				uni.setStorageSync('user_subject', {
					subject1: {
						id: this.tabbar[this.currentTabbarIndex].id,
						name: this.tabbar[this.currentTabbarIndex].name,
					},
					subject2: {
						id: subItem.id,
						name: subItem.name,
					}
				})
				
				// 通知科目选择事件
				uni.$emit('event_subject_choose', {subject_id: subItem.id})
				
				if (this.type == 'onlySubject') {
					this.tn('/pages/index/index')
				} else {
					this.utils.goback()
				}
			}
		}
	}
</script>

<style lang="scss" >
	page {
		background-color: #fff;
	}
	
	.page-b {
		max-height: 100vh;
	}

	/* 自定义导航栏内容 start */
	.custom-nav {
		height: 100%;

		&__back {
			margin: auto 30rpx;
			margin-right: 10rpx;
			flex-basis: 5%;
			width: 100rpx;
			position: absolute;
		}
	}


	.left-width {
		flex-basis: 28%;
	}

	/* 自定义导航栏内容 end */
	.tn-classify {

		/* 搜索栏 start */
		&__search {
			&--wrap {}

			&__box {
				flex: 1;
				text-align: center;
				padding: 20rpx 30rpx;
				margin: 0 30rpx;
				border-radius: 60rpx;
				font-size: 30rpx;
			}

			&__icon {
				padding-right: 10rpx;
			}

			&__text {
				padding-right: 10rpx;
			}
		}

		/* 搜索栏 end */

		/* 分类列表和内容 strat */
		&__container {}

		&__left-box {}

		&__right-box {
			background-color: #FFFFFF;
		}

		/* 分类列表和内容 end */

		/* 侧边导航 start */
		&__tabbar {
			&__item {
				height: 110rpx;

				&:first-child {
					border-top-right-radius: 0rpx;
				}

				&:last-child {
					border-bottom-right-radius: 0rpx;
				}

				&--active {
					background-color: #FFFFFF;
					position: relative;
					// font-weight: bold;
					color: #3668FC;

					&::before {
						content: '';
						position: absolute;
						width: 12rpx;
						height: 40rpx;
						top: 50%;
						left: 0;
						background-color: #3668FC;
						border-radius: 12rpx;
						transform: translateY(-50%) translateX(-50%);
					}

					&--prev {
						border-bottom-right-radius: 26rpx;
					}

					&--next {
						border-top-right-radius: 26rpx;
					}
				}
			}
		}

		/* 侧边导航 end */

		/* 分类内容 start */
		&__content {
			margin: 18rpx;

			/* 推荐商品 start */
			&__recomm {
				margin-bottom: 40rpx;

				&__swiper {}
			}

			/* 推荐商品 end */

			/* 子栏目 start */
			&__sub-classify {
				margin-bottom: 20rpx;
				padding-bottom: 40rpx;

				&--title {
					font-weight: bold;
					margin-bottom: 18rpx;
				}

				&__content {

					&__item {
						// width: 50%;
						margin-right: 20rpx;
						padding: 20rpx;
						padding-bottom: 40rpx;
					}

					&__image {
						background-color: rgba(188, 188, 188, 0.1);
						border-radius: 10rpx;
						margin: 10rpx 10rpx 0 10rpx;
						margin-left: 0;
						width: 100%;
						height: 340rpx;
						overflow: hidden;
						border: 1rpx solid rgba(0, 0, 0, 0.02);

						image {
							width: 100%;
							height: 340rpx;
						}
					}

					&__title {
						margin-right: 10rpx;
					}
				}
			}

			/* 子栏目 end */
		}

		/* 分类内容 end */
	}
</style>