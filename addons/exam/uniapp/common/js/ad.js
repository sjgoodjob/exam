var interstitialAd = null;
let interstitial = {
	load(id) {
		if (id && uni.createInterstitialAd) {
			interstitialAd = uni.createInterstitialAd({
				adUnitId: id
			})
			interstitialAd.onLoad(() => {
				console.log('插屏广告加载中')
			})
			interstitialAd.onError((err) => {
				console.log('加载错误', err)
			})
			interstitialAd.onClose((res) => {
				console.log('插屏广告关闭', res)
			})
		}
	},
	show() {
		if (interstitialAd) {
			interstitialAd.show().catch((err) => {
				console.error(err)
			})
		}
	}
}

let videoAd = null;
let rewarded = {
	load(id, e) {
		if (id && uni.createRewardedVideoAd) {
			videoAd = uni.createRewardedVideoAd({
				adUnitId: id
			})
			videoAd.onError(err => {})
			videoAd.onClose((status) => {
				if (status && status.isEnded || status === undefined) {
					e()
				} else {

				}
			})
		}
	},
	show() {
		if (videoAd) {
			videoAd.show().catch(() => {
				// 失败重试
				videoAd.load().then(() => videoAd.show()).catch(err => {
					console.log('激励视频 广告显示失败')
				})
			})
		}
	}
}

module.exports = {
	interstitial,
	rewarded
};
