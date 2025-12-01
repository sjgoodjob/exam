import Vue from 'vue'
import App from './App'
import store from './store'

// +----------------------------------------------------------------------
// | 组件引入
// +----------------------------------------------------------------------
// 引入全局TuniaoUI
import TuniaoUI from 'tuniao-ui'
Vue.use(TuniaoUI)

// 底部导航栏组件
import tabbar from '@/components/tabbar/tabbar.vue';
Vue.component('tabbar', tabbar);

// 登录框组件
import login from '@/components/login/login.vue';
Vue.component('login', login);
// |______________________________________________________________________


// +----------------------------------------------------------------------
// | 函数引入
// +----------------------------------------------------------------------
// 工具函数
import utils from './common/js/utils';
Vue.prototype.utils = utils;

// 广告函数
import adUtils from './common/js/ad';
Vue.prototype.adUtils = adUtils;

// 应用配置
const app_info = require("@/static/appInfo.js");
Vue.prototype.appInfo = app_info
Vue.config.productionTip = false

// 全局登录事件
Vue.prototype.login = function() {
	if (!this.utils.getData('token')) {
		this.$refs.login.modal = true
		return;
	}
}

let api_host = app_info.api_host;
Vue.prototype.imgUrl = api_host

// 统一请求函数
Vue.prototype.http = function(path, data, method = 'post', showLoading = true) {
	return utils.http(this, path, data, method, showLoading)
}


// 获取当前页面路径
Vue.prototype.currentPage = function () {
	var pages = getCurrentPages() // 获取栈实例
	let currentRoute  = pages[pages.length-1].route; // 获取当前页面路由
	let currentPage = pages[pages.length-1]['$page']['fullPath'] //当前页面路径(带参数)
	return currentPage
}

// 正常格式的日期时间
Vue.filter('format_date', function(time) {
	const dateTime = new Date(time * 1000);
	const YY = dateTime.getFullYear();
	const MM =
		dateTime.getMonth() + 1 < 10 ?
		'0' + (dateTime.getMonth() + 1) :
		dateTime.getMonth() + 1;
	const D =
		dateTime.getDate() < 10 ? '0' + dateTime.getDate() : dateTime.getDate();
	const hh =
		dateTime.getHours() < 10 ?
		'0' + dateTime.getHours() :
		dateTime.getHours();
	const mm =
		dateTime.getMinutes() < 10 ?
		'0' + dateTime.getMinutes() :
		dateTime.getMinutes();
	const ss =
		dateTime.getSeconds() < 10 ?
		'0' + dateTime.getSeconds() :
		dateTime.getSeconds();
	return `${YY}-${MM}-${D} ${hh}:${mm}`
});

// 秒转时分秒格式
Vue.filter('format_second', function(second, tips = '不限时') {
	if (second == undefined) return ''
	if (second == 0) return tips

	let result = parseInt(second)
	let h = Math.floor(result / 3600) < 10 ? '0' + Math.floor(result / 3600) : Math.floor(result / 3600);
	let m = Math.floor((result / 60 % 60)) < 10 ? '0' + Math.floor((result / 60 % 60)) : Math.floor((result / 60 % 60));
	let s = Math.floor((result % 60)) < 10 ? '0' + Math.floor((result % 60)) : Math.floor((result % 60));

	let res = '';
	if(h !== '00') res += `${h}时`;
	if(m !== '00') res += `${m}分`;
	res += `${s}秒`;
	return res;
});
// |______________________________________________________________________


// +----------------------------------------------------------------------
// | 全局变量及函数
// +----------------------------------------------------------------------
// 引入TuniaoUI提供的vuex简写方法
let vuexStore = require('@/store/$t.mixin.js')
Vue.mixin(vuexStore)

// 导入并挂载全局的分享方法
import wxShare from '@/common/mixins/share.js'
Vue.mixin(wxShare)
// |______________________________________________________________________

App.mpType = 'app'

const app = new Vue({
	store,
	...App
})
app.$mount()
