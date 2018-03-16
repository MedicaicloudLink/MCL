import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getter.js'
import actions from './actions.js'
import mutations from './mutation.js'
Vue.use(Vuex)

const state = {
  userinfo: {
    name: '',
    id: '',
    avatar: '',
    gender: '',
    email: '',
    work_address: '',
    abbr: ''
  },
  window_width: window.innerWidth,
  islogin: false,
  formlist: [],
  nowformid: '',
  nowforminfo: {
    name: '',
    state: '',
    sourcedata: []
  }
}

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations
})
