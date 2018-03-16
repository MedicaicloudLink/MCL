import axios from 'axios'

// axios config
axios.defaults.timeout = 100000

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
  GetProjects (params) {
    return fetch('/project/fprojectlist', params)
  },
  OpenProjectTime (params) {
    return fetch('/project/fopenproject', params)
  },

  /* 项目状态，列表 */
  // 不同状态下的随访数量
  GetProjectState (params) {
    return fetch('/follow/fstatusnum', params)
  },

  // 我的工作日志（列表，排序，搜索）
  GetWorkList (params) {
    return fetch('/follow/fworkdata', params)
  },

  // 应随访患者列表（列表，排序，搜索）
  GetFollowList (params) {
    return fetch('/follow/fshouldfollow', params)
  },

  // 保存/提交/随访患者列表（搜索）
  GetFollowStateList (params) {
    return fetch('/follow/ffollowlist', params)
  },

  /**
   * 患者随访相关接口
   */
  GetPatientBase (params) {
    return fetch('/patient/getpatientbase', params)
  },

  GetFollowForm (params) {
    return fetch('/follow/ffollowinfo', params)
  },

  GetFollowData (params) {
    return fetch('/follow/fpatientdata', params)
  },

  SetFollowData (params) {
    return fetch('/follow/feditfollow', params)
  },

  // 历史数据情况
  GetHistoryData (params) {
    return fetch('/follow/fpatientrecord', params)
  },

  // 患者登记数据
  GetRegister (params) {
    return fetch('/patient/rpatientdata', params)
  },

  LockPatient (params) {
    return fetch('/follow/fclickfollow', params)
  },

  /**
   * 备注
   */
  GetRemarks (params) {
    return fetch('/follow/fremarklist', params)
  },

  SetRemark (params) {
    return fetch('/follow/fcreateremark', params)
  },

  DelRemark (params) {
    return fetch('/follow/fdeleteremark', params)
  },

  /**
   * 附件
   */
  GetFiles (params) {
    return fetch('/follow/ffilelist', params)
  },

  SetFile (params) {
    return fetch('/follow/faddfilenote', params)
  },

  DelFile (params) {
    return fetch('/follow/fdeletefile', params)
  },

  /**
   * 公共API
   * =====================================
   * 药品检索， 上传照片
   */
  ChineseToShort (params) {
    return fetch('/user/shortpinyin', params)
  },
  SearchDrugs (params) {
    return fetch('/user/selectdrug', params)
  },
  UpFile (params, config) {
    return fetch('/follow/fupfile', params, config)
  },
  /**
   * 上传图片
   * @param {* patientid userid filename} params
   */
  PutImage (params, config) {
    return fetch('/user/picture', params, config)
  }
}
