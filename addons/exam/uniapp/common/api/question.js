import utils from "../js/utils.js";

/**
 * 题目相关接口
 */
module.exports = {

    /**
     * 记录错题
     * @returns {Promise<*>}
     */
	addQuestionWrong(handler, data = {}) {
        return utils.http(handler, 'question/wrongAdd', data)
    },
}