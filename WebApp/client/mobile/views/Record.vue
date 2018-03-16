<template>
  <div class="template">
    <div class="title bar flex-row">
      <span style="font-size: 22px;padding: 0px 10px;cursor: pointer;" @click="goback">×</span>
      <m-button type="blue" :loading="addBtnState" @click="update">更新</m-button>
    </div>
    <div class="content">
      <transition name="fade">
        <component class="input-area" v-bind:is="mycomponent" :mdid="mdid" :templateid="templateid" :source="record_data" @putsever="setRecords(arguments[0])"></component>
      </transition>
      <transition name="fade">
        <record-bar :imgshow="true" :remarkshow="true" :imgs="imgs" :remarktext="remark" @putsever="setRemarks(arguments[0])"></record-bar>
      </transition>
    </div>
  </div>
</template>

<script>
  import Vue from 'vue'
  import MButton from '../lib/Button'
  import Loading from '../lib/Loading'
  import Temp from '../template/index'
  import RecordBar from '../components/RecordBar.vue'
  import API from '../api.js'

  export default {
    name: 'record',
    components: {
      MButton,
      Loading,
      RecordBar
    },
    data () {
      return {
        addBtnState: false,
        mdid: this.$route.params.mdid,
        projectid: this.$route.params.projectid,
        templateid: this.$route.params.templateid,
        recordid: '',
        record_data: {},
        imgs: [],
        remark: '',
        postdata: {
          source_data: {},
          patientdata: {},
          imgs: '',
          remark: ''
        },
        mycomponent: 'Loading'
      }
    },
    created () {
      // 新增记录或修改记录判断（根据路由）
      this.recordid = this.$route.params.recordid
      this.getRecord()
    },
    methods: {
      goback () {
        this.$router.back()
      },
      getRecord () {
        API.GetOneRecord({recordid: this.recordid}).then(response => {
          this.postdata.source_data = JSON.parse(response.sourcedata)
          this.postdata.patientdata = JSON.parse(response.u_patientdata)
          this.postdata.remark = response.remark
          this.postdata.imgs = response.imgs

          this.record_data = JSON.parse(response.sourcedata)
          /* eslint-disable */
          let img_data = eval('{'+ response.imgs +'}')
          this.imgs = img_data.length > 0 ? img_data : []
          /* eslint-endable */
          this.remark = response.remark

          // template
          Vue.component('mytemp', Temp['template' + this.templateid])
          this.mycomponent = 'mytemp'
        }).catch(err => {
          console.log(err)
        })
      },
      setRemarks (data) {
        this.postdata.imgs = JSON.stringify(data.imgs)
        this.postdata.remark = data.remark
      },
      setRecords (data) {
        this.postdata.source_data = data.sourceData
        this.postdata.patientdata = data.showData
      },
      postRecordData () {
        return {
          createuserid: this.$root.userid,
          recordid: this.recordid,
          patientdata: JSON.stringify(this.postdata.patientdata),
          sourcedata: JSON.stringify(this.postdata.source_data),
          imgs: this.postdata.imgs,
          remark: this.postdata.remark
        }
      },
      update () {
        // 按钮点击不可点击状态
        this.addBtnState = true
        const data = this.postRecordData()
        API.UpdatePatientData(data).then(response => {
          // 按钮恢复可点击
          this.addBtnState = false
          this.$router.push({path: '/project/' + this.projectid + '/patient/' + this.mdid})
        }).catch(err => {
          // 按钮恢复可点击
          this.addBtnState = false
          console.log(err)
        })
      }
    }
  }
</script>

<style scoped>
  .title {
    justify-content: space-between;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    z-index: 100;
  }


  .title>span{
    margin: 0 20px;
  }

  .content {
    width: 600px;
    padding: 25px;
    margin: 0 auto;
    margin-top: 70px;
    background: #fff;
  }
</style>