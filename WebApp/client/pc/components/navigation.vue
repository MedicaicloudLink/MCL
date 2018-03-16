<template>
  <div id="nav" class="flex-row">
    <div class="flex-row left">
      <img @click="go('home')" src="../assets/header_svg/logo.svg" alt="Medicayun" class="logo">
      <router-link to='/home' class="appname">临床研究管理</router-link>
    </div>
    <div class="flex-row right">
      <i @click="go('notice')" class="iconfont icon-tongzhi">
        <span v-if="$root.noticeNum > 0" class="notice">{{$root.noticeNum}}</span>
      </i>
      <i @click="go('contact')" class="iconfont icon-user" style="font-size: 18px;"></span></i>
      <i class="iconfont icon-caidan-copy" @click.stop="changeCloud"></i>
      <div class="user"  @click.stop="showConfig">
        <template v-if="$root.avatar">
          <img :src="'http://' + $root.avatar" alt="userImg" class="small-avatar">
        </template>
        <span class="small-avatar" v-else>{{ $root.shortname.substring(0, 3) | uppercase}}</span>
      </div>
    </div>
    <Popover ref="config" class="config flex-col">
      <div class="user-info flex-row">
        <span class="large-avatar">{{ $root.shortname.substring(0, 3) | uppercase }}</span>
        <div class="flex-col" style="flex: 1;">
          <span class="user-name">{{ $root.username }}</span>
          <span class="user-work-unit">{{ $root.hospital }}</span>
        </div>
      </div>
      <div class="link-list flex-col">
        <a href="/user/home#/profile" target="_blank" class="link-item">用户设置</a>
        <a href="/user/home#/profile" target="_blank" class="link-item">用户帮助</a>
      </div>
      <div class="exit-btn"><m-button type="blue" @click="exit">退出</m-button></div>
    </Popover>
    <popover ref="cloud" class="cloud flex-row">
      <div class="flex-col link-apply" @click="go('mobilehome')">
        <img src="../assets/header_svg/icon_mednotes.svg" alt="mednotes">
        <span>患者登记</span>
      </div>
      <div class="flex-col link-apply" @click="go('follow')">
        <img src="../assets/header_svg/icon_follow.svg" alt="follow">
        <span>随访记录</span>
      </div>
      <div class="flex-col link-apply" @click="go('pchome')">
        <img src="../assets/header_svg/icon_manage.svg" alt="manage">
        <span>临床研究管理</span>
      </div>
      <div class="flex-col link-apply" @click="go('eform')">
        <img src="../assets/header_svg/icon_form.svg" alt="form">
        <span>报告表编辑</span>
      </div>
      <div class="flex-col link-apply">
        <img src="../assets/header_svg/icon_imaging.svg" alt="imaging">
        <span style="color: rgb(170, 170, 170)">影像中心</span>
      </div>
    </popover>
  </div>
</template>
<script>
  import MButton from '../lib/button.vue'
  import Popover from '../lib/Popover.vue'
  export default {
    name: 'Nav',
    components: {
      MButton,
      Popover
    },
    methods: {
      // 显示用户配置dialog
      showConfig (event) {
        this.$refs.config.show(event.currentTarget)
        this.$refs.cloud.hide()
      },
      // 显示云dialog
      changeCloud (event) {
        this.$refs.cloud.show(event.currentTarget)
        this.$refs.config.hide()
      },
      go (siteAddress) {
        window.location.href = window.location.origin + '/user/' + siteAddress
      },
      /* 退出 */
      exit () {
        window.sessionStorage.clear()
        if (process.env.NODE_ENV === 'development') {
          this.$router.push({path: '/login'})
        } else {
          window.location.href = window.location.origin + '/login/loginform?redirect=pc'
        }
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  /* 导航条固定定位 */
  #nav {
    background: #468df1;
    width: 100%;
    height: 56px;
    justify-content: space-between;
    position: fixed;
    color: #fff;
    top: 0;
    left: 0;
    z-index: 999;
  }
  .left, .right{
    align-items: center;
  }
  .left .logo {
    width: 40px;
    height: 32px;
    margin-left: 12px;
    margin-right: 14px;
    cursor: pointer;
  }
  .left .appname {
    font-size: 20px;
    color: #fff;
  }
  .right i.iconfont{
    cursor: pointer;
    display: inline-block;
    height: 56px;
    line-height: 56px;
    width: 56px;
    text-align: center;
    position: relative;
    font-size: 16px;
  }
  .right i.iconfont:hover {
    background: rgba(0, 0, 0, .2);
  }
  .right .notice{
    position: absolute;
    display: block;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #e51c23;
    right: 3px;
    top: 8px;
    font-size: 12px;
    text-align: center;
    line-height: 18px;
  }
  .right .user {
    width: 56px;
    height: 56px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .right .small-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    text-align: center;
    line-height: 32px;
    font-size: 12px;
    color: #000;
  }

  .config .user-info{
    height: 102px;
    width: 300px;
    padding: 0 24px;
    border-bottom: 1px solid rgb(227, 227, 227);
    background: #fafafa;
    align-items: center;
  } 
  .config .user-info .large-avatar{
    width: 70px;
    height: 70px;
    background: rgb(70, 141, 241);
    line-height: 70px;
    color: #fff;
    font-size: 22px;
    margin-right: 12px;
    border-radius: 50%;
    text-align: center;
  }
  .config .user-info .user-name {
    font-size: 18px;
    line-height: 24px;
  }
  .config .user-info .user-work-unit {
    overflow : hidden;
    font-size: 14px;
    line-height: 24px;
  }
  .config .link-list{
    color: rgb(68, 68, 68);
    margin: 0 12px;
    padding: 12px;
    border-bottom: 1px solid rgb(227, 227, 227);
  }
  .config .link-list a.link-item {
    font-size: 18px;
    line-height: 2;    
  }
  .config .link-list a:hover{
    color: rgb(70, 141, 241)
  }
  .config .exit-btn {
   background: #fff;
   text-align: right;
   padding: 12px 12px 12px 0;
 }

 .config .exit-btn .button {
   font-size: 18px;
   background: #fff;
   color: #468df1;
   border: 1px solid #468df1;
 }

 .config .exit-btn .button:hover {
   background: #468df1;
   color: #fff;
 }
 .cloud{
   width: 338px;
   flex-wrap: wrap;
 }
 .cloud .link-apply {
   width: 90px;
   align-items: center;
   cursor: pointer;
   border: 1px solid transparent;
   padding: 8px 5px;
   margin: 15px 10px;
 }
 .cloud .link-apply:hover {
   border-color: #ddd;
 }
 .cloud .link-apply span {
   font-size: 13px;
   text-align: center;
   margin-top: 14px;
 }
</style>
