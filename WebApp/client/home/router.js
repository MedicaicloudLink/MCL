import Vue from 'vue'
import VueRouter from 'vue-router'
/** all link */
import Home from './views/Home'
import HomeMore from './views/Homemore'
import CRFMore from './views/CRFmore'
import RegisterMore from './views/Registermore'
import MagMore from './views/Magmore'
import FollowMore from './views/Followmore'
import AboutOur from './views/Aboutour'
import PersonalMag from './views/Personalmag'
import GroupMag from './views/Groupmag'
import CROMag from './views/CROmag'
import PatientMag from './views/Patientmag'
import Privacy from './views/Privacy'
import Use from './views/Use'

Vue.use(VueRouter)

const router = new VueRouter({
  // mode: 'history',
  mode: 'hash',
  routes: [
    {
      path: '/',
      component: Home,
      meta: { requiresAuth: true, title: '首页' }
    },
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: { requiresAuth: true, title: '首页' }
    },
    {
      name: 'homemore',
      path: '/home_more',
      component: HomeMore,
      meta: { requiresAuth: true, title: '首页-了解更多' }
    },
    {
      name: 'CRFmore',
      path: '/CRF_more',
      component: CRFMore,
      meta: { requiresAuth: true, title: 'CRF-了解更多' }
    },
    {
      name: 'registermore',
      path: '/register_more',
      component: RegisterMore,
      meta: { requiresAuth: true, title: '患者登记-了解更多' }
    },
    {
      name: 'magmore',
      path: '/mag_more',
      component: MagMore,
      meta: { requiresAuth: true, title: '管理-了解更多' }
    },
    {
      name: 'followmore',
      path: '/follow_more',
      component: FollowMore,
      meta: { requiresAuth: true, title: '随访-了解更多' }
    },
    {
      name: 'aboutOur',
      path: '/about_our',
      component: AboutOur,
      meta: { requiresAuth: true, title: '关于我们' }
    },
    {
      name: 'personalMag',
      path: '/personal_mag',
      component: PersonalMag,
      meta: { requiresAuth: true, title: '面向个人的临床数据管理' }
    },
    {
      name: 'groupMag',
      path: '/group_mag',
      component: GroupMag,
      meta: { requiresAuth: true, title: '小型临床研究团队数据管理解决方案' }
    },
    {
      name: 'CROMag',
      path: '/CRO_mag',
      component: CROMag,
      meta: { requiresAuth: true, title: '面向CRO的EDC方案' }
    },
    {
      name: 'patientMag',
      path: '/patient_mag',
      component: PatientMag,
      meta: { requiresAuth: true, title: '医疗服务组织的患者关系管理' }
    },
    {
      name: 'privacy',
      path: '/privacy',
      component: Privacy,
      meta: { requiresAuth: true, title: '隐私条款' }
    },
    {
      name: 'use',
      path: '/use',
      component: Use,
      meta: { requiresAuth: true, title: '使用条款' }
    }
  ]
})

export default router
