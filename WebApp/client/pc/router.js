import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import App from './App'
import Login from './views/Login'
import Home from './views/Home'
import Project from './views/Project.vue'
import ProjectHome from './views/Projecthome.vue'
import PatientList from './views/Patientlist.vue'
import PatientDetail from './views/Patientdetail.vue'
import MemberManagement from './views/Membermanagement.vue'
import CenterManagement from './views/Centermanagement.vue'
import FollowPlan from './views/Followplan.vue'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: App,
      redirect: {name: 'home'}, //  重定向
      meta: {requiresAuth: true, title: '临床研究管理'}
    },
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: {requiresAuth: true, title: '临床研究管理'}
    },
    {
      path: '/project/:projectid',
      component: Project,
      meta: {requiresAuth: true},
      children: [
        {
          path: 'project_home',
          component: ProjectHome,
          meta: {requiresAuth: true, title: '项目首页'}
        },
        {
          path: 'patient_list',
          component: PatientList,
          meta: {requiresAuth: true, title: '患者病例'}
        },
        {
          path: 'patient_list/:groupid/:pagenum',
          name: 'patientList',
          component: PatientList,
          meta: {requiresAuth: true, title: '患者病例'}
        },
        {
          path: 'patient_list/:groupid/:pagenum/patient_detail/:MDID',
          component: PatientDetail,
          name: 'patientDetail',
          meta: {requiresAuth: true, title: '患者详情'}
        },
        {
          path: 'member_management',
          component: MemberManagement,
          meta: {requiresAuth: true, title: '成员管理'}
        },
        {
          path: 'center_management',
          component: CenterManagement,
          meta: {requiresAuth: true, title: '分中心管理'}
        },
        {
          path: 'follow_plans',
          component: FollowPlan,
          meta: {requiresAuth: true, title: '随访计划'}
        }
      ]
    },
    {
      path: '/login',
      component: Login,
      meta: {title: '登录'}
    },
    { path: '*', redirect: '/home' }
  ]
})

export default router
