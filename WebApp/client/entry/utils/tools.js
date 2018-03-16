/**
 * 判断数据类型
 */
export function Type (obj) {
  let toString = Object.prototype.toString
  /* eslint-disable */
  let map = {
    '[object Boolean]'  : 'boolean', 
    '[object Number]'   : 'number', 
    '[object String]'   : 'string', 
    '[object Function]' : 'function', 
    '[object Array]'    : 'array', 
    '[object Date]'     : 'date', 
    '[object RegExp]'   : 'regExp', 
    '[object Undefined]': 'undefined',
    '[object Null]'     : 'null', 
    '[object Object]'   : 'object'
  }
  /* eslint-endable */
  return map[toString.call(obj)]
}

/**
 * 压缩图片
 */
export function resizeMe (img, scale) {
  // canvas 压缩图片
  let canvas = document.createElement('canvas')

  let width = img.width
  let height = img.height

  // resize the canvas and draw the image data into it
  canvas.width = width
  canvas.height = height
  let ctx = canvas.getContext('2d')
  ctx.drawImage(img, 0, 0, width, height)

  // preview.appendChild(canvas); // do the actual resized preview
  return canvas.toDataURL('image/jpeg', scale) // get the data from canvas as 70% JPG (can be also PNG, etc.)
}

/**
 * 图片转Blod
 */
export function dataUrlTOBlod (dataURI) {
  let byteString = window.atob(dataURI.split(',')[1])

  // separate out the mime component
  let mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

  // write the bytes of the string to an ArrayBuffer
  let ab = new ArrayBuffer(byteString.length)
  let ia = new Uint8Array(ab)
  for (let i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i)
  }

  // write the ArrayBuffer to a blob, and you're done
  let blob = new window.Blob([ab], {type: mimeString})

  return blob
}


/** 随机生成颜色代码 */
export function getRandomColor () {
    let letters = '0123456789ABCDEF';
    let color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}