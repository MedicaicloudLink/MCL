<template>
<div class="editor">
  <div class="form-title">
    <div class="content flex-row">
      <div class="left flex-row">
        <router-link to="{path: '/'}"><i class="iconfont icon-fanhui"></i></router-link>
        <m-input hintText="未命名的表单" bgDark preview autoWidth :maxWidth="$root.window_width * 0.6 + 'px'" :fontsize="18" v-model="name"></m-input>
        <loading v-if="ajaxState"></loading>
      </div>
      <div class="right flex-row">
        <span class="share" @click="share_dialog = true"><i class="iconfont icon-fenxiang"></i></span>
        <m-button type="gray" v-if="ispublic === '1'" @click="public_dialog = true">发布</m-button>
        <span class="btn" v-if="ispublic === '2'" style="background: #fff;color:#333;padding: 4px 18px;margin:0 8px;border-radius: 2px;">已发布</span>
      </div>
    </div>
  </div>

  <div class="editor-preview">
    <div class="content">
      <span @click="preview = false" :class="{active: !preview}">问题</span>
      <span @click="preview = true" :class="{active: preview}">预览</span>
    </div>
  </div>


  <div class="editor-form" id="editor-items" v-show="!preview">
     <div class="item">
      <div class="form">
        <m-input full autoHeight hintText="请填写表单的标题" :fontsize="26" v-model="item[0].title" class="title"></m-input>
        <m-input full autoHeight hintText="为表单填写说明性文字（可选）" v-model="item[0].describe" class="title"></m-input>
      </div>
    </div> 


    <div class="alert-notice" v-if="noticeState">
      <div class="flex-row">
        <p><i class="iconfont icon-tishi"></i> 基本信息[默认]</p>
        <i class="iconfont icon-delect" @click="noticeState = false"></i>
      </div>
      <p>姓名， 性别，出生日期，联系电话，地址</p>
    </div>

    <div class="item" v-for="(qa, qaid) in item">
      <div class="section" v-if="qa.type === 'SECTION'" :class="{active: nowState === qaid}" @click="nowState = qaid">
        <m-input full autoHeight hintText="请填写章节的标题" :fontsize="22" v-model="qa.title" class="title" :preview="nowState !== qaid"></m-input>
        <m-input full autoHeight hintText="为本章节填写说明性文字（可选）" v-model="qa.describe.text" class="title" :preview="nowState !== qaid"></m-input>
        <div class="qa-tool flex-row" v-show="nowState === qaid">
          <i class="iconfont icon-delete2" @click="delQuestion(qaid)"></i>
        </div>
      </div>

      <div v-else-if="qa.type === 'FORM'"></div>

      <div class="questions" v-else :class="{active: nowState === qaid}">

        <div class="q flex-row" @click="nowState = qaid">
          <m-input full autoHeight hintText="请填写问题具体的内容" v-model="qa.title" :preview="nowState !== qaid"></m-input>
          <m-select v-model="qa.type" @input="typeChange(arguments[0], qaid)" v-if="nowState === qaid">
            <m-option value="SHORTTEXT">简短回答</m-option>
            <m-option value="LONGTEXT">段落式回答</m-option>
            <m-option value="RADIO">单选</m-option>
            <m-option value="CHECKBOX">多选</m-option>
            <m-option value="DROPDOWN">下拉选择</m-option>
            <m-option value="ADDRESS">地址</m-option>
            <m-option value="TABLE">表格选择</m-option>
            <m-option value="LINEARSCALE">线性量表</m-option>
            <m-option value="TIME">时间</m-option>
            <m-option value="DATE">日期</m-option>
            <m-option value="FILEUPLOAD">文件上传</m-option>
          </m-select>
        </div>
        <div class="question-other" @click="nowState = qaid">
          <div class="question-dec flex-row" v-if="qa.describe.show">
            <m-input full autoHeight hintText="请添加问题其他描述" :fontsize="13" v-model="qa.describe.text" :preview="nowState !== qaid"></m-input>
            <span @click="qa.describe.text = '', qa.describe.show = false"><i class="iconfont icon-delete2" v-show="nowState === qaid"></i></span>
          </div>
          <div class="question-link flex-row" v-if="qa.link.show">
            <m-input full hintText="请添加问题关联链接" :fontsize="13" v-model="qa.link.text" :preview="nowState !== qaid"></m-input>
            <span @click="qa.link.text = '', qa.link.show = false"><i class="iconfont icon-delete2" v-show="nowState === qaid"></i></span>
          </div>
          <div class="quesiton-img" v-if="qa.img.show">
            <img :src="'http://' + qa.img.url" alt="option" v-if="qa.img.url.length > 10" class="img-option">
            <span @click="cleanQuesionImg(qaid)"><i class="iconfont icon-delete2" v-show="nowState === qaid"></i></span>
          </div>
        </div>
        <div class="a-item" @click="nowState = qaid">
          <!--地址-->
          <m-address :value.sync="qa.answers" v-if="qa.type === 'ADDRESS'"></m-address>
          <!--地址 end-->

          <!--下拉选择-->
          <dropdowns :value.sync="qa.answers" :isjump.sync="qa.isjump" :sections="qalist" :id="qa.id" :isactive="nowState === qaid" v-if="qa.type === 'DROPDOWN'"></dropdowns>
          <!--下拉选择 end-->

          <!--单选-->
          <multiple-choice :value.sync="qa.answers" :isjump.sync="qa.isjump" :sections="qalist" :isactive="nowState === qaid" :id="qa.id" v-if="qa.type === 'RADIO'"></multiple-choice>
          <!--单选 end-->

          <!--多选-->
          <checkboxes :value.sync="qa.answers" :isactive="nowState === qaid" v-if="qa.type === 'CHECKBOX'"></checkboxes>
          <!--多选 end-->

          <!--表格选择-->
          <m-table :value.sync="qa.answers" :isactive="nowState === qaid" v-if="qa.type === 'TABLE'"></m-table>
          <!--表格选择 end-->

          <!--线性量表选择-->
          <linear-scale :value.sync="qa.answers" :isactive="nowState === qaid" v-if="qa.type === 'LINEARSCALE'"></linear-scale>
          <!--线性量表选择 end-->

          <!--时间选择-->
          <m-time :value.sync="qa.answers" v-if="qa.type === 'TIME'"></m-time>
          <!--时间选择 end-->

          <!--日期选择-->
          <m-date :value.sync="qa.answers" v-if="qa.type === 'DATE'"></m-date>
          <!--日期选择 end-->

          <!--简短回答-->
          <short-text :value.sync="qa.answers.unit" v-if="qa.type === 'SHORTTEXT'"></short-text>
          <!--简短回答 end-->

          <!--段落回答-->
          <long-text :value.sync="qa.answers" v-if="qa.type === 'LONGTEXT'"></long-text>
          <!--段落回答 end-->

          <!--文件上传-->
          <file-upload v-if="qa.type === 'FILEUPLOAD'"></file-upload>
          <!--文件上传 end-->
        </div>
      
        <div class="qa-tool flex-row" v-show="nowState === qaid">
          <i class="iconfont icon-delete2" @click="delQuestion(qaid)"></i>
          <i class="iconfont icon-copy" @click="copeQuestion(qaid)"></i>
          <span class="split-line">|</span>
          <div class="flex-row">
            <span class="dec">必答题：</span>
            <m-select v-model="qa.keep" style="width: 100px;">
              <m-option value="true">是</m-option>
              <m-option value="false">否</m-option>
            </m-select>
          </div>
        </div>

        <div class="move-tool" v-show="nowState === qaid">
          <i class="iconfont icon-top" @click="moveBefore(qaid)"></i>
          <i class="iconfont icon-bottom" @click="moveNext(qaid)"></i>
        </div>
      </div>


    </div>

  </div>

  <div class="tools" v-show="!preview" :style="{left: items_postion.left + 'px'}" v-if="ispublic !== '2'">
    <p @click="addQuestion"><i class="iconfont icon-tianjia"></i>添加新问题</p>
    <p @click="addQuestionOther('img')" style="border: none;"><i class="iconfont icon-tupian"></i>图片</p>
    <p @click="addQuestionOther('describe')" style="border: none;"><i class="iconfont icon-shuoming"></i>说明性文字</p>
    <p @click="addQuestionOther('website')"><i class="iconfont icon-link"></i>网站链接</p>
    <p @click="addSection"><i class="iconfont icon-section"></i>添加新的章节</p>
  </div>

  <preview class="preview-form" :temp="item" :patientB="{}" :data="[]" v-if="preview"></preview> 

  <div class="tools" v-if="preview" :style="{left: items_postion.left + 'px'}">
    <p v-for="s in item" v-if="s.type === 'SECTION'">{{ s.title !== '' ? s.title : '未命名章节' }}</p>
  </div>

  <addimg :open="update_img" @imgurl="getImgurl" @close="update_img = false"></addimg>

  <m-modal :open="share_dialog" title="分享表单" @close="shareDialogClose">
    <div class="serch-user" style="height: 150px;">
      <typeahead 
        placeholder="搜索用户"
        v-model="search_user"
        @change="searchUser"
        @choose="chooseUser"
        :items="search_item"></typeahead>
      <div class="choose-user">
        <h5>要分享的用户：</h5>
        <span class="gender" v-if="choose_user.gender === '1'">男</span>
        <span class="gender" v-if="choose_user.gender === '2'">女</span>
        <span>{{ choose_user.name }}</span>
      </div>
    </div>

    <div slot="footer">
      <m-button type="gray" @click="shareDialogClose">取消</m-button>
      <m-button type="blue" @click="shareForm">确定</m-button>
    </div>
  </m-modal>

  <m-modal :open="public_dialog" title="发布表单" @close="public_dialog = false">
    <div class="public_dialog" style="height: 100px;line-height: 3;">
      <p>将此表单发布到：临床研究管理</p>
      <p>您可以在临床研究管理应用中创建和管理项目时使用此表单为项目收集数据</p>
    </div>

    <div slot="footer">
      <m-button type="gray" @click="public_dialog = false">取消</m-button>
      <m-button type="blue" @click="publishForm">确认发布</m-button>
    </div>
  </m-modal>
