<template>
  <div id="userInfo">
    <mynav></mynav>
    <div class="content flex-row">
      <div class="profile">
        <div class="user-info">
          <div class="avatar">
            <span v-if="$root.userInfo.s_avatar === ''">{{ $root.shortName | uppercase }}</span>
            <span v-else><img :src="'http://' + $root.userInfo.s_avatar" alt="" class="avatar-img"></span>
          </div>
          <div class="text flex-col" style="flex: 1;">
            <span class="name">{{ $root.userInfo.s_username }}</span>
            <span class="work">{{ $root.userInfo.s_workunti }}</span>
          </div>
        </div>
        <div class="link-list flex-col">
          <router-link to="/profile" class="link-item">个人信息</router-link>
          <router-link to="/avatar" class="link-item">头像修改</router-link>
          <router-link to="/resetpw" class="link-item active">密码修改</router-link>
        </div>
      </div>
      <div class="chang-pw">
        <h2>修改密码</h2>
        <div class="option flex-row">
          <label style="width: 180px;">当前密码：</label>
          <input type="password" v-model="oldPw"/>
        </div>
        <div class="option flex-row">
          <label style="width: 180px;">输入新密码：</label>
          <input type="password" v-model="newPw" />
        </div>
        <div class="option flex-row">
          <label style="width: 180px;">再次输入新密码：</label>
          <input type="password" v-model="againPw" />
        </div>
        <div class="flex-row" style="padding: 0 10px;">
          <span style="width: 180px;"></span>
          <p class="hint" v-if="againPw.length > 0 && againPw !== newPw">两次新密码输入不一致</p>
        </div>
        <div class="flex-row" style="padding: 24px 10px 0 10px;">
          <span style="width: 180px;"></span>
          <div class="tools">
            <m-button type="blue" @click="changePw">确定</m-button>
            <m-button type="gray" @click="cancelChange">取消</m-button>
          </div>
        </div>
           
      </div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import Mynav from '../components/Header.vue'
  import MButton from '../lib/Button.vue'
  export default {
    name: 'Profile',
    components: {
      MButton,
      Mynav
    },
    data () {
      return {
        oldPw: '',
        newPw: '',
        againPw: ''
      }
    },
    methods: {
      cancelChange () {
        this.oldPw = ''
        this.newPw = ''
        this.againPw = ''
      },
      changePw () {
        // change newPw
        if (!this.oldPw || !this.newPw || !this.againPw) return
        const data = {
          userid: this.$root.userid,
          oldpassword: this.oldPw,
          newpassword: this.newPw,
          repassword: this.againPw
        }
        API.ResetPw(data).then(response => {
          this.toast({
            text: '密码修改成功',
            type: 'success',
            placement: 'bottom-left'
          })
          this.cancelChange()
        }).catch(err => {
          this.toast({
            text: '失败：' + err,
            type: 'error',
            placement: 'bottom-left'
          })
          console.log(err)
        })
      }
    }
  }
</script>
<style scoped>
  .content{
    margin: 50px auto;
    justify-content: center;
  }

  .profile {
    width: 216px;
    margin-right: 16px;
    height: 300px;
    background: #fafafa;
    box-shadow: 3px 3px 5px rgba(0, 0, 0, .13);
  }


  .profile .user-info {
    text-align: center;
    border-bottom: 1px solid #eee;
    margin: 24px 12px 0 12px;
  }

  .profile .avatar span{
    display: inline-block;
    font-size: 22px;
    height: 72px;
    width: 72px;
    line-height: 72px;
    background: #468df1;
    color: #fff;
    border-radius: 50%;
    overflow: hidden;
  }

  .avatar-img {
    display: inline-block;
    height: 72px;
    width: 72px;
  }

  .profile .user-info .text {
    padding: 12px 0;
  }

  .profile .user-info .name {
    font-size: 18px;
    line-height: 24px;
  }

  .profile .user-info .work {
   font-size: 14px;
   line-height: 24px;
   overflow: hidden;
   text-overflow: ellipsis;
   white-space: nowrap;
 }

  .profile .link-list {
   font-size: 16px;
   line-height: 40px;
   text-align: center;
 }

 .profile .link-list a{
   font-size: 16px;
 }

 .profile .link-list a.active {
   background: rgba(69, 141, 241, .12);
 }

 .chang-pw {
   background: #fff;
   padding: 24px;
   width: 650px;
   border: 1px solid #ccc;
   align-items: center;
 }

  .chang-pw h2 {
    border-bottom: 1px solid #eee;
    font-size: 22px;
    font-weight: 400;
    color: #777;
    padding-bottom: 24px;
  }
 .option {
   padding: 10px;
   border-bottom: 1px solid #eee;
 }
 .option>label{
    line-height: 32px;
  }

  .chang-pw input[type="password"] {
    flex: 1;
    height: 30px;
    line-height: 30px;
    border-radius: 2px;
  }

  .hint {
    color: darkred;
  }

  .tools{
    flex: 1;
    width: 100%;
    text-align: left;
    cursor: pointer;
  }
  .tools>span:first-child{
    margin-right: 24px;
  }
</style>