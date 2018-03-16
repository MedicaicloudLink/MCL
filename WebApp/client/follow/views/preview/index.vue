<template>
  <div class="crf">
    <div class="item" v-for="(qa, qaid) in questions" v-if="questions.length > 0">
      <div class="form-title" v-if="qa.type === 'FORM'" :key="qa.id">
        <p class="title">{{ qa.title }}</p>
        <span class="dec">{{ qa.describe.text }}</span>
      </div>
      <div class="section" v-else-if="qa.type === 'SECTION'" :key="qa.id">
        <p class="title">{{ qa.title }}</p>
        <span class="dec">{{ qa.describe.text }}</span>
      </div>

      <div class="questions" v-show="qa.show" v-else>

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
          <p-time :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'TIME'"></p-time>
          <!--时间选择 end-->

          <!--日期选择-->
          <p-date :answers.sync="qa.value" v-model="patientData[qaid].answer" :option="qa.answers" v-if="qa.type === 'DATE'"></p-date>
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

</template>

<script>
import PMultipleChoice from './MultipleChoice'
import PAddress from './Address'
import PCheckboxes from './Checkboxes'
import PDropdowns from './Dropdowns'
import PTable from './Table'
import PLinearScale from './LinearScale'
import PTime from './Time'
import PDate from './Date'
import PShortText from './ShortText'
import PLongText from './LongText'
import PFileUpload from './FileUpload'
// import { Type } from '../../tool/tools'

export default {
  name: 'FormView',
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
    data: Array,
    processdata: Array,
    temp: Array,
    keepHint: Array
  },
  data () {
    return {
      maxwidth: 0,
      questions: [],
      patientData: [],
      id_list: []
    }
  },
  watch: {
    temp () { this.init() },
    patientData () {
      this.$emit('update:data', this.questions)
      this.$emit('update:processdata', this.patientData)
    }
  },
  mounted () {
    this.maxwidth = window.innerWidth - 400 - 246 // 减去侧边栏的表单导航
    this.init()
  },
  methods: {
    init () {
      if (this.data.length > 0) {
        this.questions = JSON.parse(JSON.stringify(this.data))
        this.patientData = JSON.parse(JSON.stringify(this.processdata))
      } else {
        let list = JSON.parse(JSON.stringify(this.temp))
        list.map(i => {
          if (i.type === 'SECTION' || i.type === 'FORM') {
            this.questions.push(i)
            this.patientData.push({title: i.type, answer: i.title})
          } else {
            let form = i
            form.show = true
            if (i.type === 'SHORTTEXT' || i.type === 'ADDRESS' || i.type === 'TIME' || i.type === 'DATE') form.value = {}
            if (i.type === 'CHECKBOX' || i.type === 'TABLE') form.value = []
            if (i.type === 'LONGTEXT') form.value = ''
            if (i.type === 'FILEUPLOAD') form.value = {}
            if (i.type === 'RADIO') form.value = ''
            if (i.type === 'DROPDOWN') form.value = ''
            if (i.type === 'LINEARSCALE') form.value = ''
            this.questions.push(form)
            this.patientData.push({title: i.title, answer: ''})
          }
        })
      }

      this.questions.map(item => {
        item.type === 'SECTION' ? this.id_list.push('') : this.id_list.push(item.id)
      })
    },
    // 预览根据返回值自检， radio、dropdown跳转调整show的状态
    // jumpid 如果是false, 说明按照顺序显示，全部为show
    nextBack (jumpid, nowid) {
      console.log(jumpid + '=======' + nowid)
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
          console.log(start, nextLoop, this.questions[i].id, this.questions[i].value)

          if (!this.questions[i].isjump) continue

          const val = this.questions[i].value
          this.questions[i].answers.map(option => {
            if (val === option.text && option.next !== '') this.nextBack(option.next, this.questions[i].id)
          })
        }
      }
    }
  }
}
</script>

<style scoped>
/* .form {
  margin-top: 20px;
  width: 900px;
  background: #fff;
  align-items: flex-start;
} */


 .crf {
   margin: 0 auto;
   width: 720px;
  /* max-height: 700px; */
  /* overflow-y: scroll; */
} 

.item {
  width: 100%; 
  background: #fff;
}

.section, .form-title {
  margin-top: 30px;
  padding: 16px 24px 24px 24px;
  border-bottom: 1px solid #eee;
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
  padding: 16px 24px 24px 24px;
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

@media screen and (max-width: 1500px) {
  .crf {
    width: 640px;
    margin: 0 20px 30px;
  }
}
</style>