</div>
</template>

<script>
import API from '../api.js'
import MTable from './forms/Table.vue'
import LinearScale from './forms/LinearScale.vue'
import MTime from './forms/Time.vue'
import MDate from './forms/Date.vue'
import ShortText from './forms/ShortText.vue'
import LongText from './forms/LongText.vue'
import MAddress from './forms/Address.vue'
import MultipleChoice from './forms/MultipleChoice.vue'
import Checkboxes from './forms/Checkboxes.vue'
import Dropdowns from './forms/Dropdowns.vue'
import FileUpload from './forms/FileUpload.vue'
import { getRandomString, throttle } from '../tool/tools.js'
import EventListener from '../tool/EventListener.js'
import Loading from '../assets/Loading.vue'
// preview
import Preview from '../../parser/index'
export default {
  components: {
    Loading,
    MTime,
    MDate,
    MTable,
    LinearScale,
    ShortText,
    LongText,
    FileUpload,
    MAddress,
    MultipleChoice,
    Checkboxes,
    Dropdowns,
    Preview
  },
  data () {
    return {
      formid: '',
      name: '',
      ispublic: '',
      preview: false,
      nowState: '',
      item: [
        {type: 'FORM', title: '', describe: ''},
        {type: 'SECTION', title: '', describe: {show: false, text: ''}},
        {type: 'RADIO', title: '', describe: {show: false, text: ''}, id: this.traverseid(), keep: false, link: {show: false, text: ''}, img: {url: '', describe: '', show: false}, isjump: false, answers: [{text: '', img: '', next: ''}]}
      ],
      temp_name: '',
      temp_item: '',
      items_postion: {top: '', left: '', width: '', height: ''},
      update_img: false,
      share_dialog: false,
      public_dialog: false,
      search_user: '',
      search_item: [],
      choose_user: {name: '', id: '', gender: ''},
      editState: '',
      ajaxState: false,
      noticeState: false
    }
  },
  computed: {
    // 问题列表
    qalist: function () {
      let qa = []
      for (let sid in this.item) {
        if (this.item[sid].type === 'FORM') continue
        if (this.item[sid].type === 'SECTION') continue
        qa.push({title: this.item[sid].title, id: this.item[sid].id})
      }
      return qa
    }
  },
  mounted () {
    this.resizehandler()
    let vm = this
    if (this.$route.name !== 'neweditor') {
      this.formid = this.$route.params.formid
      this.getForm()
    } else {
      this.ispublic = '1'
      this.noticeState = true
      const formid = window.localStorage.getItem('new_form_id')
      if (formid) this.$router.push({name: 'editor', params: {formid: formid}})

      this.temp_item = JSON.stringify(this.item)

      this.editState = setInterval(function () {
        vm.saveForm()
      }, 5000)
    }

    // 监听窗口变化，重绘侧边栏位置
    this._closeEvent = EventListener.listen(window, 'resize', throttle(this.resizehandler, 200))
  },
  destroyed () {
    // 退出前仔保存一次
    this.saveForm()
    // 清除定时保存
    clearInterval(this.editState)
    // 清除监听
    if (this._closeEvent) this._closeEvent.remove()
  },
  methods: {
    // 获取侧边栏位置
    resizehandler () {
      if (!document.getElementById('editor-items')) return
      this.items_postion.left = document.getElementById('editor-items').offsetLeft + 805
    },
    // 获取Form内容，设置定时保存
    getForm () {
      API.GetFormInfo({userid: this.$root.userid, formid: this.formid}).then(forminfo => {
        this.name = forminfo.name
        this.ispublic = forminfo.state
        let formdata = JSON.parse(forminfo.sourcedata)
        if (formdata[0].type !== 'FORM') formdata.unshift({type: 'FORM', title: '', describe: ''})
        this.item = formdata
        // 未发布才自动保存，不然不可修改
        if (this.ispublic === '1') {
          this.temp_item = JSON.stringify(this.item)
          this.temp_name = forminfo.name

          this.editState = setInterval(() => {
            this.saveForm()
          }, 5000)
        }
      }).catch(err => console.error(err))
    },
    // 保存表单
    saveForm () {
      if (this.formid !== '') {
        if (this.ispublic === '2') return
        if (this.temp_item === JSON.stringify(this.item) && this.temp_name === this.name) return
        this.ajaxState = true
        let name = this.name
        if (name === '' || name === '未命名表单') name = this.item[0].title
        if (name === '' && this.item[0].title === '') name = '未命名表单'
        API.UpdateForm({ update_userid: this.$root.userid, formid: this.formid, name: name, sourcedata: JSON.stringify(this.item) }).then(rep => {
          this.temp_item = JSON.stringify(this.item)
          this.temp_name = this.name
          this.ajaxState = false
        }).catch(err => {
          console.log(err)
        })
      } else {
        let name = this.name
        if (name === '') name = this.item[0].title
        if (name === '' && this.item[0].title === '') name = '未命名表单'
        if (this.temp_item === JSON.stringify(this.item)) return
        API.createForm({userid: this.$root.userid, name: name, sourcedata: JSON.stringify(this.item)}).then(rep => {
          this.toast({ text: '保存成功' })
          this.formid = rep.formid
          window.localStorage.setItem('new_form_id', rep.formid)
          this.temp_item = JSON.stringify(this.item)
          this.temp_name = this.name
        }).catch(err => console.log(err))
      }
    },
    publishForm () {
      if (this.ispublic === '2') return
      API.publishForm({userid: this.$root.userid, formid: this.formid, state: '2'}).then(rep => {
        this.ispublic = '2'
        this.toast({ text: '发布成功' })
        this.$router.push({path: '/'})
      }).catch(err => {
        this.public_dialog = false
        if (err === '表id不能为空') {
          this.toast({ text: '请先完善表单' })
        } else {
          this.toast({ text: err })
        }
      })
    },
    // 添加问题
    addQuestion () {
      if (this.nowState !== '' && this.nowState < this.item.length) {
        this.item.splice(parseInt(this.nowState) + 1, 0,
          {type: 'RADIO', title: '', describe: {show: false, text: ''}, id: this.traverseid(), keep: false, link: {show: false, text: ''}, img: {url: '', describe: '', show: false}, isjump: false, answers: [{text: '', img: '', next: ''}]})
        // 重新设置状态
        this.nowState = parseInt(this.nowState) + 1
      } else {
        this.item.push({type: 'RADIO', title: '', describe: {show: false, text: ''}, id: this.traverseid(), keep: false, link: {show: false, text: ''}, img: {url: '', describe: '', show: false}, isjump: false, answers: [{text: '', img: '', next: ''}]})
      }
    },
    copeQuestion (id) {
      // 解决重复ID问题
      let copedata = JSON.parse(JSON.stringify(this.item[id]))
      copedata.id = this.traverseid()
      this.item.splice(id, 0, copedata)
    },
    // 移动问题, 上移，下移
    moveBefore (id) {
      if (id < 2) return
      let data = this.item[id]
      this.item.splice(id, 1)
      this.item.splice(id - 1, 0, data)
      this.nowState = id - 1
    },
    moveNext (id) {
      if (id > (this.item.length - 2)) return
      let data = this.item[id]
      this.item.splice(id, 1)
      this.item.splice(id + 1, 0, data)
      this.nowState = id + 1
    },
    // 生成一个不重复的 id
    traverseid () {
      let id = getRandomString()
      for (let i in this.item) {
        if (this.item[i].id === id) {
          id = this.traverseid()
        }
      }
      return id
    },
    // 添加章节
    addSection () {
      if (this.nowState !== '' && this.nowState < this.item.length) {
        this.item.splice(parseInt(this.nowState) + 1, 0, {type: 'SECTION', title: '', describe: {show: false, text: ''}})
        // 重新设置状态
        this.nowState = parseInt(this.nowState) + 1
      } else {
        this.item.push({type: 'SECTION', title: '', describe: {show: false, text: ''}})
      }
    },
    // 添加问题的备注，图片，网址
    addQuestionOther (type) {
      if (this.item[this.nowState].type === 'SECTION' || this.nowState === '') return
      if (type === 'img') {
        this.item[this.nowState].img.show = true
        this.update_img = true
      }
      if (type === 'describe') this.item[this.nowState].describe.show = true
      if (type === 'website') this.item[this.nowState].link.show = true
    },
    // 问题图片上传
    getImgurl (img) {
      if (img.length < 10) return
      this.item[this.nowState].img.url = img
      this.update_img = false
    },
    cleanQuesionImg (id) {
      this.item[id].img.url = ''
      this.item[id].img.show = false
      this.item[id].img.describe = ''
    },
    // 删除问题
    delQuestion (questionid) {
      this.item.splice(questionid, 1)
    },
    // 切换问题类型
    typeChange (type, questionid) {
      if (type === 'SHORTTEXT') this.item[questionid].answers = {unit: ''}
      if (type === 'LONGTEXT') this.item[questionid].answers = ''
      if (type === 'FILEUPLOAD') this.item[questionid].answers = ''
      if (type === 'ADDRESS') this.item[questionid].answers = {province: true, city: true}
      if (type === 'RADIO') this.item[questionid].answers = [{text: '', img: '', next: '', other: false}]
      if (type === 'CHECKBOX') this.item[questionid].answers = [{text: '', other: false}]
      if (type === 'DROPDOWN') this.item[questionid].answers = [{text: '', next: ''}]
      if (type === 'TABLE') this.item[questionid].answers = {row: [''], column: ['']}
      if (type === 'LINEARSCALE') this.item[questionid].answers = {left: 0, right: 5, leftText: '', rightText: ''}
      if (type === 'TIME') this.item[questionid].answers = {minute: true, second: true}
      if (type === 'DATE') this.item[questionid].answers = {year: true, month: true, day: true}
    },
    // 分享表单
    shareForm () {
      if (this.choose_user.id === '') {
        this.toast({text: '请先选择要分享的用户'})
        return
      }

      API.shareForm({share_userid: this.$root.userid, userid: this.choose_user.id, formid: this.formid}).then(userlist => {
        this.toast({text: '分享成功'})
        this.shareDialogClose()
      }).catch(err => console.log(err))
    },
    shareDialogClose () {
      this.share_dialog = false
      this.search_user = ''
      this.search_item = []
      this.choose_user.name = ''
      this.choose_user.id = ''
      this.choose_user.gender = ''
    },
    searchUser () {
      API.searchUser({userid: this.$root.userid, nameOrTel: this.search_user}).then(userlist => {
        this.search_item = userlist.splice(0, 4)
      }).catch(err => console.log(err))
    },
    chooseUser (userinfo) {
      this.choose_user.name = userinfo.s_username
      this.choose_user.id = userinfo.s_userid
      this.choose_user.gender = userinfo.s_sex
    }
  }
}
</script>

