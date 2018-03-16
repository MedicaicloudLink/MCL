/**
 * parse 解析sourcedata form数据
 * 添加form 的 show value属性
 */
const init = (form) => {
  form = JSON.parse(JSON.stringify(form))

  for (const i in form) {
    const type = form[i].type
    if (type === 'SECTION' || type === 'FORM') continue

    form[i].show = true
    if (type === 'FILEUPLOAD' || type === 'ADDRESS' || type === 'TIME' || type === 'DATE') form[i].value = {}
    if (type === 'SHORTTEXT') form[i].value = ''
    if (type === 'CHECKBOX' || type === 'TABLE') form[i].value = []
    if (type === 'LONGTEXT') form[i].value = ''
    if (type === 'RADIO') form[i].value = ''
    if (type === 'DROPDOWN') form[i].value = ''
    if (type === 'LINEARSCALE') form[i].value = ''
    // 添加其他属性
    if (type === 'RADIO') form[i].other = {title: '', text: ''}
    if (type === 'CHECKBOX') form[i].other = []
  }

  return form
}

export default init
