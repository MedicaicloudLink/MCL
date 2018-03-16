import Vue from 'vue'
import App from './App'
import router from './router.js'
import global from './data'
import './assets/main.css'
import './assets/icon/iconfont.css'
import Components from './lib'
Vue.use(Components)

import API from './api.js'
Vue.prototype.$http = API

// 注册过滤器
import * as filters from './tool/filter'
Object.keys(filters).forEach(k => Vue.filter(k, filters[k]))

// 路由切换之前全局判断
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.title)) {
    document.title = to.meta.title
  }
  if (to.matched.some(record => record.meta.requiresAuth) && !window.sessionStorage.userid) {
    if (process.env.NODE_ENV === 'development') {
      next({path: '/login'})
    } else {
      window.location.href = window.location.origin + '/login/loginform?redirect=contact'
    }
  } else {
    next() // 确保一定要调用 next()66666665
  }
})

/* eslint-disable no-new */
new Vue({
  router,
  mixins: [global],
  el: '#app',
  template: '<App/>',
  components: { App }
})