<style scoped>
.editor {
  position: relative;
  margin-bottom: 100px;
}

.form-title {
  background: #468df1;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100%;
  color: #fff;
}

.form-title .content {
  margin: 0 auto;
  justify-content: space-between;
  align-content: center;
}

.form-title a {
  display: inline-block;
  height: 62px;
  line-height: 62px;
  padding: 0 12px;
  font-size: 16px;
  color: #fff;
  font-weight: 400;
}

.form-title i.icon-fenxiang {
  display: inline-block;
  font-size: 18px;
  color: #fff;
  width: 36px;
  cursor: pointer;
}

.form-title .right .button {
  margin: 0 8px;
  border: none;
}

.editor-preview {
  background: #468df1;
  text-align: center;
  height: 42px;
  line-height: 42px;
  position: fixed;
  top: 62px;
  left: 0;
  z-index: 100;
  width: 100%;
  border-bottom: 2px solid rgba(0,0,0,0);
}

.editor-preview>.content {
  width: 770px;
  margin: 0 auto;
  text-align: center;
  background: #fff;
  border-bottom: 1px solid rgba(0,0,0,0.12);
}

.editor-preview span {
  display: inline-block;
  width: 120px;
  font-size: 18px;
  cursor: pointer;
}

.editor-preview span.active {
  border-bottom: 2px solid #458df1;
}

