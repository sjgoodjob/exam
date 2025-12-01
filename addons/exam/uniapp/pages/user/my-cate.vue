<template>
	<view>
		<view class="card-view">
			<image src="../../static/img/train-banner2.png" mode="aspectFill" style="width: 100%;"></image>
		</view>

		<view class="card-view">
			<!-- <tui-divider width="80%" gradual>选择要常用的题库</tui-divider> -->
			
			<!-- <view class="margin">
				<tn-radio-group activeColor="#5677fc" v-model="kind" @change="changeKind">
					<tn-radio name="QUESTION">试题题库</tn-radio>
					<tn-radio name="PAPER">试卷题库</tn-radio>
				</tn-radio-group>
			</view> -->
			
			<tui-cascade-selection height="200px" :itemList="cateList" @complete="complete" text="请选择题库" :defaultItemList="defaultCateList"></tui-cascade-selection>
		</view>

		<view style="width:90%; margin: 10rpx auto;" class="padding-bottom-xl">
			<tui-button shape="circle" shadow bold preventClick @click="save">保存</tui-button>
		</view>
		
		<login ref="login" v-on:succ="getCate"></login>
	</view>
</template>

<script>
	import userApi from "@/common/api/user.js"
	
	export default {
		data() {
			return {
				page: '',
				cateList: [],
				cateId: 0,
				cateName: '',
				// questionCount: 0,
				kind: 'QUESTION',
				defaultCateList: [],
				selectItem: null,
				selectCount: 0,
				subjectId: 0,
			}
		},
		onLoad(e) {
			this.page = e?.page
			this.subjectId = this.utils.getSubjectId()
			this.getCate('init')
		},
		methods: {
			// 获取题库
			getCate(type = '') {
				this.http('cate/getThree', {
					kind: this.kind,
					subject_id: this.subjectId
				}).then(res => {
					this.cateList = res.data
					
					// 模拟题库点击事件
					if (this.cateId) {
						this.complete({
							value: this.cateId,
							result: [
								{
									text: this.cateName
								}
							]
						})
					}
					
					if (type == 'init') {
						let defaultCate = this.utils.getData('default_cate-' + this.kind)
						console.log('defaultCate', defaultCate)
						if (defaultCate) {
							let lastCate = [...defaultCate.result].pop()
							console.log('lastCate', lastCate)
							
							let hasCate = false
							for (let j = 0; j < this.cateList.length; j++) {
								let cate = this.cateList[j]
								if (hasCate || cate.value == lastCate.value) {
									hasCate = true
									break
								}
								
								if (cate.children && cate.children.length > 0) {
									for (let k = 0; k < cate.children.length; k++) {
										let child_cate = cate.children[k]
										if (child_cate.value == lastCate.value) {
											hasCate = true
											break
										}
									}
								}
							}
							
							if (hasCate) {
								let defaultCateList = []
								for (var i = 0; i < defaultCate.result.length; i++) {
									defaultCateList.push(defaultCate.result[i].text)
								}
								this.defaultCateList = defaultCateList
								
								this.cateId = lastCate.value
								this.cateName = lastCate.text
								
								this.complete({
									value: this.cateId,
									result: [
										{
											text: this.cateName
										}
									]
								})
								
								this.selectCount = 0
							}
						}
					}
				})
			},
			changeKind(e) {
				console.log('changeKind', e, this.kind)
				this.getCate('init')
			},
			// 选择题库
			complete(e) {
				console.log('您选择的数据为：', e);

				this.cateId = e.value
				this.cateName = e.result[e.result.length - 1].text
				this.selectItem = e
				
				this.selectCount++;
			},
			// 保存我常用的题库
			save() {
				if (!this.cateId) {
					this.utils.toast('请先选择题库')
					return
				}
				
				if (this.selectCount > 0) {
					this.utils.setData('default_cate-' + this.kind, this.selectItem)
					this.utils.alert('保存成功', () => {
						this.utils.goback()
					})
				} else {
					this.utils.alert('保存成功', () => {
						this.utils.goback()
					})
				}
				
				
				// userApi.saveMyCate(this, {cate_id: this.cateId}).then(res => {
				// 	console.log('saveMyCate res', res)
				// 	if (res.code == 1) {
				// 		this.utils.alert('保存成功')
				// 		setTimeout(() => {
				// 			this.utils.goback()
				// 		}, 1000)
				// 	} else {
				// 		this.utils.alert(res.msg)
				// 	}
				// })
			}
		}
	}
</script>

<style>
	.tn-radio__label {
		color: #333 !important;
		font-size: 15px !important;
	}
</style>
