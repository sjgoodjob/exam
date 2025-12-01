import utils from "../js/utils.js";

/**
 * 公告相关接口
 */
module.exports = {

	/**
	 * 获取列表
	 * @returns {Promise<*>}
	 */
	getNoticeList(handler, data = {}) {
		return utils.http(handler, 'notice/index', data)
	},

	/**
	 * 获取详情
	 * @returns {Promise<*>}
	 */
	getNoticeDetail(handler, data = {}) {
		return utils.http(handler, 'notice/detail', data)
	},
}
