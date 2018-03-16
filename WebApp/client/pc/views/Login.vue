<template>
  <div class="login" :style="{height: clientHeight + 'px'}">
    <!--<h1>MedicaYun Patient Manage</h1>-->
    <div class="login-card">
      <div class="login-triangle"></div>
      
      <h2 class="login-header">阜外 PCI 用户登录</h2>

      <form class="login-container">
        <p><input type="text" placeholder="用户名"  v-model="username" /><span class="errMsg"></span></p>
        <p><input type="password" placeholder="密码" v-model="password" @keyup.enter="login" /><span class="errMsg"></span></p>
        <p><span class="submit" @click="login">登录</span></p>
      </form>
    </div>
  </div>
</template>

<script>
  import API from '../api.js'
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
        if (window.sessionStorage) {
          if (this.username !== '' && this.password !== '') {
            // 登录成功存session，并给根组件存基本信息
            API.Login({username: this.username, password: this.password}).then((response) => {
              // success callback
              window.sessionStorage.setItem('authorization', response.token)
              window.sessionStorage.setItem('userid', response.userid)
              this.$root.userid = response.userid
              this.$router.push({path: '/home'})
            }).catch((err) => {
              // error callback
              window.alert(err)
            })
          }
        }
      }
    }
  }
</script>

<style>
  .login {
    width: 100%;
    background: #456;
  }
  .login-card {
    width: 400px;
    margin: 0 auto;
    font-size: 16px;
  }
  .login-header,
  .login-card p {
    margin-top: 0;
    margin-bottom: 0;
  }

  /* The triangle form is achieved by a CSS hack */
  .login-triangle {
    width: 0;
    margin-right: auto;
    margin-left: auto;
    border: 12px solid transparent;
    border-bottom-color: #28d;
  }

  .login-header {
    background: #28d;
    padding: 20px;
    font-size: 1.4em;
    font-weight: normal;
    text-align: center;
    text-transform: uppercase;
    color: #fff;
  }

  .login-container {
    background: #ebebeb;
    padding: 12px;
  }

  /* Every row inside .login-container is defined with p tags */
  .login-card p {
    padding: 12px;
  }

  .login-card input, .login-card .submit {
    box-sizing: border-box;
    display: block;
    width: 100%;
    border-width: 1px;
    border-style: solid;
    padding: 16px;
    outline: 0;
    font-family: inherit;
    font-size: 0.95em;
  }

  .login-card input[type="text"],
  .login-card input[type="password"] {
    background: #fff;
    border-color: #bbb;
    color: #555;
  }

  /* Text fields' focus effect */
  .login-card input[type="text"]:focus,
  .login-card input[type="password"]:focus {
    border-color: #888;
  }

  .login-card .submit {
    text-align: center;
    background: #28d;
    border-color: transparent;
    color: #fff;
    cursor: pointer;
  }

  .login-card .submit:hover {
    background: #17c;
  }

  /* Buttons' focus effect */
  .login-card .submit:focus {
    border-color: #05a;
  }
</style>