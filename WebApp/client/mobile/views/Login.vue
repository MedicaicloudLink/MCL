<template>
  <div class="main" :style="{height: clientHeight + 'px'}">
    <div class="logo">
      <img src="../assets/logo.png">
    </div>
    <h3 class="center brand">登录</h3>
    <div class="login card">
      <input type="text" placeholder="手机号码或电子邮箱" v-model="username" />
      <input type="password" placeholder="密码" v-model="password" @keyup.enter="login" />
      <div class="save"><input type="checkbox" id="save"><label for="save">保存登录信息</label></div>
      <span class="btn center" @click="login">登录</span>
      <div class="forget center"><a href="/login/forgetpassword">忘记密码？</a></div>
      <p class="signin center">没有账号，<a href="/login/registershow">注册新账号</a></p>
    </div>
  </div>
</template>

<script>
  import api from '../api.js'

  export default {
    data () {
      return {
        username: '',
        password: '',
        clientHeight: document.body.scrollHeight
      }
    },
    methods: {
      login () {
        if (!window.sessionStorage) { return }
        if (this.username === '' && this.password === '') { return }
        // 登录成功存session，并给根组件存基本信息
        api.Login({username: this.username, password: this.password}).then(res => {
          window.sessionStorage.setItem('userid', res.userid)
          window.sessionStorage.setItem('authorization', res.token)
          window.sessionStorage.setItem('username', this.username)
          this.$root.userid = res.userid
          this.$router.push({path: '/'})
        })
        .catch(error => {
          console.log(error)
          console.log('error')
        })
      }
    }
  }
</script>

<style scoped>
  .logo {
      text-align: center;
      padding-top: 60px;
    }

    .center {
      text-align: center;
    }

    .brand {
      color: rgb(68, 68, 68);
      margin: 45px 0;
      font-size: 36px;
      font-weight: 300;
    }

    .card {
      background: #fff;
      padding: 45px 22px;
      box-shadow: 0 1px 4px rgba(0,0,0,.04);
      border: 1px solid rgba(0,0,0,.09);
    }

    .login {
      display: flex;
      flex-direction: column;
      width: 300px;
      margin: 0 auto;
    }

    input[type="text"], input[type="password"] {
      padding: 8px 16px;
      font-size: 16px;
      margin-bottom: 22px;
      border: 1px solid #aaa;
      border-radius: 2px;
    }

    .save, .btn {
      margin-bottom: 22px;
    }

    .forget {
      line-height: 24px;
      font-size: 16px;
    }

    .forget a, .signin a {
      color: #468df1;
      font-size: 16px;
      text-decoration: underline;
    }

    .save label {
      color: #888;
      padding-left: 10px;
      cursor: pointer;
    }

    .btn {
      height: 45px;
      line-height: 45px;
      font-size: 20px;
      background: #468df1;
      color: #fff;
      cursor: pointer;
      border-radius: 2px;
    }
</style>