/**
 * 判断数据类型
 */
const Type = (obj) => {
  let toString = Object.prototype.toString
  /* eslint-disable */
  const map = {
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

export default Type
