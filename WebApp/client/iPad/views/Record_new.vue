<template>
  <div id="record">
    <transition name="dialog">
      <div class="dialog-mask" v-if="read" :style="{width: width + 'px', top: top + 'px', left: left - width - 32 + 'px'}"></div>
    </transition>
    <!--头部工具栏-->
    <div id="header" class="header flex-row" style="flex-direction: row-reverse">
      <div class="tools flex-row" v-if="!read">
        <dropdown>
          <div class="btn-gray tool" slot="trigger"><i>添加...</i><i class="iconfont icon-xiangxia"></i></div>
          <li @click="addTextNote">文字备注说明</li>
          <li @click="upFileFlag = true">照片和音视频文件</li>
        </dropdown>
        <dropdown>
          <div class="btn-gray tool" slot="trigger">操作...<i class="iconfont icon-xiangxia"></i></div>
          <li @click="savePatientData('1')">保存并返回</li>
          <li @click="cancel">取消并返回</li>
        </dropdown>
        <div class="btn-blue submit-btn" style="width: 96px;" :class="{active: submitLoading}" @click="savePatientData('2')">提交病历</div>
      </div>
    </div>
    <!--end-->
    <!--章节标题栏-->
    <div class="right-menu" :style="{left: left + 'px', top: top + 'px'}">
      <p :class="{active: item.flag}" v-for="(item, index) in section" :key="index" @click="clickScroll(index)">
        {{item.title}}
        <span v-if="item.title === '备注说明'">（{{ remarksNum }}）</span>
        <span v-if="item.title === '文档附件'">（{{ attachFileNum }}）</span>
      </p>
    </div>
    <!--end-->
    <!--添加患者备注-->
    <div class="textnote" v-if="addTextNoteFlag" :style="{width: width + 'px'}">
      <p>添加文字备注说明</p>
      <m-input autoHeight hintText="请输入你的备注说明" full v-model="textNote"></m-input>
      <div class="textnote-tools">
        <div class="btn-gray" @click="addTextNoteFlag = false, textNote = ''">取消</div>
        <div class="btn-blue" @click="saveTextNote">保存</div>
      </div>
    </div>
    <!--end-->
    <!--添加患者文档附件对话框-->
    <addfile :open="upFileFlag" @close="closeFile"></addfile>
    <!--end-->
    <!--患者病历-->
    <div class="patient-record" id="patient-record" :style="{width: width + 'px'}">

      <div class="back" v-if="back">
        <p class="title">退回原因</p>
        <span>{{back}}</span>
      </div>

      <!--表单-->
      <form-view ref="form" 
        :temp.sync="form"
        :data.sync="sourceData"
        :patientB.sync="patientBase"
      >
      </form-view>

      <!--备注说明列表-->
      <remark ref="remarks" :remarkNum.sync="remarksNum"></remark>
      <!--end-->

      <!--文档附件及说明列表-->
      <attach-file ref="attachfiles" :attachFileNum.sync="attachFileNum"></attach-file>
      <!--end-->
    </div>
    <!--end-->
  </div>
