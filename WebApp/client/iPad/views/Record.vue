<template>
  <div id="record">
    <transition name="dialog">
      <div class="dialog-mask" v-if="read" :style="{width: width + 'px', top: top + 'px'}"></div>
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
        <div class="btn-blue" style="width: 96px;" @click="savePatientData('2')">提交病历</div>
      </div>
    </div>
    <!--end-->
    <!--章节标题栏-->
    <div class="right-menu" :style="{left: left + 'px', top: top + 'px'}">
      <p :class="{active: item.flag}" v-for="item, index in section" @click="clickScroll(index)">
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
      <div class="form" v-if="sourceData.length && sourceData[0].type === 'FORM'">
        <p class="title">{{ sourceData[0].title }}</p>
        <span class="dec">{{ sourceData[0].describe }}</span>
      </div>
      <div class="back" v-if="back">
        <p class="title" style="color: #e51c23;">退回原因</p>
        <span>{{back}}</span>
      </div>
      <!--患者基本资料-->
      <div class="patient-base">
        <div class="section item" id="patientbase">
          <p class="title">基本信息</p>
          <span class="dec">患者的基本登记信息</span>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>姓名<span class="keep">*</span><span class="keep-hint" v-if="pbaseName">(此题是必填项，必须完成之后才可以提交)</span></p></div>
            <div class="a-item"><m-input hintText="您的姓名" full :value.sync="sPatientBase.name" v-model="patientBase.name"></m-input></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>性别<span class="keep">*</span><span class="keep-hint" v-if="pbaseSex">(此题是必填项，必须完成之后才可以提交)</span></p></div>
            <div class="a-item">
              <m-radio nativeValue="1" :value.sync="sPatientBase.gender" v-model="patientBase.gender">男</m-radio>
              <m-radio nativeValue="2" :value.sync="sPatientBase.gender" v-model="patientBase.gender">女</m-radio>
            </div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q">
              <p>
                出生日期<span class="keep">*</span>
                <span class="keep-hint" v-if="pbaseBirth">(此题是必填项，必须完成之后才可以提交)</span>
                <span class="keep-hint" v-if="!pbaseBirth && pbaseBirthReg">(数据格式有误！)</span>
              </p>
            </div>
            <div class="a-item"><p-date :id="'birthday'" :answers.sync="sPatientBase.birthday" v-model="patientBase.birthday"></p-date></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>联系电话<span class="keep">*</span><span class="keep-hint" v-if="pbasePhone">(此题是必填项，必须完成之后才可以提交)</span></p></div>
            <div class="a-item"><m-input hintText="电话号码" num full :answers="sPatientBase.phone" v-model="patientBase.phone"></m-input></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>备用电话</p></div>
            <div class="a-item"><m-input hintText="电话号码" num full :answers="sPatientBase.secondphone" v-model="patientBase.secondphone"></m-input></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>入组时间 <span class="keep-hint" v-if="pbaseJoinReg">(数据格式有误！)</span></p></div>
            <div class="a-item"><p-date :id="'jointime'" :answers.sync="sPatientBase.jointime" v-model="patientBase.jointime"></p-date></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>地址</p></div>
            <div class="a-item"><p-address :answers="sPatientBase.address" v-model="patientBase.address"></p-address></div>
          </div>
        </div>
      </div>
      <!--end-->
      <!--患者病历数据-->
      <div class="item" v-for="(qa, qaid) in sourceData" v-if="sourceData.length > 0">
        <div v-if="qa.type === 'FORM'"></div>

        <div class="section" v-else-if="qa.type === 'SECTION'" :id="qa.title">
          <p class="title">{{ qa.title }}</p>
          <span class="dec">{{ qa.describe.text }}</span>
        </div>
        <div v-else>
          <div class="questions" v-show="qa.show">

            <div class="q" :key="qa.id">
              <p>
                {{ qa.title }}
                <span class="keep" v-if="qa.keep">*</span>
                <span class="keep-hint" v-if="keepHint[qaid]">(此题是必填项，必须完成之后才可以提交)</span>
                <span class="keep-hint" v-if="!keepHint[qaid] && regHint[qaid]">(数据格式有误！)</span>
              </p>
              <span class="dec">{{ qa.describe.text }}</span>
              <span class="link-dec"><a :href="'http://' + qa.link.text" target="_blank">{{ qa.link.text }}</a></span>
              <img :src="'http://' + qa.img.url" alt="option" v-if="qa.img.url.length > 10" class="img-option">
            </div>
            <div class="a-item">
              <!--单填-->
              <p-multiple-choice :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'RADIO'" @next="nextBack(arguments[0], qa.id)"></p-multiple-choice>
              <!--单选 end-->

              <!--地址-->
              <p-address :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'ADDRESS'"></p-address>
              <!--地址 end-->

              <!--多选-->
              <p-checkboxes :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'CHECKBOX'"></p-checkboxes>
              <!--多选 end-->

              <!--下拉选择-->
              <p-dropdowns :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'DROPDOWN'" @next="nextBack(arguments[0], qa.id)"></p-dropdowns>
              <!--下拉选择 end-->

              <!--表格选择-->
              <p-table :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'TABLE'"></p-table>
              <!--表格选择 end-->

              <!--线性量表选择-->
              <p-linear-scale :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'LINEARSCALE'"></p-linear-scale>
              <!--线性量表选择 end-->

              <!--时间选择-->
              <p-time :id="qa.id" :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'TIME'"></p-time>
              <!--时间选择 end-->

              <!--日期选择-->
              <p-date :id="qa.id" :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'DATE'"></p-date>
              <!--日期选择 end-->

              <!--简短回答-->
              <p-short-text :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'SHORTTEXT'"></p-short-text>
              <!--简短回答 end-->

              <!--段落回答-->
              <p-long-text :answers.sync="qa.value" v-model="patientData[qaid].answer" v-if="qa.type === 'LONGTEXT'"></p-long-text>
              <!--段落回答 end-->

              <!--上传文件类型-->
              <p-file-upload :answers.sync="qa.value" v-model="patientData[qaid].answer" v-if="qa.type === 'FILEUPLOAD'"></p-file-upload>
              <!--上传文件类型 end-->
            </div>
          </div>
        </div>

      </div>
      <!--end-->

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
  import { Type, throttle, debounce, scrollTo } from '../tool/tools.js'
  import EventListener from '../tool/EventListener'
  import PMultipleChoice from './form/MultipleChoice.vue'
  import PAddress from './form/Address.vue'
  import PCheckboxes from './form/Checkboxes.vue'
  import PDropdowns from './form/Dropdowns.vue'
  import PTable from './form/Table.vue'
  import PLinearScale from './form/LinearScale.vue'
  import PTime from './form/Time.vue'
  import PDate from './form/Date.vue'
  import PShortText from './form/ShortText.vue'
  import PLongText from './form/LongText.vue'
  import PFileUpload from './form/FileUpload.vue'
  import Remark from './remark'
  import AttachFile from './attach'
  export default {
    props: {
      projectForm: Array
    },
    data () {
      return {
        back: '',
        addTextNoteFlag: false,
        textNote: '',
        upFileFlag: false,
        read: false, // 只读 遮罩层
        section: [], // 章节标题
        remarksNum: 0, // 文字备注数量
        attachFileNum: 0, // 文档附件数量
        sPatientBase: {}, // 患者基本信息源数据
        patientBase: { // 患者基本信息
          name: '',
          gender: '',
          birthday: '',
          jointime: '',
          address: '',
          phone: '',
          secondphone: ''
        },
        sourceData: [], // 患者源数据
        patientData: [], // 患者数据
        pbaseKeepAll: false, // base必选项提示
        pbaseName: false,
        pbaseSex: false,
        pbaseBirth: false,
        pbasePhone: false,
        keepHint: [], // 必选项提示
        pbaseBirthReg: false, // base 正则提示
        pbaseJoinReg: false,
        regHint: [], // 正则提示
        left: 0,
        top: 0,
        width: 0,
        offsetTopVal: 0
      }
    },
    components: {
      PMultipleChoice,
      PAddress,
      PCheckboxes,
      PDropdowns,
      PLinearScale,
      PTable,
      PTime,
      PDate,
      PShortText,
      PLongText,
      PFileUpload,
      Remark,
      AttachFile
    },
    computed: {
      // 所有问题的id
      id_list: function () {
        let qa = []
        for (let sid in this.projectForm) {
          if (this.projectForm[sid].type === 'SECTION' || this.projectForm[sid].type === 'FORM') {
            qa.push('')
          } else {
            qa.push(this.projectForm[sid].id)
          }
        }
        return qa
      },
      saveFlag () {
        this.pbaseKeepAll = !this.pbaseName && !this.pbaseSex && !this.pbaseBirth && !this.pbasePhone
        if (this.pbaseKeepAll && !this.pbaseBirthReg && !this.pbaseJoinReg) return true
        else return false
      },
      submitFlag () {
        this.pbaseKeepAll = !this.pbaseName && !this.pbaseSex && !this.pbaseBirth && !this.pbasePhone
        if (this.keepHint.every(item => !item) && this.pbaseKeepAll && this.regHint.every(item => !item)) return true
        else return false
      }
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
        if (this.offsetTopVal > document.body.scrollTop) {
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
          this.answersInit(this.projectForm) // 项目中表单问题数据
        }
      },
      // 右侧菜单栏
      rightMenu () {
        this.section = [{title: '基本信息', flag: true}]
        for (let sid in this.sourceData) {
          if (this.sourceData[sid].type === 'SECTION') {
            this.section.push({ title: this.sourceData[sid].title, flag: false })
          }
        }
        this.section.push({title: '备注说明', flag: false})
        this.section.push({title: '文档附件', flag: false})
      },
      // 清空所有值，创建新病例
      answersInit (list) {
        for (let info in this.patientBase) {
          if (info === 'jointime') this.patientBase[info] = new Date().toJSON().substr(0, 10)
          else this.patientBase[info] = ''
        }
        this.sPatientBaseInit()
        this.$refs.attachfiles.getAttachFiles()
        this.$refs.remarks.getNotes()
        this.sourceData = []
        this.patientData = []
        for (let q in list) {
          if (list[q].type === 'FORM' || list[q].type === 'SECTION') {
            this.sourceData.push(list[q])
            this.patientData.push({title: list[q].type, answer: list[q].title})
          } else {
            let form = list[q]
            list[q].show = true
            if (list[q].type === 'SHORTTEXT') form.value = ''
            if (list[q].type === 'ADDRESS') form.value = {}
            if (list[q].type === 'CHECKBOX') form.value = []
            if (list[q].type === 'TABLE') form.value = []
            if (list[q].type === 'TIME') form.value = {}
            if (list[q].type === 'DATE') form.value = {}
            if (list[q].type === 'LONGTEXT') form.value = ''
            if (list[q].type === 'FILEUPLOAD') form.value = {}
            if (list[q].type === 'RADIO') form.value = ''
            if (list[q].type === 'DROPDOWN') form.value = ''
            if (list[q].type === 'LINEARSCALE') form.value = ''
            this.sourceData.push(form)
            this.patientData.push({title: list[q].title, answer: ''})
          }
        }
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
          this.sPatientBaseInit()
        }).catch(err => console.log(err))
      },
      // 患者源数据初始化处理
      sPatientBaseInit () {
        for (let i in this.patientBase) {
          if (i === 'birthday' || i === 'jointime') {
            this.sPatientBase[i] = {}
            if (!this.patientBase[i]) continue
            this.sPatientBase[i].year = this.patientBase[i].split('-')[0]
            this.sPatientBase[i].month = this.patientBase[i].split('-')[1]
            this.sPatientBase[i].day = this.patientBase[i].split('-')[2]
          } else if (i === 'address') {
            this.sPatientBase[i] = {}
            if (!this.patientBase[i]) continue
            this.sPatientBase[i].province = this.patientBase[i].split(' ')[0]
            this.sPatientBase[i].city = this.patientBase[i].split(' ')[1]
            this.sPatientBase[i].text = this.patientBase[i].split(' ')[2]
          } else {
            this.sPatientBase[i] = this.patientBase[i]
          }
        }
      },
      // 患者病例数据
      getPatientRecord () {
        API.GetPatientRecord({patientid: this.$route.params.mdid}).then(rep => {
          this.sourceData = JSON.parse(rep[0].sourcedata)
          this.patientData = JSON.parse(rep[0].patientdata)
          this.rightMenu()
          if (rep[0].status === '2' || rep[0].status === '4') this.read = true
          if (rep[0].status === '3') this.back = rep[0].remark
          else this.back = ''
        }).catch(err => console.log(err))
      },
      // 预览根据返回值自检， radio、dropdown跳转调整show的状态
      // jumpid 如果是false, 说明按照顺序显示，全部为show
      nextBack (jumpid, nowid) {
        const start = this.id_list.indexOf(nowid) + 1
        // radio、dropdown跳转 的 跳转最远距离
        let max = 0
        for (let i in this.sourceData[start - 1].answers) {
          let id = this.sourceData[start - 1].answers[i].next
          max > this.id_list.indexOf(id) ? '' : max = this.id_list.indexOf(id)
        }
        let nextLoop = start
        if (jumpid === false) {
          // 不跳转
          for (let i = start; i < max; i++) {
            this.sourceData[i].show = true
          }
        } else if (jumpid === 'end_form_submit') {
          // 跳转到结束问题
          for (let i = start; i < this.sourceData.length; i++) {
            this.sourceData[i].show = false
          }
        } else {
          const end = this.id_list.indexOf(jumpid)
          if (end - start < 0) return

          // 有跳转
          for (let i = start; i < this.sourceData.length; i++) {
            (parseInt(i) < parseInt(end)) ? this.sourceData[i].show = false : this.sourceData[i].show = true
          }
          nextLoop = end
        }

        for (let i = nextLoop; i < this.sourceData.length; i++) {
          if (this.sourceData[i].show) {
            if (!this.sourceData[i].isjump) continue

            const val = this.sourceData[i].value
            this.sourceData[i].answers.map(option => {
              if (val === option.text && option.next !== '') this.nextBack(option.next, this.sourceData[i].id)
            })
          }
        }
      },
      // 保存 提交 患者数据
      savePatientData (status) {
        // save 检查必选项
        this.pBaseKeepAnswers()
        if (!this.saveFlag) {
          if (!this.pbaseKeepAll) this.toast({type: 'error', text: '创建患者必填项未填！'})
          else this.toast({type: 'error', text: '患者基本信息中数据格式有误！'})
          return
        }
        // subbmit 检查必选项
        if (status === '2') {
          this.pRecordKeepAnswers()
          if (!this.submitFlag) {
            if (!(this.keepHint.every(item => !item) && this.pbaseKeepAll)) this.toast({type: 'error', text: '您有必填项未填！'})
            else this.toast({type: 'error', text: '患者病例数据格式有误！'})
            return
          }
        }
        this.patientBase.phone ? this.patientBase.phone = this.patientBase.phone.replace(/[^\d]/g, '') : ''
        this.patientBase.secondphone ? this.patientBase.secondphone = this.patientBase.secondphone.replace(/[^\d]/g, '') : ''
        for (let i in this.sourceData) {
          let item = this.sourceData[i]
          if ((typeof item.show) !== 'undefined') {
            if (!item.show) {
              // if (Type(item.value) === 'string') item.value = ''
              // if (Type(item.value) === 'object') item.value = {}
              // if (Type(item.value) === 'array') item.value = []
              this.patientData[i].answer = ''
            }
          }
        }
        // 更新, 新建病历数据
        let userinfo = JSON.parse(JSON.stringify(this.patientBase))
        userinfo.userid = this.$root.userid
        userinfo.projectid = this.$route.params.projectid
        userinfo.sourcedata = JSON.stringify(this.sourceData)
        userinfo.patientdata = JSON.stringify(this.patientData)
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
        }).catch(() => this.toast({type: 'error', text: '网络异常，创建患者失败！'}))
      },
      // 正则判断
      regExp (regStr, str) {
        let regExp = new RegExp(regStr)
        if (regExp.test(str)) return true
        else return false
      },
      // 患者基本信息判断必填项
      pBaseKeepAnswers () {
        // 基本信息
        // 必选项判断
        if (!this.patientBase.name) this.pbaseName = true
        else this.pbaseName = false
        if (!this.patientBase.gender) this.pbaseSex = true
        else this.pbaseSex = false
        if (!this.patientBase.phone) this.pbasePhone = true
        else this.pbasePhone = false
        if (JSON.stringify(this.sPatientBase.birthday) !== '{}') {
          let flagKeep = []
          let flagReg = []
          let str
          for (let i in this.sPatientBase.birthday) {
            if (this.sPatientBase.birthday[i]) {
              flagKeep.push(true)
            } else {
              flagKeep.push(false)
            }
            // 判断数据格式
            if (i === 'year') str = /^[1-9]\d{3}$/
            else if (i === 'month') str = /^([1-9]|0[1-9]|1[0-2])$/
            else str = /^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/
            flagReg.push(this.regExp(str, this.sPatientBase.birthday[i]))
          }
          if (flagKeep.every(item => item)) this.pbaseBirth = false
          else this.pbaseBirth = true
          if (flagReg.every(item => item)) this.pbaseBirthReg = false
          else this.pbaseBirthReg = true
        } else {
          this.pbaseBirth = true
        }
        // 入组时间判断
        if (JSON.stringify(this.sPatientBase.jointime) !== '{}') {
          let flag = []
          let str
          for (let j in this.sPatientBase.jointime) {
            if (j === 'year') str = /^[1-9]\d{3}$/
            else if (j === 'month') str = /^([1-9]|0[1-9]|1[0-2])$/
            else str = /^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/
            flag.push(this.regExp(str, this.sPatientBase.jointime[j]))
          }
          if (flag.every(item => item)) this.pbaseJoinReg = false
          else this.pbaseJoinReg = true
        }
      },
      // 患者病历数据必填项选择
      pRecordKeepAnswers () {
        // 必填项
        this.keepHint.splice(0)
        this.regHint.splice(0)
        for (let i in this.sourceData) {
          let item = this.sourceData[i]
          if (item.show) { // 该问题展示了，再进行必选项判断
            if (item.keep) {
              if (Type(item.value) === 'object' && JSON.stringify(item.value) !== '{}') {
                let flagKeep = []
                let flagReg = []
                let str
                for (let j in item.value) {
                  if (item.answers[j] || (typeof item.answers[j]) === 'undefined') { // 该项存在的话再去判断其值是否为空
                    if (item.value[j]) {
                      flagKeep.push(true)
                    } else {
                      flagKeep.push(false)
                    }
                    if (item.type === 'DATE') {
                      if (j === 'year') str = /^[1-9]\d{3}$/
                      else if (j === 'month') str = /^([1-9]|0[1-9]|1[0-2])$/
                      else str = /^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/
                      flagReg.push(this.regExp(str, item.value[j]))
                    } else if (item.type === 'TIME') {
                      if (j === 'hour') str = /^(\d|[0-1]\d|2[0-3])$/
                      else str = /^(\d|[0-5]\d)$/
                      flagReg.push(this.regExp(str, item.value[j]))
                    }
                  }
                }
                if (flagKeep.every(item => item)) this.keepHint.push(false)
                else this.keepHint.push(true)
                if (flagReg.every(item => item)) this.regHint.push(false)
                else this.regHint.push(true)
              } else if (Type(item.value) === 'array' && item.value.length) {
                this.keepHint.push(false)
                this.regHint.push(false)
              } else if (Type(item.value) === 'string' && item.value !== '') {
                this.keepHint.push(false)
                this.regHint.push(false)
              } else {
                this.keepHint.push(true)
                this.regHint.push(false)
              }
            } else {
              this.keepHint.push(false)
              this.regHint.push(false)
            }
          } else {
            this.keepHint.push(false)
            this.regHint.push(false)
          }
        }
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
    transition: opacity .3s ease;
  }
  .dialog-enter {
    opacity: 0;
  }

  .dialog-leave-active {
    opacity: 0;
  }

  .dialog-enter .dialog-container,
  .dialog-leave-active .dialog-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
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
  .section, .form, .back{
    margin-top: 30px;
    padding: 16px 24px 24px 42px;
    border-bottom: 1px solid #eee;
    background: #fff;
  }
  .section p.title, .form p.title, .back p.title{
    font-size: 26px;
    margin: 0;
  }
  .link-dec {
    display: block;
  }
  .link-dec a {
    color: #468df1;
  }
  .questions {
    padding: 16px 24px 24px 42px;
    border-bottom: 1px solid #ddd;
    background: #fff;
  }

  /*必答*/
  .keep {
    color: red;
    font-size: 18px;
  }
  .keep-hint {
    color: red;
    font-size: 14px;
  }
  .questions .q {
    padding: 8px 0 12px 0;
  }

  .questions .q p {
    font-size: 16px;
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