import utils from "../js/utils.js";

/**
 * 考试试卷相关接口
 */
module.exports = {

    /**
     * 获取考试试卷题目
     * @returns {Promise<*>}
     */
	getExamQuestion(handler, data = {}) {
        return utils.http(handler, 'paper/getExamQuestion', data)
    },
	
	/**
	 * 检查考试是否需要支付
	 * @returns {Promise<*>}
	 */
	checkPay(handler, data = {}) {
	    return utils.http(handler, 'paper/checkPay', data)
	},
	
	/**
	 * 创建考试订单及支付信息
	 * @returns {Promise<*>}
	 */
	createOrder(handler, data = {}) {
	    return utils.http(handler, 'paper/createOrder', data)
	},
}