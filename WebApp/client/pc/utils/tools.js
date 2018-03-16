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
 * 求日期间隔
 * @param {String} str
 * @returns 日期间隔天数
 */
export function intervalDate (str) {
  if (!str) return ''
  var date = new Date(str)
  // 现在的时间-传入的时间 = 相差的时间（单位 = 毫秒）
  var time = new Date().getTime() - date.getTime()
  if (time < 0) {
    return ''
  } else {
    return parseInt(time / 24 / 3600 / 1000)
  }
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

/** 随机生成字符串 */
export function getRandomString (length) {
    length = length || 6
    const letters = '0123456789abcdefghijklmnopqrstuvwsyz'
    let str = ''
    for (var i = 0; i < length; i++ ) {
      str += letters[Math.floor(Math.random() * 35)]
    }
    return str;
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

export function traverse (o, func) {
  for (var i in o) {
    func.apply(this, [i, o[i]])
    if (o[i] !== null && typeof(o[i]) === "object") {
      //going one step down in the object tree!!
      traverse(o[i], func)
    }
  }
}

export function throttle(fn, delay) {
  var ctx;
  var args;
  // 记录上次触发事件
  var previous = Date.now();

  var later = function () {
    fn.apply(ctx, args);
  };

  return function () {
    ctx = this;
    args = arguments;
    var now = Date.now();
    // 本次事件触发与上一次的时间比较
    var diff = now - previous - delay;

    // 如果隔间时间超过设定时间，即再次设置事件触发的定时器
    if (diff >= 0) {
      // 更新最近事件触发的时间
      previous = now;
      setTimeout(later, delay);
    }
  };
};

// 检测字符串是否为数字，数字返回数字类型，否则返回false
export function checkNum (str) {
  let val = Number(str)
  if (val === '0') return 0
  if (val.length === 0) return false
  if (!isNaN(val)) return val
  return false
}
/* istanbul ignore next */
import Vue from 'vue';
const isServer = Vue.prototype.$isServer;
export const on = (function() {
  if (!isServer && document.addEventListener) {
    return function(element, event, handler) {
      if (element && event && handler) {
        element.addEventListener(event, handler, false);
      }
    };
  } else {
    return function(element, event, handler) {
      if (element && event && handler) {
        element.attachEvent('on' + event, handler);
      }
    };
  }
})();

/* istanbul ignore next */
export const off = (function() {
  if (!isServer && document.removeEventListener) {
    return function(element, event, handler) {
      if (element && event) {
        element.removeEventListener(event, handler, false)
      }
    };
  } else {
    return function(element, event, handler) {
      if (element && event) {
        element.detachEvent('on' + event, handler);
      }
    }
  }
})()