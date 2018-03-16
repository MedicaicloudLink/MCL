<template>
  <div>
    <div class="section item" id="patientbase">
      <p class="title">基本信息</p>
      <span class="dec">患者的基本登记信息</span>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>姓名<span class="keep">*</span><span class="keep-hint" v-if="pbaseName">(此题是必填项，必须完成之后才可以提交)</span></p></div>
        <div class="a-item"><m-input hintText="您的姓名" full v-model="patientBase.name"></m-input></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>性别<span class="keep">*</span><span class="keep-hint" v-if="pbaseSex">(此题是必填项，必须完成之后才可以提交)</span></p></div>
        <div class="a-item">
          <m-radio nativeValue="1" v-model="patientBase.gender">男</m-radio>
          <m-radio nativeValue="2" v-model="patientBase.gender">女</m-radio>
        </div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>出生日期</p></div>
        <div class="a-item"><m-date :answers.sync="sPatientBase.birthday" v-model="patientBase.birthday"></m-date></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q">
          <p>登记日期
            <span class="keep">*</span>
            <span class="keep-hint" v-if="pbaseJoin">(此题是必填项，必须完成之后才可以提交)</span>
            <span class="keep-hint" v-if="!pbaseJoin && pbaseJoinReg">(数据格式有误！)</span>
          </p>
        </div>
        <div class="a-item"><m-date :answers.sync="sPatientBase.jointime" v-model="patientBase.jointime" now></m-date></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>地址</p></div>
        <div class="a-item"><m-address :answers.sync="sPatientBase.address" v-model="patientBase.address"></m-address></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>联系电话<span class="keep">*</span><span class="keep-hint" v-if="pbasePhone">(此题是必填项，必须完成之后才可以提交)</span></p></div>
        <div class="a-item"><m-input hintText="电话号码" full v-model="patientBase.phone"></m-input></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>备用电话</p></div>
        <div class="a-item"><m-input hintText="电话号码" full v-model="patientBase.secondphone"></m-input></div>
      </div>
    </div>
  </div>
</template>

<script>
import MDate from './Date.vue'
import MAddress from './Address.vue'
export default {
  name: 'Baseinfo',
  components: { MDate, MAddress },
  props: { patientB: Object },
  data () {
    return {
      patientBase: {},
      pbaseName: false,
      pbaseSex: false,
      pbasePhone: false,
      pbaseJoin: false,
      pbaseJoinReg: false,
      regHint: [] // 正则提示
    }
  },
  watch: {
    patientBase () {
      this.$emit('update:patientB', this.patientBase)
    }
  },
  mounted () { this.initBase() },
  computed: {
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
  methods: {
    initBase () {
      this.patientBase = this.patientB
      if (!this.$route.params.mdid) {
        for (let info in this.patientBase) {
          if (info === 'jointime') this.patientBase[info] = new Date().toJSON().substr(0, 10)
          else this.patientBase[info] = ''
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
      if (this.patientBase.name.length <= 0) this.pbaseName = true
      else this.pbaseName = false
      if (this.patientBase.gender.length <= 0) this.pbaseSex = true
      else this.pbaseSex = false
      if (this.patientBase.phone.length <= 0) this.pbasePhone = true
      else this.pbasePhone = false
      // 入组时间判断
      if (JSON.stringify(this.sPatientBase.jointime) !== '{}') {
        let flagKeep = []
        let flagReg = []
        let str
        for (let j in this.sPatientBase.jointime) {
          if (this.sPatientBase.jointime[j]) {
            flagKeep.push(true)
          } else {
            flagKeep.push(false)
          }
          if (j === 'year') str = /^[1-9]\d{3}$/
          else if (j === 'month') str = /^([1-9]|0[1-9]|1[0-2])$/
          else str = /^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/
          flagReg.push(this.regExp(str, this.sPatientBase.jointime[j]))
        }
        if (flagKeep.every(item => item)) this.pbaseJoin = false
        else this.pbaseJoin = true
        if (flagReg.every(item => item)) this.pbaseJoinReg = false
        else this.pbaseJoinReg = true
      } else {
        this.pbaseJoin = true
      }

      if (this.pbaseName) return false
      if (this.pbaseSex) return false
      if (this.pbaseJoin) return false
      if (this.pbasePhone) return false
      if (this.pbaseJoinReg) return false

      return true
    }
  }
}
</script>

<style scoped>
.item {
  background: #fff;
}
.section {
  margin-top: 30px;
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #eee;
}
.section .title{
  font-size: 26px;
  margin: 0;
}
.section .dec{
  color: #999;
  font-size: 12px;
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
