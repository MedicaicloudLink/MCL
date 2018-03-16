<template>
  <div id="patient-detail" class="flex-col">
    <!--患者基本信息-->
    <div class="patient-base">
      <p class="patient-name">
        <span>{{patientBase.u_patientname}}</span>
        <i v-if="patientBase.u_gender === '1'" class="iconfont icon-nan" style="color: #58B7FF;"></i>
        <i v-else-if="patientBase.u_gender === '2'"class="iconfont icon-iconfontshouyezhuyetubiao07" style="color: #e51c23;"></i>
      </p>
      <div class="flex-row" style="justify-content: space-between;">
        <p><span class="name">出生日期：</span><span>{{patientBase.u_birthday}}</span></p>
        <p><span class="name">手机号：</span><span v-text="patientBase.u_phone"></span></p>
        <p><span class="name">备用电话：</span><span v-text="patientBase.u_secondphone"></span></p>
        <p><span class="name">入组时间：</span><span v-text="patientBase.u_jointime"></span></p>
      </div>
      <div class="flex-row">
        <p><span class="name">地址：</span><span v-text="patientBase.u_address"></span></p>
      </div>
    </div>
    <!--end-->
    <!--时间轴-->
    <div class="time-line flex-row" :style="{position: fix ? 'fixed' : 'relative', width: width + 256 + 'px'}">
      <div class="task flex-row">
        <span class="flex-col"
          :class="{active: currentIndex === index ? true : false, noDrop: joinInterval <= task.month || task.status == '11' ? true : false}" 
          v-for="(task, index) in taskList" :key="index"
          @click="getPatientRecord(index, task)"
        ><i>{{ task.typename }}</i><i v-if="task.updatetime">{{task.updatetime.slice(0, 10)}}</i></span>
      </div>
      <div class="check" v-if="(status == '2' && type == 'input') || (status == '9' && type == 'follow')">
        <dropdown>
          <div class="btn-gray tool" slot="trigger"><i>审核操作</i><i class="iconfont icon-xiasanjiao-copy"></i></div>
          <li @click="checkPass">通过</li>
          <li @click="backDialog = true">退回</li>
        </dropdown>
      </div>
    </div>
    <!--end-->
    <!--退回病例对话框-->
    <DialogBox v-if="backDialog" :width="400">
      <div slot="header">病历审核</div>
      <div slot="body" class="body flex-col">
        <span>您确定将此病例退回？</span>
        <span>如果确定，请填写退回原因（选填）</span>
        <textarea v-model="backText" style="height: 100px;"></textarea>
      </div>
      <div slot="footer" class="flex-row">
        <span class="btn-gray mg-r-8" @click="backDialog = false, backText = ''">取消</span>
        <span class="btn-blue" @click="checkBack">提交</span>
      </div>
    </DialogBox>
    <!--end-->
    <div class="patient-taskform" id="patient-taskform" :style="{width: width + 'px'}"> 
      <!--患者病例详情-->
      <div class="patient-records">
        <div class="item" v-for="(item, index) in patientData" :key="index">
          <div class="section" v-if="item.title === 'SECTION'">{{ item.answer }}</div>
          <div v-else-if="item.title === 'FORM'"></div>
          <div class="question flex-row" v-else>
            <div class="flex-col">
              <span class="question-name">{{item.title}}</span>
              <span v-if="item.answer.indexOf('ufile') !== -1"><a style="color: #458df1;" :href="'http://' + item.answer.split(',')[1]" target="_blank">{{item.answer.split(',')[0]}}</a></span>
              <span v-else>{{item.answer ? item.answer : '[未填写]'}}</span> 
            </div>
          </div>
        </div>
      </div>
      <!--备注说明列表-->
      <div class="text-note">
        <p class="section">备注说明</p>
        <div class="empty" v-if="!textNoteList.length"></div>
        <div class="text-note-item" v-for="(item, index) in textNoteList" :key="index">
          <p class="text-note-content">{{ item.remark }}</p>
          <p class="text-note-log flex-row">
            <span>{{ item.s_username }} {{ item.createtime }}</span>
            <img style="cursor: pointer;" src="../assets/icon_svg/icon_delete.svg" @click="deleteTextNote(item.id, index)">
          </p>
        </div>
      </div>
      <!--end-->
      <!--文档附件及说明列表-->
      <div class="text-note">
        <p class="section">文档附件</p>
        <div class="empty" v-if="!fileList.length"></div>
        <div class="text-note-item" v-for="(item, index) in fileList" :key="index">
          <p class="text-note-content  flex-row">
            <span class="filename">{{ item.name }}</span>
            <span style="color: rgba(0,0,0,.54);">{{ item.remark }}</span>
          </p>
          <p class="text-note-log flex-row">
            <span>{{ item.s_username }} {{ item.createtime }}</span>
            <img style="cursor: pointer;" src="../assets/icon_svg/icon_delete.svg" @click="deleteFile(item.id, index)">
          </p>
        </div>
      </div>
      <!--end-->
      <!--患者记录日志-->
      <div class="commit-log" v-if="commitLog.length">
        <p v-for="(log, index) in commitLog" :key="index">
          {{ log.username }} 于 {{ log.createtime }}
          <span v-if="type === 'input'">{{ log.status == '2' ? '提交审核' : log.status == '3' ? '退回' : log.status == '4' ? '审核通过' : '-' }}</span>
          <span v-if="type === 'follow'">{{ log.status == '9' ? '提交审核' : log.status == '10' ? '退回' : log.status == '1' ? '审核通过' : '-' }}</span>
        </p>
      </div>
      <!--end-->
    </div>

    <!--章节标题栏-->
    <div class="right-menu" :style="{left: left + 'px', top: top + 'px'}">
      <p :class="{active: item.flag}" v-for="(item, index) in section" :key="index" @click="sideScrollEvent(index)">
        {{item.title}}
        <span v-if="item.title === '备注说明'">（{{ textNoteList.length }}）</span>
        <span v-if="item.title === '文档附件'">（{{ fileList.length }}）</span>
      </p>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import { Type, intervalDate, throttle, scrollTo } from '../utils/tools.js'
  import DialogBox from '../lib/dialog.vue'
  import Dropdown from '../lib/Dropdown.vue'
  import MInput from '../lib/input.vue'
  export default {
    data () {
      return {
        width: '', // form的width
        left: '', // template-menu position
        top: '',
        fix: false,
        taskList: [],
        currentIndex: 0, // 当前任务index
        status: '', // 病例状态 '2'-登记提交 '9'-随访提交
        type: '', // 任务类型 '1'-登记 '2'-随访
        recordId: '', // 随访记录id 用于审核操作
        joinInterval: '', // 此刻距离入组的时间间隔
        patientBase: {},
        commitLog: [],
        section: [],
        patientData: [],
        textNoteList: [],
        fileList: [],
        backDialog: false, // 退回病例
        backText: '',
        interval: null, // 定时器
        scrollTopVal: 0, // 文档滚动高度
        offsetTopVal: 0 // 记录点击之后的当前章节的offsetTop
      }
    },
    components: {
      Dropdown,
      MInput,
      DialogBox
    },
    mounted () {
      // template-menu 的left 和 top 值
      this.width = document.getElementById('patient-detail').offsetWidth - 240 - 16
      this.left = document.getElementById('patient-taskform').offsetLeft + this.width + 16
      this.top = document.getElementById('patient-taskform').offsetTop + 56
      window.addEventListener('resize', () => {
        if (!document.getElementById('patient-taskform')) { return }
        this.width = document.getElementById('patient-detail').offsetWidth - 240 - 16
        this.left = document.getElementById('patient-taskform').offsetLeft + this.width + 16
      }, false)
      this.getPatientBase()
      this.getTaskList()
      //
      scrollTo(document.body, 0, 300)
      window.addEventListener('scroll', (e) => {
        let vm = this
        this.scrollEvent()
        if (vm.interval === null) {
          vm.interval = setInterval(function () {
            // 定时判断页面滚动是否结束
            if (document.body.scrollTop === vm.scrollTopVal) {
              throttle(vm.sideScrollEvent(), 1000)
              if (vm.offsetTopVal > vm.scrollTopVal) {
                vm.offsetTopVal = 0
              }
              clearInterval(vm.interval)
              vm.interval = null
            } else {
              vm.scrollTopVal = document.body.scrollTop
            }
          }, 100)
        }
      }, false)
    },
    methods: {
      scrollEvent () {
        // 基本信息固定
        document.body.scrollTop > 157 ? this.fix = true : this.fix = false
        // 侧边栏状态 TODO
        document.body.scrollTop > 157 ? this.top = 168 : this.top = 328
        // 侧边栏滚动定位
      },
      // 页面滚动 侧边栏
      sideScrollEvent (index) {
        if (this.section.length !== 0) {
          // 点击章节事件
          if (Type(index) === 'number') {
            for (let i in this.section) {
              this.section[i].flag = false
            }
            this.section[index].flag = true
            this.offsetTopVal = document.getElementsByClassName('section')[index].offsetTop - 168
            scrollTo(document.body, this.offsetTopVal, 300)
          } else {
            // 滚动事件
            if (this.scrollTopVal < this.offsetTopVal) return
            for (let j = 0; j < document.getElementsByClassName('section').length; j++) {
              if (this.scrollTopVal >= document.getElementsByClassName('section')[j].offsetTop - 350) {
                for (let i in this.section) {
                  this.section[i].flag = false
                }
                this.section[j].flag = true
              }
            }
          }
        }
      },
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
        API.FollowLog({mdid: this.$route.params.MDID}).then((response) => {
          this.taskList = response
          this.type = this.taskList[0].type
          this.joinInterval = intervalDate(this.taskList[0].u_jointime) // 获取入组到现在的时间间隔
          this.getPatientRecord(0, this.taskList[0])
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 患者病例数据
      getPatientRecord (index, task) {
        // 随访时间间隔存在 并且到哦了随访的时间才可点击
        if (task.month && (this.joinInterval - task.month < 0)) return
        if (task.status === '11' || task.status === 11) return
        this.currentIndex = index
        this.type = task.type
        this.patientData = []
        this.commitLog = []
        this.textNoteList = []
        this.fileList = []
        this.status = ''
        if (this.type === 'input') {
          API.GetPatientRecord({mdid: this.$route.params.MDID}).then((response) => {
            this.patientData = JSON.parse(response[0].patientdata)
            this.commitLog = response[0].commitlog
            this.status = response[0].status
            this.rightMenu()
            this.getNoteList()
            this.getFileList()
          }).catch((err) => {
            console.log(err)
          })
        } else {
          API.GetPatientFollow({mdid: this.$route.params.MDID, taskid: task.u_follow_id}).then((response) => {
            this.patientData = JSON.parse(response[0].patientdata)
            this.commitLog = response[0].commitlog
            this.status = response[0].u_follow_status
            this.recordId = response[0].recordid
            this.getNoteList(this.recordId)
            this.getFileList(this.recordId)
            this.rightMenu()
          }).catch((err) => {
            console.log(err)
          })
        }
      },
      // 右侧菜单栏
      rightMenu () {
        this.section = []
        for (let sid in this.patientData) {
          if (this.patientData[sid].title === 'SECTION') {
            this.section.push({ title: this.patientData[sid].answer, flag: false })
          }
        }
        this.section.push({title: '备注说明', flag: false})
        this.section.push({title: '文档附件', flag: false})
        this.section[0].flag = true
      },
      // 获取患者备注
      getNoteList (recordid) {
        if (recordid) {
          API.GetFollowNote({recordid: recordid}).then((response) => {
            this.textNoteList = response
          }).catch((err) => {
            console.log(err)
          })
          return
        }
        API.GetNoteList({mdid: this.$route.params.MDID}).then((response) => {
          this.textNoteList = response
        }).catch((err) => {
          console.log(err)
        })
      },
      // 获取文件列表
      getFileList (recordid) {
        if (recordid) {
          API.GetFollowFile({recordid: recordid}).then((response) => {
            this.fileList = response
          }).catch((err) => {
            console.log(err)
          })
          return
        }
        API.GetFileList({patientid: this.$route.params.MDID}).then((response) => {
          this.fileList = response
        }).catch((err) => {
          console.log(err)
        })
      },
      // 病历审核 通过
      checkPass () {
        let vm = this
        this.confirm({
          title: '审核病历',
          message: '确定该病例通过审核？',
          onConfirm () {
            if (vm.type === 'input') {
              API.CheckRecord({userid: vm.$root.userid, mdid: vm.$route.params.MDID, projectid: vm.$route.params.projectid, status: '4', remark: vm.backText}).then((response) => {
                vm.toast({
                  type: 'success',
                  text: '操作成功！',
                  placement: 'top'
                })
                window.history.go(-1)
              }).catch(() => {
                vm.toast({
                  type: 'error',
                  text: '网络异常，重新操作！',
                  placement: 'top'
                })
              })
            } else {
              API.FollowCheck({userid: vm.$root.userid, projectid: vm.$route.params.projectid, recordid: vm.recordId, status: '1', remark: vm.backText}).then((response) => {
                vm.toast({
                  type: 'success',
                  text: '操作成功！',
                  placement: 'top'
                })
                window.history.go(-1)
              }).catch(() => {
                vm.toast({
                  type: 'error',
                  text: '网络异常，重新操作！',
                  placement: 'top'
                })
              })
            }
          }
        })
      },
      // 病历审核 退回
      checkBack () {
        if (this.type === 'input') {
          // 基线
          API.CheckRecord({userid: this.$root.userid, mdid: this.$route.params.MDID, projectid: this.$route.params.projectid, status: '3', remark: this.backText}).then((response) => {
            this.toast({
              type: 'success',
              text: '操作成功！',
              placement: 'top'
            })
            window.history.go(-1)
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '网络异常，重新操作！',
              placement: 'top'
            })
          })
        } else {
          // 随访
          API.FollowCheck({userid: this.$root.userid, projectid: this.$route.params.projectid, recordid: this.recordId, status: '10', remark: this.backText}).then((response) => {
            this.toast({
              type: 'success',
              text: '操作成功！',
              placement: 'top'
            })
            window.history.go(-1)
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '网络异常，重新操作！',
              placement: 'top'
            })
          })
        }
      }
    }
  }
