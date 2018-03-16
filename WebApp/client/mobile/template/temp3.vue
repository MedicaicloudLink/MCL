<template>
  <div class="temp4">
    <h3>主要危险因素</h3>
    <div class="form-group flex-row">
      <div class="question">
        <label for="hypertension" style="width:120px;">高血压病史：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="hypertension1" v-model="hypertension">
          <label for="hypertension1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="hypertension === '有'">
          <div class="flex-col">
            <label for="hypertensionDate" style="width: 150px;">确诊时间: </label>
            <datepicker v-model="hypertensionDate"></datepicker>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="hypertensionLine" id="hypertensionLine1" value="已控制达标">
            <label for="hypertensionLine1">已控制达标</label>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="hypertensionLine" id="hypertensionLine2" value="不达标">
            <label for="hypertensionLine2">不达标</label>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="无" id="hypertension2" v-model="hypertension" @click="clearn('hypertensionDate', 'hypertensionLine')">
          <label for="hypertension2" style="padding-right: 16px">无</label>
        </div>
        <div class="radio">
          <input type="radio" value="不清楚" id="hypertension3" v-model="hypertension" @click="clearn('hypertensionDate', 'hypertensionLine')">
          <label for="hypertension3">不清楚</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="hyperlipidemia" style="width:120px;">高脂血症病史：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="hyperlipidemia1" v-model="hyperlipidemia">
          <label for="hyperlipidemia1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="hyperlipidemia === '有'">
          <div class="flex-col">
            <label for="hyperlipidemiaDate" style="width: 150px;">确诊时间</label>
            <datepicker v-model="hyperlipidemiaDate"></datepicker>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="hyperlipidemiaLine" id="hyperlipidemiaLine1" value="已控制达标">
            <label for="hyperlipidemiaLine1">已控制达标</label>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="hyperlipidemiaLine" id="hyperlipidemiaLine2" value="不达标">
            <label for="hyperlipidemiaLine2">不达标</label>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="无" id="hyperlipidemia2" v-model="hyperlipidemia" @click="clearn('hyperlipidemiaDate', 'hyperlipidemiaLine')">
          <label for="hyperlipidemia2" style="padding-right: 16px">无</label>
        </div>
        <div class="radio">
          <input type="radio" value="不清楚" id="hyperlipidemia3" v-model="hyperlipidemia" @click="clearn('hyperlipidemiaDate', 'hyperlipidemiaLine')">
          <label for="hyperlipidemia3">不清楚</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="diabetes" style="width:120px;">糖尿病病史：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="diabetes1" v-model="diabetes">
          <label for="diabetes1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="diabetes === '有'">
          <div class="flex-col">
            <label for="diabetesDate" style="width: 150px;">确诊时间</label>
            <datepicker v-model="diabetesDate"></datepicker>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="diabetesLine" id="diabetesLine1" value="已控制达标">
            <label for="diabetesLine1">已控制达标</label>
          </div>
          <div class="radio">
            <input type="radio" class="btn" v-model="diabetesLine" id="diabetesLine2" value="不达标">
            <label for="diabetesLine2">不达标</label>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="无" id="diabetes2" v-model="diabetes" @click="clearn('diabetesDate', 'diabetesLine')">
          <label for="diabetes2" style="padding-right: 16px">无</label>
        </div>
        <div class="radio">
          <input type="radio" value="不清楚" id="diabetes3" v-model="diabetes" @click="clearn('diabetesDate', 'diabetesLine')">
          <label for="diabetes3">不清楚</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="peripheralVascular" style="width:120px;">外周血管疾病史：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="peripheralVascular1" v-model="peripheralVascular">
          <label for="peripheralVascular1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="peripheralVascular === '有'">
          <div class="flex-col">
            <label for="peripheralVascularDate" style="width: 150px;">确诊时间</label>
            <datepicker v-model="peripheralVascularDate"></datepicker>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="无" id="peripheralVascular2" v-model="peripheralVascular" @click="clearn('peripheralVascularDate')">
          <label for="peripheralVascular2" style="padding-right: 16px">无</label>
        </div>
        <div class="radio">
          <input type="radio" value="不清楚" id="peripheralVascular3" v-model="peripheralVascular" @click="clearn('peripheralVascularDate')">
          <label for="peripheralVascular3">不清楚</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="cerebrovascular" style="width:120px;">脑血管疾病史：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="cerebrovascular1" v-model="cerebrovascular">
          <label for="cerebrovascular1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="cerebrovascular === '有'">
          <div class="flex-col">
            <label for="cerebrovascularDate" style="width: 150px;">确诊时间</label>
            <datepicker v-model="cerebrovascularDate"></datepicker>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="无" id="cerebrovascular2" v-model="cerebrovascular" @click="clearn('cerebrovascularDate')">
          <label for="cerebrovascular2" style="padding-right: 16px">无</label>
        </div>
        <div class="radio">
          <input type="radio" value="不清楚" id="cerebrovascular3" v-model="cerebrovascular" @click="clearn('cerebrovascularDate')">
          <label for="cerebrovascular3">不清楚</label>
        </div>
      </div>
    </div>
    

    <h3>其他危险因素</h3>

    <div class="form-group flex-row">
      <div class="question">
        <label for="family" style="width: 200px;">早发心血管疾病家族史：</label>
        <p class="dec">男>55，女>65岁</p>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="family1" v-model="family">
          <label for="family1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="family === '有'">
          <div class="flex-col">
            <label for="">家族病史</label>
            <div class="checkbox">
              <input type="checkbox" id="familyFather" value="父亲" v-model="familyGroup">
              <label for="familyFather">父亲</label>
            </div>
            <div class="checkbox">
              <input type="checkbox" id="familyMother" value="母亲" v-model="familyGroup">
              <label for="familyMother">母亲</label>
            </div>
            <div class="checkbox">
              <input type="checkbox" id="familySister" value="兄弟姐妹" v-model="familyGroup">
              <label for="familySister">兄弟姐妹</label>
            </div>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="无" id="family2" v-model="family" @click="clearn('familyGroup')">
          <label for="family2" style="padding-right: 16px">无</label>
        </div>
        <div class="radio">
          <input type="radio" value="不清楚" id="family3" v-model="family" @click="clearn('familyGroup')">
          <label for="family3">不清楚</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="smoke" style="width: 120px;">吸烟史：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" value="有" id="smoke1" v-model="smoke" @click="clearn('stopsmokeyear')">
          <label for="smoke1" style="padding-right: 16px">有</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="smoke === '有'">
          <div class="flex-col">
            <label for="smokeyear">累计吸烟时间</label>
            <div class="flex-row"><input type="number" id="smokeyear" v-model="smokeyear"><span>年</span></div>
          </div>
          <div class="flex-col">
            <label for="smoketime" style="width: 130px;">每日吸烟频次：</label>
            <div>
              <input type="radio" id="smoketime1" value="小于10支" v-model="smoketime">
              <label for="smoketime1">小于10支</label>
            </div>
            <div>
              <input type="radio" id="smoketime2" value="约10到20支" v-model="smoketime">
              <label for="smoketime2">约10到20支</label>
            </div>
            <div>
              <input type="radio" id="smoketime3" value="多于20支" v-model="smoketime">
              <label for="smoketime3">多于20支</label>
            </div>
          </div>
        </div>
        <!-- 子选项 end -->
        <div class="radio">
          <input type="radio" value="没有" id="smoke2" v-model="smoke" @click="clearn('stopsmokeyear', 'smokeyear', 'smoketime')">
          <label for="smoke2" style="padding-right: 16px">没有</label>
        </div>
        <div class="radio">
          <input type="radio" value="已经戒烟" id="smoke3" v-model="smoke" @click="clearn('smokeyear', 'smoketime')">
          <label for="smoke3">已经戒烟</label>
        </div>
        <!-- 子选项 -->
        <div class="flex-col child-option" v-if="smoke === '已经戒烟'">
          <div class="flex-col">
            <label for="stopsmokeyear">已经戒烟时间</label>
            <div class="flex-row"><input type="number" id="stopsmokeyear" v-model="stopsmokeyear"><span>年</span></div>
          </div>
        </div>
        <!-- 子选项 end -->
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="menopause">绝经(女性)：</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" class="btn" v-model="menopause" id="menopause1" value="是">
          <label for="menopause1">是</label>
        </div>
        <div class="radio">
          <input type="radio" class="btn" v-model="menopause" id="menopause2" value="否">
          <label for="menopause2">否</label>
        </div>
        <div class="radio">
          <input type="radio" class="btn" v-model="menopause" id="menopause3" value="不适用">
          <label for="menopause3">不适用</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="gastropathy">胃食管返流疾病史</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" v-model="gastropathy" id="gastropathy1" value="没有">
          <label for="gastropathy1">没有</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="gastropathy" id="gastropathy2" value="不清楚">
          <label for="gastropathy2">不清楚</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="gastropathy" id="gastropathy3" value="曾有过此病史">
          <label for="gastropathy3">曾有过此病史</label>
        </div>
         <div class="radio">
          <input type="radio" v-model="gastropathy" id="gastropathy4" value="有此病且目前有症状">
          <label for="gastropathy4">有此病且目前有症状</label>
        </div>
      </div>
    </div>

    <div class="form-group flex-row">
      <div class="question">
        <label for="nephropathy">肾病史</label>
      </div>
      <div class="flex-col">
        <div class="radio">
          <input type="radio" v-model="nephropathy" id="nephropathy1" value="没有">
          <label for="nephropathy1">没有</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="nephropathy" id="nephropathy2" value="不清楚">
          <label for="nephropathy2">不清楚</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="nephropathy" id="nephropathy3" value="曾有过此病史">
          <label for="nephropathy3">曾有过此病史</label>
        </div>
        <div class="radio">
          <input type="radio" v-model="nephropathy" id="nephropathy4" value="有此病且目前有症状">
          <label for="nephropathy4">有此病且目前有症状</label>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import Modal from '../lib/modal.vue'
