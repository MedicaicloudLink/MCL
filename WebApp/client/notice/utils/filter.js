/**
 * 格式化时间
 * @param {String} str
 * @returns 格式化后的时间
 */
export const formatDate = (str) => {
  if (!str) return ''
  var date = new Date(str)
  // 现在的时间-传入的时间 = 相差的时间（单位 = 毫秒）
  var time = new Date().getTime() - date.getTime()
  if (time < 0) {
    return ''
  } else if ((time / 1000 < 30)) {
    return '刚刚'
  } else if (time / 1000 < 60) {
    return parseInt((time / 1000)) + '秒前'
  } else if ((time / 60000) < 60) {
    return parseInt((time / 60000)) + '分钟前'
  } else if ((time / 3600000) < 24) {
    return parseInt(time / 3600000) + '小时前'
  } else {
    return str.slice(0, 10)
  }
}
/** 转换大写 */
export const uppercase = (str) => {
  if (!str) return ''
  str = str.toString()
  return str.toUpperCase()
}

