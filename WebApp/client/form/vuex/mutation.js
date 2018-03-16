export default {
  LOGIN: (state, {userbase}) => {
    state.userinfo.id = userbase.userid
    state.userinfo.token = userbase.token
    window.sessionStorage.setItem('userid', state.userinfo.id)
    window.sessionStorage.setItem('authorization', state.userinfo.token)
  },
  SET_LOCAL_USER_BASE: (state) => {
    state.userinfo.id = window.sessionStorage.getItem('userid')
    state.userinfo.token = window.sessionStorage.getItem('authorization')
  },
  SET_USER: (state, {userinfo}) => {
    state.userinfo.name = userinfo[0].s_username
    state.userinfo.avatar = userinfo[0].s_avatar
    state.userinfo.work_address = userinfo[0].s_workunti
  },
  SET_USER_NAME_ABBR: (state, {abbr}) => {
    state.userinfo.abbr = abbr.shortpinyin.toUpperCase()
  },
  SET_FORM_LIST: (state, {formlist}) => {
    state.formlist = formlist
  },
  SET_NOW_FORM_INFO: (state, {forminfo}) => {
    state.forminfo.name = forminfo.name
    state.forminfo.source = forminfo.sourcedata
  },
  IS_LOGIN: (state) => {
    state.islogin = state.userinfo.id !== ''
  }
}
