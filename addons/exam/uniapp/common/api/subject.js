import utils from "../js/utils.js";

/**
 * 科目相关接口
 */
module.exports = {
	/**
	 * 获取列表
	 * @returns {Promise<*>}
	 */
	getSubjectList(handler, data = {}) {
		return utils.http(handler, 'subject/index', data)
	},
	
	/**
	 * 获取已绑定科目列表
	 * @returns {Promise<*>}
	 */
	getOnlySubjectList(handler, data = {}) {
		return utils.http(handler, 'subject/onlySubject', data)
	},
	
	/**
	 * 获取详情
	 * @returns {Promise<*>}
	 */
	getSubjectDetail(handler, data = {}) {
		return utils.http(handler, 'subject/detail', data)
	},
}
