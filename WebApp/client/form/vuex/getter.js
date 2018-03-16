export default {
  isLogin (state) {
    return state.islogin
  },

  userInfo (state) {
    return state.userinfo
  },

  formInfo (state, id) {
    return state.formlist.filter(form => form.formid === id)
  }
}