import MButton from '../lib/Button.vue'
import Datepicker from '../lib/Date.vue'
import { Type } from '../utils/tools.js'
export default {
  name: 'temp3',
  components: {
    Modal,
    MButton,
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
      hypertension: '',   // 高血压
      hypertensionDate: '',
      hypertensionLine: '',
      hyperlipidemia: '',  // 高血脂症
      hyperlipidemiaDate: '',
      hyperlipidemiaLine: '',
      diabetes: '',         // 糖尿病
      diabetesDate: '',
      diabetesLine: '',
      peripheralVascular: '',        // 外周血管疾病
      peripheralVascularDate: '',
      cerebrovascular: '',           // 脑血管疾病
      cerebrovascularDate: '',
      family: '',                     // 家族病史
      familyGroup: [],
      smoke: '',
      smokeyear: '',
      smoketime: '',
      stopsmokeyear: '',
      menopause: '',                   // 绝经
      gastropathy: '',                  // 胃病
      nephropathy: ''                  // 肾病
    }
  },
  created () {
    if (JSON.stringify(this.source) !== '{}') {
      this.hypertension = this.source.hypertension
      this.hypertensionDate = this.source.hypertensionDate
      this.hypertensionLine = this.source.hypertensionLine
      this.hyperlipidemia = this.source.hyperlipidemia
      this.hyperlipidemiaDate = this.source.hyperlipidemiaDate
      this.hyperlipidemiaLine = this.source.hyperlipidemiaLine
      this.diabetes = this.source.diabetes
      this.diabetesDate = this.source.diabetesDate
      this.diabetesLine = this.source.diabetesLine
      this.peripheralVascular = this.source.peripheralVascular
      this.peripheralVascularDate = this.source.peripheralVascularDate
      this.cerebrovascular = this.source.cerebrovascular
      this.cerebrovascularDate = this.source.cerebrovascularDate
      this.family = this.source.family
      this.familyGroup = this.source.familyGroup
      this.smoke = this.source.smoke
      this.smokeyear = this.source.smokeyear
      this.smoketime = this.source.smoketime
      this.stopsmokeyear = this.source.stopsmokeyear
      this.menopause = this.source.menopause
      this.gastropathy = this.source.gastropathy
      this.nephropathy = this.source.nephropathy
    }
    // 监听所有绑定值，emit 返回父级
    for (let n in this.$data) {
      this.$watch(n, (val, oldVal) => {
        this.$emit('putsever', {sourceData: this.$data, showData: this.changeData()})
      })
    }
  },
  methods: {
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
        '高血压': this.hypertension,
        '高血压确诊时间': this.hypertensionDate,
        '高血压是否控制达标': this.hypertensionLine,
        '高血脂症': this.hyperlipidemia,
        '高血脂症确诊时间': this.hyperlipidemiaDate,
        '高血脂症是否控制达标': this.hyperlipidemiaLine,
        '糖尿病': this.diabetes,
        '糖尿病确诊时间': this.diabetesDate,
        '糖尿病是否控制达标': this.diabetesLine,
        '外周血管疾病': this.peripheralVascular,
        '外周血管疾病确诊时间': this.peripheralVascularDate,
        '脑血管疾病': this.cerebrovascular,
        '脑血管疾病确诊时间': this.cerebrovascularDate,
        '早发心血管疾病家族史': this.family,
        '家族史': this.familyGroup.toString(),
        '吸烟': this.trigger,
        '累计吸烟时间': this.smokeyear !== '' ? this.smokeyear + '年' : '',
        '每日吸烟频次': this.smoketime,
        '已经戒烟时间': this.stopsmokeyear !== '' ? this.stopsmokeyear + '年' : '',
        '绝经(女性)': this.menopause,
        '胃食管反流': this.gastropathy,
        '肾病': this.nephropathy
      }

      return showData
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .temp4 h3 {
    /*background: #E3F2FD;*/
    font-size: 18px;
    font-weight: 400;
  }
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

  .dec {
    font-size: 13px;
    line-height: 22px;
    color: #999;
  }

  .child-option {
    margin-left: 28px;
  }
  

</style>