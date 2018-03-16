<template>
  <div class="temp4">
    <h3>生活</h3>

    <div class="form-group flex-row">
      <div class="question">
        <label for="education" style="width: 120px;">受教育程度</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" v-model="education" id="education1" value="高中以下">
          <label for="education1">高中以下</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="education" id="education2" value="高中">
          <label for="education2">高中</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="education" id="education3" value="大学">
          <label for="education3">大学</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="education" id="education4" value="研究生及以上">
          <label for="education4">研究生及以上</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="work" style="width: 120px;">目前的工作状态</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="已经退休或停止工作" id="work1" v-model="work" @click="clearn('workHours', 'workSit6')">
          <label for="work1">已经退休或停止工作</label>
        </div>
        <div class="radio">
          <input type="radio" value="还在工作" id="work2" v-model="work">
          <label for="work2">还在工作</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="work === '还在工作'">
          <div class="flex-col">
            <label for="workHours">每天工作时长</label>
            <div class="radio">
              <input type="radio" id="workHours1" value="小于8小时" v-model="workHours">
                <label for="workHours1">小于8小时</label>
            </div>
            <div class="radio">
              <input type="radio" id="workHours2" value="大约8到10小时" v-model="workHours">
                <label for="workHours2">大约8到10小时</label>
            </div>
            <div class="radio">
              <input type="radio" id="workHours3" value="大于10小时" v-model="workHours">
                <label for="workHours3">大于10小时</label>
            </div>
          </div>
        </div>
        <!-- 子选项 end -->
      </div>
    </div>

    <div class="form-group flex-row" v-if="work === '还在工作'">
      <div class="question">
        <label for="workSit6">工作中每天是否久坐超过6小时</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" class="btn" v-model="workSit6" id="workSit61" value="是">
          <label for="workSit61">是</label>
        </div>
        <div class="radio">
          <input type="radio" class="btn" v-model="workSit6" id="workSit62" value="否">
          <label for="workSit62">否</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="eat">是否有下面这些饮食习惯？</label>
      </div>
      <div class="flex-col">
        <div class="checkbox">
          <input type="checkbox" value="饮酒" id="drink" v-model="eat" @click="eatConfig">
          <label for="drink">喝酒</label>
        </div>
        <div class="checkbox">
          <input type="checkbox" value="肉食" id="meat" v-model="eat" @click="eatConfig">
          <label for="meat">肉食</label>
        </div>
        <div class="checkbox">
          <input type="checkbox" value="快餐" id="snack" v-model="eat" @click="eatConfig">
          <label for="snack">快餐</label>
        </div>
        <div class="checkbox">
          <input type="checkbox" value="没有上述习惯" id="nobadEat" v-model="eatNone" @click="eatSpecial">
          <label for="nobadEat">没有上述习惯</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="sport">是否有坚持运动的习惯？</label>
        <p class="dec">指至少是一小时以上的有氧运动</p>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="每周达到或超过3次" id="sport1" v-model="sport">
          <label for="sport1">每周达到或超过3次</label>
        </div>
        <div class="radio">
          <input type="radio" value="每周少于三次" id="sport2" v-model="sport">
          <label for="sport2">每周少于三次</label>
        </div>
        <div class="radio">
          <input type="radio" value="没有运动习惯" id="sport3" v-model="sport">
          <label for="sport3">没有运动习惯</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="weight">是否正在进行减肥？</label>
        <p class="dec">目标是降低体重5%</p>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="不需要减肥" id="weight1" v-model="weight">
          <label for="weight1">不需要减肥</label>
        </div>
        <div class="radio">
          <input type="radio" value="正在减肥，达不到降低5%的目标" id="weight2" v-model="weight">
          <label for="weight2">正在减肥，未达到目标</label>
        </div>
        <div class="radio">
          <input type="radio" value="正在减肥并且达到降低5%的目标" id="weight3" v-model="weight">
          <label for="weight3">正在减肥并且达到目标</label>
        </div>
        <div class="radio">
          <input type="radio" value="多次减肥不成功" id="weight4" v-model="weight">
          <label for="weight4">多次减肥不成功</label>
        </div>
      </div>
    </div>

    <h3>认知和心理</h3>

    <div class="form-group flex-row">
      <div class="question">
        <label>对心脏病（冠心病）的了解程度是</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="不了解" id="diseaseUnderstand1" v-model="diseaseUnderstand">
          <label for="diseaseUnderstand1">不了解</label>
        </div>
        <div class="radio">
          <input type="radio" value="一般了解" id="diseaseUnderstand2" v-model="diseaseUnderstand">
          <label for="diseaseUnderstand2">一般了解</label>
        </div>
        <div class="radio">
          <input type="radio" value="较多了解" id="diseaseUnderstand3" v-model="diseaseUnderstand">
          <label for="diseaseUnderstand3">较多了解</label>
        </div>
        <div class="radio">
          <input type="radio" value="非常了解" id="diseaseUnderstand4" v-model="diseaseUnderstand">
          <label for="diseaseUnderstand4">非常了解</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label>对自己的心脏问题是不是会常常感到焦虑或紧张？</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="没有焦虑和紧张" id="diseasePsychology1" v-model="diseasePsychology">
          <label for="diseasePsychology1">没有焦虑和紧张</label>
        </div>
        <div class="radio">
          <input type="radio" value="有一点紧张和焦虑" id="diseasePsychology2" v-model="diseasePsychology">
          <label for="diseasePsychology2">有一点紧张和焦虑</label>
        </div>
        <div class="radio">
          <input type="radio" value="比较焦虑和紧张" id="diseasePsychology3" v-model="diseasePsychology">
          <label for="diseasePsychology3">比较焦虑和紧张</label>
        </div>
        <div class="radio">
          <input type="radio" value="非常焦虑和紧张" id="diseasePsychology4" v-model="diseasePsychology">
          <label for="diseasePsychology4">非常焦虑和紧张</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label>对冠心病CT检查的了解程度</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="不了解" id="CTUnderstand1" v-model="CTUnderstand">
          <label for="CTUnderstand1">不了解</label>
        </div>
        <div class="radio">
          <input type="radio" value="一般了解" id="CTUnderstand2" v-model="CTUnderstand">
          <label for="CTUnderstand2">一般了解</label>
        </div>
        <div class="radio">
          <input type="radio" value="较多了解" id="CTUnderstand3" v-model="CTUnderstand">
          <label for="CTUnderstand3">较多了解</label>
        </div>
        <div class="radio">
          <input type="radio" value="非常了解" id="CTUnderstand4" v-model="CTUnderstand">
          <label for="CTUnderstand4">非常了解</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label>对冠心病CT检查有没有畏惧和紧张情绪？</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="没有焦虑和紧张" id="CTPsychology1" v-model="CTPsychology">
          <label for="CTPsychology1">没有焦虑和紧张</label>
        </div>
        <div class="radio">
          <input type="radio" value="有一点紧张和焦虑" id="CTPsychology2" v-model="CTPsychology">
          <label for="CTPsychology2">有一点紧张和焦虑</label>
        </div>
        <div class="radio">
          <input type="radio" value="比较焦虑和紧张" id="CTPsychology3" v-model="CTPsychology">
          <label for="CTPsychology3">比较焦虑和紧张</label>
        </div>
        <div class="radio">
          <input type="radio" value="非常焦虑和紧张" id="CTPsychology4" v-model="CTPsychology">
          <label for="CTPsychology4">非常焦虑和紧张</label>
        </div>
      </div>
    </div>
     
  </div>
