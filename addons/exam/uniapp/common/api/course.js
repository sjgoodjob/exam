import utils from "../js/utils.js";

export const courseIndex = (handler, params) => {
	return utils.http(handler, 'course/index', params)
}

export const courseDetail = (handler, params) => {
	return utils.http(handler, 'course/detail', params)
}

export const courseBuy = (handler, params) => {
	return utils.http(handler, 'course/buy', params)
}

export const coursePay = (handler, params) => {
	return utils.http(handler, 'course/pay', params)
}

export const courseOrders = (handler, params) => {
	return utils.http(handler, 'course/orders', params)
}

export const courseOrderDetail = (handler, params) => {
	return utils.http(handler, 'course/orderDetail', params)
}

export const coursesOrderDelete = (handler, params) => {
	return utils.http(handler, 'course/orderDelete', params)
}

export const courseCollect = (handler, params) => {
	return utils.http(handler, 'course/collect', params)
}

export const courseUnCollect = (handler, params) => {
	return utils.http(handler, 'course/unCollect', params)
}

export const courseCollects = (handler, params) => {
	return utils.http(handler, 'course/collects', params)
}

export const courseLike = (handler, params) => {
	return utils.http(handler, 'course/like', params)
}

export const courseUnLike = (handler, params) => {
	return utils.http(handler, 'course/unLike', params)
}

export const courseTeacher = (handler, params) => {
	return utils.http(handler, 'course/teacher', params)
}

export const coursesTudyCourse = (handler, params) => {
	return utils.http(handler, 'course/studyCourse', params)
}


// 唤起微信支付
export const requestPayment = (wxRes) => {
	console.log('wxRes: ',wxRes);
	return new Promise((resolve, reject) => {
		uni.requestPayment({
			timeStamp: wxRes.timeStamp,
			nonceStr: wxRes.nonceStr,
			package: wxRes.package,
			signType: wxRes.signType,
			paySign: wxRes.paySign,
			success: (res) => {
				let payInfo = {
					isPaySuccess: true,
					msg: ''
				}
				resolve(payInfo)
			},
			fail: (res) => {
				console.log('res: ',res);
				let payInfo = {
					isPaySuccess: false,
					msg: res
				}
				resolve(payInfo)
			},
		});
	})
}