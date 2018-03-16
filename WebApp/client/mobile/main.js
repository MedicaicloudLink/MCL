import Vue from 'vue'
import App from './App'
import router from './router.js'
import './assets/main.css'
import ga from './utils/analytics.js'
import { Type } from './utils/tools.js'

// 注册过滤器
import * as filters from './utils/filter'
Object.keys(filters).forEach(k => Vue.filter(k, filters[k]))

// 全局提示
import Toast from './lib/toast'
Vue.prototype.toast = Toast
import Confirm from './lib/confirm.js'
Vue.prototype.confirm = Confirm
// 注册一个全局自定义指令 v-imgshow
import ImgModal from './lib/ImgModal.js'
Vue.directive('imgshow', {
  // 当绑定元素插入到 DOM 中。
  inserted: function (el) {
    el.addEventListener('click', (e) => {
      ImgModal(el.getAttribute('src'))
    })
  }
})

// 路由切换之前全局判断
router.beforeEach((to, from, next) => {
  // google analytics 数据追踪
  ga('set', 'page', '/mobile' + to.fullPath)
  ga('send', 'pageview')

  if (to.matched.some(record => record.meta.title)) {
    document.title = to.meta.title
  }

  // 获取 sessionStorage 中的 userid 和 token
  const userid = window.sessionStorage.getItem('userid')
  const authorization = window.sessionStorage.getItem('authorization')

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (Type(userid) !== 'string' && Type(authorization) !== 'string') {
      if (process.env.NODE_ENV === 'development') {
        next({path: '/login'})
      } else {
        window.location.href = window.location.origin + '/?redirect=mobile'
      }
    } else {
      next()
    }
  } else {
    next() // 确保一定要调用 next()
  }
})

/* eslint-disable no-new */
new Vue({
  router,
  el: '#app',
  data () {
    return {
      userid: window.sessionStorage.getItem('userid'),
      userInfo: {},
      shortName: '',
      currentProject: {}
    }
  },
  template: '<App/>',
  components: { App }
})
