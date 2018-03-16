<template>
  <div id="followUp">
    <div class="btn-blue" style="width: 96px;margin: 32px 0;" @click="createFollowDialog = true">添加随访</div>
    <!--创建计划对话框-->
    <DialogBox v-if="createFollowDialog">
      <h3 slot="header">创建新随访计划</h3>
      <div slot="body" class="flex-col">
        <div class="option">
          <label>随访名称<span style="color: red">*</span></label>  
          <input type="text" v-model="followName" style="flex: 1;"/>
        </div>
        <div class="option">
          <label>随访登记表<span style="color: red">*</span></label>
          <select class="flex" v-model="followForm" style="flex: 1;" :style="{color: selectColorFlag ? 'rgba(0,0,0,.87)' : 'rgba(0,0,0,.26)'}">
            <option value='' style="display:none;">请选择患者随访表单</option>  
            <option class="color87" v-for="form in formList" :value="form.formid">{{form.formname}}</option>
          </select>
        </div>
        <div class="option">
          <label>随访条件<span style="color: red">*</span></label>
          入组后第
          <input type="text" v-model="followMonth" style="width: 40px;margin: 0 5px;">天
        </div>
        <div class="option" style="align-items: flex-start;">
          <label>随访简介<span style="color: red">*</span></label>
          <textarea v-model="followMem"style="height: 120px;flex: 1;"></textarea>
        </div>
      </div>
      <div slot="footer">
        <div class="btn-gray" @click="createFollowDialog = false">取消</div>        
        <div class="btn-blue" @click="createFollow">确定</div>
      </div>
    </DialogBox>
    <!--end-->
    <div class="follow-list-show">
      <div class="follow-list"  v-for="(item,index) in editFollowList" :key="item.taskid">
        <div v-if="!item.edit" class="flex-row tools">
          <div class="btn-gray" @click="editFollow(index)">编辑</div>
          <div class="btn-gray flex-row" style="width: 32px; padding-top: 4px;margin-left: 8px;" @click="deleteFollow(item.taskid, index)"><img src="../assets/icon_svg/icon_delete.svg"/></div>   
        </div>     
        <div class="flex-col">
          <div class="option">
            <label>随访名称<span v-if="item.edit" style="color: red;">*</span></label>  
            <input class="flex" type="text" v-model="item.taskname" :readonly="!item.edit" :class="{active: !item.edit}"/>
          </div>
          <div class="option">
            <label>创建时间</label>  
            <input class="flex" type="text" v-model="item.createtime" readonly style="border: none;"/>
          </div>
          <div class="option">
            <label>随访登记表<span v-if="item.edit" style="color: red;">*</span></label>
            <select class="flex" v-model="item.form" :disabled="!item.edit" :class="{active: !item.edit}" @change="changeCRF(index)">
              <option :value="followList[index].form" disabled>{{followList[index].form.formname}}</option>
              <option v-for="form in formList" :value="form">{{form.formname}}</option>
            </select>
          </div>
          <div class="option">
            <label style="margin-right: 32px;">随访条件<span v-if="item.edit" style="color: red;">*</span></label>
            入组后第
            <input type="text" v-model="item.taskmonth" style="width: 40px;margin: 0 5px;" :readonly="!item.edit" :class="{active: !item.edit}">天
          </div>
          <div class="option" style="align-items: flex-start">
            <label>随访简介<span v-if="item.edit" style="color: red;">*</span></label>
            <textarea class="flex" v-model="item.taskcontent" :readonly="!item.edit" :class="{active: !item.edit}" :style="{'padding': item.edit ? '8px' : '1px 8px'}"></textarea>
          </div>
        </div>
        <div v-if="item.edit" style="text-align: right;margin-top: 16px;">
          <div class="btn-gray" @click="cancelEdit(index)">取消</div>        
          <div class="btn-blue" @click="sureEditFollow(item, index)">保存</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import DialogBox from '../lib/dialog.vue'
  import API from '../api.js'
  export default {
    data () {
      return {
        projectid: this.$route.params.id,
        createFollowDialog: false,
        selectColorFlag: false,
        followName: '',
        followForm: '',
        followMonth: '',
        followMem: '',
        followList: [],
        editFollowList: [],
        currentIndex: 0,
        formList: []
      }
    },
    components: {
      DialogBox
    },
    created () {
      this.getFollowList()
      this.getFormList()
    },
    watch: {
      followForm (val) {
        val !== '' ? this.selectColorFlag = true : this.selectColorFlag = false
      }
    },
    computed: {
      required () {
        return this.followName && this.followForm && this.followMonth && this.followMem
      },
      requiredEdit () {
        return Object.values(this.editFollowList[this.currentIndex]).every(value => value)
      }
    },
    methods: {
      // 获取项目下的随访计划
      getFollowList () {
        this.followList = []
        this.editFollowList = []
        API.GetFollows({userid: window.sessionStorage.getItem('userid'), projectid: this.$route.params.projectid}).then((response) => {
          response.map(n => this.followList.push({
            taskid: n.taskid,
            taskname: n.taskname,
            createtime: n.createtime.substr(0, 10),
            form: {
              formname: n.formname,
              formdata: n.formdata
            },
            taskmonth: n.taskmonth,
            taskcontent: n.taskcontent,
            edit: false
          }))
          response.map(n => this.editFollowList.push({
            taskid: n.taskid,
            taskname: n.taskname,
            createtime: n.createtime.substr(0, 10),
            form: {
              formname: n.formname,
              formdata: n.formdata
            },
            taskmonth: n.taskmonth,
            taskcontent: n.taskcontent,
            edit: false
          }))
        }).catch((err) => {
          console.log(err)
        })
      },
      // 创建新随访计划
      createFollow () {
        if (this.required) {
          API.CreateFollow({
            userid: window.sessionStorage.getItem('userid'),
            projectid: this.$route.params.projectid,
            taskname: this.followName,
            taskformid: this.followForm,
            taskmonth: this.followMonth,
            taskcontent: this.followMem
          }).then((response) => {
            this.createFollowDialog = false
            this.getFollowList()
            this.toast({
              type: 'success',
              text: '创建新随访计划成功！',
              placement: 'top'
            })
            this.followName = ''
            this.followForm = ''
            this.followMonth = ''
            this.followMem = ''
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '网络异常，创建新随访计划失败！',
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'error',
            text: '创建失败，*为必填项！',
            placement: 'top'
          })
        }
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
      // 编辑随访
      editFollow (index) {
        this.currentIndex = index
        this.editFollowList = []
        this.followList.map(n => this.editFollowList.push({
          taskid: n.taskid,
          taskname: n.taskname,
          createtime: n.createtime.substr(0, 10),
          form: n.form,
          taskmonth: n.taskmonth,
          taskcontent: n.taskcontent,
          edit: false
        }))
        this.editFollowList[index].edit = true
      },
      // 是否修改CRF
      changeCRF (index) {
        if (!this.editFollowList[index].edit) return
        let vm = this
        this.confirm({
          title: '修改表单',
          message: '修改表单后，该随访的表单将改变，确定修改？',
          onCancel () {
            vm.editFollowList[index].form = vm.followList[index].form
          }
        })
      },
      // 确认编辑随访计划
      sureEditFollow (followinfo, index) {
        if (!this.requiredEdit) {
          this.toast({
            type: 'error',
            text: '保存成功，*为必填项！',
            placement: 'top'
          })
          return
        }
        API.EditFollow({
          userid: this.$root.userid,
          projectid: this.$route.params.projectid,
          taskid: followinfo.taskid,
          taskname: followinfo.taskname,
          formname: followinfo.form.formname,
          formdata: followinfo.form.formdata,
          taskmonth: followinfo.taskmonth,
          taskcontent: followinfo.taskcontent
        }).then((response) => {
          followinfo.edit = false
          for (let i in this.followList[index]) {
            this.followList[index][i] = this.editFollowList[index][i]
          }
          this.toast({
            type: 'success',
            text: '编辑随访成功！',
            placement: 'top'
          })
        }).catch(() => {
          this.toast({
            type: 'error',
            text: '网络异常，编辑随访失败！',
            placement: 'top'
          })
        })
      },
      // 取消编辑
      cancelEdit (index) {
        for (let i in this.editFollowList[index]) {
          this.editFollowList[index][i] = this.followList[index][i]
        }
      },
      // 删除随访计划
      deleteFollow (taskId, index) {
        let vm = this
        this.confirm({
          title: '删除任务',
          message: '您确定要删除此随访任务吗？',
          onConfirm () {
            API.DeleteFollow({userid: vm.$root.userid, projectid: vm.$route.params.projectid, taskid: taskId}).then((response) => {
              vm.toast({
                type: 'success',
                text: '删除成功！',
                placement: 'top'
              })
              vm.editFollowList.splice(index, 1)
              vm.followList.splice(index, 1)
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '网络异常，删除失败！',
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
  /* 创建计划对话框 */
  .option{
    display: flex;
    display: -webkit-flex;
    align-items: center;
    margin-bottom: 16px;
    margin-right: 72px;
  }
  .option label{
    width: 120px;
    margin-right: 32px;
    text-align: right;
    color: rgba(0,0,0,.54);
  }
  .option .active{
    border-color: #fff;
  }
  .option select.active{
    background-image: none;
  }
  /* 计划列表显示 */
  .follow-list{
    padding: 16px 0;
    padding-right: 32px;
    background: #fff;
    margin-bottom: 32px;
    position: relative;
  }
  .tools{
    position: absolute;
    right:32px;
  }
</style>