const path = require('path')
const fs = require('fs')

const fromFile = path.join(__filename, '../../pages.json')
const toFile = path.join(__filename, '../../router/index.js')
const buffPrefix = Buffer.from('export default ')

const fileData = fs.readFileSync(fromFile)

// 转成可读的js, 正则删除注释， 不然JSON.parse会报错
const fileObj = JSON.parse(fileData.toString().replace(/\/\/.*/g, ''))

// 遍历，只取路径和标题，其他的不要，已减小文件体积
const routes = fileObj.pages.map(e => {
  return {
    title: e.style ? e.style.navigationBarTitleText : '未知',
    path: e.path,
  }
})

fs.writeFileSync(toFile, buffPrefix + Buffer.from(JSON.stringify(routes)))

module.exports = {
  configureWebpack: {
    plugins: []
  }
}