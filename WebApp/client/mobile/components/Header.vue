<template>
  <div id="header">
    <div class="left">
      <div class="flex-row brand">
        <img src="../assets/logo.svg" alt="Medicayun Electronic Case Report Form">
        <router-link to="/home" tag="span">患者登记</router-link>
      </div>
    </div>
    <div class="right">
      <router-link to="/notice" tag="i" class="iconfont icon-tongzhi1"></router-link>
      <i class="iconfont icon-caidan-copy" @click.stop="changeCloud"></i>
      <span class="avatar" id="avatar-pop" @click.stop="showConfig" v-if="$root.userInfo.s_avatar === ''"><span>{{ $root.shortName.substring(0, 3) | uppercase }}</span></span>
      <span class="avatar" id="avatar-pop" @click.stop="showConfig" v-else><img :src="'http://' + $root.userInfo.s_avatar" alt="" class="avatar-img"></span>
    </div>

    <popover ref="config" class="config flex-col">
      <div id="user-info" class="flex-row">
        <span class="avatar large">{{ $root.shortName.substring(0, 3) | uppercase }}</span>
        <div class="text flex-col" style="flex: 1;">
          <span class="name">{{ $root.userInfo.s_username }}</span>
          <span class="work">{{ $root.userInfo.s_workunti }}</span>
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
        <img src="../assets/icon_mednotes.svg" alt="患者登记">
        <span style="font-size: 13px;text-align: center;">患者登记</span>
      </div>
      <div class="flex-col" @click="go('pchome')">
        <img src="../assets/icon_manage.svg" alt="临床研究管理">
        <span style="font-size: 13px;text-align: center;">临床研究管理</span>
      </div>
      <div class="flex-col" @click="go('eform')">
        <img src="../assets/icon_form.svg" alt="报告表编辑">
        <span style="font-size: 13px;text-align: center;">报告表编辑</span>
      </div>
      <div class="flex-col">
        <img src="../assets/icon_imaging.svg" alt="">
        <span style="font-size: 13px;text-align: center;color: #aaa;">影像中心</span>
      </div>
    </popover>

  </div>
</template>

<script>
  import API from '../api.js'
  import Popover from '../lib/Popover.vue'
  import MButton from '../lib/Button.vue'

  export default {
    name: 'Header',
    components: {
      MButton,
      Popover
    },
    data () {
      return {
        slidenavState: false
      }
    },
    created () {
      // 只有用户信息为空的时候请求（解决刷新，页面切换重复请求问题）
      if (Object.keys(this.$root.userInfo).length === 0) {
        this.getUserInfo()
      }
    },
    methods: {
      getUserInfo () {
        API.GetUserinfo({userid: this.$root.userid}).then(response => {
          this.nameToshort(response[0].s_username)
          this.$root.userInfo = response[0]
        }).catch(err => {
          console.log(err)
        })
      },
      nameToshort (str) {
        API.ChineseToShort({string: str}).then(response => {
          this.$root.shortName = response.shortpinyin
        }).catch(err => {
          console.log(err)
        })
      },
      changeNav () {
        this.slidenavState ? this.slidenavState = false : this.slidenavState = true
      },
      closeNav () {
        this.slidenavState = false
      },
      showConfig (event) {
        this.$refs.config.show(document.getElementById('avatar-pop'))
        this.$refs.outsite.hide()
      },
      changeCloud (event) {
        this.$refs.outsite.show(event.target)
        this.$refs.config.hide()
      },
      go (siteAddress) {
        window.location.href = window.location.origin + '/user/' + siteAddress
      },
      exit () {
        window.sessionStorage.clear()
        if (process.env.NODE_ENV === 'development') {
          this.$router.push({path: '/login'})
        } else {
          window.location.href = window.location.origin + '/?redirect=mobile'
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
   /*border-bottom: 1px solid #e1e4e8;*/
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
 }

 .brand span {
   display: inline-block;
   /*width: 100px;*/
   font-size: 20px;
   /*padding-left: 14px;*/
   /*border-left: 1px solid rgba(0, 0, 0, .12);*/
   letter-spacing: 1px;
   font-weight: 400;
   cursor: pointer;
   line-height: 56px;
 }

 #header .left {
   display: flex;
   align-items: center;
 }


 #header .right {
   display: flex;
   align-items: center;
 }

 #header .right i.iconfont {
   cursor: pointer;
   display: inline-block;
   height: 56px;
   line-height: 56px;
   width: 56px;
   /*border-left: 1px solid rgba(0, 0, 0, .12);*/
   text-align: center;
 }

 #header .right i.iconfont:hover {
   background: rgba(255, 255, 255, .2);
   border-left: 1px solid #468df1;
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
</style>