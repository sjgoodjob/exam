import utils from "../js/utils.js";

/**
 * 证书相关接口
 */
module.exports = {

	/**
	 * 获取列表
	 * @returns {Promise<*>}
	 */
	getCertList(handler, data = {}) {
		return utils.http(handler, 'cert/index', data)
	},

	/**
	 * 获取详情
	 * @returns {Promise<*>}
	 */
	getCertDetail(handler, data = {}) {
		return utils.http(handler, 'cert/detail', data)
	},
}
