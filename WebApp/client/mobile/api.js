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
   * 获取公开项目
   */
  GetAllProjects (params) {
    return fetch('/project/allprojects', params)
  },
  /**
   * 申请加入项目
   */
  ApplyProject (params) {
    return fetch('/project/applyproject', params)
  },
  /**
   * 获取用户参与的项目
   * @param {* userid } params
   */
  GetProjects (params) {
    return fetch('/user/getprojects', params)
  },
  /**
   * 获取用户通知
   * @param {* userid } params
   */
  GetNotices (params) {
    return fetch('/user/noticelist', params)
  },
  /**
   * 获取项目信息
   * @param {* projectid } params
   */
  GetProjectInfo (params) {
    return fetch('/project/projectdetail', params)
  },
  /**
   * 获取项目病历列表
   */
  GetProjectPatients (params) {
    return fetch('/patient/getprojectpatients', params)
  },
  /**
   * 获取项目模板
   */
  GetpProjectTemplates (params) {
    return fetch('/project/gettemplates', params)
  },
  /**
   * 创建患者
   */
  CreatePatient (params) {
    return fetch('/patient/createrecord', params)
  },
  /**
   * 修改患者基本信息
   */
  UpdatePatientBase (params) {
    return fetch('/patient/updatepatientbase', params)
  },
  /**
   * 删除患者
   */
  DeletePatients (params) {
    return fetch('/patient/deletepatient', params)
  },
  /**
   * 创建患者记录
   */
  CreatePatientData (params) {
    return fetch('/patient/createpatientdata', params)
  },
  /**
   * 更新患者记录
   */
  UpdatePatientData (params) {
    return fetch('/patient/updatepatientdata', params)
  },
  /**
   * 获取患者基本信息
   */
  GetPatientBase (params) {
    return fetch('/patient/getpatientbase', params)
  },
  /**
   * 获取患者记录列表
   */
  GetPatientRecords (params) {
    return fetch('/patient/getrecords', params)
  },
  /**
   * 获取患者某条记录
   */
  GetOneRecord (params) {
    return fetch('/patient/getrecord', params)
  },
  /**
   * 删除患者某条记录
   */
  DeletePatientData (params) {
    return fetch('/patient/deletepatientdata', params)
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
  PutImage (params) {
    return fetch('/user/picture', params)
  }
}
