import utils from "../js/utils.js";

export const signUpSubmit = (handler, params) => {
	return utils.http(handler, 'sign_up/submit', params)
}

export const signUpList = (handler, params) => {
	return utils.http(handler, 'sign_up/list', params)
}

export const signUpDetail = (handler, params) => {
	return utils.http(handler, 'sign_up/detail', params)
}

export const signUpEdit = (handler, params) => {
	return utils.http(handler, 'sign_up/edit', params)
}

export const signUpCateGetThree = (handler, params) => {
	return utils.http(handler, 'cate/getThree', params)
}

export const signUpPay = (handler, params) => {
	return utils.http(handler, 'sign_up/pay', params)
}

export const signUpQueryPay = (handler, params) => {
	return utils.http(handler, 'sign_up/queryPay', params)
}