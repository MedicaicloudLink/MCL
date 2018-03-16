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

  // 搜索用户，邀请添加联系人
  SearchUser (params) {
    return fetch('/contact/searchuser', params)
  },

  // 添加好友申请
  addContact (params) {
    return fetch('/contact/addcontacter', params)
  },

  replyContactMsg (params) {
    return fetch('/contact/doapply', params)
  },

  // 获取联系人列表
  GetContacts (params) {
    return fetch('/contact/contactmanlist', params)
  },

  GetContactInfo (params) {
    return fetch('/contact/contactmaninfo', params)
  },

  SearchContact (params) {
    return fetch('/contact/searchcontact', params)
  },

  DelContacts (params) {
    return fetch('/contact/deletecontactman', params)
  },

  inviteContact (params) {
    return fetch('/user/inviteuser', params)
  },

  // 通知
  GetNotices (params) {
    return fetch('/user/allnotice', params)
  },

  GetNoticeNum (params) {
    return fetch('/user/noreadnum', params)
  },

  /**
   * 公共API
   * =====================================
   * 药品检索， 上传照片
   */
  ChineseToShort (params) {
    return fetch('/user/shortpinyin', params)
  },
  /**
   * 上传图片
   * @param {* patientid userid filename} params
   */
  PutImage (params, config) {
    return fetch('/user/picture', params, config)
  }
}
