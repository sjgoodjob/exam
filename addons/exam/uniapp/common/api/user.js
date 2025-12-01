import utils from "../js/utils.js";

/**
 * 用户相关接口
 */
module.exports = {
  /**
   * 获取用户信息
   * @returns {Promise<*>}
   */
  getUserInfo(handler) {
    return utils.http(handler, "user/info");
  },

  /**
   * 获取会员开通配置
   * @returns {Promise<*>}
   */
  getMemberOpenConfig(handler) {
    return utils.http(handler, "user/memberOpenConfig");
  },

  /**
   * 创建会员开通订单
   * @returns {Promise<*>}
   */
  createMemberOrder(handler, member_config_id) {
    return utils.http(handler, "user/createMemberOrder", {
      member_config_id: member_config_id,
    });
  },

  /**
   * 获取会员配置
   * @returns {Promise<*>}
   */
  getMemberConfigs(handler) {
    return utils.http(handler, "user/memberConfigs");
  },

  /**
   * 获取微信绑定的手机号码
   * @returns {Promise<*>}
   */
  // getWechatPhone(handler, data) {
  //   return utils.http(handler, "user/getWechatPhone", data);
  // },

  /**
   * 保存用户信息
   * @returns {Promise<*>}
   */
  save(handler, data) {
    return utils.http(handler, "user/save", data);
  },

  /**
   * 保存我常用的题库
   * @returns {Promise<*>}
   */
  saveMyCate(handler, data) {
    return utils.http(handler, "user/saveMyCate", data);
  },

  /**
   * 会员码激活
   * @returns {Promise<*>}
   */
  activateMember(handler, code) {
    return utils.http(handler, "user/activateMember", { code: code });
  },

  /**
   * 注册
   * @returns {Promise<*>}
   */
  register(handler, data) {
    return utils.http(handler, "user/register", data);
  },

  /**
   * 账号密码登录
   * @returns {Promise<*>}
   */
  login(handler, data) {
    return utils.http(handler, "user/userLogin", data);
  },

  /**
   * 手机号码快速登录
   * @returns {Promise<*>}
   */
  getWechatPhone(handler, data) {
    return utils.http(handler, "user/getWechatPhone", data);
  },

  /**
   * 用户密码修改
   * @returns {Promise<*>}
   */
  changePassword(handler, data) {
    return utils.http(handler, "user/changePassword", data);
  },
};
