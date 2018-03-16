import Vue from 'vue'
import App from './App'
import router from './router.js'
import global from './data'
import 'assets/main.css'
import ga from './utils/analytics.js'

// 注册过滤器
import * as filters from './utils/filter'
Object.keys(filters).forEach(k => Vue.filter(k, filters[k]))

// 全局提示
import Toast from './lib/toast'
Vue.prototype.toast = Toast

// 全局对话框提示
import Confirm from './lib/confirm.js'
Vue.prototype.confirm = Confirm

import API from './api.js'
Vue.prototype.$http = API

// 全局对话框提示
import echarts from 'echarts'
Vue.prototype.$echarts = echarts

// 路由切换之前全局判断
router.beforeEach((to, from, next) => {
  // google analytics 数据追踪
  ga('set', 'page', '/pc' + to.fullPath)
  ga('send', 'pageview')
  if (to.matched.some(record => record.meta.title)) {
    document.title = to.meta.title
  }

  if (to.matched.some(record => record.meta.requiresAuth)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (window.sessionStorage) {
      // 获取 sessionStorage 中的 userid 和 token
      let userid = window.sessionStorage.getItem('userid')
      let authorization = window.sessionStorage.getItem('authorization')

      // 验证,设置token
      if (userid !== null && userid !== '' && authorization !== null && authorization !== '') {
        // console.log('登录成功' + userid)
        next()
      } else {
        if (process.env.NODE_ENV === 'development') {
          next({path: '/login'})
        } else {
          window.location.href = window.location.origin + '/?redirect=pc'
        }
      }
    }
  } else {
    next() // 确保一定要调用 next()
  }
})

/* eslint-disable no-new */
new Vue({
  router,
  el: '#app',
  mixins: [global],
  template: '<App/>',
  components: { App }
})

