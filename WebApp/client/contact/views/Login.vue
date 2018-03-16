<template>
  <div class="login center">
    <input type="text" placeholder="手机号码或电子邮箱" v-model="username" /><br/>
    <input type="password" placeholder="密码" v-model="password" @keyup.enter="login" /><br/>
    <span class="btn center" @click="login">登录</span>
  </div>
</template>

<script>
export default {
  data () {
    return {
      username: '',
      password: ''
    }
  },
  methods: {
    login () {
      let data = {username: this.username, password: this.password}
      this.$http.Login(data).then(rep => {
        window.sessionStorage.setItem('userid', rep.userid)
        window.sessionStorage.setItem('authorization', rep.token)
        this.$root.userid = rep.userid
        this.$root.GET_USERINFO()
        this.$router.push({name: 'home'})
      }).catch(err => console.log(err))
    }
  }
}
</script>

<style>

</style>