.editor-form, .preview-form {
  width: 770px;
  margin: 106px auto 0;
  position: relative;
}

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
  border-bottom: 1px solid #eee;
}

.questions {
  position: relative;
  background: #fff;
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #ddd;
}

.q.flex-row {
  min-height: 48px;
}

.q .select {
  margin-left: 30px;
  background: #f5f5f5;
  border-radius: 2px;
  padding-right: 10px;
}

.questions.active, .section.active {
  padding-bottom: 16px;
  border-left: 3px solid #458df1;
}

.qa-tool {
  margin-top: 12px;
  flex-direction: row-reverse;
}

.qa-tool .split-line {
  color: #999;
  padding: 0 8px 0 20px;
}

.qa-tool .dec {
  display: inline-block;
  width: 60px;
}

.qa-tool i.iconfont {
  font-size: 18px;
  color: #999;
  display: inline-block;
  width: 48px;
  text-align: center;
  cursor: pointer;
}

.move-tool {
  position: absolute;
  right: -28px;
  top: 40%;
  display: flex;
  flex-direction: column;
  background: #fff;
}

.move-tool i {
  font-size: 20px;
  padding: 4px;
  box-shadow: 1px 1px 2px rgba(0, 0, 0, .12);
  cursor: pointer;
}
.move-tool i.icon-top {
  border-bottom: 1px solid #eee;
}

