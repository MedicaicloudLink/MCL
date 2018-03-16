<template>
  <div class="nav flex-row" :class="{navBG: navBGFlag}">
    <router-link to="/home" class="logo"><img src="../assets/home/logo.svg"></router-link>
    <div class="nav-right flex-row">
      <router-link to="/" class="home" v-if="$route.path === '/'">首页</router-link>
      <router-link to="/home" class="home" v-else>首页</router-link>
      <dropdown :background="'rgba(0,0,0,.87)'">
        <div slot="trigger" class="flex-row" style="height: 64px; cursor: pointer;"><i>我们的产品和方案</i><img style="margin-left: 4px;" src="../assets/home/icon_triangle.svg"></div>
        <div class="dropdown-content">
          <ul style="border-bottom: 1px solid rgba(255,255,255,.12);">
            <li><router-link to="/CRF_more">报告表编辑</router-link></li>
            <li><router-link to="/register_more">患者登记</router-link></li>
            <li><router-link to="/follow_more">随访记录</router-link></li>
            <li><router-link to="/mag_more">临床研究管理</router-link></li>
          </ul>
          <ul>
            <li><router-link to="/personal_mag">面向个人的临床数据管理（免费）</router-link></li>
            <li><router-link to="/group_mag">小型临床研究团队数据管理解决方案</router-link></li>
            <li><router-link to="/CRO_mag">面向CRO的EDC方案</router-link></li>
            <li><router-link to="/patient_mag">医疗服务组织的患者关系管理</router-link></li>
          </ul>
        </div>
      </dropdown>
      <router-link to="/about_our" class="about-our">关于我们</router-link>
      <a class="btn-blue" style="margin-right: 16px;" href="/login/loginform" target="_blank">登录</a>
      <a class="btn-gray" href="/login/registershow" target="_blank">注册</a>
    </div>
  </div>
</template>
<script>
  import Dropdown from '../lib/Dropdown.vue'
  export default {
    name: 'Nav',
    components: {
      Dropdown
    },
    data () {
      return {
        navBGFlag: false,
        flag: false
      }
    },
    props: {
      scrollTop: String
    },
    mounted () {
      // 侧边栏随页面滚动
      if (this.scrollTop === '0') {
        this.navBGFlag = true
        return
      }
      this._scrollEvent = window.addEventListener('scroll', this.scrollEvent, false)
    },
    destroyed () {
      // 清除监听
      if (this._scrollEvent) this._scrollEvent.remove()
    },
    methods: {
      scrollEvent () {
        let scrollTop
        if (document.body.scrollTop) scrollTop = document.body.scrollTop
        else scrollTop = document.documentElement.scrollTop
        if (scrollTop >= this.scrollTop) {
          this.navBGFlag = true
        } else {
          this.navBGFlag = false
        }
      }
    }
  }
</script>
<style scoped>
  .nav {
    position: fixed;
    width: 100%;
    height: 64px;
    justify-content: space-between;
    padding: 0 32px;
  }
  .nav.navBG {
    background: rgba(0,0,0,.87);
  }
  .nav-right, .nav-right a{
    color: rgba(255,255,255,1);
  }
  .dropdown-content {
    padding: 10px 8px;
    width: 256px;
  }
  .dropdown-content ul li{
    padding: 6px 8px;
    cursor: pointer;  
  }
  .dropdown-content ul li a:hover {
    color: #fff;
  }
  .nav-right a.btn-gray, .nav-right a.btn-blue {
    color: #fff;
  }
  .btn-gray:hover{
    background: rgba(255,255,255,.12);
  }
  .home{
    margin-right: 32px;
  }
  .about-our{
    margin: 0 32px;
    cursor: pointer;
  }
  .router-link-active {
    color: #458df1 !important;
  }
</style>