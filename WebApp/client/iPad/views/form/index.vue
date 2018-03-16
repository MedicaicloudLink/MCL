<template>
  <div class="preview-form">
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
          <div class="a-item"><p-date :answers.sync="sPatientBase.birthday" v-model="patientBase.birthday"></p-date></div>
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
    <!--患者病历数据-->
    <div v-if="patientData.length > 0">
      <div class="item" v-for="(qa, qaid) in sourceData" :key="qaid">

        <div class="form-title" v-if="qa.type === 'FORM'">
          <p class="title">{{ sourceData[0].title }}</p>
          <span class="dec">{{ sourceData[0].describe }}</span>
        </div>

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
    </div>
    <!--end-->
  </div>
</template>

<script>
// preview
import PMultipleChoice from './MultipleChoice.vue'
import PAddress from './Address.vue'
import PCheckboxes from './Checkboxes.vue'
import PDropdowns from './Dropdowns.vue'
import PTable from './Table.vue'
import PLinearScale from './LinearScale.vue'
import PTime from './Time.vue'
import PDate from './Date.vue'
import PShortText from './ShortText.vue'
import PLongText from './LongText'
import PFileUpload from './FileUpload'
import { Type } from '../../tool/tools'

export default {
  name: 'FromView',
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
  props: {
    patientB: Object,
    pbaseRule: Boolean,
    formData: Array,
    source: Array,
    patient: Array,
    recordRule: Boolean
  },
  data () {
    return {
      sourceData: [],
      patientData: [],
      patientBase: {},
      pbaseName: false, // 基本信息必选项
      pbaseSex: false,
      pbaseBirth: false,
      pbasePhone: false,
      pbaseBirthReg: false, // base 正则提示
      pbaseJoinReg: false,
      saveRule: false,
      keepHint: [], // 必选项提示
      regHint: [] // 正则提示
    }
  },
  computed: {
    // 所有问题的id
    id_list: function () {
      let qa = []
      for (let sid in this.sourceData) {
        if (this.sourceData[sid].type === 'SECTION') {
          qa.push('')
        } else {
          qa.push(this.sourceData[sid].id)
        }
      }
      return qa
    },
    // 患者基本信息源数据初始化处理
    sPatientBase: function () {
      let spb = {}
      for (let i in this.patientBase) {
        if (i === 'birthday' || i === 'jointime') {
          spb[i] = {}
          if (!this.patientBase[i]) continue
          spb[i].year = this.patientBase[i].split('-')[0]
          spb[i].month = this.patientBase[i].split('-')[1]
          spb[i].day = this.patientBase[i].split('-')[2]
        } else if (i === 'address') {
          spb[i] = {}
          if (!this.patientBase[i]) continue
          spb[i].province = this.patientBase[i].split(' ')[0]
          spb[i].city = this.patientBase[i].split(' ')[1]
          spb[i].text = this.patientBase[i].split(' ')[2]
        } else {
          spb[i] = this.patientBase[i]
        }
      }
      return spb
    }
  },
  watch: {
    formData () {
      this.initQuestionList()
    },
    patientBase () {
      this.$emit('update:patientB', this.patientBase)
    },
    sourceData () {
      this.$emit('update:source', this.sourceData)
      this.$emit('update:patient', this.patientData)
    }
  },
  mounted () {
    this.initQuestionList()
  },
  methods: {
    initQuestionList () {
      this.patientBase = this.patientB
      if (this.$route.params.mdid) {
        // 编辑
        this.sourceData = JSON.parse(JSON.stringify(this.source))
        this.patientData = JSON.parse(JSON.stringify(this.patient))
      } else {
        // 新建 患者源数据初始化处理
        for (let info in this.patientBase) {
          if (info === 'jointime') this.patientBase[info] = new Date().toLocaleDateString()
          else this.patientBase[info] = ''
        }
        this.patientData = []
        this.sourceData = JSON.parse(JSON.stringify(this.formData))
        for (let q in this.sourceData) {
          let item = this.sourceData[q]
          if (item.type === 'FORM' || item.type === 'SECTION') {
            this.patientData.push({title: item.type, answer: item.title})
          } else {
            item.show = true
            if (item.type === 'SHORTTEXT') item.value = ''
            if (item.type === 'ADDRESS') item.value = {}
            if (item.type === 'CHECKBOX') item.value = []
            if (item.type === 'TABLE') item.value = []
            if (item.type === 'TIME') item.value = {}
            if (item.type === 'DATE') item.value = {}
            if (item.type === 'LONGTEXT') item.value = ''
            if (item.type === 'FILEUPLOAD') item.value = {}
            if (item.type === 'RADIO') item.value = ''
            if (item.type === 'DROPDOWN') item.value = ''
            if (item.type === 'LINEARSCALE') item.value = ''
            this.patientData.push({title: item.title, answer: ''})
          }
        }
      }
    },
    // 预览根据返回值自检， radio、dropdown跳转调整show的状态
    // jumpid 如果是false, 说明按照顺序显示，全部为show
    nextBack (jumpid, nowid) {
      if (!this.sourceData.length) return
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
      this.saveRule = !this.pbaseName && !this.pbaseSex && !this.pbaseBirth && !this.pbasePhone && !this.pbaseBirthReg && !this.pbaseJoinReg
      this.$emit('update:pbaseRule', this.saveRule)
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
      let submitRule = this.keepHint.every(item => !item) && this.regHint.every(item => !item) && this.saveRule
      this.$emit('update:recordRule', submitRule)
    }
  }
}
</script>

<style scoped>
.section, .form-title, .back{
  margin-top: 30px;
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #eee;
  background: #fff;
}
.section p.title, .form-title p.title, .back p.title{
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
</style>