/*dialog 分享弹出框*/
.choose-user {
  margin-top: 20px;
  line-height: 2;
}

.choose-user h5 {
  line-height: 3
}

.choose-user .gender {
  display: inline-block;
  text-align: center;
  border-radius: 50%;
  background: #458df1;
  color: #fff;
  height: 30px;
  width: 30px;
}

.tools {
  position: fixed;
  width: 192px;
  right: 216px;
  top: 175px;
  display: flex;
  flex-direction: column;
  background: #fff;
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .2), 0 1px 5px 0 rgba(0, 0, 0, .12);
}

.tools p {
  cursor: pointer;
  line-height: 40px;
  padding: 0 10px;
  border-bottom: 1px solid #eee;
  display: flex;
  align-items: center;
}

.tools p i {
  color: #666;
  margin-right: 8px;
  font-size: 18px;
}

/* 提示框 */
.alert-notice {
  background: #fff;
  margin-top: 30px;
  padding: 16px;
  color: rgba(0,0,0,.65);
  line-height: 1;
}
.alert-notice>div {
  justify-content: space-between;
  align-items: center;
  font-size: 22px;
}
.alert-notice>div i.icon-tishi {
  margin-right: 10px;
  font-size: 24px;
  color: #108ee9;
}
.alert-notice>div i.icon-delect {
  cursor: pointer;
}
.alert-notice>p {
  padding-left: 40px;
  padding-top: 16px;
  font-size: 16px;
}

.preview-form {
  width: 770px;
  margin: 106px auto 0;
  position: relative;
}
</style>