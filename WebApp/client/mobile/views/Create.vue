<template>
  <div id="create-new" @scroll="scrollPosition">
    <mynav></mynav>
    <h2 class="title">
      <p>
        <router-link :to="'/project/' + projectid + '/patients/1'" style="margin-right: 14px;"><i class="iconfont icon-left"></i>返回</router-link>{{ $root.currentProject.u_projectName }}
      </p>
    </h2>
    
    <!--侧边栏-->
    <div class="fixed-right" :style="{left: content_width + 'px'}">
      <div class="temp-menu">
        <div v-for="(t, index) in templates" 
          :class="{active: t.u_templateid === scrollState}"
          @click="choiceRecord(index)">
          <p>{{ t.u_templatename }}</p>
          <!--<span :class="{active: t.u_templateid === templateid}"></span>-->
        </div>
      </div>
      <router-link :to="'/project/' + projectid + '/patients/1'" class="back-btn btn">创建新病历</router-link>
    </div>

    <!--主体内容，病历记录卡片-->
    <div class="content" id="records">

      <div class="cards flex-col" id="record-list">
        <div class="card base-info">
          <patientbase :source="patientBase" @putsever="basereq(arguments[0])"></patientbase>
          <div class="bar flex-row">
              <div class="flex-row"></div>
              <div>
                <span class="button button-gray" v-if="patientBase.isInit"><span>保存</span></span>
                <m-button type="blue" v-else :loading="baseBtnState" @click="updateBase">保存</m-button>
                <!--<m-button type="blue" :loading="baseBtnState" @click="updateBase">保存</m-button>-->
              </div>
            </div>
        </div>

        <transition-group name="list">
          <div class="card add-record-area" v-for="(r, index) in records" v-bind:key="r" :id="r.templateid">
            <div class="card-header flex-row" style="justify-content: space-between; align-items: center;">
              <h3>{{ r.type }}</h3>
              <div class="flex-row" v-if="!r.new">
                <dropdown>
                  <span class="button tools" slot="trigger">操作<i class="iconfont icon-bottom"></i></span>
                  <li @click="delRecord(r.recordid, index)">删除</li>
                </dropdown>
              </div>
            </div>
            <transition name="fade">
              <component class="input-area" v-bind:is="r.template" :source="r.sourcedata" @putsever="setRecords(arguments[0], index)"></component>
            </transition>
            <transition name="fade">
              <record-bar :imgshow="r.imgState" :remarkshow="r.remarkState" :imgs="r.imgs" :remarktext="r.remark" @putsever="setRemarks(arguments[0], index)"></record-bar>
            </transition>
            <div class="bar flex-row">
              <div class="flex-row">
                <i class="iconfont icon-tupian add-remark" @click="r.imgState = true"></i>
                <i class="iconfont icon-beizhu add-remark" @click="r.remarkState = true"></i>
              </div>
              <div>
                <template v-if="r.new">
                  <m-button type="gray" style="margin-right: 24px;" @click="cancelInputState(index)">取消</m-button>
                  <m-button type="blue" :loading="r.addBtnState" @click="putData(index)">添加</m-button>
                </template>
                <span class="button button-gray" v-else-if="r.isInit"><span>保存</span></span>
                <m-button type="blue" v-else :loading="r.addBtnState" @click="update(index)">保存</m-button>
              </div>
            </div>
          </div>
        </transition-group>
      </div>
    </div>

  </div>
</template>

