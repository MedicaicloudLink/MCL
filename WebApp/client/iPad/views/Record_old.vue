<template>
  <div id="record">
    <transition name="dialog">
      <div class="dialog-mask" v-if="read" :style="{width: width + 'px', top: top + 'px'}"></div>
    </transition>
    <!--头部工具栏-->
    <div id="header" class="header flex-row" style="flex-direction: row-reverse">
      <div class="tools flex-row" v-if="!read">
        <dropdown>
          <div class="btn-gray tool" slot="trigger"><i>添加...</i><i class="iconfont icon-xiasanjiao-copy"></i></div>
          <li @click="addTextNote">文字备注说明</li>
          <li @click="upFileFlag = true">照片和音视频文件</li>
        </dropdown>
        <dropdown>
          <div class="btn-gray tool" slot="trigger">操作...<i class="iconfont icon-xiasanjiao-copy"></i></div>
          <li @click="savePatientData('1')">保存并返回</li>
          <li @click="cancel">取消并返回</li>
        </dropdown>
        <div class="btn-blue" style="width: 96px;" @click="submit">提交病历</div>
      </div>
    </div>
    <!--end-->
    <!--章节标题栏-->
    <div class="right-menu" :style="{left: left + 'px', top: top + 'px'}">
      <p :class="{active: item.flag}" v-for="item, index in section" @click="scrollEvent(index)">
        {{item.title}}
        <span v-if="item.title === '备注说明'">（{{ textNoteList.length }}）</span>
        <span v-if="item.title === '文档附件'">（{{ fileList.length }}）</span>
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
    <addfile :open="upFileFlag" @close="close"></addfile>
    <!--end-->
    <!--患者病历-->
    <div class="patient-record" id="patient-record" :style="{width: width + 'px'}">
      <div class="form" v-if="questions.length">
        <p class="title">{{ questions[0].title }}</p>
        <span class="dec">{{ questions[0].describe }}</span>
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
            <div class="a-item"><m-input hintText="您的姓名" full :value.sync="sPatientBase.u_patientname" v-model="patientBase.u_patientname"></m-input></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>性别<span class="keep">*</span><span class="keep-hint" v-if="pbaseSex">(此题是必填项，必须完成之后才可以提交)</span></p></div>
            <div class="a-item">
              <m-radio nativeValue="男" :value.sync="sPatientBase.u_gender" v-model="patientBase.u_gender">男</m-radio>
              <m-radio nativeValue="女" :value.sync="sPatientBase.u_gender" v-model="patientBase.u_gender">女</m-radio>
            </div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>出生日期<span class="keep">*</span><span class="keep-hint" v-if="pbaseBirth">(此题是必填项，必须完成之后才可以提交)</span></p></div>
            <div class="a-item"><p-date :answers.sync="sPatientBase.u_birthday" :id="'birthday'" v-model="patientBase.u_birthday"></p-date></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>联系电话<span class="keep">*</span><span class="keep-hint" v-if="pbaseBirth">(此题是必填项，必须完成之后才可以提交)</span></p></div>
            <div class="a-item"><m-input hintText="电话号码" full :answers="sPatientBase.u_phone" v-model="patientBase.u_phone"></m-input></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>备用电话</p></div>
            <div class="a-item"><m-input hintText="电话号码" full :answers="sPatientBase.u_secondphone" v-model="patientBase.u_secondphone"></m-input></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>入组时间</p></div>
            <div class="a-item"><p-date :answers="sPatientBase.u_jointime" :id="'jointime'" v-model="patientBase.u_jointime"></p-date></div>
          </div>
        </div>
        <div class="item base-info">
          <div class="questions">
            <div class="q"><p>地址</p></div>
            <div class="a-item"><p-address :answers="sPatientBase.u_address" v-model="patientBase.u_address"></p-address></div>
          </div>
        </div>
      </div>
      <!--end-->
      <!--患者病历数据-->
      <div class="item" v-for="(qa, qaid) in questions" v-if="sourceData.length > 0">
        <div class="form" v-if="qa.type === 'FORM'"></div>

        <div class="section" v-else-if="qa.type === 'SECTION'" :id="qa.title">
          <p class="title">{{ qa.title }}</p>
          <span class="dec">{{ qa.describe.text }}</span>
        </div>

        <div class="questions" v-else>

          <div class="q" v-show="qa.show" :key="qa.id">
            <p>
              {{ qa.title }}
              <span class="keep" v-if="qa.keep">*</span>
              <span class="keep-hint" v-if="keepHint[qaid]">(此题是必填项，必须完成之后才可以提交)</span>
            </p>
            <span class="dec">{{ qa.describe.text }}</span>
            <span class="link-dec"><a :href="'http://' + qa.link.text" target="_blank">{{ qa.link.text }}</a></span>
            <img :src="'http://' + qa.img.url" alt="option" v-if="qa.img.url.length > 10" class="img-option">
          </div>
          <div class="a-item" v-show="qa.show" >
            <!--单填-->
            <p-multiple-choice :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'RADIO'" @next="nextBack(arguments[0], qa.id)"></p-multiple-choice>
            <!--单选 end-->

            <!--地址-->
            <p-address :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'ADDRESS'" @next="nextBack(arguments[0], qa.id)"></p-address>
            <!--地址 end-->

            <!--多选-->
            <p-checkboxes :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'CHECKBOX'"></p-checkboxes>
            <!--多选 end-->

            <!--下拉选择-->
            <p-dropdowns :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'DROPDOWN'"></p-dropdowns>
            <!--下拉选择 end-->

            <!--表格选择-->
            <p-table :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'TABLE'"></p-table>
            <!--表格选择 end-->

            <!--线性量表选择-->
            <p-linear-scale :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'LINEARSCALE'"></p-linear-scale>
            <!--线性量表选择 end-->

            <!--时间选择-->
            <p-time :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'TIME'"></p-time>
            <!--时间选择 end-->

            <!--日期选择-->
            <p-date :answers.sync="sourceData[qaid].answers" :id="qa.id" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'DATE'"></p-date>
            <!--日期选择 end-->

            <!--简短回答-->
            <p-short-text :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" :option="qa.answers" v-if="qa.type === 'SHORTTEXT'"></p-short-text>
            <!--简短回答 end-->

            <!--段落回答-->
            <p-long-text :answers.sync="sourceData[qaid].answers" v-model="patientData[qaid].answers" v-if="qa.type === 'LONGTEXT'"></p-long-text>
            <!--段落回答 end-->

            <!--上传文件类型-->
            <p-file-upload :value.sync="qa.value" v-if="qa.type === 'FILEUPLOAD'"></p-file-upload>
            <!--上传文件类型 end-->
          </div>
        </div>

      </div>
      <!--end-->
      <!--备注说明列表-->
      <div class="section text-note">
        <p class="title">备注说明</p>
        <div class="text-note-item" v-for="item, index in textNoteList">
          <p class="text-note-content">{{ item.remark }}</p>
          <p class="text-note-log flex-row">
            <span>{{ item.s_username }} {{ item.createtime }}</span>
            <img style="cursor: pointer;" src="../assets/icon_svg/icon_delete.svg" @click="deleteTextNote(item.id, index)">
          </p>
        </div>
      </div>
      <!--end-->
      <!--文档附件及说明列表-->
      <div class="section text-note">
        <p class="title">文档附件</p>
        <div class="text-note-item" v-for="item, index in fileList">
          <p class="text-note-content  flex-row">
            <span style="width: 250px;margin-right: 20px;">{{ item.name }}</span>
            <span style="color: rgba(0,0,0,.54);">{{ item.remark }}</span>
          </p>
          <p class="text-note-log flex-row">
            <span>{{ item.s_username }} {{ item.createtime }}</span>
            <img style="cursor: pointer;" src="../assets/icon_svg/icon_delete.svg" @click="deleteFile(item.id, index)">
          </p>
        </div>
      </div>
      <!--end-->
    </div>
    <!--end-->
  </div>