</template>
<script>
  import API from '../api.js'
  import { throttle, debounce, scrollTo } from '../tool/tools.js'
  import EventListener from '../tool/EventListener.js'
  import parseForm from '../../parser/parse.js'
  import FormView from '../../parser'
  // import FormView from './form'
  import Remark from './remark'
  import AttachFile from './attach'
  export default {
    props: {
      projectForm: Array
    },
    data () {
      return {
        addTextNoteFlag: false,
        textNote: '',
        upFileFlag: false,
        read: false, // 只读 遮罩层
        section: [], // 章节标题
        remarksNum: 0, // 文字备注数量
        attachFileNum: 0, // 文档附件数量
        patientBase: { // 患者基本信息
          name: '', gender: '', birthday: '', jointime: '', address: '', phone: '', secondphone: ''
        },
        form: [], // 表单数据
        sourceData: [], // 患者源数据
        saveRule: false,
        submitRule: false,
        submitLoading: false, // 防止重复提交
        back: '', // 退回病历退回原因
        left: 0, // 侧边栏位置
        top: 0,
        width: 0,
        offsetTopVal: 0
      }
    },
    components: {
      FormView,
      Remark,
      AttachFile
    },
    mounted () {
      window.sessionStorage.setItem('textNote', JSON.stringify([]))
      window.sessionStorage.setItem('fileList', JSON.stringify([]))
      this.sideMenuStyle()
      // 判断提交病历状态
      if (this.$route.name === 'commitlist-record') this.read = true
      this.init()
      // 侧边栏随页面滚动和resize定位
      this._scrollEvent = EventListener.listen(window, 'scroll', debounce(this.scrollEvent, 150))
      this._resizeEvent = EventListener.listen(window, 'resize', throttle(this.sideMenuStyle, 150))
    },
    destroyed () {
      // 清除监听
      if (this._resizeEvent) this._resizeEvent.remove()
      if (this._scrollEvent) this._scrollEvent.remove()
    },
    watch: {
      $route () { this.init() },
      projectForm () { this.init() }
    },
    methods: {
      // 侧边栏点击章节事件
      clickScroll (index) {
        if (this.section.length === 0) return
        for (let i in this.section) {
          this.section[i].flag = false
        }
        this.section[index].flag = true
        this.offsetTopVal = document.getElementsByClassName('section')[index].offsetTop - 168
        scrollTo(document.documentElement, this.offsetTopVal, 300)
      },
      // 侧边栏随页面滚动定位
      scrollEvent () {
        if (this.section.length === 0) return
        if (this.offsetTopVal > document.documentElement.scrollTop) {
          this.offsetTopVal = 0
          return
        }
        for (let i in this.section) {
          this.section[i].flag = false
        }
        let els = document.getElementsByClassName('section')
        for (let j = 1; j < els.length; j++) {
          if ((els[j].getBoundingClientRect().top) > 300) {
            // 第一个距离页面top为正的section设置true，停止循环
            this.section[j - 1].flag = true
            break
          }
        }
      },
      // 侧边导航位置样式
      sideMenuStyle () {
        if (!document.getElementById('record')) return
        this.width = document.getElementById('record').offsetWidth - 192 - 64 - 32
        this.left = document.getElementById('patient-record').offsetLeft + this.width + 32
        this.top = document.getElementById('patient-record').offsetTop
      },
      // 右侧菜单栏
      rightMenu () {
        this.section = [{title: '基本信息', flag: true}]
        for (const index in this.form) {
          if (this.form[index].type === 'SECTION') {
            this.section.push({ title: this.form[index].title, flag: false })
          }
        }
        this.section.push({title: '备注说明', flag: false})
        this.section.push({title: '文档附件', flag: false})
      },
      // 病历初始化
      init () {
        scrollTo(document.documentElement, 0, 300)
        if (this.$route.name !== 'newrecord') {
          // 编辑
          this.getPatientBase()
          this.getPatientRecord() // 当前患者对应的表单数据
        } else {
          // 新建
          this.read = false
          this.form = this.projectForm
        }
        this.$refs.attachfiles.getAttachFiles()
        this.$refs.remarks.getNotes()
        this.rightMenu()
        this.$nextTick()
      },
      // 患者基本信息
      getPatientBase () {
        this.$http.GetPatientBase({mdid: this.$route.params.mdid}).then(rep => {
          const data = rep[0]
          this.patientBase.name = data.u_patientname
          this.patientBase.gender = data.u_gender
          this.patientBase.birthday = data.u_birthday
          this.patientBase.jointime = data.u_jointime
          this.patientBase.address = data.u_address
          this.patientBase.phone = data.u_phone
          this.patientBase.secondphone = data.u_secondphone
        }).catch(err => console.log(err))
      },
      // 患者病例数据
      getPatientRecord () {
        API.GetPatientRecord({patientid: this.$route.params.mdid}).then(rep => {
          this.sourceData = JSON.parse(rep[0].sourcedata)
          this.form = JSON.parse(rep[0].template)
          this.rightMenu()
          if (rep[0].status === '2' || rep[0].status === '4') this.read = true
          if (rep[0].status === '3') this.back = rep[0].remark
          else this.back = ''
        }).catch((err) => console.log(err))
      },
      // 保存 提交 患者数据
      savePatientData (status) {
        // 提交按钮一旦点击，禁止再点击
        if (this.submitLoading) return
        // save 检查必选项
        if (!this.$refs.form.saveVerify()) {
          this.toast({type: 'error', text: '必填项未填或者数据格式不正确！', placement: 'top'})
          return
        }
        // submit 检查必选项
        if (status === '2') {
          if (!this.$refs.form.submitVerify()) {
            this.toast({type: 'error', text: '必填项未填或者数据格式不正确！', placement: 'top'})
            return
          }
        }
        this.submitLoading = true
        this.patientBase.phone ? this.patientBase.phone = this.patientBase.phone.replace(/[^\d]/g, '') : ''
        this.patientBase.secondphone ? this.patientBase.secondphone = this.patientBase.secondphone.replace(/[^\d]/g, '') : ''
        // 更新, 新建病历数据
        let userinfo = JSON.parse(JSON.stringify(this.patientBase))
        userinfo.userid = this.$root.userid
        userinfo.projectid = this.$route.params.projectid
        userinfo.sourcedata = JSON.stringify(this.sourceData)
        const patientData = parseForm(this.sourceData)
        userinfo.patientdata = JSON.stringify(patientData)
        userinfo.status = status
        // 更新
        if (this.$route.params.mdid) userinfo.mdid = this.$route.params.mdid
        let text = status === '2' ? '提交成功！' : '保存成功！'
        this.$http.SaveRecord(userinfo).then((response) => {
          // 新建病例时
          if (!this.$route.params.mdid) {
            // 文字备注
            let textArr = JSON.parse(window.sessionStorage.getItem('textNote'))
            for (let i in textArr) {
              this.$http.CreateTextNote({userid: this.$root.userid, mdid: response, remark: textArr[i]}).then((response) => {
                // window.sessionStorage.removeItem('textNote')
              }).catch(() => {
                console.log('保存失败')
              })
            }
            window.sessionStorage.removeItem('textNote')
            // 文档附件
            let fileArr = JSON.parse(window.sessionStorage.getItem('fileList'))
            if (!fileArr) fileArr = []
            API.AddFileNote({userid: this.$root.userid, mdid: response, data: JSON.stringify(fileArr)}).then(rep => {
              window.sessionStorage.removeItem('textNote')
            }).catch(err => {
              // this.loading = false
              console.log(err)
            })
          }
          this.toast({type: 'success', text: text})
          this.$emit('getCaseState')
          // 返回的位置
          if (this.$route.name === 'newrecord') {
            this.$router.push({name: 'workdata'})
          } else {
            window.history.go(-1)
          }
        }).catch(() => {
          this.submitLoading = false
          this.toast({type: 'error', text: '网络异常，创建患者失败！'})
        })
      },
      // 添加文字备注说明
      addTextNote () {
        this.addTextNoteFlag = true
        scrollTo(document.documentElement, 0, 300)
      },
      // 保存备注
      saveTextNote () {
        if (!this.$route.params.mdid) {
          let textArr = JSON.parse(window.sessionStorage.getItem('textNote'))
          if (!textArr) textArr = []
          textArr.push(this.textNote)
          window.sessionStorage.setItem('textNote', JSON.stringify(textArr))
          this.textNote = ''
          this.addTextNoteFlag = false
          this.$refs.remarks.getNotes()
          return
        }
        this.$http.CreateTextNote({userid: this.$root.userid, mdid: this.$route.params.mdid, remark: this.textNote}).then((response) => {
          this.toast({type: 'success', text: '创建备注成功'})
          this.textNote = ''
          this.addTextNoteFlag = false
          // 更新子组件的列表
          this.$refs.remarks.getNotes()
        }).catch(() => {
          this.toast({type: 'error', text: '创建备注失败'})
        })
      },
      // 关闭addfile对话框
      closeFile () {
        this.upFileFlag = false
        this.$refs.attachfiles.getAttachFiles()
      },
      // 取消返回
      cancel () {
        if (this.$route.name === 'newrecord') {
          window.sessionStorage.removeItem('textNote')
          this.$router.push({name: 'workdata'})
        } else {
          window.history.go(-1)
        }
      }
    }
  }
