<template>
  <div id="home">
    <Navigation></Navigation>
    <div class="main">
      <div class="btn-blue" style="width: 88px; margin: 32px 0" @click="createProjectDialog = true">新建项目</div>
      <!--新建项目对话框-->
      <DialogBox v-if="createProjectDialog">
        <div slot="header">新建项目</div>
        <div slot="body" class="body">
          <div class="option flex-row">
            <label class="label">项目名称<span style="color: red">*</span></label>
            <input type="text" v-model="projectName"/>
          </div>
          <div class="option flex-row">
            <label class="label">开始时间<span style="color: red">*</span></label>
            <datepicker class="date" v-model="startTime" :width="150" :placeholder="'请输入研究开始时间'"></datepicker>
            <label class="label" style="width: 74px;margin-right: 10px;">结束时间<span style="color: red">*</span></label>
            <datepicker class="date" v-model="endTime" :width="150" :placeholder="'请输入研究结束时间'"></datepicker>            
          </div>
          <div class="option flex-row" style="align-items: flex-start;">
            <label class="label">项目简介<span style="color: red">*</span></label>
            <textarea v-model="projectMem"></textarea>
          </div>
          <div class="option flex-row" style="margin-right: 72px;">
            <label class="label">患者登记表<span style="color: red">*</span></label>
            <select v-model="templateID" :style="{color: selectColorFlag ? 'rgba(0,0,0,.87)' : 'rgba(0,0,0,.26)'}">
              <option value='' style="display:none;">请选择首次记录患者信息的表单</option>  
              <option v-for="form in formList" :value="form.formid" style="color: rgba(0,0,0,.87)">{{form.formname}}</option>
            </select>
          </div>
          <div class="option flex-row">
            <label class="label">创建人</label>
            <input type="text" style="border: none; padding: 0;" :value="$root.username" readonly/>
          </div>
          <div class="option flex-row" style="align-items: center;">
            <label class="label">是否公开项目<span style="color: red">*</span></label>
            <div>
              <input type="radio" id="yes" name="public" v-model="public" value="1"/>
              <label for="yes" style="width: 40px;margin-right: 32px;">是</label>
            </div>
            <div>
              <input type="radio" id="no" name="public" v-model="public" value="0"/> 
              <label for="no" style="width: 40px;">否</label>
            </div>
          </div>
        </div>
        <div slot="footer" class="flex-row">
          <span class="btn-gray mg-r-8" @click="createProjectDialog = false">取消</span>
          <span class="btn-blue" @click="createProject">保存</span>
        </div>
      </DialogBox>
      <!--end-->
      <!--用户参加的项目卡片列表-->
      <div class="flex-row" style="flex-wrap: wrap">
        <div v-if="noproject" style="width: 100%; margin-top: 68px;text-align: center;"><img src="../assets/icon_svg/icon_empty_page.svg"/></div>
        <div class="project-card" v-for="(project, index) in projectList">
          <router-link :to="'/project/' + project.u_projectID + '/project_home'">
            <!--:class="{project1: project.u_projectID === '1', project2: project.u_projectID === '2'}"-->
            <div class="icon flex-row">
              <i class="iconfont" :class="project.u_projectID === '1' ? 'icon-yiliao' : 'icon-xindiantu'"></i>
            </div>
            <div class="project-text">
              <div class="project-name color87" v-text="project.u_projectName"></div>
              <p class="color54" v-text="project.u_projectMem" style="overflow : hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"></p>
            </div>
          </router-link>
          <div class="delete-project border-t flex-row">
            <span class="color26" v-text="project.u_projectstate === '1' ? '公开' : project.u_projectstate === '0' ? '不公开' : ''"></span>
            <div class="flex-row">
              <span class="color54 mg-r-8" @click="editProject(project.u_projectID)"><i class="iconfont icon-edit"></i></span>
              <span class="color54" style="cursor: pointer;" @click="deleteProject(project.u_projectID, index)"><i class="iconfont icon-icon_delete"></i></span>
            </div>
          </div>
        </div>
      </div>
      <!--end-->
      <!--编辑项目对话框-->
      <DialogBox v-if="editProjectDialog">
        <div slot="header">编辑项目</div>
        <div slot="body" class="body">
          <div class="option flex-row">
            <label class="label">项目名称<span style="color: red">*</span></label>
            <input type="text" v-model="editProjectInfo.u_projectName"/>
          </div>
          <div class="option flex-row">
            <label class="label">开始时间<span style="color: red">*</span></label>
            <datepicker class="date" v-model="editProjectInfo.starttime" :width="150" :placeholder="'请输入研究开始时间'"></datepicker>
            <label class="label" style="width: 74px;margin-right: 10px;">结束时间<span style="color: red">*</span></label>
            <datepicker class="date" v-model="editProjectInfo.endtime" :width="150" :placeholder="'请输入研究结束时间'"></datepicker>            
          </div>
          <div class="option flex-row" style="align-items: flex-start">
            <label class="label">项目简介<span style="color: red">*</span></label>
            <textarea v-model="editProjectInfo.u_projectMem"></textarea>
          </div>
          <div class="option flex-row">
            <label class="label">患者登记表<span style="color: red">*</span></label>
            <select v-model="editProjectInfo.form" style="background-position: 365px center;" @change="changeCRF">
              <option :value="projectForm" disabled>{{projectForm.formname}}</option>
              <option v-for="form in formList" :value="form">{{form.formname}}</option>
            </select>
          </div>
          <div class="option flex-row">
            <label class="label">创建人</label>
            <input type="text" style="border: none; padding: 0;" :value="$root.username" readonly/>
          </div>
          <div class="option flex-row" style="align-items: center;">
            <label class="label">是否公开项目<span style="color: red">*</span></label>
            <div>
              <input type="radio" id="yes" name="public" v-model="editProjectInfo.u_projectstate" value="1"/>
              <label for="yes" style="width: 40px; margin-right: 32px;">是</label>
            </div>
            <div>
              <input type="radio" id="no" name="public" v-model="editProjectInfo.u_projectstate" value="0"/> 
              <label for="no" style="width: 40px;">否</label>
            </div>
          </div>
        </div>
        <div slot="footer" class="flex-row">
          <span class="btn-blue mg-r-8" @click="sureEditProject">保存</span>
          <span class="btn-gray" @click="editProjectDialog = false">取消</span>
        </div>
      </DialogBox>
      <!--end-->
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import Navigation from '../components/navigation.vue'
  import DialogBox from '../lib/dialog.vue'
  import Datepicker from '../lib/Date.vue'
  export default {
    name: 'Home',
    data () {
      return {
        noproject: false,
        projectList: [],
        formList: [],
        createProjectDialog: false,
        selectColorFlag: false,
        projectName: '',
        startTime: '',
        endTime: '',
        projectMem: '',
        templateID: '',
        public: '',
        editProjectDialog: false,
        projectForm: {
          formname: '',
          formdata: ''
        },
        editProjectInfo: {
          u_projectID: '',
          u_projectName: '',
          form: {
            formname: '',
            formdata: ''
          },
          u_projectMem: '',
          starttime: '',
          endtime: '',
          u_projectstate: ''
        }
      }
    },
    components: {
      Navigation,
      DialogBox,
      Datepicker
    },
    created () {
      this.getProjectList()
      this.getFormList()
    },
    watch: {
      templateID (val) {
        val !== '' ? this.selectColorFlag = true : this.selectColorFlag = false
      }
    },
    computed: {
      required () {
        return this.projectName && this.startTime && this.endTime && this.projectMem && this.templateID && this.public
      },
      requiredEdit () {
        return Object.values(this.editProjectInfo).every(value => value)
      }
    },
    methods: {
      // 获得项目列表
      getProjectList () {
        this.projectList = []
        API.GetMyProjects({userid: this.$root.userid}).then(response => {
          if (!response.length) {
            this.noproject = true
            return
          } else {
            this.noproject = false
          }
          response.map(n => this.projectList.push(n))
        }).catch(err => {
          console.log(err)
        })
      },
      // 获得表单列表
      getFormList () {
        this.formList = []
        API.GetFormList({userid: this.$root.userid, type: 'publish'}).then(response => {
          response.map(n => this.formList.push({
            formid: n.formid,
            formname: n.name,
            formdata: n.sourcedata
          }))
        }).catch(err => {
          console.log(err)
        })
      },
      // 创建项目
      createProject () {
        if (this.required) {
          API.CreateProject({
            userid: this.$root.userid,
            name: this.projectName,
            templateid: this.templateID,
            projectmem: this.projectMem,
            starttime: this.startTime,
            endtime: this.endTime,
            status: this.public
          }).then(response => {
            this.toast({
              type: 'success',
              text: '新建项目成功！',
              placement: 'top'
            })
            this.getProjectList()
            this.createProjectDialog = false
            this.projectName = ''
            this.startTime = ''
            this.endTime = ''
            this.projectMem = ''
            this.templateID = ''
            this.public = ''
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '新建项目失败，重新提交试试！',
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'error',
            text: '保存失败，*为必填项！',
            placement: 'top'
          })
        }
      },
      // 编辑项目
      editProject (projectid) {
        this.editProjectDialog = true
        API.GetProjectInfo({userid: this.$root.userid, projectid: projectid}).then(response => {
          for (let i in this.editProjectInfo) {
            if (i === 'form') {
              this.editProjectInfo.form.formname = this.projectForm.formname = response[0].formname
              this.editProjectInfo.form.formdata = this.projectForm.formdata = response[0].formdata
            } else this.editProjectInfo[i] = response[0][i]
          }
          this.editProjectInfo.starttime = this.editProjectInfo.starttime.substr(0, 10)
          this.editProjectInfo.endtime = this.editProjectInfo.endtime.substr(0, 10)
        }).catch((err) => {
          console.log(err)
        })
      },
      // 是否修改CRF
      changeCRF () {
        let vm = this
        this.confirm({
          title: '修改表单',
          message: '修改表单后，登记端的表单将改变，确定修改？',
          onCancel () {
            vm.editProjectInfo.form = vm.projectForm
          }
        })
      },
      // 确认编辑项目
      sureEditProject () {
        if (this.requiredEdit) {
          this.editProjectDialog = false
          API.EditProject({
            userid: this.$root.userid,
            projectid: this.editProjectInfo.u_projectID,
            name: this.editProjectInfo.u_projectName,
            projectmem: this.editProjectInfo.u_projectMem,
            starttime: this.editProjectInfo.starttime,
            endtime: this.editProjectInfo.endtime,
            formname: this.editProjectInfo.form.formname,
            formdata: this.editProjectInfo.form.formdata,
            status: this.editProjectInfo.u_projectstate
          }).then(response => {
            this.toast({
              type: 'success',
              text: '编辑项目成功！',
              placement: 'top'
            })
            this.getProjectList()
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '编辑项目失败！',
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'error',
            text: '保存失败，*是必填项！',
            placement: 'top'
          })
        }
      },
      // 删除项目
      deleteProject (projectId, index) {
        let vm = this
        this.confirm({
          title: '删除项目',
          message: '确定删除此项目？',
          onConfirm () {
            API.DeleteProject({userid: vm.$root.userid, projectid: projectId}).then(response => {
              vm.projectList.splice(index, 1)
              vm.toast({
                type: 'success',
                text: '删除项目成功！',
                placement: 'top'
              })
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '删除项目失败，重新删除！',
                placement: 'top'
              })
            })
          }
        })
      }
    }
  }
