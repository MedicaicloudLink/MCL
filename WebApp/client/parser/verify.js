import Type from '../tools/Type'
// 必选项
export const keepVerify = (data) => {
  data = JSON.parse(JSON.stringify(data))
  let keepHint = []
  for (const i in data) {
    const item = data[i]
    // 跳过的问题和非必选项
    if (!item.show || !item.keep) {
      keepHint.push(false)
      continue
    }

    // 该问题展示了，再进行必选项判断
    const value = item.value
    // 字符串类型：linerscale, dropdown, longText, mutipleChoice
    // 数组类型：checkboxes, table
    if (Type(value) === 'string' || Type(value) === 'array') {
      value.length > 0 ? keepHint.push(false) : keepHint.push(true)
      continue
    }
    // 对象类型：address, date, time, file
    if (Type(value) === 'object') {
      if (JSON.stringify(value) !== '{}') {
        let flagKeep = []
        for (let j in value) {
          if (item.answers[j] || (typeof item.answers[j]) === 'undefined') { // 该项存在的话再去判断其值是否为空
            if (value[j]) {
              flagKeep.push(true)
            } else {
              flagKeep.push(false)
            }
          }
        }
        if (flagKeep.every(item => item)) keepHint.push(false)
        else keepHint.push(true)
      } else {
        keepHint.push(true)
      }
      continue
    }
    keepHint.push(false)
  }
  return keepHint
}
// 正则
export const regExp = (regStr, str) => {
  let regExp = new RegExp(regStr)
  if (regExp.test(str)) return true
  else return false
}
// data time 的数据格式正确性
export const regVerify = (data) => {
  data = JSON.parse(JSON.stringify(data))
  let regHint = []

  for (const i in data) {
    const item = data[i]
    // 跳过的问题和非必选项
    if (!item.show || !item.keep) {
      regHint.push(false)
      continue
    }

    const value = item.value
    // 对象类型：date, time
    if (item.type === 'DATE' || item.type === 'TIME') {
      let flagReg = []
      let str
      for (let j in value) {
        if (item.answers[j] || (typeof item.answers[j]) === 'undefined') { // 该项存在的话再去判断其值是否为空
          if (item.type === 'DATE') {
            if (j === 'year') str = /^[1-9]\d{3}$/
            else if (j === 'month') str = /^([1-9]|0[1-9]|1[0-2])$/
            else str = /^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/
            flagReg.push(regExp(str, value[j]))
          } else if (item.type === 'TIME') {
            if (j === 'hour') str = /^(\d|[0-1]\d|2[0-3])$/
            else str = /^(\d|[0-5]\d)$/
            flagReg.push(regExp(str, value[j]))
          }
        }
      }
      flagReg.every(item => item) ? regHint.push(false) : regHint.push(true)
      continue
    }
    regHint.push(false)
  }
  return regHint
}
