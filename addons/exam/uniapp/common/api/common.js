import utils from "../js/utils.js";

/**
 * 公共接口
 */
module.exports = {

	/**
	 * 上传文件
	 * @returns {Promise<*>}
	 */
	uploadFile(handler, filePath, data = {}, fileType = 'image') {
		
		uni.uploadFile({
			url: fun.SITE + 'api/wx/wxpayApplyImage',
			header: {
				'token': uni.getStorageSync('token'),
				"Content-Type": "multipart/form-data"
			},
			fileType: fileType,
			filePath: filePath,
			formData: data,
			name: 'file',
			success: (res) => {
				if (res.statusCode == 413) {
					this.modalDo(true, '图片太大了，请压缩后再重新上传')
					return
				}

				let data = JSON.parse(res.data)
				if (data.status == 1) {
					let result = data.result
					this.ocrResult(type, result, lists)
					return
				} else {
					this.modalDo(true, '识别有误，提交可能影响审核，建议重新上传更清晰的图片')
					return
				}
			},
			fail: (res) => {
				console.log('fail res', res)
			},
			complete: (uploadFileRes) => {
				this.showPopup = false
			},
		})
	},
	
	/**
	 * 获取列表
	 * @returns {Promise<*>}
	 */
	getFriendAppsList(handler, data = {}) {
		return utils.http(handler, 'common/friendApps', data)
	},
}
