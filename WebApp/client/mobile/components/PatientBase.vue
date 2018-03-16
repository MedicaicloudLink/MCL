<template>
<div id="base-info">
  <div class="flex-row">
    <div class="name">{{ $root.shortName.substring(0, 3) | uppercase }}</div>
    <div class="create-base">
      <div class="flex-row" style="justify-content: space-between;align-item: center;">
        <input type="text" placeholder="请添加患者姓名" class="username" v-model="info.name" @keyup.enter="createMoreState = true"/>
        <i class="iconfont icon-right" :class="{active: info.name.length > 0}" v-show="!createMoreState" @click="createMoreState = true"></i>
      </div>
      <div class="create-more" v-show="createMoreState">
        <div class="form-item">
          <div class="flex-row">
            <label for="gender" style="width: 85px;">性别</label>
            <div style="flex: 1;">
              <input type="radio" value="1" v-model="info.gender" id="man">
              <label for="man" style="padding-right: 24px;">男</label>
              <input type="radio" value="2" v-model="info.gender" id="woman">
              <label for="woman">女</label>
            </div>
          </div>
        </div>
        <div class="form-item">
          <div class="flex-row">
            <label for="patientname" style="width: 85px;">出生日期</label>
            <datepicker v-model="info.birthday" :width="125"></datepicker>
          </div>
        </div>
        <div class="form-item">
          <div class="flex-row">
            <label for="patientname" style="width: 85px;">入组时间</label>
            <datepicker v-model="info.jointime" :width="125"></datepicker>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-menu" v-show="createMoreState">
    <m-button type="gray" style="margin-right: 24px;" @click="createMoreState = false">取消</m-button>
    <m-button type="blue" :loading="createNewBtn" @click="createPatient">确认</m-button>
  </div>
</div>
</template>

<script>
  import API from '../api.js'
  import MButton from '../lib/Button.vue'
  import Datepicker from '../lib/Date.vue'
  export default {
    name: 'Base',
    components: {
      MButton,
      Datepicker
    },
    props: {
      projectid: String
    },
    data () {
      return {
        createNewBtn: false,    // 创建新病例按钮状态
        patientid: '',
        info: {
          name: '',
          gender: '',
          birthday: '',
          jointime: ''
        },
        createMoreState: false
      }
    },
    mounted () {
      this.info.jointime = new Date().toJSON().slice(0, 10)
    },
    methods: {
      /** 创建患者 */
      createPatient () {
        this.createNewBtn = false
        const putdata = {
          userid: this.$root.userid,
          projectid: this.projectid,
          name: this.info.name,
          gender: this.info.gender,
          birthday: this.info.birthday,
          jointime: this.info.jointime
        }
        // 提交
        API.CreatePatient(putdata).then(response => {
          this.toast({
            text: '新患者创建成功',
            type: 'success',
            placement: 'bottom-left'
          })
          this.$router.push({path: '/project/' + this.projectid + '/patient/' + response.recordid + '?new=1'})
        }).catch(err => {
          this.createNewBtn = false
          this.toast({
            text: '创建失败，请重新尝试',
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
  #base-info {
    width: 354px;
    padding: 16px;
    background: #fff;
  }

  #base-info .name {
    background: #45a0d4;
    color: #fff;
    height: 36px;
    width: 36px;
    font-size: 12px;
    line-height: 36px;
    text-align: center;
    margin-right: 8px;
    border-radius: 50%;
  }

  .create-base {
    background: #fff;
    flex: 1;
  }

  .create-base input.username {
    font-size: 16px !important;
    display: inline-block;
    width: 210px;
    margin: 2px 0;
    border: 1px solid rgba(0, 0, 0, 0);
    color: #555;
    font-weight: 500;
  }

  .create-base i.icon-right {
    height: 24px;
    line-height: 24px;
    /*background: #ddd;*/
    width: 24px;
    text-align: center;
    border-radius: 24px;
    margin: 3px 0;
    border: 1px solid #ddd;
    cursor: pointer;
    color: #ddd;
  }

  .create-base i.icon-right.active {
    /*background: #45a0d4;*/
    color: #555;
    font-weight: 600;
    border: 1px solid #555;
  }

  .create-base input:focus {
    outline: none;
    border-color: rgba(0, 0, 0, 0);
  }

  .create-base .form-item {
    padding: 8px 0 8px 8px;
  }

  .form-item>div>label {
    height: 32px;
    line-height: 32px;
    color: #777;
  }

  .create-base .datepicker input {
    width: 100px !important;
  }

  .create-more {
    margin-top: 16px;
  }

  .card-menu {
    text-align: right;
    padding-top: 16px;
    border-top: 1px solid #eee;
  }

  input[type="text"] {
    outline: none;
    border-color: rgba(0, 0, 0, 0);
  }
  input:focus {
    outline: none;
    border-color: rgba(0, 0, 0, 0);
  }
</style>
