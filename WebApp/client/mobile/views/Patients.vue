<template>
  <div class="patients">
    <mynav></mynav>
    <h2 class="title"><p><router-link to="/home" style="margin-right: 14px;"><i class="iconfont icon-left"></i>返回</router-link>{{ projectInfo.u_projectName }}</p></h2>
    <div class="content">
      <div class="create">
        <p class="card-header">创建新病历记录</p>
        <patient-base :projectid="projectid"></patient-base>
      </div>

      <div class="show-area">
        <p class="card-header">患者列表</p>
        <div class="tools">
          <!--<span>患者列表</span>-->
          <div style="background: #eee;margin-right: 24px; padding: 0 18px 0 8px;border-radius: 2px;border: 1px solid #ccc;">
            <div class="checkbox">
              <input type="checkbox" id="allpatient"/>
              <label for="allpatient" @click="allChecked">全选</label>
            </div>
          </div>
          <m-button type="blue" :class="{disabled: disabled}" @click="deletePatients">删除</m-button>
        </div>
        <div class="show-container">
            <div class="recordSheet header">
                <div class="checkbox-option"></div>
                <div class="patient-name">姓名</div>
                <div class="patientID">ID</div>
                <div class="patient-sex">性别</div>
                <div class="patient-birthday">入组年龄</div>
                <div class="create-date">住址</div>
                <div class="join-data">入组时间</div>
            </div>
        </div>
        <div class="show-container"  v-for="(item, index) in patients" :key="item.u_MDID" :class="{check: checkList.indexOf(item.u_MDID) !== -1}">
            <div class="recordSheet">
                <div class="checkbox-option">
                  <input type="checkbox" :id="item.u_MDID" :value="item.u_MDID" v-model="checkList" />
                  <label :for="item.u_MDID"></label>
                </div>
                <div class="patient-name"><router-link :to="'/project/'+ projectid + '/patient/'+ item.u_MDID" v-text="item.u_patientname"></router-link></div>
                <div class="patientID">{{ item.u_MDID }}</div>
                <div class="patient-sex" v-text="item.u_gender == '' ? '' : item.u_gender == '1' ? '男' : '女'"></div>
                <div class="patient-birthday">{{ item.u_jointime, item.u_birthday | dateSubtract }}</div>
                <div class="create-date">{{ item.address.length > 0 ? item.address[0].substr(0, 3) : '---' }}</div>
                <div class="join-data">{{ item.u_jointime }}</div>
            </div> 
        </div>
        <div style="padding: 10px 0;text-align: center;"><pagination :total="patient_total" :current.sync="pagenum" :pagesize="30" @change="changsite"></pagination></div>
      </div>

    </div>
  </div>
</template>

<script>
  import API from '../api.js'
  import Mynav from '../components/Header.vue'
  import MButton from '../lib/Button.vue'
  import PatientBase from '../components/PatientBase'
  import Pagination from '../lib/page.vue'

  export default {
    name: 'Patients',
    components: {
      Mynav,
      MButton,
      Pagination,
      PatientBase
    },
    data () {
      return {
        username: window.sessionStorage.getItem('username'),
        projectid: '',
        projectInfo: {},
        pagenum: 1,
        patient_total: null,
        patients: [],
        disabled: true,
        checkList: []
      }
    },
    created () {
      this.projectid = this.$route.params.projectid
      this.pagenum = parseInt(this.$route.params.pagenum)
      if (this.$root.currentProject.u_projectID !== this.projectid) {
        this.getProjectInfo()
      } else {
        this.projectInfo = this.$root.currentProject
      }
      this.getProjectPatients()
    },
    watch: {
      // 如果路由有变化，会再次执行该方法
      '$route': 'getProjectPatients',
      // 删除按钮状态根据选中 checcheckList 的长度判断
      checkList (val, oldVal) {
        val.length > 0 ? this.disabled = false : this.disabled = true
      }
    },
    methods: {
      changsite () {
        let pagenum = arguments[0]
        this.pagenum = pagenum
        this.$router.push({name: 'PatientList', params: {pagenum: pagenum}})
      },
      getProjectInfo () {
        API.GetProjectInfo({projectId: this.projectid, userId: this.$root.userid}).then(response => {
          this.projectInfo = response[0]
          this.$root.currentProject = this.projectInfo
        }).catch(err => {
          console.log(err)
        })
      },
      getProjectPatients () {
        this.patients = []
        API.GetProjectPatients({projectid: this.projectid, userid: this.$root.userid, pagenum: this.pagenum}).then(response => {
          this.patient_total = Number(response.allnum)
          response.patientList.map(n => this.patients.push(n))
        }).catch(err => {
          console.log(err)
        })
      },
      /* 全选或全不选 */
      allChecked () {
        if (this.checkList.length !== this.patients.length) {
          this.checkList = []
          for (let p in this.patients) {
            this.checkList.push(this.patients[p].u_MDID)
          }
        } else {
          this.checkList = []
        }
      },
      /* 删除患者操作 */
      deletePatients () {
        if (this.disabled === true) { return }
        let vm = this
        this.confirm({
          title: '删除病历',
          message: '是否确认删除所选的病历',
          onConfirm () {
            API.DeletePatients({userid: vm.$root.userid, mdid: vm.checkList.join(',')}).then(response => {
              vm.getProjectPatients()
            }).catch(err => {
              console.log(err)
            })
          }
        })
      }
    }
  }