<script>
  import Vue from 'vue'
  import API from '../api.js'
  import EventListener from '../utils/EventListener.js'
  import { scrollTo, Type, deepEquals, throttle } from '../utils/tools.js'
  import Mynav from '../components/Header.vue'
  import MButton from '../lib/Button.vue'
  import Loading from '../lib/Loading'
  import Temp from '../template/index'
  import Patientbase from '../template/patientbase'
  import RecordBar from '../components/RecordBar.vue'
  import Dropdown from '../lib/Dropdown.vue'

  export default {
    name: 'Create',
    components: {
      Mynav,
      Dropdown,
      Patientbase,
      RecordBar,
      Loading,
      MButton
    },
    data () {
      return {
        content_width: '',
        userid: '',
        projectid: '',
        mdid: '',
        templates: [],
        patientBase: {},
        patientBaseInit: {},
        shortName: '',    // 患者姓名拼音缩写
        baseBtnState: false,    // 基本信息按钮状态
        records: [],
        recordsInit: [],
        scrollState: ''
      }
    },
    mounted () {
      this.projectid = this.$route.params.projectid
      this.mdid = this.$route.params.mdid
      this.content_width = document.getElementById('record-list').offsetLeft + document.getElementById('record-list').offsetWidth + 16
      this.getTemplates()
      this.getPatientBase()
      this.getPatientRecords()

      this._resizeEvent = EventListener.listen(window, 'resize', (e) => {
        this.content_width = document.getElementById('record-list').offsetLeft + document.getElementById('record-list').offsetWidth + 16
      })

      this._scrollEvent = EventListener.listen(window, 'scroll', (e) => {
        throttle(this.scrollEvent(), 1000)
      })
    },
    destroyed () {
      // 清除监听
      if (this._resizeEvent) this._resizeEvent.remove()
      if (this._scrollEvent) this._scrollEvent.remove()
    },
    methods: {
      scrollEvent () {
        for (let i = 0; i < this.records.length; i++) {
          let tempid = this.records[i].templateid
          let top = document.getElementById(tempid).getBoundingClientRect().top + 10
          if (top > 0) {
            this.scrollState !== tempid ? this.scrollState = tempid : ''
            break
          }
        }
        // this.scrollState = showid
      },
      /** 获取患者基本信息 */
      getPatientBase () {
        API.GetPatientBase({mdid: this.mdid}).then(response => {
          this.patientBase = response[0]
          this.$set(this.patientBase, 'isInit', true)
          // this.patientBase.isInit = true
          this.patientBaseInit = {
            patientname: response[0].u_patientname,
            gender: response[0].u_gender,
            birthday: response[0].u_birthday,
            jointime: response[0].u_jointime
          }

          this.nameToshort(response[0].u_patientname)
        }).catch(err => {
          console.log(err)
        })
      },
      nameToshort (str) {
        API.ChineseToShort({string: str}).then(response => {
          this.shortName = response.shortpinyin
        }).catch(err => {
          console.log(err)
        })
      },
      basereq (data) {
        this.patientBase.u_patientname = data.patientname
        this.patientBase.u_gender = data.gender
        this.patientBase.u_birthday = data.birthday
        this.patientBase.u_jointime = data.jointime

        if (this.patientBaseInit.patientname !== data.patientname) {
          this.patientBase.isInit = false
          return
        }
        if (this.patientBaseInit.gender !== data.gender) {
          this.patientBase.isInit = false
          return
        }
        if (this.patientBaseInit.birthday !== data.birthday) {
          this.patientBase.isInit = false
          return
        }
        if (this.patientBaseInit.jointime !== data.jointime) {
          this.patientBase.isInit = false
          return
        }

        this.patientBase.isInit = true
      },
      // 更新患者基本信息
      updateBase () {
        let vm = this
        let data = {
          userid: this.$root.userid,
          mdid: this.patientBase.u_MDID,
          projectid: this.projectid,
          name: this.patientBase.u_patientname,
          gender: this.patientBase.u_gender,
          birthday: this.patientBase.u_birthday,
          jointime: this.patientBase.u_jointime
        }
        this.confirm({
          title: '更新患者基本信息',
          message: '确定更新患者基本信息！',
          onConfirm () {
            vm.baseBtnState = true
            // 更新
            API.UpdatePatientBase(data).then(response => {
              // 初始化患者状态更新
              vm.patientBaseInit = {
                patientname: vm.patientBase.u_patientname,
                gender: vm.patientBase.u_gender,
                birthday: vm.patientBase.u_birthday,
                jointime: vm.patientBase.u_jointime
              }
              vm.patientBase.isInit = true
              vm.baseBtnState = false

              vm.toast({
                text: '更新成功',
                type: 'success',
                placement: 'bottom-left'
              })
            }).catch(err => {
              vm.baseBtnState = false
              vm.toast({
                text: err,
                type: 'error',
                placement: 'bottom-left'
              })
              console.log(err)
            })
          }
        })
      },
      /** 获取患者病历记录列表 */
      getPatientRecords () {
        this.records = []
        this.recordsInit = []
        API.GetPatientRecords({mdid: this.mdid}).then(response => {
          response.map(n => {
            Vue.component('temp' + n.u_templetid, Temp['template' + n.u_templetid])
            this.records.push({
              type: n.u_templatename,
              recordid: n.u_recordid,
              templateid: n.u_templetid,
              template: 'temp' + n.u_templetid,
              create_time: n.u_createtime,
              update_time: n.u_updatetime,
              patientdata: JSON.parse(n.u_patientdata),
              sourcedata: JSON.parse(n.sourcedata),
              /* eslint-disable */
              imgs: n.imgs.length > 10 ? eval('{'+ n.imgs +'}') : new Array(),
              /* eslint-endable */
              remark: n.remark,
              imgState: n.imgs.length > 10 ? true : false,
              remarkState: n.remark.length > 0 ? true : false,
              isInit: true,
              addBtnState: false
            })

            this.initData(this.recordsInit.length)
          })
          // 排序
          this.records.sort((a, b) => {
            if (a.templateid > b.templateid) { return 1 }
            if (a.templateid < b.templateid) { return -1 }
            return 0
          })
        }).catch(err => {
          console.log(err)
        })
      },
      /** 获取项目模板 */
      getTemplates () {
        API.GetpProjectTemplates({projectid: this.projectid}).then(response => {
          this.templates = response
          if (this.newPatientState()) {
            this.changeTemplate(this.templates[0].u_templateid)
          }
          for (let n in this.templates) {
            this.templates[n]
          }
        }).catch(err => {
          console.log(err)
        })
      },
      /** 新建患者直接进入编辑页面 */
      newPatientState () {
        return this.$route.query.new === '1'
      },
      /** 切换模板 */
      changeTemplate (id) {
        // 初始化状态
        this.cleanTempState()
        // 修改模板
        this.addState = true
        this.mycomponent = ''
        Vue.component('temp', Temp['template' + id])
        this.mycomponent = 'temp'
        this.templateid = id
      },
      /* 关闭输入模式 */
      cancelInputState (index) {
        let vm = this
        if (this.records[index].imgs.length > 0 || this.records[index].remark.length > 0 || Object.keys(this.records[index].patientdata).length !== 0) {
          this.confirm({
            title: '退出编辑状态',
            message: '退出编辑状态会清空现在已经输入的数据！',
            onConfirm () {
              vm.records.splice(index, 1)
            }
          })
        } else {
          this.records.splice(index, 1)
        }
      },
      /** 切换下一个模板 */
      nextTemplate () {
        let index = 0
        for (let n in this.templates) {
          if (this.templateid === this.templates[n].u_templateid) {
            index = parseInt(n) + 1
            break
          }
        }
        if ((this.templates.length - 1) >= parseInt(index)) {
          this.changeTemplate(this.templates[index].u_templateid)
        } else {
          this.cleanTempState()
        }
      },
      // 滚动位置判断
      scrollPosition () {
        // if (window.offsetTop === )
      },
      // 导航
      choiceRecord (index) {
        // 未添加的记录类型
        let isHave = false
        let tempid = this.templates[index].u_templateid
        this.records.map(n => { if (tempid === n.templateid) { isHave = true } })
        if (!isHave) {
          Vue.component('temp' + tempid, Temp['template' + tempid])
          this.records.push({
            type: this.templates[index].u_templatename,
            templateid: tempid,
            template: 'temp' + tempid,
            patientdata: {},
            sourcedata: parseInt(tempid) === 5 ? [] : {},
            imgs: new Array(),
            remark: '',
            imgState: false,
            remarkState: false,
            addBtnState: false,
            new: true
          })
          // 排序
          this.records.sort((a, b) => {
            if (a.templateid > b.templateid) { return 1 }
            if (a.templateid < b.templateid) { return -1 }
            return 0
          })
          Vue.nextTick(() => {
            let to_postion = document.getElementById(this.templates[index].u_templateid).offsetTop
            scrollTo(document.body, to_postion, 300)
          })
        }

        // 已有直接滚动
        if (isHave) {
          let to_postion = document.getElementById(this.templates[index].u_templateid).offsetTop
          scrollTo(document.body, to_postion, 300)
        }
      },
      initData (index) {
        this.recordsInit[index] = JSON.parse(JSON.stringify(this.records[index]))
        // 排序
        this.recordsInit.sort((a, b) => {
          if (a.templateid > b.templateid) { return 1 }
          if (a.templateid < b.templateid) { return -1 }
          return 0
        })
      },
      setRemarks (data, index) {
        console.log(Type(data.imgs))
        this.records[index].imgs = data.imgs
        this.records[index].remark = data.remark
        this.checkDataIsInitState(index)
      },
      setRecords (data, index) {
        // Type(data)
        this.records[index].sourcedata = data.sourceData
        this.records[index].patientdata = data.showData

        this.recordsInit.map(n => {
          if (n.recordid === this.records[index].recordid) {
            this.checkDataIsInitState(index)
          }
        })
      },
      // 初始化状态检查
      checkDataIsInitState (index) {
        // 新建的病历类型处理，初始状态没有此项，初始化状态检查会出错
        if (Type(this.recordsInit[index]) === 'undefined') return
        if (Type(this.recordsInit[index]) === 'null') return

        const new_data = this.records[index].sourcedata
        const init_data = this.recordsInit[index].sourcedata
        const imgChangeState = JSON.stringify(this.records[index].imgs) !== JSON.stringify(this.recordsInit[index].imgs)
        const remarkChangeState = this.records[index].remark !== this.recordsInit[index].remark
        if (deepEquals(new_data, init_data).indexOf(false) !== -1 || imgChangeState || remarkChangeState) {
          this.records[index].isInit = false
        } else {
          this.records[index].isInit = true
        }
      },
      // 更新病历记录
      updateRecordData (data) {
        return {
          createuserid: this.$root.userid,
          recordid: data.recordid,
          patientdata: JSON.stringify(data.patientdata),
          sourcedata: JSON.stringify(data.sourcedata),
          imgs: JSON.stringify(data.imgs),
          remark: data.remark
        }
      },
      update (index) {
        // 按钮点击不可点击状态
        this.records[index].addBtnState = true
        const data = this.updateRecordData(this.records[index])
        // console.log(data)
        API.UpdatePatientData(data).then(response => {
          // 更新初始状态
          this.records[index].isInit = true
          this.initData(index)

          // 按钮恢复可点击
          this.records[index].addBtnState = false
          this.toast({
            text: '保存成功',
            type: 'success',
            placement: 'bottom-left'
          })
        }).catch(err => {
          // 按钮恢复可点击
          this.records[index].addBtnState = false
          console.log(err)
        })
      },
      // 添加病历记录
      postRecordData (data) {
        return {
          createuserid: this.$root.userid,
          mdid: this.mdid,
          templateid: data.templateid,
          patientdata: JSON.stringify(data.patientdata),
          sourcedata: JSON.stringify(data.sourcedata),
          imgs: JSON.stringify(data.imgs),
          remark: data.remark
        }
      },
      putData (index) {
        // 按钮点击不可点击状态
        this.records[index].addBtnState = true
        const data = this.postRecordData(this.records[index])
        if (data.sourcedata === '{}') {
          this.toast({
            text: '新纪录没有任何数据，请添加数据后保存',
            type: 'error',
            placement: 'bottom-left'
          })
          this.records[index].addBtnState = false
          return
        }
        API.CreatePatientData(data).then(response => {
          this.toast({
            text: '新纪录添加成功',
            type: 'success',
            placement: 'bottom-left'
          })
          // 按钮恢复可点击
          this.records[index].addBtnState = false
          this.getPatientRecords()
        }).catch(err => {
          // 按钮恢复可点击
          this.records[index].addBtnState = false
          this.toast({
            text: '创建失败，请重新尝试',
            type: 'error',
            placement: 'bottom-left'
          })
          console.log(err)
        })
      },
      // 删除记录
      delRecord (recordid, index) {
        let vm = this
        API.DeletePatientData({userid: this.$root.userid, recordid: recordid}).then(response => {
          this.confirm({
            title: '删除病历记录',
            message: '确定删除这条数据！',
            onConfirm () {
              vm.records.splice(index, 1)
              vm.recordsInit.splice(index, 1)
              vm.toast({
                text: '删除成功',
                type: 'success',
                placement: 'bottom-left'
              })
            }
          })
        }).catch(err => {
          this.toast({
            text: err,
            type: 'error',
            placement: 'bottom-left'
          })
          console.log(err)
        })
      }
    }
  }