</script>
<style scoped>
  #home .main {
    width: 1016px;
    margin: 56px auto;
  }
  /* 创建新项目对话框 */
  .option{
    margin-bottom: 16px;
    align-items: center;
    display: flex;
    margin-right: 72px;
  }
  .option .label{
    font-size: 14px;
    font-family: -webkit-body;
    margin-right: 24px;
    width: 120px;
    text-align: right;
    color: rgba(0, 0, 0, .54);
  }
  .option input[type=text], .option textarea, .option select{
    width: 384px;
  }
  .option textarea {
    height: 120px;
  }
  /* 项目卡片 */
  .main .project-card {
    width: 238px;
    height: 288px;
    background: #fff;
    border: 1px rgb(0,0,0,.12) solid;
    margin-right: 16px;
    margin-bottom: 16px;
  }
  .project-card .icon{
    height: 164px;
    background-size: cover;
    background-position: center;
    background-color: #41a3fc;
    color: #fff;
    padding: 5px 10px;
    justify-content: center;
    align-items: center;
    font-size: 60px;
  }

  .project-card .project-text {
    height: 90px;
    padding: 12px 16px;
    overflow: hidden;
  }
  .project-card .project-text .project-name {
    font-size: 16px;
    line-height: 21px;
    margin-bottom: 8px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .project-card .project-text p{
    height: 38px;
    line-height: 19px;
    word-break:break-all;  
  }
  .project-card .delete-project {
    height: 32px;
    line-height: 32px;
    padding: 0 16px;
    justify-content: space-between;
  }

</style>