</template>
<script>
  import API from '../api.js'
  import { Type, throttle, scrollTo } from '../tool/tools.js'
  import PMultipleChoice from './preview/MultipleChoice.vue'
  import PAddress from './preview/Address.vue'
  import PCheckboxes from './preview/Checkboxes.vue'
  import PDropdowns from './preview/Dropdowns.vue'
  import PTable from './preview/Table.vue'
  import PLinearScale from './preview/LinearScale.vue'
  import PTime from './preview/Time.vue'
  import PDate from './preview/Date.vue'
  import PShortText from './preview/ShortText.vue'
  import PLongText from './preview/LongText.vue'
  import PFileUpload from './preview/FileUpload.vue'
  export default {
    data () {
      return {
        addTextNoteFlag: false,
        textNote: '',
        upFileFlag: false,
        read: false, // 只读 遮罩层
        section: [], // 章节标题
        textNoteList: [], // 文字备注列表
        fileList: [], // 文档附件列表
        questions: [], // 表单问题数据
        sourceData: [], // 患者源数据
        patientData: [], // 患者数据
        sPatientBase: {}, // 患者基本信息源数据
        patientBase: { // 患者基本信息
          u_patientname: '',
          u_gender: '',
          u_birthday: '',
          u_jointime: '',
          u_address: '',
          u_phone: '',
          u_secondphone: ''
        },
        pbaseName: false, // base必选项提示
        pbaseSex: false,
        pbaseBirth: false,
        pbasePhone: false,
        keepHint: [], // 必选项提示
        left: 0,
        top: 0,
        width: 0,
        interval: null, // 定时器
        scrollTopVal: 0, // 文档滚动高度
        offsetTopVal: 0 // 记录点击之后的当前章节的offsetTop
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
      PFileUpload
    },
    computed: {
      id_list: function () {
        let qa = []
        for (let sid in this.questions) {
          if (this.questions[sid].type === 'SECTION' || this.questions[sid].type === 'FORM') {
            qa.push('')
          } else {
            qa.push(this.questions[sid].id)
          }
        }
        return qa
      },
      saveFlag () {
        if (!this.pbaseName && !this.pbaseSex && !this.pbaseBirth) return true
        else return false
      },
      submitFlag () {
        if (this.keepHint.every(item => !item) && !this.pbaseName && !this.pbaseSex && !this.pbaseBirth) return true
        else return false
      }
    },
    beforeRouteEnter (to, from, next) {
      next(vm => {
        vm.init()
      })
    },
    mounted () {
      // right-menu 的left 和 top 值  病例主体的width
      this.width = document.getElementById('record').offsetWidth - 192 - 64 - 32
      this.left = document.getElementById('patient-record').offsetLeft + this.width + 32
      this.top = document.getElementById('patient-record').offsetTop
      window.addEventListener('resize', () => {
        if (!document.getElementById('record')) return
        this.width = document.getElementById('record').offsetWidth - 192 - 64 - 32
        this.left = document.getElementById('patient-record').offsetLeft + this.width + 32
      }, false)
      // 判断提交病历状态
      if (this.$route.name === 'commitlist-record') this.read = true
      else this.read = false
      this.init()
      //
      scrollTo(document.body, 0, 300)
      window.addEventListener('scroll', (e) => {
        let vm = this
        if (vm.interval === null) {
          vm.interval = setInterval(function () {
            // 定时判断页面滚动是否结束
            if (document.body.scrollTop === vm.scrollTopVal) {
              throttle(vm.scrollEvent(), 1000)
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
      // 页面滚动
      scrollEvent (index) {
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
      // 病历初始化
      init () {
        if (this.$route.name !== 'newrecord') {
          // 编辑
          this.getPatientBase()
          this.getPatientRecord() // 当前患者对应的表单数据
          this.getNoteList()
          this.getFileList()
        } else {
          // 新建
          this.getProjectInfo() // 项目中表单问题数据
        }
      },
      // 右侧菜单栏
      rightMenu () {
        this.section = [{title: '基本信息', flag: true}]
        for (let sid in this.questions) {
          if (this.questions[sid].type === 'SECTION') {
            this.section.push({ title: this.questions[sid].title, flag: false })
          }
        }
        this.section.push({title: '备注说明', flag: false})
        this.section.push({title: '文档附件', flag: false})
      },
      // 获取项目表单
      getProjectInfo () {
        API.GetProjectInfo({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          let list = JSON.parse(response[0].formdata)
          for (let sid in list) {
            list[sid].show = true
          }
          this.questions = list
          this.answersInit()
          this.rightMenu()
        }).catch((err) => {
          console.log(err)
        })
      },
      // 初始化问题答案形式
      answersInit () {
        this.sourceData = []
        for (let q in this.questions) {
          if (this.questions[q].type === 'FORM') {
            this.sourceData.push({
              id: '表单名称',
              title: this.questions[q].title
            })
            this.patientData.push({
              title: '表单名称',
              answers: this.questions[q].title
            })
          } else if (this.questions[q].type === 'SECTION') {
            this.sourceData.push({
              id: '章节名称',
              title: this.questions[q].title
            })
            this.patientData.push({
              title: '章节名称',
              answers: this.questions[q].title
            })
          } else {
            let answers = ''
            this.patientData.push({
              title: this.questions[q].title,
              answers: answers
            })
            if (this.questions[q].type === 'SHORTTEXT') answers = {}
            if (this.questions[q].type === 'ADDRESS') answers = {}
            if (this.questions[q].type === 'CHECKBOX') answers = []
            if (this.questions[q].type === 'TABLE') answers = []
            if (this.questions[q].type === 'TIME') answers = {}
            if (this.questions[q].type === 'DATE') answers = {}
            this.sourceData.push({
              id: this.questions[q].id,
              title: this.questions[q].title,
              keep: this.questions[q].keep,
              answers: answers
            })
          }
        }
      },
      // 患者基本信息
      getPatientBase () {
        API.GetPatientBase({mdid: this.$route.params.mdid}).then((response) => {
          for (let i in this.patientBase) {
            if (i === 'u_gender') {
              this.patientBase[i] = response[0][i] === '1' ? '男' : response[0][i] === '2' ? '女' : ''
              this.sPatientBase[i] = this.patientBase[i]
              continue
            }
            this.patientBase[i] = response[0][i]
            if (i === 'u_birthday' || i === 'u_jointime') {
              if (!response[0][i]) continue
              this.sPatientBase[i] = {}
              this.sPatientBase[i].year = response[0][i].split('-')[0]
              this.sPatientBase[i].month = response[0][i].split('-')[1]
              this.sPatientBase[i].day = response[0][i].split('-')[2]
            } else if (i === 'u_address') {
              if (!response[0][i]) continue
              this.sPatientBase[i] = {}
              this.sPatientBase[i].province = response[0][i].split(' ')[0]
              this.sPatientBase[i].city = response[0][i].split(' ')[1]
              this.sPatientBase[i].text = response[0][i].split(' ')[2]
            } else {
              this.sPatientBase[i] = response[0][i]
            }
          }
        }).catch((err) => {
          console.log(err)
        })
      },
      // 患者病例数据
      getPatientRecord () {
        API.GetPatientRecord({patientid: this.$route.params.mdid}).then((response) => {
          this.sourceData = JSON.parse(response[0].sourcedata)
          this.patientData = JSON.parse(response[0].patientdata)
          let list = JSON.parse(response[0].template)
          for (let sid in list) {
            list[sid].show = true
          }
          this.questions = list
          this.rightMenu()
          if (response[0].status === '2') this.read = true
        }).catch((err) => {
          console.log(err)
        })
      },
      // 获取患者备注
      getNoteList () {
        API.GetNoteList({mdid: this.$route.params.mdid}).then((response) => {
          this.textNoteList = response
        }).catch((err) => {
          console.log(err)
        })
      },
      // 获取文件列表
      getFileList () {
        API.GetFileList({patientid: this.$route.params.mdid}).then((response) => {
          this.fileList = response
        }).catch((err) => {
          console.log(err)
        })
      },
      // 预览根据返回值自检， radio、dropdown跳转调整show的状态
      // jumpid 如果是false, 说明按照顺序显示，全部为show
      nextBack (jumpid, nowid) {
        const start = this.id_list.indexOf(nowid) + 1
        if (jumpid === false) {
          for (let i = start; i < this.questions.length; i++) {
            this.questions[i].show = true
          }
          return
        }
        const end = this.id_list.indexOf(jumpid)
        if (end - start < 0) return

        for (let i = start; i < this.questions.length; i++) {
          i < end ? this.questions[i].show = false : this.questions[i].show = true
        }
      },
      // 保存患者数据
      savePatientData (status) {
        this.pBaseKeepAnswers()
        if (this.saveFlag) {
          let gender = this.patientBase.u_gender === '男' ? '1' : this.patientBase.u_gender === '女' ? '2' : ''
          if (this.$route.params.mdid) {
            // 患者基本信息修改
            API.EditPatientBase({
              userid: this.$root.userid,
              projectid: this.$route.params.projectid,
              mdid: this.$route.params.mdid,
              name: this.patientBase.u_patientname,
              gender: gender,
              birthday: this.patientBase.u_birthday,
              jointime: this.patientBase.u_jointime,
              address: this.patientBase.u_address,
              phone: this.patientBase.u_phone,
              secondphone: this.patientBase.u_secondphone
            }).then((response) => {
              this.savePatientRecord(this.$route.params.mdid, status)
            }).catch((err) => {
              console.log(err)
            })
          } else {
            // 创建新患者病例
            API.SavePatientBase({
              userid: this.$root.userid,
              projectid: this.$route.params.projectid,
              name: this.patientBase.u_patientname,
              gender: gender,
              birthday: this.patientBase.u_birthday,
              jointime: this.patientBase.u_jointime,
              address: this.patientBase.u_address,
              phone: this.patientBase.u_phone,
              secondphone: this.patientBase.u_secondphone
            }).then((response) => {
              this.savePatientRecord(response.recordid, status)
            }).catch(() => {
              this.toast({
                type: 'error',
                text: '网络异常，创建患者失败！',
                placement: 'top'
              })
            })
          }
        } else {
          this.toast({
            type: 'error',
            text: '创建患者必填项未填！',
            placement: 'top'
          })
        }
      },
      savePatientRecord (mdid, status) {
        API.SavePatientRecord({
          userid: this.$root.userid,
          mdid: mdid,
          patientdata: JSON.stringify(this.patientData),
          sourcedata: JSON.stringify(this.sourceData),
          status: status
        }).then((response) => {
          // 返回的位置
          if (this.$route.name === 'newrecord') {
            this.$router.push({name: 'savelist'})
          } else {
            window.history.go(-1)
          }
          this.$emit('getCaseState')
          this.toast({
            type: 'success',
            text: '保存成功！',
            placement: 'top'
          })
        }).catch(() => {
          API.DeletePatient({userid: this.$root.userid, mdid: mdid}).then((response) => {
            console.log(response)
          }).catch((err) => { console.log(err) })
          this.toast({
            type: 'error',
            text: '保存失败，重新保存试试！',
            placement: 'top'
          })
        })
      },
      // 添加文字备注说明
      addTextNote (e) {
        console.log(e.target)
        this.addTextNoteFlag = true
        scrollTo(document.body, 0, 300)
      },
      // 保存备注
      saveTextNote () {
        if (this.$route.params.mdid) {
          API.CreateTextNote({userid: this.$root.userid, mdid: this.$route.params.mdid, remark: this.textNote}).then((response) => {
            this.toast({
              type: 'success',
              text: '创建备注成功',
              placement: 'top'
            })
            this.textNote = ''
            this.addTextNoteFlag = false
            this.getNoteList()
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '创建备注失败',
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'warning',
            text: '请先创建患者',
            placement: 'top'
          })
        }
      },
      // 删除备注
      deleteTextNote (noteid, index) {
        let vm = this
        this.confirm({
          title: '删除备注',
          message: '确定删除此备注？',
          onConfirm () {
            API.DeleteTextNote({userid: vm.$root.userid, id: noteid}).then((response) => {
              vm.textNoteList.splice(index, 1)
              vm.toast({
                type: 'success',
                text: '删除备注成功！',
                placement: 'top'
              })
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '网络异常，删除失败，重新删除！',
                placement: 'top'
              })
            })
          }
        })
      },
      // 删除文档附件
      deleteFile (noteid, index) {
        let vm = this
        this.confirm({
          title: '删除文档附件',
          message: '确定删除此文档附件？',
          onConfirm () {
            API.DeleteFile({userid: vm.$root.userid, id: noteid}).then((response) => {
              vm.fileList.splice(index, 1)
              console.log(vm.fileList.length)
              vm.toast({
                type: 'success',
                text: '删除文档附件成功！',
                placement: 'top'
              })
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '网络异常，删除失败，重新删除！',
                placement: 'top'
              })
            })
          }
        })
      },
      // 关闭addfile对话框
      close () {
        this.upFileFlag = false
        this.getFileList()
      },
      // 患者基本信息判断必填项
      pBaseKeepAnswers () {
        let flag = []
        // 基本信息
        if (!this.sPatientBase.u_patientname) this.pbaseName = true
        else this.pbaseName = false
        if (!this.sPatientBase.u_gender) this.pbaseSex = true
        else this.pbaseSex = false
        if (!this.sPatientBase.u_phone) this.pbasePhone = true
        else this.pbasePhone = false
        if (this.sPatientBase.u_birthday) {
          for (let i in this.sPatientBase.u_birthday) {
            if (this.sPatientBase.u_birthday[i]) {
              flag.push(true)
            } else {
              flag.push(false)
            }
          }
          if (flag.every(item => item)) this.pbaseBirth = false
          else this.pbaseBirth = true
        } else {
          this.pbaseBirth = true
        }
      },
      // 患者病历数据必填项选择
      pRecordKeepAnswers () {
        // 病历数据
        let flag = []
        this.keepHint.splice(0)
        for (let i in this.sourceData) {
          if (this.sourceData[i].keep) {
            if (Type(this.sourceData[i].answers) === 'object' && JSON.stringify(this.sourceData[i].answers) !== '{}') {
              for (let j in this.sourceData[i].answers) {
                if (this.sourceData[i].answers[j]) {
                  flag.push(true)
                } else {
                  flag.push(false)
                }
              }
              if (flag.every(item => item)) this.keepHint.push(false)
              else this.keepHint.push(true)
            } else if (Type(this.sourceData[i].answers) === 'array' && this.sourceData[i].answers !== []) {
              this.keepHint.push(false)
            } else if (Type(this.sourceData[i].answers) === 'string' && this.sourceData[i].answers !== '') {
              this.keepHint.push(false)
            } else {
              this.keepHint.push(true)
            }
          } else {
            this.keepHint.push(false)
          }
          console.log(this.keepHint)
        }
      },
      // 提交病历
      submit () {
        this.pBaseKeepAnswers()
        this.pRecordKeepAnswers()
        if (this.submitFlag) {
          this.savePatientData('2')
        } else {
          this.toast({
            type: 'warning',
            text: '您有必填项未填！',
            placement: 'top'
          })
        }
      },
      // 取消返回
      cancel () {
        if (this.$route.name === 'newrecord') {
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
  .section, .form{
    margin-top: 30px;
    padding: 16px 24px 24px 42px;
    border-bottom: 1px solid #eee;
    background: #fff;
  }
  .section p.title, .form p.title{
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