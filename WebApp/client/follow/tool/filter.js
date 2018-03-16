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
  } else if ((time / 86400000) < 31) {
    return parseInt(time / 86400000) + '天前'
  } else if ((time / 2592000000) < 12) {
    return parseInt(time / 2592000000) + '月前'
  } else {
    return parseInt(time / 31536000000) + '年前'
  }
}

/** 转换大写 */
export const uppercase = (str) => {
  if (!str) return ''
  str = str.toString()
  return str.toUpperCase()
}

export const dateSubtract = (date1, date2) => {
  const d1 = new Date(date1)
  const d2 = new Date(date2)

  return d1.getFullYear() - d2.getFullYear()
}

/** 名字缩写处理 */
export const nameShort = (str) => {
  if (!str) return ''
  if (str.length === 2) return (str[0] + '-' + str[1]).toUpperCase()
  return str.toUpperCase()
}

/** 出生日期转年龄 */
export const dateToAge = (date) => {
  if (date.length < 4) return '--'
  const birthdayYear = new Date(date).getFullYear()
  return new Date().getFullYear() - birthdayYear
}

/** 患者状态判断 */
export const patientState = (state) => {
  if (parseInt(state) === 1) return '审核通过'
  if (parseInt(state) === 2) return '其他原因'
  if (parseInt(state) === 3) return '号码错误'
  if (parseInt(state) === 4) return '无人接听'
  if (parseInt(state) === 5) return '患者不配合'
  if (parseInt(state) === 6) return '因意外事件打断'
  if (parseInt(state) === 7) return '保存'
  if (parseInt(state) === 9) return '待审核'
  if (parseInt(state) === 10) return '审核未通过'

  return ''
}
