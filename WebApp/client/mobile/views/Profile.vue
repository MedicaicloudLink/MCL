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
          <router-link to="/profile" class="link-item active">个人信息</router-link>
          <router-link to="/avatar" class="link-item">头像修改</router-link>
          <router-link to="/resetpw" class="link-item">密码修改</router-link>
        </div>
      </div>
      <div class="config">
        <h2>个人资料</h2>
        <div class="option flex-row">
          <label style="width: 120px;">姓名</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_username" :readonly="!state.s_username" :class="{edit: state.s_username}" :disabled="!state.s_username"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_username" @click="state.s_username = true"></i>
            </div>
            <div class="tools" v-show="state.s_username">
              <m-button type="blue" @click="saveInfo('s_username')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_username')">取消</m-button>
            </div>
          </div>
        </div>
        <div class="option flex-row">
          <label style="width: 120px;">性别</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <select v-model="editInfo.s_sex" :readonly="!state.s_sex" :class="{edit: state.s_sex}" :disabled="!state.s_sex">
                <option value="1">男</option> 
                <option value="2">女</option>
              </select>
              <i class="iconfont icon-xindenew85" v-show="!state.s_sex" @click="state.s_sex = true"></i>
            </div>
            <div class="tools" v-show="state.s_sex">
              <m-button type="blue" @click="saveInfo('s_sex')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_sex')">取消</m-button>
            </div>
          </div>
        </div>
        <div class="option flex-row">
          <label style="width: 120px;">出生日期</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input v-show="!state.s_mybirthday" :value="editInfo.s_mybirthday" readonly/>
              <datepicker v-model="editInfo.s_mybirthday" v-show="state.s_mybirthday" format="yyyy-MM-dd"></datepicker>
              <i class="iconfont icon-xindenew85" v-show="!state.s_mybirthday" @click="state.s_mybirthday = true"></i>
            </div>
            <div class="tools" v-show="state.s_mybirthday">
              <m-button type="blue" @click="saveInfo('s_mybirthday')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_mybirthday')">取消</m-button>
            </div>
          </div>
        </div>
        <div class="option flex-row">
          <label style="width: 120px;">工作单位</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_workunti" :readonly="!state.s_workunti" :class="{edit: state.s_workunti}" :disabled="!state.s_workunti"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_workunti" @click="state.s_workunti = true"></i>
            </div>
            <div class="tools" v-show="state.s_workunti">
              <m-button type="blue" @click="saveInfo('s_workunti')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_workunti')">取消</m-button>
            </div>
          </div>
        </div>
        <div class="option flex-row">
          <label style="width: 120px;">部门</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_department" :readonly="!state.s_department" :class="{edit: state.s_department}" :disabled="!state.s_department"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_department" @click="state.s_department = true"></i>
            </div>
            <div class="tools" v-show="state.s_department">
              <m-button type="blue" @click="saveInfo('s_department')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_department')">取消</m-button>
            </div>
          </div>
        </div>

        <div class="option flex-row">
          <label style="width: 120px;">职务/职称</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_joblevel" :readonly="!state.s_joblevel" :class="{edit: state.s_joblevel}" :disabled="!state.s_joblevel"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_joblevel" @click="state.s_joblevel = true"></i>
            </div>
            <div class="tools" v-show="state.s_joblevel">
              <m-button type="blue" @click="saveInfo('s_joblevel')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_joblevel')">取消</m-button>
            </div>
          </div>
        </div>    

        <div class="option flex-row">
          <label style="width: 120px;">手机号码</label>
          <input type="text" v-model="editInfo.s_cellphone" readonly class="phone-input"/>
        </div>

        <div class="option flex-row">
          <label style="width: 120px;">工作电话</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_workphone" :readonly="!state.s_workphone" :class="{edit: state.s_workphone}" :disabled="!state.s_workphone"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_workphone" @click="state.s_workphone = true"></i>
            </div>
            <div class="tools" v-show="state.s_workphone">
              <m-button type="blue" @click="saveInfo('s_workphone')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_workphone')">取消</m-button>
            </div>
          </div>
        </div>

        <div class="option flex-row">
          <label style="width: 120px;">联系地址</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_address" :readonly="!state.s_address" :class="{edit: state.s_address}" :disabled="!state.s_address"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_address" @click="state.s_address = true"></i>
            </div>
            <div class="tools" v-show="state.s_address">
              <m-button type="blue" @click="saveInfo('s_address')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_address')">取消</m-button>
            </div>
          </div>
        </div>

        <div class="option flex-row">
          <label style="width: 120px;">电子邮件</label>
          <div class="flex-col">
            <div class="flex-row between center">
              <input type="text" v-model="editInfo.s_userEmail" :readonly="!state.s_userEmail" :class="{edit: state.s_userEmail}" :disabled="!state.s_userEmail"/>
              <i class="iconfont icon-xindenew85" v-show="!state.s_userEmail" @click="state.s_userEmail = true"></i>
            </div>
            <div class="tools" v-show="state.s_userEmail">
              <m-button type="blue" @click="saveInfo('s_userEmail')">确定</m-button>
              <m-button type="gray" @click="cancelChange('s_userEmail')">取消</m-button>
            </div>
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
  import Datepicker from '../lib/Date.vue'
  
  export default {
    name: 'Profile',
    components: {
      Mynav,
      MButton,
      Datepicker
    },
    data () {
      return {
        userInfo: {},
        editInfo: {
          s_username: '',
          s_sex: '',
          s_mybirthday: '',
          s_userEmail: '',
          s_cellphone: '',
          s_joblevel: '',
          s_department: '',
          s_workunti: '',
          s_workphone: '',
          s_address: ''
        },
        state: {
          s_username: false,
          s_sex: false,
          s_userEmail: false,
          s_mybirthday: false,
          s_cellphone: false,
          s_joblevel: false,
          s_department: false,
          s_workunti: false,
          s_workphone: false,
          s_address: false
        }
      }
    },
    mounted () {
      this.getUserinfo()
    },
    methods: {
      getUserinfo () {
        API.GetUserinfo({userid: this.$root.userid}).then(response => {
          this.userInfo = response[0]
          for (let i in this.editInfo) {
            this.editInfo[i] = this.userInfo[i]
          }
        }).catch(err => {
          console.log(err)
        })
      },
      cancelChange (changId) {
        this.state[changId] = false
        this.editInfo[changId] = this.userInfo[changId]
      },
      saveInfo (changId) {
        let data = {
          s_userid: this.userInfo.s_userid,
          s_username: this.userInfo.s_username,
          s_sex: this.userInfo.s_sex,
          s_cellphone: this.userInfo.s_cellphone,
          s_userEmail: this.userInfo.s_userEmail,
          s_joblevel: this.userInfo.s_joblevel,
          s_workunti: this.userInfo.s_workunti,
          s_department: this.userInfo.s_department
        }
        data[changId] = this.editInfo[changId]
        API.SetUserinfo({userInfo: JSON.stringify(data)}).then(response => {
          this.toast({
            text: '修改成功',
            type: 'success',
            placement: 'bottom-left'
          })
          this.state[changId] = false
          this.$root.userInfo[changId] = this.editInfo[changId]
        }).catch(err => {
          this.toast({
            text: '保存失败，重新提交试试',
            type: 'success',
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
    padding: 24px 12px 0 12px;
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

 .config {
   background: #fff;
   padding: 24px;
   width: 650px;
   border: 1px solid #ccc;
 }

 .config h2 {
    border-bottom: 1px solid #eee;
    font-size: 22px;
    font-weight: 400;
    color: #777;
    padding-bottom: 24px;
  }

  .option{
    padding: 10px;
    border-bottom: 1px solid #eee;
  }

  .option>div.flex-col {
    flex: 1;
  }

  .flex-row.between {
    justify-content: space-between;
  }

  .flex-row.center {
    align-items: center;
  }

  .option>label{
    line-height: 32px;
  }
  .option input{
    flex: 1;
    border: none;
    background: #fff;
    border: 1px #fff solid;
    padding: 0 5px;
    height: 30px;
    margin-left: 5px;
    font-size: 16px;
  }
  .option input.edit, .option select.edit{
    border: 1px #eee solid;
    border-radius: 3px;
  }
  .option select{
    text-align: left;
    flex: 1;
    border: none;
    background: #fff;
    border: 1px #fff solid;
    padding: 0 5px;
    margin-left: 5px;
    -moz-appearance: none;
    -webkit-appearance: none;
    /*appearance: none;*/
    font-size: 16px;
    color: #000;
  }

  .phone-input:focus {
    background: #fff;
    border-color: rgba(0, 0, 0, 0);
  }

  .option .iconfont {
    cursor: pointer;
  }

  .tools{
    width: 100%;
    text-align: left;
    margin: 12px 5px;
    cursor: pointer;
  }
  .tools>span:first-child{
    margin-right: 24px;
  }
</style>