</script>
<style scoped>
  .time-line {
    z-index: 2;
    background: #eee;
    justify-content: space-between;
    padding-top: 20px;
  }
  .time-line .task span{
    padding: 12px 40px 0;
    position: relative;
    border-top: 2px solid #D6DCE0;
    cursor: pointer;
    line-height: 1.5;
    text-align: center;
  }
  .time-line .task span:before {
    content: "";
    width: 25px;
    height: 25px;
    background-color: white;
    border-radius: 25px;
    border: 1px solid #ddd;
    position: absolute;
    top: -15px;
    left: 42%;
    transition: all 200ms ease-in;
    z-index: 1;
  }
  .time-line .task span:first-child {
    padding-left: 0;
    text-align: left;
  }
  .time-line .task span:first-child::before {
    left: 0;
  }
  .time-line .task span.active {
    color: #458df1;
    text-decoration: underline;
  }
  .time-line .task span.active:before {
    background: #458df1;
    border-color: #458df1;
  }
  .time-line .task span.noDrop {
    color: rgba(0,0,0,.26);
    cursor: no-drop;
  }
  .time-line .check{
    align-items: center;
  }
  .time-line .check .tool {
    width: 120px;
    display: flex;
    justify-content: space-between;
    padding: 0 8px;
    background: #fff;
  }
  .time-line .check li{
    cursor: pointer;
    padding: 6px;
  }
  .time-line .check li:hover {
    background: rgba(70,141,241,.26)
  }

  .body {
    padding: 0 32px;
  }
  .body span {
    margin-bottom: 10px;
  }
  /* 基本信息 */
  .patient-base {
    background: #fff;
    margin-top: 32px;
    padding: 16px 32px;
    border-radius: 8px;
  }
  .patient-base .patient-name{
    font-size: 22px;
    line-height: 22px;
  }
  .patient-base .patient-name i{
     font-size: 18px;
     margin-left: 4px;
  }
  .patient-base p{
    height: 32px;
    line-height: 1;
    display: flex;
    align-items: center;
  }
  .patient-base p .name {
    color: rgba(0,0,0,.54);
  }

  .patient-taskform {
    margin-right: 16px;
  }
  .item{
    background: #fff;
  }
  .commit-log {
    background: #fff;
    line-height: 1.7;
    color: rgba(0,0,0,.54);
    padding: 10px 32px;
    margin: 20px 0;
  }

  .question{
    line-height: 1.7;
    margin-left: 32px;
    margin-right: 20px;
    padding: 5px 0 10px;
  }
  .question-name {
    color: rgba(0,0,0,.54);
    font-size: 16px;
  }
  .section {
    border-bottom: 1px rgba(0,0,0,.12) solid;
    height: 60px;
    font-size: 20px;
    color: rgba(0,0,0,.87);
    display: flex;
    align-items: center;
    padding: 0 32px;
    margin-top: 20px;
  }
  .section:first-child {
    margin-top: 0;
  }
  .empty {
    height: 50px;
  }
  .text-note {
    padding-bottom: 10px;
    background: #fff;
    margin-top: 30px;
  }
  .text-note .section{
    border-bottom: 2px #ddd solid;
  }
  .text-note .text-note-item {
    padding: 24px 32px 16px;
    border-bottom: 1px #ddd solid;
  }
  .text-note-item .text-note-content {
    margin-bottom: 20px;
    color: rgba(0,0,0,.87);
  }
  .text-note-item .text-note-log {
    color: rgba(0,0,0,.26);
    font-size: 12px;
    justify-content: space-between;
  }
  .text-note-item .filename {
    width: 250px;
    margin-right: 20px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
  .right-menu {
    width: 240px;
    background: #fff;
    position: fixed;
    z-index: 1;
  }
  .right-menu p{
    height: 40px;
    cursor: pointer;
    text-align: center;
    line-height: 40px;
  }
  .right-menu p.active {
    background: rgba(70, 141, 241, .24);
  }
  .back-record {
    width: 196px;
    margin-bottom: 10px;
  }
</style>