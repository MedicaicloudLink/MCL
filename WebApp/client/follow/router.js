import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App.vue'
import Home from './views/Home'
// import Login from './views/Login'
import Project from './views/Project'
import Work from './views/table/Work'
import Follow from './views/table/Follow'
import Save from './views/table/Save'
import Commit from './views/table/Commit'
import Back from './views/table/Back'
import Search from './views/table/Search'
import Patient from './views/Patient'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: App,
      redirect: {name: 'home'},
      meta: { requiresAuth: true, title: '随访记录' }
    },
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: { requiresAuth: true, title: '随访记录' }
    },
    {
      path: '/project/:projectid',
      component: Project,
      meta: {requiresAuth: true, title: '项目'},
      children: [
        {
          name: 'frecord',
          path: 'follow/:mdid/:taskid',
          component: Patient,
          meta: {requiresAuth: true, title: '随访'}
        },
        {
          name: 'wrecord',
          path: 'work/:mdid/:taskid/:recordid',
          component: Patient,
          meta: {requiresAuth: true, title: '随访'}
        },
        {
          name: 'srecord',
          path: 'save/:mdid/:taskid/:recordid',
          component: Patient,
          meta: {requiresAuth: true, title: '随访'}
        },
        {
          name: 'crecord',
          path: 'commit/:mdid/:taskid/:recordid',
          component: Patient,
          meta: {requiresAuth: true, title: '随访'}
        },
        {
          name: 'work',
          path: 'work',
          component: Work,
          meta: {requiresAuth: true, title: '我的工作日志'}
        },
        {
          name: 'follow',
          path: 'follow',
          component: Follow,
          meta: {requiresAuth: true, title: '应随访的病历'}
        },
        {
          name: 'save',
          path: 'save',
          component: Save,
          meta: {requiresAuth: true, title: '已保存'}
        },
        {
          name: 'commit',
          path: 'commit',
          component: Commit,
          meta: {requiresAuth: true, title: '已提交'}
        },
        {
          name: 'back',
          path: 'back',
          component: Back,
          meta: {requiresAuth: true, title: '被退回'}
        },
        {
          name: 'search',
          path: 'search',
          component: Search,
          meta: {requiresAuth: true, title: '搜索'}
        }
      ]
    },
    // { path: '/login', component: Login },
    { path: '*', redirect: {name: 'home'} }
  ]
})

export default router
