import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App'
import Login from './views/Login'
import Home from './views/Home'
import Project from './views/Project.vue'
import WorkData from './views/table/Workdata.vue'
import SaveList from './views/table/SaveList.vue'
import CommitList from './views/table/CommitList.vue'
import BackList from './views/table/BackList.vue'
import Search from './views/table/Search.vue'
import Record from './views/Record_new.vue'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: App,
      redirect: {name: 'home'}, //  重定向
      meta: {requiresAuth: true, title: '患者登记'}
    },
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: {requiresAuth: true, title: '患者登记'}
    },
    {
      path: '/project/:projectid',
      component: Project,
      meta: {requiresAuth: true, title: '项目'},
      children: [
        {
          name: 'newrecord',
          path: 'patient/new',
          component: Record,
          meta: {requiresAuth: true, title: '创建新患者病例'}
        },
        {
          name: 'record',
          path: 'patient/:mdid',
          component: Record,
          meta: { requiresAuth: true, title: '编辑患者病例' }
        },
        {
          name: 'workdata-record',
          path: 'workdata/patient/:mdid',
          component: Record,
          meta: { requiresAuth: true, title: '日志-编辑患者病例' }
        },
        {
          name: 'savelist-record',
          path: 'savelist/patient/:mdid',
          component: Record,
          meta: { requiresAuth: true, title: '保存-编辑患者病例' }
        },
        {
          name: 'commitlist-record',
          path: 'commitlist/patient/:mdid',
          component: Record,
          meta: { requiresAuth: true, title: '提交-查看患者病例' }
        },
        {
          name: 'backlist-record',
          path: 'backlist/patient/:mdid',
          component: Record,
          meta: { requiresAuth: true, title: '退回-编辑患者病例' }
        },
        {
          name: 'workdata',
          path: 'workdata',
          component: WorkData,
          meta: {requiresAuth: true, title: '我的工作日志'}
        },
        {
          name: 'savelist',
          path: 'savelist',
          component: SaveList,
          meta: {requiresAuth: true, title: '已保存的病例'}
        },
        {
          path: 'commitlist',
          component: CommitList,
          meta: {requiresAuth: true, title: '已提交的病例'}
        },
        {
          path: 'backlist',
          component: BackList,
          meta: {requiresAuth: true, title: '被退回的病例'}
        },
        {
          name: 'search',
          path: 'search',
          component: Search,
          meta: {requiresAuth: true, title: '搜索病例'}
        }
      ]
    },
    { path: '/login', component: Login },
    { path: '*', redirect: '/home' }
  ]
})

export default router
