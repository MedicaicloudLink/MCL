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
  //
  // 通知
  //
  /**
   * 获取未读通知数量
   * @param {* userid } params
   */
  GetNoticeNum (params) {
    return fetch('/user/noreadnum', params)
  },
  //
  // 用户
  //
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
   * 获取用户名字的缩写
   * @param {* string : 名字 } params
   */
  ChineseToShort (params) {
    return fetch('/user/shortpinyin', params)
  },
  //
  //
  //
  /**
   * 获取用户已参与的项目
   * @param {* userid } params
   */
  GetProjects (params) {
    return fetch('/project/rprojectlist', params)
  },
  /**
   * 获取项目的具体信息
   * @param {* projectid userid} params
   */
  GetProjectInfo (params) {
    return fetch('/project/projectdetail', params)
  },
  /**
   * 记录打开项目的时间
   *  @param {*userid projectid } params
   */
  OpenProjectTime (params) {
    return fetch('/project/ropenproject', params)
  },
  //
  /**
   * 当天提交和保存的患者数量
   * @param {* projectid  userid } params
   */
  TodayCount (params) {
    return fetch('/patient/rtodaynum', params)
  },
  /**
   * 根据入组时间段搜索项目下的患者
   * @param {* projectid  startjoin endjoin } params
   */
  SearchPatientJointime (params) {
    return fetch('/patient/searchpatientjointime', params)
  },
  /**
   * 获取我的工作日志
   * @param {* projectid userid type sort search pagenum} params
   */
  WorkData (params) {
    return fetch('/patient/rworkdata', params)
  },
  // 已保存/已提交/被退回的病例
  GetCaseHistory (params) {
    return fetch('/patient/rpatientlist', params)
  },
  /**
   * 删除保存列表中患者的病例
   * @param {* userid mdid} params
   */
  DeleteSavePatient (params) {
    return fetch('/patient/rdeletepatient', params)
  },
  // 已保存/已提交/被退回的各种状态下数量
  GetCaseState (params) {
    return fetch('/patient/rstatusnum', params)
  },
  // 全局搜索病例（中心）
  SearchCase (params) {
    return fetch('/patient/rsearchpatient', params)
  },
  /**
   * 创建患者基本信息
   * @param {* userid projectid patientbase} params
   */
  SavePatientBase (params) {
    return fetch('/patient/createrecord', params)
  },
  /**
   * 删除患者
   * @param {* userid mdid} params
   */
  DeletePatient (params) {
    return fetch('/patient/deletepatient', params)
  },
  // 创建，修改患者基本信息和数据
  SaveRecord (params) {
    return fetch('/patient/reditrecord', params)
  },
  /**
   * 获取患者基本信息
   * @param {* mdid} params
   */
  GetPatientBase (params) {
    return fetch('/patient/getpatientbase', params)
  },
  /**
   * 修改患者基本信息
   * @param {* userid projectid mdid patientbase} params
   */
  EditPatientBase (params) {
    return fetch('/patient/updatepatientbase', params)
  },
  /**
   * 保存或修改患者数据
   * @param {* mdid userid patientdata sourcedata status} params
   */
  SavePatientRecord (params) {
    return fetch('/patient/reditrecord', params)
  },
  /**
   * 获取患者数据
   * @param {* mdid} params
   */
  GetPatientRecord (params) {
    return fetch('/patient/rpatientdata', params)
  },
  /**
   * 创建备注
   * @param {* mdid userid note} params
   */
  CreateTextNote (params) {
    return fetch('/patient/rcreateremark', params)
  },
  /**
   * 备注列表
   * @param {* mdid} params
   */
  GetNoteList (params) {
    return fetch('/patient/rremarklist', params)
  },
  /**
   * 删除备注
   * @param {* userid, nodeid} params
   */
  DeleteTextNote (params) {
    return fetch('/patient/rdeleteremark', params)
  },
  /**
   * 上传图片
   * @param {* patientid userid filename} params
   */
  PutImage (params, config) {
    return fetch('/user/picture', params, config)
  },
  /**
   * 上传文件
   * @param {* patientid userid filename} params
   */
  UpFile (params, config) {
    return fetch('/patient/rupfile', params, config)
  },
  /**
   * 上传文件备注
   * @param {* userid data[fileid,note]} params
   */
  AddFileNote (params) {
    return fetch('/patient/raddfilenote', params)
  },
  /**
   * 文件列表
   * @param {* patientid} params
   */
  GetFileList (params) {
    return fetch('/patient/rfilelist', params)
  },
  /**
   * 删除文件及文件备注
   * @param {* userid, fileid} params
   */
  DeleteFile (params) {
    return fetch('/patient/rdeletefile', params)
  }
}