</script>

<style scoped>
  .title {
    background: #fff;
    height: 48px;
    color: #555;
    font-size: 16px;
    line-height: 48px;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.12);
  }

  .title>p {
    width: 768px;
    margin: 0 auto;
  }

  .title i {
    color: #468df1;
    font-size: 16px;
    margin-right: 4px;
  }

  .title a {
    display: inline-block;
    height: 48px;
    line-height: 48px;
    padding: 0 12px;
    font-size: 16px;
    color: #468df1;
    border-left: 1px solid rgba(0, 0, 0, 0.12);
    border-right: 1px solid rgba(0, 0, 0, 0.12);
    font-weight: 400;
  }

  .menu {
    width: 180px;
    padding: 10px;
  }

  .menu a {
    display: block;
    font-size: 16px;
    color: #777;
    padding: 15px 10px;
  }

  .content {
    width: 768px;
    margin: 24px auto 60px;
  }


  .bar {
    padding: 8px 0;
    justify-content: space-between;
  }

  .bar>div:first-child{
    align-items: center;
  }

  .bar>div:first-child i {
    margin-right: 10px;
  }

  i.add-remark {
    color: #468df1;
    font-size: 24px;
  }

  .fixed-right {
    position: fixed;
    top: 140px;
    right: 30px;
    width: 220px;
  }

  .temp-menu {
    color: #777;
    background: #fff;
    font-size: 16px;
    line-height: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    border: 1px solid rgba(0,0,0,.09);
  }

  .temp-menu>div{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    padding: 14px;
    border-left: 3px solid rgba(0,0,0,0);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .temp-menu>div.active {
    border-left-color: #468df1;
  }
  
  .back-btn {
    margin-top: 8px;
    text-align: center;
    display: block;
    padding: 14px 0;
    background: #fff;
    color: #468df1;
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    border: 1px solid rgba(0,0,0,.09);
  }

  .hint {
    width: 100%;
    text-align: center;
    color: #777;
  }


  .cards {
    display: flex;
    flex-wrap: wrap;
  }

  .card {
    background: #fff;
    margin: 12px 0;
    padding: 25px;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.2), 0 1px 5px 0 rgba(0,0,0,.12);
    font-size: 16px;
    line-height: 32px;
  }

  .card:hover {
    box-shadow: 0 2px 8px 0 rgba(0,0,0,.14), 0 3px 4px -5px rgba(0,0,0,.2), 0 1px 8px 0 rgba(0,0,0,.12);
  }

  .card-header {
    border-bottom: 1px solid #f3f3f3;
    /*padding-bottom: 8px;*/
  }

  .card-header h3 {
    font-size: 22px;
    line-height: 48px;
    color: #777;
    font-weight: 400;
  }

  .button.tools {
    background: #eee;
    border: 1px solid #d4d4d4;
  }

  .button.tools>i {
    margin-left: 8px;
  }

  .card-main {
    line-height: 48px;
    font-size: 16px;
    cursor: pointer;
  }

  .main-record {
    display: inline-block;
    width: 170px;
  }

  .card-remark.text{
    padding: 4px 20px;
    margin: 10px 0 12px;
    border-left: 3px solid #3cb6ec;
    line-height: 2;
    font-size: 16px;
    color: rgba(0,0,0,.64);
  }

  .card-footer {
    text-align: right;
  }

  .card.base-info {
    line-height: 48px;
  }

  .add-record-area {
    margin-top: 35px;
  }

  .dropdown-menu li {
    cursor: pointer;
    padding: 0px 8px;
  }

  .dropdown-menu li:hover {
    background: #eee;
  }

  .list-enter-active, .list-leave-active {
    transition: all .5s;
  }
  .list-enter, .list-leave-active {
    opacity: 0;
    transform: translateX(30px);
  }

  .fade-enter-active, .fade-leave-active {
    transition: all .2s ease-in;
  }
  .fade-enter, .fade-leave-active {
    opacity: 0;
  }

  @media screen and (max-width: 2000px) {
    .content {
      width: 626px;
      margin: 24px auto 100px;
      padding-right: 140px;
    }
  }

  @media screen and (max-width: 1024px) {
    .content {
      width: 626px;
      margin: 24px 0 0 100px;
    }

    .fixed-right {
      width: 170px;
    }

    .temp-menu>div {
      font-size: 14px;
    }
  }

  @media screen and (max-width: 770px) {
    .content {
      width: 500px;
      margin: 24px 0 0 20px;
    }

    .fixed-right {
      width: 170px;
    }

    .temp-menu>div {
      font-size: 13px;
    }
  }
</style>