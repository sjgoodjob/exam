import utils from "../js/utils.js";

/**
 * 分类相关接口
 */
module.exports = {
	/**
	 * 检测是否已开通题库
	 * @returns {Promise<*>}
	 */
	checkPay(handler, cate_id) {
		return utils.http(handler, 'cate/checkPay', {cate_id: cate_id})
	},

	/**
	 * 创建题库开通订单
	 * @returns {Promise<*>}
	 */
	createOrder(handler, cate_id) {
		return utils.http(handler, 'cate/createOrder', {cate_id: cate_id})
	},

	/**
	 * 题库码激活
	 * @returns {Promise<*>}
	 */
	activateCate(handler, cate_id, code) {
		return utils.http(handler, 'cate/activateCate', {cate_id: cate_id, code: code})
	},
}
