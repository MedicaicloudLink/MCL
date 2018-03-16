<template>
  <div class="temp4">
    <div class="form-group flex-row" style="border: 0;">
      <div class="question">
        <label></label>
      </div>
      <div class="flex-col">
        <label>血生化化验检查日期：</label>
        <datepicker :width="170" v-model="date"></datepicker>
      </div>
    </div>
    <div class="form-group flex-row">
      <div class="question">
        <label>近期的血生化化验检查结果</label>
      </div>
      <div class="flex-col">
        <label>血糖(Glu)</label>
        <div class="flex-row"><input type="number" v-model="Glu" /><span class="unit">mmol/L</span></div>
        <label>肌酐(Cr)</label>
        <div class="flex-row"><input type="number" v-model="Cr" /><span class="unit">umol/L</span></div>
        <label>尿素氮(BUN)</label>
        <div class="flex-row"><input type="number" v-model="BUN" /><span class="unit">umol/L</span></div>
        <label>尿酸(UA)</label>
        <div class="flex-row"><input type="number" v-model="UA" /><span class="unit">umol/L</span></div>
        <label>脂蛋白(a)(LP(a))</label>
        <div class="flex-row"><input type="number" v-model="LP" /><span class="unit">mg/L</span></div>
        <label>高敏C反应蛋白(hs-CRP)</label>
        <div class="flex-row"><input type="number" v-model="hs_crp" /><span class="unit">mg/L</span></div>
        <label>载脂蛋白A1(apoA1)</label>
        <div class="flex-row"><input type="number" v-model="AI" /><span class="unit">g/L</span></div>
        <label>载脂蛋白B(apoB)</label>
        <div class="flex-row"><input type="number" v-model="apoB" /><span class="unit">g/L</span></div>
        <label>甘油三酯(TG)</label>
        <div class="flex-row"><input type="number" v-model="Tg" /><span class="unit">mmol/L</span></div>
        <label>总胆固醇(TCHO)</label>
        <div class="flex-row"><input type="number" v-model="Tcho" /><span class="unit">mmol/L</span></div>
        <label>高密度脂蛋白(HDL-C)</label>
        <div class="flex-row"><input type="number" v-model="HDL" /><span class="unit">mmol/L</span></div>
        <label>低密度脂蛋白(LDL-C)</label>
        <div class="flex-row"><input type="number" v-model="LDL" /><span class="unit">mmol/L</span></div>
        <label>同型半胱氨酸(HCY)</label>
        <div class="flex-row"><input type="number" v-model="hc" /><span class="unit">g/L</span></div>
        <label>糖化血红蛋白(HbA1c)</label>
        <div class="flex-row"><input type="number" v-model="hb" /><span class="unit">%</span></div>
      </div>
    </div>
  </div>
</template>

<script>
import Datepicker from '../lib/Date.vue'
export default {
  name: 'temp2',
  components: {
    Datepicker
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
      date: '',
      Cr: '',
      BUN: '',
      UA: '',
      Glu: '',
      Tcho: '',
      Tg: '',
      HDL: '',
      LDL: '',
      LP: '',
      AI: '',
      apoB: '',
      hc: '',
      hs_crp: '',
      hb: ''
    }
  },
  created () {
    if (JSON.stringify(this.source) !== '{}') {
      this.date = this.source.date
      this.Cr = this.source.Cr
      this.BUN = this.source.BUN
      this.UA = this.source.UA
      this.Glu = this.source.Glu
      this.Tcho = this.source.Tcho
      this.Tg = this.source.Tg
      this.HDL = this.source.HDL
      this.LDL = this.source.LDL
      this.LP = this.source.LP
      this.AI = this.source.AI
      this.apoB = this.source.apoB
      this.hc = this.source.hc
      this.hs_crp = this.source.hs_crp
      this.hb = this.source.hb
    }
    // 监听所有绑定值，emit 返回父级
    for (let n in this.$data) {
      this.$watch(n, (val, oldVal) => {
        this.$emit('putsever', {sourceData: this.$data, showData: this.changeData()})
      })
    }
  },
  methods: {
    // 显示数据
    changeData () {
      let showData = {
        '检查日期': this.date,
        '肌酐(Cr)': this.Cr !== '' ? this.Cr + 'umol/L' : '',
        '尿素氮(BUN)': this.BUN !== '' ? this.BUN + 'umol/L' : '',
        '尿酸(UA)': this.UA !== '' ? this.UA + 'umol/L' : '',
        '血糖(Glu)': this.Glu !== '' ? this.Glu + 'umol/L' : '',
        '总胆固醇(TCHO)': this.Tcho !== '' ? this.Tcho + 'mmol/L' : '',
        '甘油三酯(TG)': this.Tg !== '' ? this.Tg + 'mmol/L' : '',
        '高密度脂蛋白(HDL-C)': this.HDL !== '' ? this.HDL + 'mmol/L' : '',
        '低密度脂蛋白(LDL-C)': this.LDL !== '' ? this.LDL + 'mmol/L' : '',
        '脂蛋白(a)(LP(a))': this.LP !== '' ? this.LP + 'mg/L' : '',
        '载脂蛋白A1(apoA1)': this.AI !== '' ? this.AI + 'g/L' : '',
        '载脂蛋白B(apoB)': this.apoB !== '' ? this.apoB + 'g/L' : '',
        '同型半胱氨酸': this.hc !== '' ? this.hc + 'g/L' : '',
        '高锰C反应蛋白(hs-CRP)': this.hs_crp !== '' ? this.hs_crp + 'mg/L' : '',
        '糖化血红蛋白(HbA1c)': this.hb !== '' ? this.hb + '%' : ''
      }

      return showData
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
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

  .form-group>div>label {
    margin-bottom: 8px;
  }

  .form-group>div input {
    margin-bottom: 8px;
  }
</style>