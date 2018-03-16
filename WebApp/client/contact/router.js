import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App.vue'
import Home from './views/Home'
// import Login from './views/Login'
import Contacts from './views/Contacts'
import Profile from './views/Profile'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: App,
      redirect: {name: 'home', params: {pagenum: 1}},
      meta: { requiresAuth: true, title: '联系人' }
    },
    {
      name: 'home',
      path: '/home/:pagenum',
      component: Home,
      meta: { requiresAuth: true, title: '联系人' }
    },
    {
      name: 'profile',
      path: '/profile/:id',
      component: Profile,
      meta: { requiresAuth: true, title: '个人资料' }
    },
    {
      name: 'contacts',
      path: '/contacts',
      component: Contacts,
      meta: { requiresAuth: true, title: '所有联系人' }
    },
    // { path: '/login', component: Login },
    { path: '*', redirect: {name: 'home', params: {pagenum: 1}} }
  ]
})

export default router
