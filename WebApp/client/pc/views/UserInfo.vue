<template>
  <div class="config">
    <div class="card-header">个人资料</div>
    <div class="option">
      <label class="label">姓名</label>
      <div class="flex-col flex">
        <div class="flex-row between center">
          <input type="text" v-model="editInfo.s_username" :readonly="!state.s_username" :class="{edit: state.s_username}"/>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_username" @click="state.s_username = true"></i>
        </div>
        <div class="tools" v-show="state.s_username">
          <div class="btn-blue" @click="saveInfo('s_username')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_username')">取消</div>
        </div>
      </div>
    </div>
    <div class="option">
      <label class="label">性别</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <select v-model="editInfo.s_sex" :readonly="!state.s_sex" :class="{edit: state.s_sex}" :disabled="disabled = !state.s_sex">
            <option value="1">男</option> 
            <option value="2">女</option>
          </select>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_sex" @click="state.s_sex = true"></i>
      </div>
      <div class="tools" v-show="state.s_sex">
          <div class="btn-blue" @click="saveInfo('s_sex')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_sex')">取消</div>
      </div>
      </div>
    </div>
    <div class="option">
      <label class="label">出生日期</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <input v-show="!state.s_mybirthday" :value="editInfo.s_mybirthday" readonly/>
          <Datepicker :width='508' v-model="editInfo.s_mybirthday" v-show="state.s_mybirthday" format="yyyy-MM-dd"></Datepicker>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_mybirthday" @click.stop="state.s_mybirthday = true"></i>
      </div>
      <div class="tools" v-show="state.s_mybirthday">
          <div class="btn-blue" @click="saveInfo('s_mybirthday')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_mybirthday')">取消</div>
      </div>
      </div>
    </div>
    <div class="option">
      <label class="label">工作单位</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <input type="text" v-model="editInfo.s_workunti" :readonly="!state.s_workunti" :class="{edit: state.s_workunti}"/>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_workunti" @click="state.s_workunti = true"></i>
      </div>
      <div class="tools" v-show="state.s_workunti">
          <div class="btn-blue" @click="saveInfo('s_workunti')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_workunti')">取消</div>
      </div>
      </div>
    </div>
    <div class="option">
      <label class="label">部门</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <input type="text" v-model="editInfo.s_department" :readonly="!state.s_department" :class="{edit: state.s_department}"/>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_department" @click="state.s_department = true"></i>
      </div>
      <div class="tools" v-show="state.s_department">
          <div class="btn-blue" @click="saveInfo('s_department')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_department')">取消</div>
      </div>
      </div>
    </div>
    <div class="option">
      <label class="label">职务/职称</label>
      <div class="flex-col flex">
        <div class="flex-row between center">
            <input type="text" v-model="editInfo.s_joblevel" :readonly="!state.s_joblevel" :class="{edit: state.s_joblevel}"/>
            <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_joblevel" @click="state.s_joblevel = true"></i>
        </div>
        <div class="tools" v-show="state.s_joblevel">
            <div class="btn-blue" @click="saveInfo('s_joblevel')">确定</div>
            <div class="btn-gray" @click="cancelChange('s_joblevel')">取消</div>
        </div>
      </div>
    </div>    
    <div class="option">
      <label class="label">手机号码</label>
      <input type="text" v-model="editInfo.s_cellphone" readonly/>
    </div>
    <div class="option">
      <label class="label">工作电话</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <input type="text" v-model="editInfo.s_workphone" :readonly="!state.s_workphone" :class="{edit: state.s_workphone}"/>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_workphone" @click="state.s_workphone = true"></i>
      </div>
      <div class="tools" v-show="state.s_workphone">
          <div class="btn-blue" @click="saveInfo('s_workphone')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_workphone')">取消</div>
      </div>
      </div>
    </div>
    <div class="option">
      <label class="label">联系地址</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <input type="text" v-model="editInfo.s_address" :readonly="!state.s_address" :class="{edit: state.s_address}"/>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_address" @click="state.s_address = true"></i>
      </div>
      <div class="tools" v-show="state.s_address">
          <div class="btn-blue" @click="saveInfo('s_address')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_address')">取消</div>
      </div>
      </div>
    </div>
    <div class="option">
      <label class="label">电子邮件</label>
      <div class="flex-col flex">
      <div class="flex-row between center">
          <input type="text" v-model="editInfo.s_userEmail" :readonly="!state.s_userEmail" :class="{edit: state.s_userEmail}"/>
          <i class="iconfont icon-icon-yxj-edit" v-show="!state.s_userEmail" @click="state.s_userEmail = true"></i>
      </div>
      <div class="tools" v-show="state.s_userEmail">
          <div class="btn-blue" @click="saveInfo('s_userEmail')">确定</div>
          <div class="btn-gray" @click="cancelChange('s_userEmail')">取消</div>
      </div>
      </div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import MButton from '../lib/button.vue'
  import Datepicker from '../lib/Date.vue'
  export default {
    name: 'UserInfo',
    components: {
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
      this.getUserInfo()
    },
    methods: {
      // 获得用户信息
      getUserInfo () {
        API.GetUserInfo({userid: this.$root.userid}).then(response => {
          this.userInfo = response[0]
          for (let i in this.editInfo) {
            this.editInfo[i] = this.userInfo[i]
          }
        }).catch(err => {
          console.log(err)
        })
      },
      // 取消修改
      cancelChange (changeOption) {
        this.state[changeOption] = false
        this.editInfo[changeOption] = this.userInfo[changeOption]
      },
      // 保存修改
      saveInfo (changeOption) {
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
        data[changeOption] = this.editInfo[changeOption]
        API.EditUserInfo({userInfo: JSON.stringify(data)}).then(response => {
          this.toast({
            text: '修改成功',
            type: 'success',
            placement: 'top'
          })
          this.state[changeOption] = false
          this.userInfo[changeOption] = this.editInfo[changeOption]
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
  .option{
    display: flex;
    padding: 10px 0;
    margin: 0 24px;
    border-bottom: 1px solid rgba(0,0,0,.12);
  } 
  .option .label{
    width: 120px;
    text-align: right;
    color: rgba(0,0,0,.54);
    margin-right: 24px;
    height: 40px;
    line-height: 40px;
  }
  .option .between {
    justify-content: space-between;
    align-items: center;
  }
  .option input, .option select{
    border-color: #fff;
    flex: 1;
    background-position: 492px center;
  }
  .option input.edit, .option select.edit{
    border-color: rgba(0,0,0,.12);
  }

  .option .iconfont {
    cursor: pointer;
  }
  .tools{
    margin: 12px 0px;
  }
  
</style>