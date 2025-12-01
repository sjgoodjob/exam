import utils from "../js/utils.js";

/**
 * 学习动态相关接口
 */
module.exports = {
  /**
   * 获取列表
   * @returns {Promise<*>}
   */
  getNewsList(handler, data = {}) {
    return utils.http(handler, "news/index", data);
  },

  /**
   * 获取详情
   * @returns {Promise<*>}
   */
  getNewsDetail(handler, data = {}) {
    return utils.http(handler, "news/detail", data);
  },
};
