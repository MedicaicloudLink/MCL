<template>
  <div class="patient" >
    <div class="info card">
      <div class="base">
        <div class="flex-row space">
          <div>
            <p class="pname">
              <span class="gf">{{ name }}</span>
              <span class="psex">
                <i v-if="gender === '男'" class="iconfont icon-nan"></i>
                <i v-if="gender === '女'" class="iconfont icon-nv"></i>
              </span>
              <span class="lock cg">{{ lockState ? '已锁定' : '允许操作' }}</span>
            </p>
            <p><span class="cg w100">生日</span>{{ birtday }}</p>
            <p><span class="cg w100">地址</span>{{ address }}</p>
            <p><span class="cg w100">联系电话</span>{{ phone }} <span v-if="phone2 !== ''">，{{ phone2 }}</span></p>
          </div>
          <div class="flex-row" v-if="followState !== '9' && followState !== '1' && !lockState">
            <dropdown class="other-btn">
              <div class="btn-gray tool" slot="trigger"><i>操作...</i><i class="iconfont icon-xiangxia"></i></div>
              <li @click="setFollowData('7')">保存并返回</li>
              <li @click="cancelDialog = true">取消本次随访</li>
            </dropdown>
            <m-button type="blue" @click="setFollowData('9')">提交记录</m-button>
          </div>
        </div>
      </div>

      <time-line class="history" :value="history" :start="join_time"></time-line>

      <div class="fixed base flex-row" v-if="base_view" :style="{width: base_fixed_width + 'px'}">
        <p class="pname">
          <span class="gf">{{ name }}</span>
          <span class="psex">
            <i v-if="gender === '男'" class="iconfont icon-nan"></i>
            <i v-if="gender === '女'" class="iconfont icon-nv"></i>
          </span>
          <span class="lock cg">{{ lockState ? '已锁定' : '允许操作' }}</span>
        </p>
        <div class="flex-row" v-if="followState !== '9' && followState !== '1' && !lockState">
          <dropdown class="other-btn">
            <div class="btn-gray tool" slot="trigger"><i>操作...</i><i class="iconfont icon-xiangxia"></i></div>
            <li @click="setFollowData('7')">保存并返回</li>
            <li @click="cancelDialog = true">取消本次随访</li>
          </dropdown>
          <m-button type="blue" @click="setFollowData('9')">提交记录</m-button>
        </div>
      </div>
    </div>


    <div class="side-nav" :style="{top: navPostion.top + 'px', left: navPostion.left + 'px'}">
      <div class="nav">
        <p :class="{active: item.flag}" v-for="item, index in sections" @click="clickScroll(index)" v-if="sections.length > 0">
        {{item.title}}
        </p>
      </div>

      <remark-view :recordid="recordid" :opreate="followState !== '9' && followState !== '1' && !lockState"></remark-view>
    </div>

    <div class="form-content">
      <form-view ref="crf" :temp="form" :data.sync="formdata" :isBase="false"></form-view>
    </div>

    <m-modal :open="cancelDialog" title="取消本次随访" @close="cancelDialog = false">
      <h1 style="font-size: 20px;padding-bottom: 12px;">取消本次随访的原因</h1>
      <div class="option">
        <m-radio v-model="followState" :nativeValue="3">号码错误</m-radio>
        <m-radio v-model="followState" :nativeValue="4">无人接听</m-radio>
        <m-radio v-model="followState" :nativeValue="5">患者不配合</m-radio>
        <m-radio v-model="followState" :nativeValue="6">因意外事件打断</m-radio>
        <m-radio v-model="followState" :nativeValue="2">其他原因</m-radio>
        <m-input v-show="followState === 2" full v-model="cancelReson"></m-input>
      </div>

      <div slot="footer" class="tools">
        <m-button @click="cancelDialog = false">取消</m-button>
        <m-button class="add" type="blue" @click="setFollowData(followState)">确定</m-button>
      </div>
    </m-modal>

  </div>
</template>

