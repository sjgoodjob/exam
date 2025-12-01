import utils from "../js/utils.js";

/**
 * 积分相关接口
 */
module.exports = {

    /**
     * 获取用户积分记录
     * @returns {Promise<*>}
     */
	getUserScoreLog(handler, data = {}) {
        return utils.http(handler, 'score/logs', data)
    },
	
	/**
	 * 获取积分商品列表
	 * @returns {Promise<*>}
	 */
	getGoodsList(handler, data = {}) {
	    return utils.http(handler, 'score/goods', data)
	},
	
	/**
	 * 获取积分商品详情
	 * @returns {Promise<*>}
	 */
	getGoodsDetail(handler, data = {}) {
	    return utils.http(handler, 'score/goodDetail', data)
	},
	
	/**
	 * 积分兑换
	 * @returns {Promise<*>}
	 */
	goodExchange(handler, data = {}) {
	    return utils.http(handler, 'score/exchange', data)
	},
	
	/**
	 * 获取积分兑换订单列表
	 * @returns {Promise<*>}
	 */
	getOrdersList(handler, data = {}) {
	    return utils.http(handler, 'score/orders', data)
	},
	
	/**
	 * 获取积分兑换订单详情
	 * @returns {Promise<*>}
	 */
	getOrderDetail(handler, data = {}) {
	    return utils.http(handler, 'score/orderDetail', data)
	},
	
	/**
	 * 积分兑换订单完成
	 * @returns {Promise<*>}
	 */
	orderComplete(handler, data = {}) {
	    return utils.http(handler, 'score/complete', data)
	},
}
