/**
 * parse 解析sourcedata form数据，转换为patientdata
 * 1. 先把 value 非 string 问题类型转换成 answe[string]
 * 2. 根据 show 的判断跳转，删除 answer 和 other
 * 3. 统一 push answer and other
 */
const parse = (form) => {
  form = JSON.parse(JSON.stringify(form))
  let parseForm = []
  // console.log(form)

  for (const i in form) {
    const type = form[i].type
    let answer = form[i].value

    // 1. value 转换合并
    if (type === 'SHORTTEXT') {
      answer = form[i].value + form[i].answers.unit
    }

    if (type === 'ADDRESS') {
      answer = form[i].value.province + ' ' + form[i].value.city + ' ' + form[i].value.text
    }

    if (type === 'CHECKBOX') {
      answer = form[i].value.join(',')
    }

    if (type === 'DATE') {
      let arr = []
      // 根据 date 输入状态定义值
      if (form[i].answers.year) arr.push(form[i].value.year)
      if (form[i].answers.month) arr.push(form[i].value.month)
      if (form[i].answers.day) arr.push(form[i].value.day)
      answer = arr.join('-')
    }

    if (type === 'TIME') {
      let arr = []
      // 根据 date 输入状态定义值
      if (form[i].answers.hour) arr.push(form[i].value.hour)
      if (form[i].answers.minute) arr.push(form[i].value.minute)
      if (form[i].answers.second) arr.push(form[i].value.second)
      answer = arr.join(':')
    }

    if (type === 'FILEUPLOAD') {
      answer = form[i].value.filename + ',' + form[i].value.fileurl
    }

    if (type === 'TABLE') {
      let value = ''
      for (let lineVal in form[i].value) {
        if (form[i].value[lineVal].value) {
          value += form[i].value[lineVal].label + ',' + form[i].value[lineVal].value + '、'
        }
      }
      answer = value.substring(0, value.length - 1)
    }

    // 2. 处理跳转，有跳转的时候根据 show, 清空 answer 和 other
    if (form[i].show === false) {
      answer = ''
      if (type === 'RADIO' && form[i].other) {
        form[i].other.text = ''
      }

      if (type === 'CHECKBOX' && form[i].other) {
        for (const otherIndex in form[i].other) {
          form[i].other[otherIndex].text = ''
        }
      }
    }

    // 3. 全部整合 arr
    if (type === 'FORM' || type === 'SECTION') {
      parseForm.push({title: form[i].type, answer: form[i].title})
    } else if (type === 'RADIO') {
      parseForm.push({title: form[i].title, answer: answer})
      // 是否有其他选项
      if (form[i].other) {
        for (const j in form[i].answers) {
          if (form[i].answers[j].other) {
            const other_text = form[i].other.title === form[i].answers[j].text ? form[i].other.text : ''
            parseForm.push({
              title: form[i].title + '[' + form[i].answers[j].text + ']',
              answer: other_text
            })
          }
        }
      }
    } else if (type === 'CHECKBOX') {
      parseForm.push({title: form[i].title, answer: answer})

      // 是否有其他选项
      if (form[i].other) {
        form[i].other.map(item => {
          parseForm.push({
            title: form[i].title + '[' + item.title + ']',
            answer: item.text
          })
        })
      }
    } else {
      parseForm.push({title: form[i].title, answer: answer})
    }
  }

  // console.log(parseForm)

  return parseForm
}

export default parse
