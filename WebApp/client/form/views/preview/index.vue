<template>
  <div class="preview-form">
    <div class="item">
      <div class="form">
        <p class="title">{{ sourcedata[0].title }}</p>
        <span class="dec">{{ sourcedata[0].describe }}</span> 
      </div>
    </div>
    <formbase></formbase>

    <div class="item" v-for="(qa, qaid) in questions">
      <div class="section" v-if="qa.type === 'SECTION'">
        <p class="title">{{ qa.title }}</p>
        <span class="dec">{{ qa.describe.text }}</span>
      </div>

      <div v-else-if="qa.type === 'FORM'"></div>

      <div class="questions" v-else>

        <div class="q" v-show="qa.show">
          <p>{{ qa.title }} <span class="keep" v-if="qa.keep">*</span></p>
          <span class="dec">{{ qa.describe.text }}</span>
          <span class="link-dec"><a :href="'http://' + qa.link.text" target="_blank">{{ qa.link.text }}</a></span>
          <img :src="'http://' + qa.img.url" alt="option" v-if="qa.img.url.length > 10" class="img-option">
        </div>
        <div class="a-item" v-show="qa.show">
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
import Formbase from '../Baseinfo.vue'
import { Type } from '../../tool/tools'

export default {
  name: 'Preview',
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
    sourcedata: Array
  },
  data () {
    return {
      baseinfo: {gender: '', birthday: ''},
      questions: [],
      patientData: [],
      sections: []
    }
  },
  computed: {
    id_list: function () {
      let qa = []
      this.questions.map(item => {
        item.type === 'SECTION' ? qa.push('') : qa.push(item.id)
      })
      return qa
    }
  },
  watch: {
    sourcedata () {
      this.initQuestionList()
    }
  },
  mounted () {
    this.initQuestionList()
  },
  methods: {
    initQuestionList () {
      let list = JSON.parse(JSON.stringify(this.sourcedata))
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
    },
    // 预览根据返回值自检， radio、dropdown跳转调整show的状态
    // jumpid 如果是false, 说明按照顺序显示，全部为show
    nextBack (jumpid, nowid) {
      console.log(jumpid + '=======' + nowid)
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
        if (i < end) {
          this.questions[i].show = false
          if (Type(this.questions[i].value) === 'string') this.questions[i].value = ''
          if (Type(this.questions[i].value) === 'array') this.questions[i].value = []
          if (Type(this.questions[i].value) === 'object') this.questions[i].value = {}
        } else {
          this.questions[i].show = true
        }
      }
    }
  }
}
</script>

<style scoped>
.item {
  width: 770px;
  margin: 0 auto;
  background: #fff;
}

.section {
  margin-top: 30px;
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #eee;
}

.form {
  padding: 24px;
  text-align: center;
  border-bottom: 1px solid #eee;
}

.section p.title, .form p.title {
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

.questions .q {
  padding: 8px 0 12px 0;
}

.questions .q p {
  font-size: 16px;
}
</style>
