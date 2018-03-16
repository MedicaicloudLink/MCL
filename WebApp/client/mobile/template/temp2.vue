<template>
  <div class="main">
    <div class="form-group flex-row">
      <div class="question">
        <label for="height">身高 </label>
      </div>
      <div  class="flex-row">
        <input type="number" v-model="height" id="height" placeholder="00" />
        <span class="unit" style="padding-right: 16px">cm</span> 
      </div>
    </div>
    <div class="form-group flex-row">
      <div class="question">
        <label for="abdominalGirth">体重 </label>
      </div>
      <div class="flex-row">
        <input type="number" v-model="weight" id="weight" placeholder="00">
        <span class="unit" style="padding-right: 8px">kg</span>
      </div>
    </div>
    <div class="form-group flex-row">
      <div class="question">
        <label for="abdominalGirth">BMI </label>
      </div>
      <div> {{ BMI }} </div>
    </div>
    <div class="form-group flex-row">
      <div class="question">
        <label for="abdominalGirth">腹围 </label>
      </div>
      <div class="flex-row">
        <input type="number" v-model="abdominalGirth" id="abdominalGirth">
        <span class="unit">cm(厘米)</span>
      </div>
    </div>
    <div class="form-group" style="display: flex;">
      <div class="question">
        <label for="symptom">症状</label>
      </div>
      <div class="flex-col">
        <div class="checkbox">
          <input type="checkbox" v-model="symptom" id="symptom1" value="胸痛" @click="symptomConfig">
          <label for="symptom1">胸痛</label>
        </div>
        <div class="flex-col child-option" v-show="symptom.indexOf('胸痛') !== -1">
          <div class="radio">
            <input type="radio" class="btn" v-model="trigger" id="trigger1" value="通常是因为运动或情绪激动触发">
            <label for="trigger1">通常是因为运动或情绪激动触发</label>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="trigger" id="trigger2" value="不是因为运动或情绪激动触发">
            <label for="trigger2">不是因为运动或情绪激动触发</label>
          </div>
        </div>
        <div>
          <input type="checkbox" v-model="symptom" id="symptom2" value="呼吸困难" @click="symptomConfig">
          <label for="symptom2">呼吸困难</label>
        </div>
        <div>
          <input type="checkbox" v-model="symptom" id="symptom3" value="其他症状" @click="symptomConfig">
          <label for="symptom3">其他症状</label>
        </div>
        <div>
          <input type="checkbox" v-model="symptomNone" id="symptom4" value="无上述症状" @click="symptomSpecial">
          <label for="symptom4">无上述症状</label>
        </div>
      </div>
    </div>

    <div class="form-group" style="display: flex;" v-show="symptom.indexOf('胸痛') !== -1">
      <div class="question">
        <label for="symptom">您的胸痛会因为服用硝酸甘油有所缓解吗？</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" class="btn" v-model="remission" id="remission1" value="会">
          <label for="remission1">会</label>
        </div>
        <div class="radio">
          <input type="radio" class="btn" v-model="remission" id="remission2" value="不会">
          <label for="remission2">不会</label>
        </div>
      </div>
    </div>


  </div>
</template>

<script>
export default {
  name: 'temp2',
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
      height: '',
      weight: '',
      BMI: '',
      abdominalGirth: '',   // 腹围
      symptom: [],        // 症状
      symptomNone: false,    // 症状 --> 什么都没有
      trigger: '',        // 胸痛触发
      remission: ''       // 胸痛缓解
    }
  },
  created () {
    if (JSON.stringify(this.source) !== '{}') {
      this.height = this.source.height
      this.weight = this.source.weight
      this.BMI = this.source.BMI
      this.abdominalGirth = this.source.abdominalGirth
      this.symptom = this.source.symptom
      this.symptomNone = this.source.symptomNone
      this.trigger = this.source.trigger
      this.remission = this.source.remission
    }
    // 监听所有绑定值，emit 返回父级
    for (let n in this.$data) {
      this.$watch(n, (val, oldVal) => {
        this.$emit('putsever', {sourceData: this.$data, showData: this.changeData()})
      })
    }
  },
  watch: {
    height (val, oldVal) {
      let bmi = this.weight / ((val / 100) * (val / 100))
      this.BMI = bmi.toFixed(2)
    },
    weight (val, oldVal) {
      let bmi = val / ((this.height / 100) * (this.height / 100))
      this.BMI = bmi.toFixed(2)
    }
  },
  methods: {
    symptomSpecial () {
      this.symptomNone === true ? this.symptom = ['无上述症状'] : this.symptom = []
      // 清除子选项
      this.trigger = ''
      this.remission = ''
    },
    symptomConfig () {
      // 清除 symptom 中的‘无上述症状’，隐藏 symptomNone
      this.symptomNone = false
      let symptomNoneSite = this.symptom.indexOf('无上述症状')
      symptomNoneSite !== -1 ? this.symptom.splice(symptomNoneSite, 1) : ''
      // 清除子选项
      if (this.symptom.indexOf('胸痛') === -1) {
        this.trigger = ''
        this.remission = ''
      }
    },
    // 显示数据
    changeData () {
      let showData = {
        '身高': this.height !== '' ? this.height + 'cm' : '',
        '体重': this.weight !== '' ? this.weight + 'kg' : '',
        'BMI': this.BMI,
        '腹围': this.abdominalGirth !== '' ? this.abdominalGirth + 'cm' : '',
        '症状': this.symptom.toString(),
        '胸痛通常是因为运动或情绪激动触发': this.trigger,
        '胸痛会因为服用硝酸甘油有所缓解': this.remission
      }

      return showData
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  /*form design*/
  .form-group {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .form-group>.question {
    line-height: 32px;
    width: 140px;
    padding-right: 30px;
  }

  .question>label {
    color: #777;
  }

  .child-option {
    margin-left: 28px;
  }

  .unit {
    font-size: 14px;
    padding: 0 4px;
  }

  .disabled {
    pointer-events: none;
  }

  
</style>