<script>
import FormView from '../../parser/index'
import RemarkView from './remark'
import TimeLine from './widget/TimeLine'
import EventListener from '../tool/EventListener'
import { scrollTo } from '../tool/tools'
import parse from '../../parser/parse.js'

export default {
  name: 'Patient',
  components: { FormView, RemarkView, TimeLine },
  props: { projectid: String },
  data () {
    return {
      mdid: this.$route.params.mdid,
      taskid: this.$route.params.taskid,
      recordid: this.$route.params.recordid,
      lockState: false,
      base_view: false,
      base_fixed_width: window.innerWidth - 326,
      navPostion: {top: 345, left: -888},   // 导航距离顶部高度
      name: '',
      gender: '',
      birtday: '',
      join_time: '',
      address: '',
      phone: '',
      phone2: '',
      form: [],
      formname: '',
      formdata: [],
      followState: 1,   // 随访状态（1:已完成随访（审核通过） 2：其他原因 3：号码错误 4：无人接听 5：患者不配合6：因意外事件打断；7.保存；9提交（待审核）；10审核未通过',）
      cancelDialog: false,    // 取消本次随访
      cancelReson: '',
      processdata: [],
      history: [],
      sections: []
    }
  },
  watch: {
    followState (val) { this.cancelReson = '' }
  },
  mounted () {
    this.navPostion.left = this.$refs.crf.$el.getBoundingClientRect().right + 20
    this.getPatientBase()
    this.getHistory()
    this.lockPatient()
    if (this.$route.name === 'frecord') this.getForm()
    if (this.recordid) this.getFollowData()

    this._scrollEvent = EventListener.listen(this.$el, 'scroll', this.scrollEvent)
  },
  destroyed () {
    // 清除监听
    // if (this._resizeEvent) this._resizeEvent.remove()
    if (this._scrollEvent) this._scrollEvent.remove()
  },
  methods: {
    sideMenuInit () {
      this.form.map(i => {
        if (i.type === 'SECTION') this.sections.push({ title: i.title, flag: false })
      })
    },
    // 侧边栏点击章节事件
    clickScroll (index) {
      this.sections.map(i => { i.flag = false })
      this.sections[index].flag = true
      let offsetTopVal = document.getElementsByClassName('section')[index].offsetTop - 138
      scrollTo(this.$el, offsetTopVal, 300)
    },
    // 侧边栏随页面滚动定位
    sideScrollEvent () {
      this.sections.map(i => { i.flag = false })
      let els = document.getElementsByClassName('section')
      for (let j = 0; j < els.length; j++) {
        if ((els[j].getBoundingClientRect().top + 10) > 0 && (els[j].getBoundingClientRect().top + 10) > 0) {
          // 第一个距离页面top为正的section设置true，停止循环
          this.sections[j].flag = true
          break
        }
      }
    },
    getPatientBase () {
      this.$http.GetPatientBase({mdid: this.mdid}).then(rep => {
        this.name = rep[0].u_patientname
        this.gender = rep[0].u_gender === '1' ? '男' : '女'
        this.birtday = rep[0].u_birthday
        this.join_time = rep[0].u_jointime
        this.address = rep[0].u_address
        this.phone = rep[0].u_phone
        this.phone2 = rep[0].u_secondphone
      }).catch(err => console.log(err))
    },
    getForm () {
      this.$http.GetFollowForm({taskid: this.taskid}).then(rep => {
        this.form = JSON.parse(rep.formdata)
        this.formname = rep.formname
        this.sideMenuInit()
      }).catch(err => console.log(err))
    },
    getFollowData () {
      this.$http.GetFollowData({recordid: this.recordid}).then(rep => {
        this.form = JSON.parse(rep[0].formdata)
        this.formname = rep[0].formname
        this.formdata = JSON.parse(rep[0].sourcedata)
        this.processdata = JSON.parse(rep[0].patientdata)
        this.followState = rep[0].u_follow_status
        this.sideMenuInit()
      }).catch(err => console.log(err))
    },
    getHistory () {
      this.$http.GetHistoryData({mdid: this.mdid}).then(rep => {
        this.history = rep
      }).catch(err => console.log(err))
    },
    lockPatient () {
      this.$http.LockPatient({userid: this.$root.userid, mdid: this.mdid, taskid: this.taskid}).then(rep => {
        console.log(rep)
        if (rep !== 'allow') this.lockState = true
      }).catch(err => console.log(err))
    },
    setFollowData (type) {
      // 患者病历数据必填项选择
      const verify_result = this.$refs.crf.submitVerify()
      if (!verify_result && type === '9') {
        this.toast({type: 'error', text: '必填项未填或者数据格式不正确！', placement: 'top'})
        return
      }

      const parse_data = parse(this.formdata)

      // 状态1:已完成随访（审核通过） 2：其他原因 3：号码错误 4：无人接听 5：患者不配合6：因意外事件打断；7.保存；9提交（待审核）；10审核未通过'
      let data = {
        userid: this.$root.userid,
        projectid: this.projectid,
        mdid: this.mdid,
        taskid: this.taskid,
        sourcedata: JSON.stringify(this.formdata),
        patientdata: JSON.stringify(parse_data),
        status: type,
        other_reason: this.cancelReson
      }
      this.$http.SetFollowData(data).then(rep => {
        this.$router.push({name: 'follow', params: {projectid: this.projectid}})
      }).catch(err => console.log(err))
    },
    scrollEvent (e) {
      // 基本信息固定
      e.target.scrollTop > 160 ? this.base_view = true : this.base_view = false
      // 侧边栏状态 TODO
      e.target.scrollTop > 160 ? this.navPostion.top = 140 : this.navPostion.top = 350
      // 侧边栏滚动定位
      this.sideScrollEvent()
    }
  }
}
</script>

