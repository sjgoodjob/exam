import utils from "../js/utils.js";

/**
 * 证书相关接口
 */
module.exports = {

	/**
	 * 获取列表
	 * @returns {Promise<*>}
	 * 获取招聘信息列表
// https://exam-zp.chaohang.gd.cn/addons/exam/recruit/index
// keyword=关键词，搜职位
// wage=薪资范围，直接传文本，面议或1-3k这样
// bonus=福利，也是直接传文本，多个逗号隔开
	 */
	getRecruitIndex(handler, data = {}) {
		return utils.http(handler, 'recruit/index', data)
	},

	/**
	 * 获取详情
	 * @returns {Promise<*>}
	 */
	getRecruitDetail(handler, data = {}) {
		return utils.http(handler, 'recruit/detail', data)
	},
	/**
	 * 获取详情
	 * @returns {Promise<*>}
	 */
	getRecruitCommon(handler, data = {}) {
		return utils.http(handler, 'recruit/common', data)
	},
	
}