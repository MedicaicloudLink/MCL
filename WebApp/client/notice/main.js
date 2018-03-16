import Vue from 'vue'
import App from './App'
import global from './data'
import './assets/main.css'
import './assets/iconfont/iconfont.css'

// 全局提示
import Toast from './lib/toast'
Vue.prototype.toast = Toast
// 全局对话框提示
import Confirm from './lib/confirm'
Vue.prototype.confirm = Confirm
import API from './api.js'
Vue.prototype.$http = API
// 注册过滤器
import * as filters from './utils/filter'
Object.keys(filters).forEach(k => Vue.filter(k, filters[k]))

/* eslint-disable no-new */
new Vue({
  el: '#app',
  mixins: [global],
  template: '<App/>',
  components: { App }
})

