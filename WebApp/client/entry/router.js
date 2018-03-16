import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App.vue'
import Home from './views/Home'
import Profile from './views/Profile.vue'
import Resetpw from './views/Resetpw.vue'
import Avatar from './views/Avatar.vue'
// import Login from './views/Login'

Vue.use(VueRouter)

const router = new VueRouter({
  // mode: 'history',
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: App,
      redirect: {name: 'home'},
      meta: { requiresAuth: true, title: '主页' }
    },
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: { requiresAuth: true, title: '主页' }
    },
    {
      name: 'profile',
      path: '/profile',
      component: Profile,
      meta: { requiresAuth: true, title: '个人信息' }
    },
    {
      name: 'resetpw',
      path: '/resetpw',
      component: Resetpw,
      meta: { requiresAuth: true, title: '修改密码' }
    },
    {
      name: 'avatar',
      path: '/avatar',
      component: Avatar,
      meta: { requiresAuth: true, title: '修改头像' }
    },
    // { path: '/login', component: Login },
    { path: '*', redirect: {name: 'home'} }
  ]
})

export default router
