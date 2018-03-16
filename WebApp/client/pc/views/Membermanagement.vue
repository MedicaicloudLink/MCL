<template>
  <div id="member-management">
    <!--添加成员-->
    <div class="card flex-col">
      <div class="card-header">
        <span>添加项目成员</span>
        <!--<div class="btn-gray" style="width: 104px; font-size: 14px;" @click="getApplyList">申请列表</div>-->
      </div>
      <div class="card-search flex-row">
        <input class="phone" type="text" v-model="phone" placeholder="请输入手机号搜索用户" @keyup="searchUser"/>
        <div class="search-btn" @click="searchUser"></div>
      </div>
      <!--搜索列表-->
      <div class="table">
        <div class="table-tr table-header" style="padding: 0 32px;" v-if="searchFlag">
          <div class="flex-row flex">
            <span class="w20">姓名</span>
            <span class="w10">性别</span>
            <span class="w40">工作单位</span>
            <span class="w30">所属科室</span>
          </div>
          <div class="w10">操作</div>
        </div>
        <div class="empty-tr" v-if="searchFlag && JSON.stringify(searchUserResult) == '{}'"></div>
        <div class="table-tr" style="padding: 0 32px;" v-if="JSON.stringify(searchUserResult) != '{}'">
          <div class="flex flex-row" v-if="searchUserResult.type == '5' || searchUserResult.type == '1'">
            <span class="w20">{{ searchUserResult.s_username }}</span>
            <span class="w10">{{ searchUserResult.s_sex === '1' ? '男' : searchUserResult.s_sex === '2' ? '女' : ''}}</span>
            <span class="w40">{{ searchUserResult.s_workunti }}</span>
            <span class="w30">{{ searchUserResult.s_department }}</span>
          </div>
          <div class="flex" style="text-align: center;" v-else>
            <span v-if="searchUserResult.type == '4'">{{phone}} 还未注册梅地卡尔临床数据云，是否邀请注册？</span>
            <span class="red" v-if="searchUserResult.type == '2'">只有成为您的联系人，才能邀请参加项目</span>
          </div>
          <div class="w10">
            <span v-if="searchUserResult.type == '5'" class="color54">已添加至项目</span>
            <span v-if="searchUserResult.type == '3'" class="color54">是本人</span>
            <!--邀请注册-->
            <span v-if="searchUserResult.type == '4'">
              <i class="blue mg-r-20" @click="addUser">是</i>
              <i class="red mg-r-20" @click="phone = ''">否</i>
            </span>
            <!--邀请加入项目-->
            <span v-if="searchUserResult.type == '2'">
              <i class="blue mg-r-20" @click="addContact(searchUserResult.s_userid)">添加为联系人</i>
              <i class="red mg-r-20" @click="phone = ''">否</i>
            </span>
            <span v-if="searchUserResult.type == '1'" class="blue" @click="addProject(searchUserResult.s_userid)">邀请加入项目</span>
          </div>
        </div>
      </div>
      <!--end-->

      <!--申请列表对话框-->
      <!--<DialogBox v-if="applyListDialog" :width='640'>
        <h3 slot="header">申请列表</h3>
        <div slot="body">
          <div class="table">
            <div class="table-tr table-header" style="padding: 0 16px;">
              <div class="w120">姓名</div>
              <div class="w60">性别</div>
              <div class="flex">工作单位</div>
              <div class="w140">所属科室</div>
              <div class="w160">操作</div>
            </div>
            <div class="empty-tr" v-if="applyList.length == 0"></div>
            <div class="table-tr" style="padding: 0 16px;" v-for="item in applyList">
              <div class="w120">{{ item.s_username }}</div>
              <div class="w60">{{ item.s_sex === '1' ? '男' : item.s_sex === '2' ? '女' : ''}}</div>
              <div class="flex">{{ item.s_workunti }}</div>
              <div class="w140">{{ item.s_workunti }}</div>
              <div class="w160 flex-row">
                <span class="blue mg-r-20" @click="handleApply(item.id, '1')">添加至项目</span>
                <span class="red" @click="handleApply(item.id, '2')">拒绝</span>
              </div>
            </div>
          </div>
        </div>
        <div slot="footer">
          <div class="btn-gray" @click="applyListDialog = false">取消</div>
        </div>
      </DialogBox>-->
      <!--end-->

    </div>
    <!--成员列表-->
    <div class="card" >
      <div class="card-header">成员列表</div>
      <div class="table flex-col">
        <div class="table-tr table-header">
          <div class="w10">序号</div>
          <div class="w15">姓名</div>
          <div class="w20">所属分中心</div>
          <div class="flex">任务</div>
          <div class="w20">操作</div>
        </div>
        <div class="table-body" :style="{height: tableHeight + 'px'}">
          <div style="padding-bottom: 10px" v-if="loading"><Loading></Loading></div>
          <div class="table-tr" v-for="(member, index) in memberList">
            <div class="w10">{{ index + 1 }}</div>
            <div class="w15">{{ member.s_username }}</div>
            <div class="w20">
              <span v-text="member.centername"></span>
            </div>
            <div class="flex">{{ member.task }}</div>
            <div class="w20 flex-row">
              <span class="blue mg-r-20" @click="addToCenterDialog = true, currentId = member.u_userID">分配分中心</span>
              <span class="blue mg-r-20" @click="addTaskDialog = true, currentId = member.u_userID, checkedTaskId = member.taskid">分配任务</span>
              <span class="red" @click="deleteMember(member.u_userID, index)">删除</span>
            </div>
          </div>
        </div>
        <DialogBox v-if="addToCenterDialog" :width='360'>
          <h3 slot="header">成员分配至分中心</h3>
          <div slot="body" class="option">
            <div v-for="option in centerList" style="height: 32px;">
              <input type="radio" :id="option.u_centerID" v-model="checkedCenterId" :value="option.u_centerID"/>
              <label :for="option.u_centerID">{{ option.u_centername }}</label>
            </div>
          </div>
          <div slot="footer">
            <div class="btn-blue" @click="addMemberToCenter">确定</div>
            <div class="btn-gray" @click="addToCenterDialog = false, checkedCenterId = ''">取消</div>
          </div>
        </DialogBox>
        <DialogBox v-if="addTaskDialog" :width='360'>
          <h3 slot="header">给项目成员分配任务</h3>
          <div slot="body" class="option">
            <div v-for="option in taskList">
              <input type="checkbox" :id="option.taskid" v-model="checkedTaskId" :value="option.taskid"/>
              <label :for="option.taskid">{{ option.taskname }}</label>
            </div>
          </div>
          <div slot="footer">
            <div class="btn-blue" @click="addTask">确定</div>
            <div class="btn-gray" @click="addTaskDialog = false, checkedTaskId = []">取消</div>
          </div>
        </DialogBox>
      </div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import DialogBox from '../lib/dialog.vue'
  import Loading from '../lib/loading.vue'
  export default {
    data () {
      return {
        loading: false,
        memberList: [],
        centerList: [],
        taskList: [],
        applyList: [],
        currentId: '',
        currentTask: '',
        date: new Date().getFullYear(),
        applyListDialog: false,
        phone: '',
        searchFlag: false,
        searchUserResult: {},
        checkedCenterId: '',
        checkedTaskId: [],
        addToCenterDialog: false,
        addTaskDialog: false,
        tableHeight: 0
      }
    },
    components: {
      DialogBox,
      Loading
    },
    mounted () {
      this.getMemberList()
      this.getCenterList()
      this.getTaskList()
      const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
      this.tableHeight = window.innerHeight - tableTop - 30
    },
    watch: {
      phone (val) {
        if (this.phone.length < 11) this.searchUserResult = {}
      }
    },
    methods: {
      // 获取项目参与成员列表
      getMemberList () {
        this.loading = true
        API.GetMember({userid: window.sessionStorage.getItem('userid'), projectid: this.$route.params.projectid}).then((response) => {
          this.memberList = response
          this.loading = false
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 获取项目分中心列表
      getCenterList () {
        API.GetProjectCenter({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          this.centerList = response
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 获取项目任务列表
      getTaskList () {
        API.GetTaskList({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          this.taskList = response
        }).catch((err) => {
          window.alert(err)
        })
      },
      // // 获取申请列表
      // getApplyList () {
      //   this.applyListDialog = true
      //   API.GetApplyList({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
      //     this.applyList = response
      //   }).catch((err) => {
      //     window.alert(err)
      //   })
      // },
      // // 处理申请
      // handleApply (id, status) {
      //   API.HandleNotice({userid: this.$root.userid, noticeid: id, status: status}).then(response => {
      //     if (!response) { return }
      //     this.getApplyList()
      //   }).catch(err => {
      //     this.toast({
      //       text: err,
      //       type: 'error',
      //       placement: 'top'
      //     })
      //   })
      // },
      /* 搜索用户信息 */
      searchUser () {
        if (this.phone.length > 10) {
          API.FindUserInfo({userid: this.$root.userid, projectid: this.$route.params.projectid, mobile: this.phone}).then((response) => {
            this.searchUserResult = response
            this.searchFlag = true
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      // 添加联系人
      addContact (touserid) {
        API.AddContact({userid: this.$root.userid, touserid: touserid}).then((response) => {
          this.toast({type: 'success', text: '联系人邀请已发出！'})
          let vm = this
          setTimeout(() => { vm.phone = '' }, 3000)
        }).catch(() => {
          this.toast({type: 'error', text: '发送失败！'})
        })
      },
      /* 邀请加入项目 */
      addProject (touserid) {
        API.AddProject({userid: window.sessionStorage.getItem('userid'), projectid: this.$route.params.projectid, touserid: touserid}).then((response) => {
          this.toast({type: 'success', text: '项目邀请已发出！'})
          let vm = this
          setTimeout(() => { vm.phone = '' }, 3000)
        }).catch(() => {
          this.toast({type: 'error', text: '发送失败！'})
        })
      },
      // 邀约注册用户
      addUser () {
        API.AddUser({userid: window.sessionStorage.getItem('userid'), projectid: this.$route.params.projectid, mobile: this.phone}).then((response) => {
          this.toast({type: 'success', text: '注册邀请已发出！'})
          let vm = this
          setTimeout(() => { vm.phone = '' }, 3000)
        }).catch(() => {
          this.toast({type: 'error', text: '发送失败！'})
        })
      },
      /* 给项目成员分配分中心 */
      addMemberToCenter () {
        if (this.checkedCenterId) {
          this.addToCenterDialog = false
          API.AddMemberToCenter({userid: this.$root.userid, projectid: this.$route.params.projectid, centerid: this.checkedCenterId, memberid: this.currentId}).then((response) => {
            this.toast({
              type: 'success',
              text: '添加项目成员到分中心成功！',
              placement: 'top'
            })
            this.getMemberList()
            this.checkedCenterId = ''
          }).catch((err) => {
            this.checkedCenterId = ''
            let type = err === '用户已被分配到中心' ? 'default' : 'error'
            err = err === '用户已被分配到中心' ? '用户已被分配到中心' : '添加失败，请重新尝试！'
            this.toast({
              type: type,
              text: err,
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'warning',
            text: '请选择分中心',
            placement: 'top'
          })
        }
      },
      /* 给项目成员分配任务 */
      addTask () {
        this.addTaskDialog = false
        API.AddTask({userid: this.$root.userid, projectid: this.$route.params.projectid, taskid: this.checkedTaskId.join(','), memberid: this.currentId}).then((response) => {
          this.toast({
            type: 'success',
            text: '给项目成员分配任务成功',
            placement: 'top'
          })
          this.getMemberList()
          this.checkedTaskId = []
        }).catch(() => {
          this.toast({
            type: 'error',
            text: '给项目成员分配任务失败',
            placement: 'top'
          })
        })
      },
      /* 删除项目成员 */
      deleteMember (memberId, index) {
        let vm = this
        this.confirm({
          title: '删除项目成员',
          message: '您确定要将该成员移除项目吗？',
          onConfirm () {
            API.DeleteProjectMember({userid: vm.$root.userid, projectid: vm.$route.params.projectid, memberid: memberId}).then((response) => {
              vm.memberList.splice(index, 1)
              vm.toast({
                type: 'success',
                text: '删除成功！',
                placement: 'top'
              })
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '删除失败，请再次尝试！',
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
  .option{
    padding-left: 32px;
    line-height: 32px;
  }
  .card-search {
    padding: 24px 32px;
    width: 448px;
    position: relative;
  }
  .card-search input{
    width: 448px;
  }
  .table-tr{
    padding: 0 32px;
  }
  .table-tr.table-header{
    padding-right: 40px;
  }
  .empty-tr {
    height: 40px;
  }
</style>