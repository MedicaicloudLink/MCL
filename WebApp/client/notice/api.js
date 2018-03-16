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
    // console.log(process.env.NODE_ENV)
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
  /**
   * 用户登录
   * @param {* username, password} params
   */
  Login (params) {
    return fetch('/login/login', params)
  },
  /**
   * 获取用户信息
   * @param {* userid} params
   */
  GetUserInfo (params) {
    return fetch('/user/userinfo', params)
  },
  /**
   * 获取项目的具体信息
   * @param {* projectid userid} params
   */
  GetProjectInfo (params) {
    return fetch('/project/projectdetail', params)
  },
   /**
   * 项目参与的成员
   * @param {* projectid userid } params
   */
  GetProjectMember (params) {
    return fetch('/project/getmemberlistbyprojectid', params)
  },
  /**
   * 获取联系人信息
   * @param {* userid, touserid} params
   */
  GetInviterInfo (params) {
    return fetch('/contact/contactmaninfo', params)
  },
  /**
   * 获取用户名字的缩写
   * @param {* string : 名字 } params
   */
  ChineseToShort (params) {
    return fetch('/user/shortpinyin', params)
  },

  // 获取未读消息条数
  GetNoticeNum (params) {
    return fetch('/user/noreadnum', params)
  },

  /**
   * 获取所有通知
   * @param {* userid ，page， type(通知类型)} params
   */
  GetAllNotices (params) {
    return fetch('/user/allnotice', params)
  },
  /**
   * 处理联系人邀请通知
   * @param {* userid touserid status} params
   */
  HandleContactApply (params) {
    return fetch('/contact/doapply', params)
  },
  /**
   * 处理项目邀请通知
   * @param {* userid projectid touserid status} params
   */
  HandleProjectApply (params) {
    return fetch('/project/doapply', params)
  }
}