</template>

<script>
import Modal from '../lib/modal.vue'
import MButton from '../lib/Button.vue'
import { Type } from '../utils/tools.js'
export default {
  name: 'temp2',
  components: {
    Modal,
    MButton
  },
  props: {
    mdid: String,
    templateid: String,
    recordid: String,
    source: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  data () {
    return {
      education: '',
      work: '',
      workHours: '',
      workSit6: '',
      eat: [],
      eatNone: false,   // 没有上述饮食习惯
      sport: '',
      weight: '',
      diseaseUnderstand: '',
      diseasePsychology: '',
      CTUnderstand: '',
      CTPsychology: ''
    }
  },
  created () {
    if (JSON.stringify(this.source) !== '{}') {
      this.education = this.source.education
      this.work = this.source.work
      this.workHours = this.source.workHours
      this.workSit6 = this.source.workSit6
      this.eat = this.source.eat
      this.sport = this.source.sport
      this.weight = this.source.weight
      this.diseaseUnderstand = this.source.diseaseUnderstand
      this.diseasePsychology = this.source.diseasePsychology
      this.CTUnderstand = this.source.CTUnderstand
      this.CTPsychology = this.source.CTPsychology
    }
    // 监听所有绑定值，emit 返回父级
    for (let n in this.$data) {
      this.$watch(n, (val, oldVal) => {
        this.$emit('putsever', {sourceData: this.$data, showData: this.changeData()})
      })
    }
  },
  methods: {
    eatSpecial () {
      this.eatNone === true ? this.eat = ['没有上述习惯'] : this.eat = []
    },
    eatConfig () {
      // 清除 symptom 中的‘无上述症状’，隐藏 symptomNone
      this.eatNone = false
      let eatSite = this.eat.indexOf('没有上述习惯')
      eatSite !== -1 ? this.eat.splice(eatSite, 1) : ''
    },
    clearn (...index) {
      for (let i in index) {
        let val = this.$data[index[i]]
        Type(val)
        if (Type(val) === 'string' || Type(val) === 'date' || Type(val) === 'number') {
          this.$data[index[i]] = ''
        } else if (Type(val) === 'array') {
          this.$data[index[i]] = []
        }
      }
    },
    // 显示数据
    changeData () {
      let showData = {
        '教育程度': this.education,
        '工作状态': this.work,
        '每天工作时长': this.workHours,
        '工作中每天是否久坐超过6小时': this.workSit6,
        '饮食习惯': this.eat.toString(),
        '运动': this.sport,
        '减肥': this.weight,
        '对心脏病的了解程度': this.diseaseUnderstand,
        '平常对心脏问题的紧张程度': this.diseasePsychology,
        '对冠心病CT检查的了解程度': this.CTUnderstand,
        '对冠心病CT检查的紧张程度': this.CTPsychology
      }

      return showData
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .temp4 h3 {
    font-size: 18px;
    font-weight: 400;
  }
  /*form design*/

  .form-group {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .form-group>.question {
    padding-top: 4px;
    line-height: 22px;
    width: 140px;
    padding-right: 30px;
  }

  .question>label {
    color: #777;
  }

  .dec {
    font-size: 12px;
    line-height: 20px;
    color: #aaa;
  }

  .child-option {
    margin-left: 28px;
  }
</style>