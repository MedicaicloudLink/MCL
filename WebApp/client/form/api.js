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

export function fetch (url, params, config) {
  axios.defaults.headers.common['token'] = window.sessionStorage.getItem('authorization')
  return new Promise((resolve, reject) => {
    // console.log(process.env.NODE_ENV === 'production')
    process.env.NODE_ENV === 'development' ? url = '/api' + url : ''
    axios.post(url, params, config).then(response => {
      return resolve(response)
    }, err => {
      reject(err)
    }).catch(error => {
      reject(error)
    })
  })
}

export default {
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

  // 通知
  GetNoticeNum (params) {
    return fetch('/user/noreadnum', params)
  },

  /**
   * 获取用户参与的项目
   * @param {* userid } params
   */
  GetFormList (params) {
    return fetch('/user/formlist', params)
  },
  GetFormInfo (params) {
    return fetch('/user/formdata', params)
  },
  UpdateForm (params) {
    return fetch('/user/editform', params)
  },
  createForm (params) {
    return fetch('/user/createform', params)
  },
  delForm (params) {
    return fetch('/user/deleteform', params)
  },
  publishForm (params) {
    return fetch('/user/ispublishform', params)
  },
  shareForm (params) {
    return fetch('/user/shareform', params)
  },
  // search user
  searchUser (params) {
    return fetch('/project/finduserinfo', params)
  },
  /**
   * 公共API
   * ===========================================================================================
   * 药品检索， 上传照片
   */
  ChineseToShort (params) {
    return fetch('/user/shortpinyin', params)
  },
  SearchDrugs (params) {
    return fetch('/user/selectdrug', params)
  },
  PutImage (params, config) {
    return fetch('/user/picture', params, config)
  }
}
