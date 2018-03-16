import Vue from 'vue'
import App from './App'
import router from './router.js'
import './assets/main.css'
import ga from './tool/analytics.js'

// 注册过滤器
import * as filters from './tool/filter'
Object.keys(filters).forEach(k => Vue.filter(k, filters[k]))

// 全局提示
import Toast from './lib/toast'
Vue.prototype.toast = Toast
import Confirm from './lib/confirm.js'
Vue.prototype.confirm = Confirm

// 路由切换之前全局判断
router.beforeEach((to, from, next) => {
  // google analytics 数据追踪
  ga('set', 'page', '/site' + to.fullPath)
  ga('send', 'pageview')

  if (to.matched.some(record => record.meta.title)) {
    document.title = to.meta.title
  }
  next()
})

/* eslint-disable no-new */
new Vue({
  router,
  el: '#app',
  data () {
    return {
    }
  },
  template: '<App/>',
  components: { App }
})
