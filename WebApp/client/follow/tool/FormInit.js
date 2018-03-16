export function init (data) {
  const form = []
  data.map(i => {
    if (i.type !== 'RADIO' && i.type !== 'DROPDOWN') {
      delete i.isjump
    }

    if (i.type === 'FILEUPLOAD') {
      i.answer = {filename: '', fileurl: ''}
    }

    if (i.type === 'SHORTTEXT' || i.type === 'ADDRESS' || i.type === 'TIME' || i.type === 'DATE') form.value = {}
    if (i.type === 'CHECKBOX' || i.type === 'TABLE') form.value = []
    if (i.type === 'LONGTEXT') form.value = ''
    if (i.type === 'FILEUPLOAD') form.value = ''
    if (i.type === 'RADIO') form.value = ''
    if (i.type === 'DROPDOWN') form.value = ''
    if (i.type === 'LINEARSCALE') form.value = ''

    return i
  })

  return form
}
