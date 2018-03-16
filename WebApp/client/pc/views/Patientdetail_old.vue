<template>
  <div id="patient-detail" class="flex-col">
    <div class="tools flex-row">
      <span v-text="task.taskname" v-for="task in taskList"></span>     
    </div>
    <div class="patient-taskform" id="patient-taskform" :style="{width: width + 'px'}">
      <div class="patient-base card">
        <p><span class="name">姓名</span><span v-text="patientBase.u_patientname"></span></p>        
        <p><span class="name">性别</span><span v-text="patientBase.u_gender == '' ? '' : patientBase.u_gender == '1' ? '男' : '女'"></span></p>
        <p><span class="name">出生日期</span><span v-text="patientBase.u_birthday"></span></p>
        <p><span class="name">入组时间</span><span v-text="patientBase.u_jointime"></span></p>        
      </div> 
      <div class="patient-records card" v-for="item in recordsData">
        <div class="card-header flex-row">
          <span>{{ item.templatename }}</span>
          <span style="font-size: 12px; color: #777;">{{item.createtime | formatDate }}</span>
        </div>
        <div class="card-body">
          <p v-for="(value, key) in item.patientdata"><span class="name">{{ key }}</span><span>{{ value }}</span></p>
        </div>
        <div class="card-imgs flex-row">
          <template v-for="img in item.images" >
            <img class="small-img" :src="'http://' + img.url" @click="img.zoom = true"/>
            <div class="imgDialog" v-if="img.zoom" @click="img.zoom = false">
              <img class="large-img" :src="'http://'+ img.url"/>
            </div>
          </template>
        </div>
        <div class="card-remark flex-col" v-if="item.remark !== ''" >
          <p>{{ item.remark }}</p>
        </div>
      </div>
      <div class="card" v-if="noRecordsDataFlag">
        <div class="card-header flex-row" style="justify-content: space-between; align-items: center;">
          <span>{{ currentTemplateName}}</span>
        </div>
        <div class="card-body" style="color: #ccc; padding-left: 32px;"><p>此项记录暂无数据！</p></div>
      </div> 
    </div>

    <div class="template-menu" :style="{left: left + 'px', top: top + 'px'}">
      <p v-for="item in templates" :class="{active: item.u_templateid === currentTemplateId}" v-text="item.u_templatename" @click.stop="changeTemplateData(item.u_templateid, item.u_templatename)"></p>
    </div>  
  </div>
</template>
<script>
  import API from '../api.js'
  export default {
    data () {
      return {
        // template-menu position
        width: '',
        left: '',
        top: '',
        patientBase: {},
        taskList: [],
        templates: [],
        currentTemplateId: '',
        currentTemplateName: '',
        noRecordsDataFlag: false,
        recordsData: []
      }
    },
    mounted () {
      // template-menu 的left 和 top 值
      this.width = document.getElementById('patient-detail').offsetWidth - 240 - 16
      this.left = document.getElementById('patient-taskform').offsetLeft + this.width + 16
      this.top = document.getElementById('patient-taskform').offsetTop
      window.addEventListener('resize', () => {
        if (!document.getElementById('patient-taskform')) { return }
        this.width = document.getElementById('patient-detail').offsetWidth - 240 - 16
        this.left = document.getElementById('patient-taskform').offsetLeft + this.width + 16
      }, false)
      this.getPatientBase()
      this.getProjectTemplate()
      this.getTaskList()
    },
    methods: {
      /* patientBase */
      getPatientBase () {
        API.GetPatientBase({mdid: this.$route.params.MDID}).then((response) => {
          this.patientBase = response[0]
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* tasklist */
      getTaskList () {
        API.GetTask({projectid: this.$route.params.projectid, userid: this.$root.userid}).then((response) => {
          this.taskList = response
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* projectTemplate */
      getProjectTemplate () {
        API.GetTemplates({projectid: this.$route.params.projectid}).then((response) => {
          this.templates = response
          this.changeTemplateData(this.templates[0].u_templateid, this.templates[0].u_templatename)
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* 点击template-menu show对应的records */
      changeTemplateData (templateid, templatename) {
        this.currentTemplateId = templateid
        this.currentTemplateName = templatename
        this.recordsData = []
        API.GetPatientRecords({mdid: this.$route.params.MDID, templateid: templateid}).then((response) => {
          if (response.length === 0) {
            this.noRecordsDataFlag = true
            return
          }
          this.noRecordsDataFlag = false
          response.map(n => this.recordsData.push({
            templatename: n.u_templatename,
            createtime: n.u_createtime,
            patientdata: JSON.parse(n.u_patientdata),
            /* eslint-disable */
            images: eval('{'+ n.imgs +'}'),
            /* eslint-endable */
            remark: n.remark // 长文本
          }))
          // 添加图片放大的属性
          for(let key in this.recordsData) {
            let images = this.recordsData[key].images
            this.recordsData[key].images = []
            for(let i in images) {
              this.recordsData[key].images.push({
                url: images[i],
                zoom: false //放大图片
              })
            }
          }
        }).catch((err) => {
          console.log(err)
        })
      }
    }
  }
</script>
<style scoped>
  .tools {
    width: 386px;
    border: 1px solid rgba(0,0,0,.12);
    height: 32px;
    line-height: 30px;
    background: #fff;
    margin-bottom: 32px;
  }
  .tools span{
    flex: 1;
    text-align: center;
    overflow: hidden;
  }
  .tools span.active {
    background: #458df1;
    color: #fff;
  }
  /* 基本信息 */
  .patient-taskform {
    margin-right: 16px;
  }
  .patient-base {
    padding: 16px 0;
  }
  .card{
    background: #fff;
    margin-bottom: 16px;
  }
  .card p {
    height: 40px;
    display: flex;
    align-items: center;
  }
  .card p .name {
    width: 140px;
    text-align: right;
    margin-right: 32px;
    color: rgba(0,0,0,.54);
  }
  .card-header {
    border-bottom: 1px rgba(0,0,0,.12) solid;
    height: 60px;
    font-size: 20px;
    color: rgba(0,0,0,.87);
    justify-content: space-between;
    align-items: center;
    padding: 0 32px;
  }
  .card-body {
    padding-top: 8px;
    padding-bottom: 24px;
  }
  .card-remark{
    padding-left: 10px;
    margin-bottom: 10px;
    border-left: 3px solid #3cb6ec;
    color: rgba(0,0,0,.54);
    font-weight: bold;
    font-size: 15px;
  }
  .card-imgs {
    padding: 0 32px;
  }
  .card-imgs .small-img {
    width: 80px;
    height: 80px;
    margin-left: 12px;
    margin-bottom: 10px;
    cursor: pointer;
  }
  .imgDialog{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    background: rgba(0,0,0,.5);
    width: 100%;
    height:  100%;
    display: flex;
    justify-content: center;
    align-items: center; 
  }
  .card-imgs .large-img{
    width: 600px;
  }

  .template-menu {
    width: 240px;
    background: #fff;
    position: fixed;
    z-index: 1;
  }
  .template-menu p{
    height: 40px;
    cursor: pointer;
    text-align: center;
    line-height: 40px;
  }
  .template-menu p.active {
    background: rgba(70, 141, 241, .12);
  }
</style>