</script>

<style scoped>
  /* 禁用遮罩层 */
  .dialog-mask{
    position: fixed;
    z-index: 999;
    top: 0;
    left: 216px;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, 0);
    display: table;
  }
 

  .header {
    width: 416px;
    height: 56px;
    padding: 0 32px;
    color: rgba(0,0,0,.87);
    position: fixed;
    top: 56px;
    right: 0;
    z-index: 1001;
  }
  .tools {
    text-align: right;
  }
  .tools .tool {
    width: 120px;
    margin-right: 8px;
    display: flex;
    justify-content: space-between;
  }
  .tools li{
    cursor: pointer;
    padding: 6px;
  }
  .tools li:hover {
    background: rgba(70,141,241,.26)
  }
  .tools i.icon-arrow-down {
    font-size: 12px;
    color: #333;
  }
  .submit-btn.active{
    color: rgba(255,255,255,.3);
    border: 1px rgba(255,255,255,.3) solid;
    cursor: no-drop;
  } 
  /* add textnote*/
  .textnote {
    background: #fff;
    border: 1px #ccc solid;
    border-left: 3px #468df1 solid;
    margin-top: 32px;
    margin-left: 32px;
    padding: 0 32px;
  }
  .textnote p{
    font-size: 20px;
    color: rgba(0,0,0,.87);
    padding: 30px 0;
  }
  .textnote .textnote-tools {
    text-align: right;
    margin-top: 40px;
    margin-bottom: 16px;
  }
  .tools .link-list{
    line-height: 30px;
    padding: 0 10px;
  }
  .patient-record {
    margin: 32px;
  }
  .back{
    margin-top: 30px;
    padding: 16px 24px 24px 42px;
    border-bottom: 1px solid #eee;
    background: #fff;
  }
  .back p.title{
    font-size: 26px;
    margin: 0;
    color: #e51c23;
  }
  .text-note {
    padding: 0;
    background: #fff;
    margin-top: 30px;
  }
  .text-note .title{
    font-size: 20px;
    height: 60px;
    line-height: 58px;
    padding-left: 32px;
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
  
  .right-menu {
    width: 196px;
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
</style>