const app_info = require("@/static/appInfo.js");
const api_host = app_info.api_host;
const version = app_info.version;
const api_prefix = api_host + "/addons/exam/";

//工具类
module.exports = {
  isShowTip(apiUrl) {
    let filterList = ["/agency/getAuditVersion"];
    if (filterList.indexOf(apiUrl) != -1) {
      return false;
    } else {
      return true;
    }
  },

  timestamp() {
    return parseInt(new Date().getTime() / 1000);
  },
  cons_log(...params) {
    for (let p of params) {
      console.log(p);
    }
  },
  cons_log_json(...params) {
    for (let p of params) {
      console.log(JSON.stringify(p));
    }
  },

  goto(url) {
    uni.navigateTo({
      url: url,
    });
  },

  goreto(url) {
    uni.redirectTo({
      url: url,
    });
  },

  gotore(url) {
    uni.reLaunch({
      url: url,
    });
  },

  goback(delta = 1) {
    uni.navigateBack({
      delta: delta,
    });
  },

  toast(msg, icon = "none", duration = 2000, mask = false, image = "") {
    uni.showToast({
      title: msg,
      icon: icon,
      duration: duration,
      mask: mask,
      image: image,
    });
  },

  alert(content, callBack) {
    uni.hideLoading();
    uni.showModal({
      content: content,
      showCancel: false,
      success: function () {
        if (callBack != undefined) {
          callBack();
        }
      },
    });
  },

  getData(key, is_clear = false) {
    var value = uni.getStorageSync(key);
    if (is_clear) {
      uni.setStorageSync(key, "");
    }
    // console.log('get cache', key, value)
    return value;
  },

  setData(key, value) {
    // console.log('set cache', key, value)
    uni.setStorageSync(key, value);
  },

  array_column(obj, key) {
    var objects = [];
    for (var i in obj) {
      if (!obj.hasOwnProperty(i)) continue;
      if (typeof obj[i] == "object") {
        objects = objects.concat(this.array_column(obj[i], key));
      } else if (i == key) {
        objects.push(obj[i]);
      }
    }
    return objects;
  },

  shuffle(arr) {
    var l = arr.length;
    var index, temp;
    while (l > 0) {
      index = Math.floor(Math.random() * l);
      temp = arr[l - 1];
      arr[l - 1] = arr[index];
      arr[index] = temp;
      l--;
    }
    return arr;
  },

  // 秒数转时分秒格式
  sec_to_time(s) {
    var t;
    if (s > -1) {
      var hour = Math.floor(s / 3600);
      var min = Math.floor(s / 60) % 60;
      var sec = s % 60;
      if (hour < 10) {
        t = "0" + hour + ":";
      } else {
        t = hour + ":";
      }

      if (min < 10) {
        t += "0";
      }
      t += min + ":";
      if (sec < 10) {
        t += "0";
      }
      t += sec.toFixed(2);
    }
    return t;
  },

  login() {
    var token = this.getData("token");
    var user = this.getData("user");
    if (!token || !user) {
      const pages = getCurrentPages();
      const currentPage = pages[pages.length - 1];
      const url = currentPage.route;
      const options = currentPage.options;
      let urlWithArgs = `/${url}?`;
      for (let key in options) {
        const value = options[key];
        urlWithArgs += `${key}=${value}&`;
      }
      urlWithArgs = urlWithArgs.substring(0, urlWithArgs.length - 1);
      uni.setStorageSync("route", urlWithArgs);
      console.log("没有缓存数据，需先授权");
      uni.redirectTo({
        url: "/pages/login/login",
      });
      return;
    }
    return true;
  },

  /**
   * rich-text富文本中图片宽度最大100%
   * @param {String} html
   */
  formatRichText(html) {
    // 去掉img标签里的style、width、height属性
    let content_data = html.replace(/<img[^>]*>/gi, function (match, capture) {
      match = match
        .replace(/style="[^"]+"/gi, "")
        .replace(/style='[^']+'/gi, "");
      match = match
        .replace(/width="[^"]+"/gi, "")
        .replace(/width='[^']+'/gi, "");
      match = match
        .replace(/height="[^"]+"/gi, "")
        .replace(/height='[^']+'/gi, "");
      return match;
    });

    // 修改所有style里的width属性为max-width:100%
    content_data = content_data.replace(
      /style="[^"]+"/gi,
      function (match, capture) {
        match = match
          .replace(/width:[^;]+;/gi, "max-width:100%;")
          .replace(/width:[^;]+;/gi, "max-width:100%;");
        return match;
      }
    );

    // 去掉<br/>标签
    content_data = content_data.replace(/<br[^>]*\/>/gi, "");
    // img标签添加style属性：max-width:100%;height:auto
    content_data = content_data.replace(
      /\<img/gi,
      '<img style="max-width:100%;height:auto;display:block;margin:0px auto;"'
    );

    return content_data;
  },

  /**
   * 获取已选科目ID
   */
  getSubjectId() {
    let user_subject = uni.getStorageSync("user_subject");
    if (user_subject && user_subject.subject2 && user_subject.subject2.id) {
      return user_subject.subject2.id;
    }
    return 0;
  },

  /**
   * http请求
   * @param handler
   * @param path
   * @param data
   * @param method
   * @param showLoading
   * @returns {Promise<unknown>}
   */
  http(handler, path, data = {}, method = "post", showLoading = true) {
    let url = api_prefix + path;
    let timestamp = Date.parse(new Date());
    if (typeof data == "object") {
      data["timestamp"] = timestamp;
      // data['version'] = version
    }

    if (showLoading) {
      uni.showLoading({
        title: "加载中..",
        mask: true,
      });
    }

    return new Promise((resolve, reject) => {
      uni.request({
        url: url,
        method: method,
        data: data,
        header: {
          "content-type": "application/json",
          token: this.getData("token"),
          "app-version": version,
        },
        success: (res) => {
          if (res.statusCode == 200) {
            // 过滤掉一些接口code为0的报错不提示，默认所有接口code为0时提示报错
            // 不需要报错提示，自行到isShowTip方法里面的filterList数组添加不需要提示的接口名
            let isShowTip = this.isShowTip(path);
            if (res.data.code == 0) {
              if (isShowTip) {
                uni.showToast({
                  title: res.data.msg,
                  icon: "none",
                });
              }
            }
            resolve(res.data);
          } else {
            console.log("http statusCode", res.statusCode);
            uni.showToast({
              title: res.data.msg,
              icon: "none",
            });
            // 未登录
            if (res.statusCode == 401) {
              // console.log('this.$refs', this.$refs)
              // this.$refs.login.modal = true
              // uni.$emit('uniLogin', handler)

              handler.$refs.login.modal = true;
              // handler.$refs.login.afterMethod = () => {
              // 	this.http(handler, path, data)
              // }
            }
            reject(res);
          }
        },
        fail: (res) => {
          console.log(res);
          reject(res);
          // return false;
        },
        complete: (res) => {
          showLoading && uni.hideLoading();
        },
      });
    });
  },

  /**
   * 上传文件
   * @returns {Promise<*>}
   */
  uploadFile(
    handler,
    filePath,
    data = {},
    fileType = "image",
    showLoading = true
  ) {
    if (showLoading) {
      uni.showLoading({
        title: "加载中..",
        mask: true,
      });
    }

    return new Promise((resolve, reject) => {
      uni.uploadFile({
        url: api_prefix + "common/upload",
        header: {
          token: uni.getStorageSync("token"),
          "Content-Type": "multipart/form-data",
        },
        fileType: fileType,
        filePath: filePath,
        formData: data,
        name: "file",
        success: (res) => {
          console.log("upload res", res);
          let data = JSON.parse(res.data);
          if (res.statusCode == 200) {
            resolve(data);
          } else {
            console.log("upload statusCode", res.statusCode);
            uni.showToast({
              title: data.message,
              icon: "none",
            });

            // 未登录
            if (res.statusCode == 401) {
              handler.$refs.login.modal = true;
            }
          }
        },
        fail: (res) => {
          console.log("fail res", res);
          return false;
        },
        complete: (res) => {
          showLoading && uni.hideLoading();
        },
      });
    });
  },
};
