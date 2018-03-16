import axios from 'axios'

// axios config
axios.defaults.timeout = 10000

// 返回状态判断
axios.interceptors.response.use((res) => {
  console.log(res)
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
    // console.log(process.env.NODE_ENV)
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
  /**
   * 编辑用户信息
   * @param {* userInfo} params
   */
  EditUserInfo (params) {
    return fetch('/user/usersetting', params)
  },
  /**
   * 上传图片
   * @param {* UploadForm[file] } params
   */
  PutImage (params) {
    return fetch('/user/picture', params)
  },
  /**
   * 修改用户密码
   * @param {* userid  oldpassword newpassword repassword} params
   */
  ResetPassWord (params) {
    return fetch('/user/uppassword', params)
  },
  //
  //
  //
  /**
   * 获取未读通知数量
   * @param {* userid } params
   */
  GetNoticeNum (params) {
    return fetch('/user/noreadnum', params)
  },
  //
  //
  /**
   * 获取用户参与的项目
   * @param {* userid } params
   */
  GetMyProjects (params) {
    return fetch('/project/pcprojectlist', params)
  },
  /**
   * 获取表单列表
   * @param {* userid , type="pulish(公布)"} params
   */
  GetFormList (params) {
    return fetch('/user/formlist', params)
  },
  /**
   * 创建项目
   * @param {* userid name templateid projectmem starttime endtime status} params
   */
  CreateProject (params) {
    return fetch('/user/addproject', params)
  },
  /**
   * 获取项目的具体信息
   * @param {* projectid userid} params
   */
  GetProjectInfo (params) {
    return fetch('/project/projectdetail', params)
  },
  /**
   * 结束项目
   * @param {* projectid(name) userid} params
   */
  EndProject (params) {
    return fetch('/user/endproject', params)
  },
  /**
   * 编辑项目
   * @param {* userid projectid params} params
   */
  EditProject (params) {
    return fetch('/project/editproject', params)
  },
  /**
   * 删除项目
   * @param {* userid projectid } params
   */
  DeleteProject (params) {
    return fetch('/project/deleteproject', params)
  },
  //
  //
  // 统计部分
  /**
   * 基线统计
   *  @param {*userid projectid centerid} params
   */
  BaselineCount (params) {
    return fetch('/patient/baselinestatistics', params)
  },
  /**
   * 随访统计
   *  @param {*userid projectid} params
   */
  FollowCount (params) {
    return fetch('/follow/followstatistics', params)
  },
  //
  //
  // 患者管理部分
  //
  /**
   * 根据姓名搜索项目下的患者
   * @param {* projectid  name } params
   */
  SearchPatientName (params) {
    return fetch('/patient/searchpatientname', params)
  },
  /**
   * 根据入组时间段搜索项目下的患者
   * @param {* projectid  startjoin endjoin } params
   */
  SearchPatientJointime (params) {
    return fetch('/patient/searchpatientjointime', params)
  },
  /**
   * 获取患者列表
   * @param {* projectid  pagenum userid type} params
   */
  GetPatients (params) {
    return fetch('/patient/getprojectpatients', params)
  },
  /**
   * 删除患者
   * @param {* userid  mdids } params
   */
  DeletePatients (params) {
    return fetch('/patient/deletepatient', params)
  },
  /**
   * 新建分组
   * @param {* userid projectid groupname} params
  */
  CreateGroup (params) {
    return fetch('/patient/creategroup', params)
  },
  /**
   * 搜索分组
   * @param {* userid projectid groupname} params
  */
  SearchGroup (params) {
    return fetch('/patient/searchgroup', params)
  },
  /**
   * 修改组名
   * @param {* userid groupid groupname} params
  */
  EditGroupName (params) {
    return fetch('/patient/updategroup', params)
  },
  /**
   * 删除组
   * @param {* groupid} params
  */
  DeleteGroup (params) {
    return fetch('/patient/deletegroup', params)
  },
  /**
   * 获取患者的分组
   * @param {* userid } params
  */
  GetGroups (params) {
    return fetch('/patient/grouplist', params)
  },
  /**
   * 获取分组下的患者
   * @param {* groupid pagenum} params
  */
  GetGroupPatients (params) {
    return fetch('/patient/grouppatientlist', params)
  },
  /**
   * 添加患者到选中的组
   * @param {* userid groupid patients} params
  */
  AddGroupPatient (params) {
    return fetch('/patient/addgrouppatient', params)
  },
  /**
   * 添加患者到选中的组
   * @param {* groupid patients} params
  */
  DeleteGroupPatient (params) {
    return fetch('/patient/deletegrouppatient', params)
  },
  /**
   * 批量审查通过
   * @param {* userid mdid projectid} params
  */
  CheckPass (params) {
    return fetch('/patient/passcheck', params)
  },
  /**
   * 审查病历
   * @param {* userid mdid projectid status remark} params
  */
  CheckRecord (params) {
    return fetch('/patient/checkpatient', params)
  },
  /**
   * 获取项目下所有任务
   * @param {* userid projectid } params
   */
  GetTask (params) {
    return fetch('/project/tasklist', params)
  },
  /**
   * 获取患者的基本信息
   * @param {* mdid } params
   */
  GetPatientBase (params) {
    return fetch('/patient/getpatientbase', params)
  },
  /**
   * 获取患者病历
   * @param {* mdid templateid} params
   */
  GetPatientRecord (params) {
    return fetch('/patient/getrecords', params)
  },
  /**
   * 备注列表
   * @param {* mdid} params
   */
  GetNoteList (params) {
    return fetch('/patient/rremarklist', params)
  },
  /**
   * 文件列表
   * @param {* patientid} params
   */
  GetFileList (params) {
    return fetch('/patient/rfilelist', params)
  },
  /**
   * 获取患者随访病例数据
   * @param {* mdid taskid} params
   */
  GetPatientFollow (params) {
    return fetch('/follow/mpatientdata', params)
  },
  /**
   * 随访备注列表
   * @param {* recordid} params
   */
  GetFollowNote (params) {
    return fetch('/follow/fremarklist', params)
  },
  /**
   * 随访文件列表
   * @param {* recordid} params
   */
  GetFollowFile (params) {
    return fetch('/follow/ffilelist', params)
  },
  /**
   * 随访病例审查
   * @param {* userid projectid status remark recordid} params
   */
  FollowCheck (params) {
    return fetch('/follow/checkpatient', params)
  },
  /**
   * 患者历史记录
   * @param {* mdid} params
   */
  FollowLog (params) {
    return fetch('/follow/fpatientrecord', params)
  },
  //
  //
  //
  // 项目管理部分
  //
  /**
   * 获取项目的分中心
   * @param {* projectid userid} params
   */
  GetProjectCenter (params) {
    return fetch('/project/centerlistbyprojectid', params)
  },
   /**
   * 创建项目的分中心
   * @param {* projectid userid projectcentercontant} params
   */
  CreateCenter (params) {
    return fetch('/project/createcenter', params)
  },
  /**
   * 删除项目的分中心
   * @param {* projectid userid centerid} params
   */
  DeleteCenter (params) {
    return fetch('/project/deletecenter', params)
  },
  /**
   * 编辑项目的分中心
   * @param {* projectid userid centermsg} params
   */
  EditCenter (params) {
    return fetch('/project/editcentermsg', params)
  },
  /**
   * 项目参与的成员
   * @param {* projectid userid } params
   */
  GetMember (params) {
    return fetch('/project/getmemberlistbyprojectid', params)
  },
  /**
   * 搜索用户信息
   * @param {* projectid userid mobile} params
   */
  FindUserInfo (params) {
    return fetch('/project/searchuser', params)
  },
  /**
   * 邀请注册
   * @param {* userid projectid mobile} params
   */
  AddUser (params) {
    return fetch('/user/inviteuser', params)
  },
  /**
   * 添加联系人邀请
   * @param {* projectid userid touserid} params
   */
  AddContact (params) {
    return fetch('/contact/addcontacter', params)
  },
  // /**
  //  * 申请加入项目
  //  *  @param {*userid projectid } params
  //  */
  // ApplyProject (params) {
  //   return fetch('/project/applyproject', params)
  // },
  // /**
  //  * 申请列表
  //  *  @param {*userid projectid } params
  //  */
  // GetApplyList (params) {
  //   return fetch('/user/applyprojectlist', params)
  // },
  /**
   * 邀请加入项目
   * @param {* projectid userid touserid} params
   */
  AddProject (params) {
    return fetch('/project/inviteprojectuser', params)
  },
  /**
   * 删除项目成员
   * @param {* projectid userid memberid} params
   */
  DeleteProjectMember (params) {
    return fetch('/project/removememberinproject', params)
  },
  /**
   * 添加项目成员到分中心
   * @param {* projectid userid memberid centerid} params
   */
  AddMemberToCenter (params) {
    return fetch('/project/addcentermember', params)
  },
  /**
   * 从分中心中移除项目成员
   * @param {* projectid userid memberid centerid} params
   */
  RemoveMemberInCenter (params) {
    return fetch('/project/removememberincenter', params)
  },
  /**
   * 添加项目管理员
   * @param {* projectid userid memberid } params
   */
  AddAdmin (params) {
    return fetch('/project/membertoadmin', params)
  },
  /**
   * 移除项目管理员
   * @param {* projectid userid memberid } params
   */
  DeleteAdmin (params) {
    return fetch('/project/memberremoveadmin', params)
  },
  /**
   * 获取项目任务列表
   * @param {* projectid userid } params
   */
  GetTaskList (params) {
    return fetch('/project/tasklist', params)
  },
  /**
   * 给用户分配任务
   * @param {* projectid userid admin taskid} params
   */
  AddTask (params) {
    return fetch('/project/addusertask', params)
  },
  //
  //
  //
  // 随访计划
  //
  /**
   * 获取用户参与项目对应的随访计划列表
   * @param {* userid } params
   */
  GetFollows (params) {
    return fetch('/follow/showfollows', params)
  },
  /**
   * 创建随访计划
   * @param {* userid projectid followContent} params
   */
  CreateFollow (params) {
    return fetch('/follow/createfollow', params)
  },
  /**
   * 编辑随访
   * @param {* userid projectid taskid taskname taskformid taskmonth taskcontent}
   */
  EditFollow (params) {
    return fetch('/follow/editfollow', params)
  },
  /**
   * 删除随访
   * @param {* userid projectid followid} params
   */
  DeleteFollow (params) {
    return fetch('/follow/deletefollow', params)
  }
}
