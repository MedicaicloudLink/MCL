import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App.vue'
import Home from './views/Home'
// import Login from './views/Login'
import Editor from './views/Editor'

Vue.use(VueRouter)

const router = new VueRouter({
  // mode: 'history',
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: App,
      redirect: {name: 'home'},
      meta: { requiresAuth: true, title: '报告表编辑' }
    },
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: { requiresAuth: true, title: '报告表编辑' }
    },
    {
      name: 'neweditor',
      path: '/editor/new',
      component: Editor,
      meta: { requiresAuth: true, title: '创建新表单' }
    },
    {
      name: 'editor',
      path: '/editor/:formid',
      component: Editor,
      meta: { requiresAuth: true, title: '编辑' }
    },
    // { path: '/login', component: Login },
    { path: '*', redirect: {name: 'home'} }
  ]
})

export default router
