import Api from '../api.js'

export default {
  login: ({ dispatch, commit, state }, { data }) => {
    Api.Login(data)
      .then(userbase => {
        commit('LOGIN', {userbase})
        dispatch('checkLoginState')
        return Promise.resolve('login ok')
      })
      .catch(err => console.error(err))
  },
  get_userinfo: ({ dispatch, commit, state }) => {
    Api.GetUserinfo({userid: state.userinfo.id})
      .then(userinfo => {
        commit('SET_USER', {userinfo})
        dispatch('get_name_abbr')
      })
      .catch(err => console.error(err))
  },
  get_form_list: ({ dispatch, commit, state }) => {
    Api.GetFormList({userid: state.userinfo.id})
      .then(formlist => {
        commit('SET_FORM_LIST', {formlist})
      })
      .catch(err => console.error(err))
  },
  get_form_info: ({ dispatch, commit, state }, fromid) => {
    Api.GetFormInfo({userid: state.userinfo.id, fromid: fromid})
      .then(forminfo => {
        commit('SET_NOW_FORM_INFO', forminfo)
      })
      .catch(err => console.error(err))
  },
  get_name_abbr: ({ dispatch, commit, state }, fromid) => {
    Api.ChineseToShort({string: state.userinfo.name})
      .then(abbr => {
        commit('SET_USER_NAME_ABBR', {abbr})
      })
      .catch(err => console.error(err))
  },
  checkLoginState: ({ dispatch, commit, state }) => {
    const id = window.sessionStorage.getItem('userid')
    const token = window.sessionStorage.getItem('authorization')
    if (id && token) {
      commit('SET_LOCAL_USER_BASE')
      commit('IS_LOGIN')
      dispatch('get_userinfo')
      dispatch('get_form_list')
      return true
    } else {
      return false
    }
  }
}
