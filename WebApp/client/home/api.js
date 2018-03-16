import axios from 'axios'

// axios config
axios.defaults.timeout = 10000

// 返回状态判断
axios.interceptors.response.use((res) => {
  // console.log(res)
  if (res.data.code !== 'succ') {
    return Promise.reject(res.data.message)
  }
  return Promise.resolve(res.data.data)
}, (error) => {
  return Promise.reject(error)
})

export function fetch (url, params) {
  axios.defaults.headers.common['token'] = window.sessionStorage.getItem('authorization')
  return new Promise((resolve, reject) => {
    // console.log(process.env.NODE_ENV === 'production')
    process.env.NODE_ENV === 'development' ? url = '/api' + url : ''
    axios.post(url, params).then(response => {
      return resolve(response)
    }, err => {
      reject(err)
    }).catch(error => {
      reject(error)
    })
  })
}

export default {
  /**
   * 用户登录
   * @param {* username, password} params
   */
  Login (params) {
    return fetch('/login/login', params)
  },
  /**
   * 用户资料
   * @param {* userid} params
   */
  GetUserinfo (params) {
    return fetch('/user/userinfo', params)
  },
  /**
   * 修改用户资料
   */
  SetUserinfo (params) {
    return fetch('/user/usersetting', params)
  },
  /**
   * 修改用户密码
   */
  ResetPw (params) {
    return fetch('/user/uppassword', params)
  },
  /**
   * 公共API
   * ===========================================================================================
   * 药品检索， 上传照片
   */
  ChineseToShort (params) {
    return fetch('/user/shortpinyin', params)
  },
  PutImage (params) {
    return fetch('/user/picture', params)
  }
}
