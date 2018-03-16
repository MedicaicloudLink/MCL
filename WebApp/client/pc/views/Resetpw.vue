<template>
  <div class="card">
    <div class="card-header">修改密码</div>
    <div class="option flex-row"style="margin-top: 8px;">
      <label>当前密码</label>
      <input type="password" v-model="oldPw"/>
    </div>
    <div class="option flex-row">
      <label>输入新密码</label>
      <input type="password" v-model="newPw" />
    </div>
    <div class="option flex-row">
      <label>再次输入新密码</label>
      <input type="password" v-model="againPw" />
    </div>
    <div class="tools">
      <div class="btn-blue mg-r-20" @click="changePw">确定</div>
      <div class="btn-gray" @click="cancelChange">取消</div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import MButton from '../lib/button.vue'
  export default {
    name: 'Profile',
    components: {
      MButton
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
        API.ResetPassWord({userid: this.$root.userid, oldpassword: this.oldPw, newpassword: this.newPw, repassword: this.againPw}).then((response) => {
          this.toast({
            text: '修改成功',
            type: 'success',
            placement: 'top'
          })
        }).catch(err => {
          this.toast({
            text: '保存失败，重新提交试试',
            type: 'error',
            placement: 'top'
          })
          console.log(err)
        })
      }
    }
  }
</script>
<style scoped>
 .option {
   align-items: center;
   margin-bottom: 16px;
 }
 .option label{
    width: 120px;
    text-align: right;
    margin-right: 32px;
    color: rgba(0,0,0,.54);
  }
  .option input{
    flex: 1;
    margin-right: 72px;
  }
  .tools{
    width: 100%;
    text-align: center;
    padding-top: 16px;
  }
</style>