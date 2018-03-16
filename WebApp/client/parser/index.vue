<template>
  <div class="crf">
    <div class="item form-title" v-if="questions.length && questions[0].type === 'FORM'">
      <p class="title">{{ questions[0].title }}</p>
      <span class="dec">{{ questions[0].describe }}</span>
    </div>
    <formbase ref="base" :patientB="patientB" v-if="isBase"></formbase>
    <div v-if="questions.length > 0">
      <div class="item" v-for="(qa, qaid) in questions" :key="qa.id">
        <div v-if="qa.type === 'FORM'"></div>
        
        <div class="section" v-else-if="qa.type === 'SECTION'">
          <p class="title">{{ qa.title }}</p>
          <span class="dec">{{ qa.describe.text }}</span>
        </div>

        <div class="questions" v-show="qa.show" v-else>

          <div class="q">
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
            <p-multiple-choice :answers.sync="qa.value" :otherVal.sync="qa.other" :option="qa.answers" v-if="qa.type === 'RADIO'" @next="nextBack(arguments[0], qa.id)"></p-multiple-choice>
            <!--单选 end-->

            <!--地址-->
            <p-address :answers.sync="qa.value" :option="qa.answers" v-if="qa.type === 'ADDRESS'"></p-address>
            <!--地址 end-->

            <!--多选-->
            <p-checkboxes :answers.sync="qa.value" :otherVal.sync="qa.other" :option="qa.answers" v-if="qa.type === 'CHECKBOX'"></p-checkboxes>
            <!--多选 end-->

            <!--下拉选择-->
            <p-dropdowns :answers.sync="qa.value"  :option="qa.answers" v-if="qa.type === 'DROPDOWN'" @next="nextBack(arguments[0], qa.id)"></p-dropdowns>
            <!--下拉选择 end-->

            <!--表格选择-->
            <p-table :answers.sync="qa.value"  :option="qa.answers" v-if="qa.type === 'TABLE'"></p-table>
            <!--表格选择 end-->

            <!--线性量表选择-->
            <p-linear-scale :answers.sync="qa.value"  :option="qa.answers" v-if="qa.type === 'LINEARSCALE'"></p-linear-scale>
            <!--线性量表选择 end-->

            <!--时间选择-->
            <p-time :answers.sync="qa.value"  :option="qa.answers" v-if="qa.type === 'TIME'"></p-time>
            <!--时间选择 end-->

            <!--日期选择-->
            <p-date :answers.sync="qa.value"  :option="qa.answers" v-if="qa.type === 'DATE'"></p-date>
            <!--日期选择 end-->

            <!--简短回答-->
            <p-short-text :answers.sync="qa.value"  :option="qa.answers" v-if="qa.type === 'SHORTTEXT'"></p-short-text>
            <!--简短回答 end-->

            <!--段落回答-->
            <p-long-text :answers.sync="qa.value"  v-if="qa.type === 'LONGTEXT'"></p-long-text>
            <!--段落回答 end-->

            <!--上传文件类型-->
            <p-file-upload :answers.sync="qa.value"  v-if="qa.type === 'FILEUPLOAD'"></p-file-upload>
            <!--上传文件类型 end-->
          </div>
        </div>

      </div>
    </div>
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
import PFileUpload from './FileUpload.vue'
import Formbase from './Baseinfo.vue'
// import { Type } from '../tools/Type'

import initForm from './init'
import { keepVerify, regVerify } from './verify'

export default {
  name: 'Form',
  components: {
    Formbase,
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
    data: Array,
    isBase: {type: Boolean, default: true},
    patientB: Object,
    temp: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      questions: [],
      id_list: [],
      keepHint: [],
      regHint: []
    }
  },
  watch: {
    temp () { this.init() },
    questions (val) { this.$emit('update:data', val) }
  },
  mounted () { this.init() },
  methods: {
    // 初始化 question 和 id_list
    init () {
      if (this.data.length > 0) {
        // 编辑
        this.questions = JSON.parse(JSON.stringify(this.data))
      } else {
        // 新建
        this.keepHint = []
        this.regHint = []
        this.questions = initForm(this.temp)
        if (this.isBase) this.$refs.base.initBase()
      }
      // 获取所有问题的id 用于问题跳转
      for (const i in this.questions) {
        const id = this.questions[i].id
        id ? this.id_list.push(id) : this.id_list.push('')
      }
    },
    // 预览根据返回值自检， radio、dropdown跳转调整show的状态
    // jumpid 如果是false, 说明按照顺序显示，全部为show
    nextBack (jumpid, nowid) {
      // console.log(jumpid + '=======' + nowid)
      const start = this.id_list.indexOf(nowid) + 1

      // 选项无跳转
      let max = 0
      for (let i in this.questions[start - 1].answers) {
        let id = this.questions[start - 1].answers[i].next
        max > this.id_list.indexOf(id) ? '' : max = this.id_list.indexOf(id)
      }

      let nextLoop = start
      if (jumpid === false) {
        for (let i = start; i < max; i++) {
          this.questions[i].show = true
        }
      } else if (jumpid === 'end_form_submit') {
        for (let i = start; i < this.questions.length; i++) {
          this.questions[i].show = false
        }
      } else {
        const end = this.id_list.indexOf(jumpid)
        if (end - start < 0) return

        for (let i = start; i < this.questions.length; i++) {
          parseInt(i) < parseInt(end) ? this.questions[i].show = false : this.questions[i].show = true
        }
        nextLoop = end
      }

      for (let i = nextLoop; i < this.questions.length; i++) {
        if (this.questions[i].show) {
          const type = this.questions[i].type
          if (type !== 'RADIO' && type !== 'DROPDOWN') continue
          // console.log(start, nextLoop, this.questions[i].id, this.questions[i].value)

          if (!this.questions[i].isjump) continue

          const val = this.questions[i].value
          this.questions[i].answers.map(option => {
            if (val === option.text && option.next !== '') this.nextBack(option.next, this.questions[i].id)
          })
        }
      }
    },
    saveVerify () {
      return this.$refs.base.pBaseKeepAnswers()
    },
    submitVerify () {
      const keep_result = keepVerify(this.questions)
      const reg_result = regVerify(this.questions)
      this.keepHint = keep_result
      this.regHint = reg_result
      if (this.isBase) {
        if (!this.saveVerify()) return false
      }
      if (this.keepHint.indexOf(true) !== -1) return false
      if (this.regHint.indexOf(true) !== -1) return false

      return true
      // if (this.isBase) {
      //   if (this.saveVerify() && this.keepHint.indexOf(true) === -1 && this.regHint.indexOf(true) === -1) return true
      // } else {
      //   if (this.keepHint.indexOf(true) === -1 && this.regHint.indexOf(true) === -1) return true
      // }
      // return false
    }
  }
}
</script>

<style scoped>
.crf {
  min-width: 300px;
} 

.item {
  background: #fff;
}

.section, .form-title {
  margin-top: 30px;
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #eee;
}

.form-title {
  text-align: center;
}

.section p.title, .form-title p.title {
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