</script>

<style scoped>
  .title {
    background: #fff;
    height: 48px;
    color: #555;
    font-size: 16px;
    line-height: 48px;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.12);
  }

  .title>p {
    width: 768px;
    margin: 0 auto;
  }

  .title i {
    color: #468df1;
    font-size: 16px;
    margin-right: 4px;
  }

  .title a {
    display: inline-block;
    height: 48px;
    line-height: 48px;
    padding: 0 12px;
    font-size: 16px;
    color: #468df1;
    border-left: 1px solid rgba(0, 0, 0, 0.12);
    border-right: 1px solid rgba(0, 0, 0, 0.12);
    font-weight: 400;
  }

  .create {
    width: 386px;
    margin: 32px auto;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.2), 0 1px 5px 0 rgba(0,0,0,0.12);
  }

  .create>.card-header, .card-header{
    background: #fff;
    border-bottom: 1px solid #eee;
    padding-left: 16px;
    font-size: 18px;
    line-height: 50px;
    font-weight: 600;
    color: #555;
  }

  .content {
    width: 768px;
    margin: 0 auto;
  }

  /*病例展示区域style*/
  .show-area{
    background: #fff;
    margin-bottom: 120px;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.2), 0 1px 5px 0 rgba(0,0,0,0.12);
  }

  .show-area>.card-header {
    padding-left: 25px;
  }

  /* tools */
  .tools{
    display: flex;
    display: -webkit-flex;
    flex-direction: row;
    align-items: center;
    padding: 16px 25px;
    border-bottom: 1px solid #eee;
    font-size: 18px;
  }

  .tools .button.disabled{
    opacity: .4;
    cursor: default;
    padding-top: 5px;
    padding-bottom: 5px;
  }
  /* patient 展示区域*/
  .show-container{
    border-bottom: 1px #e9e9e9 solid;
    line-height: 2;
    padding: 0 25px;
  }

  .show-container:frist-child:hover{
    background: #fff;
  }

  .show-container:hover, .show-container.check{
    background: #EEEEEE;
  }
  .recordSheet{
    display: table;
    text-align: center;
    padding: 8px 0;
    font-size: 14px;
  }

  .recordSheet.header {
    color: #555;
    font-size: 15px;
  }

  .checkbox-option {
    width: 42px;
  }

  
  .patient-name{
    display: table-cell;
    width: 110px;
    text-align: left;
  }
  .patient-name a{
    color: #3bb5ed;
    text-decoration: underline;
  }
  .patientID {
    display: table-cell;
    width: 140px;
    text-align: left;
  }
  .patient-sex{
    display: table-cell;
    width: 100px;
    text-align: left;
  }
  .patient-birthday{
    display: table-cell;
    width: 110px;
    text-align: left;
  }
  .join-data{
    display: table-cell;
    width: 120px;
    text-align: left;
  }
  .create-date{
    display: table-cell;
    width: 150px;
    text-align: left;
  }

</style>
