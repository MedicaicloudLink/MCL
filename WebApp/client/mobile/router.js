import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App.vue'
import Home from './views/Home'
// import Login from './views/Login'
import Create from './views/Create'
import Patients from './views/Patients'
import Notice from './views/Notice.vue'

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
      name: 'notice',
      path: '/notice',
      component: Notice,
      meta: { requiresAuth: true, title: '通知' }
    },
    {
      name: 'PatientList',
      path: '/project/:projectid/patients/:pagenum',
      component: Patients,
      meta: { requiresAuth: true, title: '患者列表' }
    },
    {
      name: 'Patiens',
      path: '/project/:projectid/patient/:mdid',
      component: Create,
      meta: { requiresAuth: true, title: '患者信息' }
    },
    // {path: '/login', component: Login},
    { path: '*', redirect: {name: 'home'} }
  ]
})

export default router
