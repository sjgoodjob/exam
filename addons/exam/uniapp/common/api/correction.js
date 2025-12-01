import utils from "../js/utils.js";

/**
 * 纠错相关接口
 */
module.exports = {

	/**
	 * 纠错类型
	 * @returns {Promise<*>}
	 */
	getCorrectionTypes(handler, data = {}) {
		return utils.http(handler, 'correction/types', data)
	},

	/**
	 * 提交纠错
	 * @returns {Promise<*>}
	 */
	submitCorrection(handler, data = {}) {
		return utils.http(handler, 'correction/submit', data)
	},

	/**
	 * 获取纠错反馈列表
	 * @returns {Promise<*>}
	 */
	getCorrectionList(handler, data = {}) {
		return utils.http(handler, 'correction/list', data)
	},
}
