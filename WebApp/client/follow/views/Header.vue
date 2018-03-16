<template>
  <div id="header">
    <div class="left">
      <div class="flex-row brand" @click="go('home')">
        <img src="../assets/appicon/logo.svg" alt="Medicayun Electronic Case Report Form">
      </div>
      <router-link class="product" to="/home" tag="span">随访记录</router-link>
    </div>
    <div class="right">
      <a href="/user/notice">
        <i class="iconfont icon-tongzhi">
          <span class="hint" v-if="$root.noticeNum > 0">{{ $root.noticeNum }}</span>
        </i>
      </a>
      <a href="/user/contact"><i class="iconfont icon-patient"></i></a>
      <i class="iconfont icon-caidan-copy" @click.stop="changeCloud"></i>
      <span class="avatar" id="avatar-pop" @click.stop="showConfig" v-if="$root.avatar === ''"><span>{{ $root.shortname.substring(0, 3) | uppercase }}</span></span>
      <span class="avatar" id="avatar-pop" @click.stop="showConfig" v-else><img :src="'http://' + $root.avatar" alt="" class="avatar-img"></span>
    </div>

    <popover ref="config" class="config flex-col">
      <div id="user-info" class="flex-row">
        <span class="avatar large">{{ $root.shortname.substring(0, 3) | uppercase }}</span>
        <div class="text flex-col" style="flex: 1;">
          <span class="name">{{ $root.username }}</span>
          <span class="work">{{ $root.hospital }}</span>
        </div>
      </div>
      <div class="link-list flex-col">
        <a href="/user/home#/profile" target="_blank">用户设置</a>
        <a href="/user/home#/profile" target="_blank">用户帮助</a>
      </div>
      <div class="exit"><m-button type="blue" @click="exit">退出</m-button></div>
    </popover>
    <popover ref="outsite" class="outsite">
      <div class="flex-col" @click="go('mobilehome')">
        <img src="../assets/appicon/icon_mednotes.svg" alt="患者登记">
        <span>患者登记</span>
      </div>
      <div class="flex-col" @click="go('follow')">
        <img src="../assets/appicon/follow.svg" alt="随访记录">
        <span>随访记录</span>
      </div>
      <div class="flex-col" @click="go('pchome')">
        <img src="../assets/appicon/icon_manage.svg" alt="临床研究管理">
        <span>临床研究管理</span>
      </div>
      <div class="flex-col" @click="go('eform')">
        <img src="../assets/appicon/icon_form.svg" alt="报告表编辑">
        <span>报告表编辑</span>
      </div>
      <div class="flex-col">
        <img src="../assets/appicon/icon_imaging.svg" alt="">
        <span style="color: #aaa;">影像中心</span>
      </div>
    </popover>

  </div>
</template>

<script>
  export default {
    name: 'Header',
    methods: {
      showConfig (event) {
        this.$refs.config.show(document.getElementById('avatar-pop'))
        this.$refs.outsite.hide()
      },
      changeCloud (event) {
        this.$refs.outsite.show(event.target)
        this.$refs.config.hide()
      },
      showContacts (event) {
        this.$refs.friend.show(event.target)
      },
      go (siteAddress) {
        window.location.href = window.location.origin + '/user/' + siteAddress
      },
      exit () {
        window.sessionStorage.clear()
        if (process.env.NODE_ENV === 'development') {
          this.$router.push({path: '/login'})
        } else {
          window.location.href = window.location.origin + '/login/loginform?redirect=follow'
        }
      }
    }
  }
</script>

<style scoped>
 #header {
   display: flex;
   justify-content: space-between;
   align-items: center;
   font-size: 18px;
   color: #fff;
   height: 56px;
   background: #468df1;
   box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, 0.03), 0 1px 5px 0 rgba(0, 0, 0, .12);
 }

 .brand {
   align-items: center;
   padding-left: 12px;
 }

 .brand img{
  display: inline-block;
  width: 40px;
  height: 31px;
  margin-right: 14px;
  cursor: pointer;
 }

 .product {
   display: inline-block;
   font-size: 20px;
   letter-spacing: 2px;
   cursor: pointer;
   font-family: "Microsoft YaHei";
 }

 #header .left {
   display: flex;
   align-items: center;
 }


 #header .right {
   display: flex;
   align-items: center;
 }

 #header .right>a {
   color: #fff;
 }

 #header .right i.iconfont {
   cursor: pointer;
   display: inline-block;
   height: 56px;
   line-height: 56px;
   width: 56px;
   border-left: 1px solid rgba(0, 0, 0, 0);
   text-align: center;
 }

 #header .right i.iconfont:hover {
   background: rgba(0, 0,0, .2);
   border-left: 1px solid #468df1;
 }

  .icon-tongzhi {
    position: relative;
  }

 .icon-tongzhi .hint {
   position: absolute;
   top: 6px;
   z-index: 100;
   right: 3px;
   background-color: #e74955;
   color: #fff;
   font-size: 12px;
   font-family: Helvetica;
   border-radius: 50%;
   text-align: center;
   padding: 0 6px;
   height: 18px;
   line-height: 18px;
   font-weight: bold;
 }

 .avatar {
   display: flex;
   align-items: center;
   justify-content: center;
   /*border-left: 1px solid rgba(0, 0, 0, .12);*/
   width: 56px;
   height: 56px;
   font-size: 12px;
   text-align: center;
   color: #222;
   cursor: pointer;
   overflow: hidden;
 }

 .avatar>span, .avatar-img {
   display: inline-block;
   height: 32px;
   width: 32px;
   line-height: 32px;
   background: #fff;
   border-radius: 50%;
 }

 .config {
   width: 288px;
   background: #fff;
 }

 .config #user-info {
   height: 102px;
   display: flex;
   align-items: center;
   padding: 0 24px;
   overflow-x: hidden;
   border-bottom: 1px solid #eee;
   background: #fafafa;
 }

 .config #user-info .avatar {
   height: 70px;
   width: 70px;
   background: #468df1;
   color: #fff;
   line-height: 70px;
   font-size: 22px;
   margin-left: 0;
   margin-right: 12px;
   border-radius: 50%;
 }

 .config #user-info .name {
   font-size: 18px;
   line-height: 24px;
 }

 .config #user-info .work {
   font-size: 14px;
   line-height: 24px;
 }
 .config .link-list {
   font-size: 18px;
   line-height: 24px;
   padding: 12px;
   background: #fff;
   margin: 0 12px;
   border-bottom: 1px solid #eee;
 }

 .config .link-list a{
   font-size: 18px;
   line-height: 2;
 }

 .config .exit {
   background: #fff;
   text-align: right;
   padding: 12px 12px 12px 0;
 }

 .config .exit .button {
   font-size: 18px;
   background: #fff;
   color: #468df1;
   border: 1px solid #468df1;
 }

 .config .exit .button:hover {
   background: #468df1;
   color: #fff;
 }
 
 #header .outsite{
   width: 324px;
   background: #fff;
   display: flex;
   flex-wrap: wrap;
 }

 .outsite>div {
   width: 80px;
   justify-content: center;
   align-items: center;
   cursor: pointer;
   border: 1px solid rgba(0, 0, 0, 0);
   margin: 20px 8px;
   padding: 4px;
 }

 .outsite>div:hover {
   border: 1px solid #ddd;
 }

 .outsite img {
   display: inline-block;
   line-height: 64px;
   margin-bottom: 14px;
 }

 .outsite span {
   font-size: 13px;
   text-align: center;
 }
</style>