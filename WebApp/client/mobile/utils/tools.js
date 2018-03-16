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
 * 移动设备判断
 */
export function getMobileOperatingSystem () {
  let userAgent = navigator.userAgent || navigator.vendor || window.opera

  if (/android/i.test(userAgent)) {
    return "Android";
  }

  if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
      return "iOS";
  }

  return "unknown";
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

/** 页面平滑滚动 */
// 滚动到顶部 scrollTo(document.body, 0, 400)
export function scrollTo (element, to, duration) {
    if (duration <= 0) return;
    var difference = to - element.scrollTop;
    var perTick = difference / duration * 10;

    setTimeout(function() {
        element.scrollTop = element.scrollTop + perTick;
        if (element.scrollTop === to) return;
        scrollTo(element, to, duration - 10);
    }, 10);
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

export function deepEquals (o1, o2) {
  // console.log(JSON.stringify(o1) + '=====' + JSON.stringify(o2))
  if (Type(o1) === 'array' && Type(o2) === 'array') {
    const result = JSON.stringify(o1) === JSON.stringify(o2)
    return new Array(result)
  }

  var k1 = Object.keys(o1).sort()
  var k2 = Object.keys(o2).sort()
  if (k1.length != k2.length) return false
  return k1.map(keyPair => {
    if (Type(o1[keyPair]) === "object" && Type(o2[keyPair]) === "object") {
      return deepEquals(o1[keyPair], o2[keyPair])
    } else if (Type(o1[keyPair]) === Type(o2[keyPair]) === "boolean") {
      return o1[keyPair] === o2[keyPair]
    } else if (Type(o1[keyPair]) === Type(o2[keyPair]) === "array") {
      return JSON.stringify(o1[keyPair]) === JSON.stringify(o2[keyPair])
    } else {
      return o1[keyPair].toString() === o2[keyPair].toString()
    }
  })
}

export function throttle(func, wait, options) {
  var context, args, result;
  var timeout = null;
  var previous = 0;
  if (!options) options = {};
  var later = function() {
    previous = options.leading === false ? 0 : Date.now();
    timeout = null;
    result = func.apply(context, args);
    if (!timeout) context = args = null;
  };
  return function() {
    var now = Date.now();
    if (!previous && options.leading === false) previous = now;
    var remaining = wait - (now - previous);
    context = this;
    args = arguments;
    if (remaining <= 0 || remaining > wait) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
      previous = now;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }
    return result;
  };
};