<style scoped>
.side-nav {
  position: fixed;
  top: 250px;
  right: 8px;
  width: 320px;
  z-index: 1;
}

.nav p {
  background: #fff;
  border-bottom: 1px solid #ededed;
  height: 40px;
  cursor: pointer;
  padding: 0 10px;
  text-align: left;
  line-height: 40px;
}

.nav p.active {
  color: #468df1;
}

.flex-row.space {
  justify-content: space-between;
  align-items: flex-start;
}
.patient {
  align-items: flex-start;
}

.icon-nan {
  color: #24AE69;
}

.icon-nv {
  color: red;
}

.form-content {
  margin: 0 auto;
  width: 720px;
}

.card {
  background: #fff;
  border-radius: 3px;
  padding: 12px 8px;
}

.info {
  border-radius: 7px;
  margin: 12px 20px;
  padding: 16px 24px;
  line-height: 2;
}

.psex {
  margin: 0px 8px 0 4px;
}

.btn-gray {
  width: 96px;
  margin-right: 8px;
  display: flex;
  justify-content: space-between;
  height: 32px;
  text-align: center;
  line-height: 30px;
  cursor: pointer;
  padding: 0 8px;
  color: rgba(0,0,0,.87);
  border: 1px solid rgba(0,0,0,.12);
}

.other-btn li {
    cursor: pointer;
    padding: 6px;
}

.other-btn li:hover {
  background: rgba(107, 167, 251, 0.11);
}

/* 字体 */
.gf {
  font-size: 22px;
  line-height: 1;
}

.cg {
  color: rgba(0, 0, 0, .6);
}


.w100 {
  display: inline-block;
  width: 100px;
}

.history {
  border-top: 1px solid #e1e1e1;
}

.base.fixed {
  position: fixed;
  left: 246px;
  top: 56px;
  background: #fff;
  justify-content: space-between;
  border-bottom: 1px solid #ddd;
  padding: 12px 24px;
  z-index: 10;
}

@media screen and (max-width: 1280px) {
  .base.fixed {
    left: 200px;
  }
}

@media screen and (max-width: 1500px) {
  .form-content {
    width: 640px;
    margin: 0 20px 30px;
  }
}
</style>
