<template>
  <div id="all-patients">
    <div class="tools-top flex-row">
      <div class="search border-r flex-row">
        <input type="text" v-model="patientName" placeholder="搜索患者姓名" @keyup.enter="searchPatientName"/>
        <div class="search-btn" @click.stop="searchPatientName"></div>
      </div>
      <div class="search flex-row">
        <span style="color: rgba(0,0,0,.54); width: 68px;">入组时间</span>
        <datepicker v-model="startJoinTime" :width="170"></datepicker>
        <span style="color: rgba(0,0,0,.54); width: 32px; text-align: center;">至</span>
        <datepicker v-model="endJoinTime" :width="170"></datepicker>
        <div class="search-btn" @click.stop="searchPatientJointime" style="border: 1px solid rgba(0, 0, 0, .12); border-left: none;left: 472px;"></div>                        
      </div>
    </div>
    <div class="tools-middle flex-row">
      <div class="flex-row">
        <select v-model="currentGroup" class="mg-r-8" style="width: 160px; height: 32px;">
          <option value="0">全部</option>
          <option value="no">未分组</option>
          <option v-for="group in groupList" :key="group.u_groupid" :value="group.u_groupid">{{ group.u_groupname }}</option>
        </select>
        <div class="btn-gray mg-r-8" :class="{disabled: disabled}" @click="deletePatients()">删除</div>
        <div class="btn-gray mg-r-8" :class="{disabled: disabled}" @click="shareGroupDialog = !disabled">分组</div>
        <div class="btn-gray mg-r-8" :class="{disabled: disabled}" style="width: 96px;" @click="cancelGroupDialog = !disabled">取消分组</div>  
        <div class="btn-gray mg-r-8" :class="{disabled: disabled}" style="width: 96px;" @click="exportPatient">导出数据</div>
        <div class="btn-gray  mg-r-8" :class="{disabled: disabled}" style="width: 96px;" @click="checkPass">通过审核</div>
        <div class="btn-gray" :class="{disabled: !disabled}"style="width: 120px;" @click="exportAllPatient">导出全部数据</div>
      </div>
      <!--将选中的患者添加到组-->
      <DialogBox v-if="shareGroupDialog" :width="352">
        <h3 slot="header">分组</h3>
        <div slot="body" class="flex-col" style="padding: 24px 32px 0">
          <!--搜索组-->
          <div class="flex-row" style="margin-bottom: 16px; position: relative;">
            <input type="text" placeholder="请输入组名搜索" v-model="groupName" style="flex: 1;" @keyup.enter="searchGroup"/>
            <div class="search-btn" style="right: 0;" @click.stop="searchGroup"></div>
          </div>
          <!--组列表-->
          <div v-for="(group, index) in editGroupList" :key="index" class="flex-row"  style="align-items: center; justify-content: space-between;">
            <div class="flex-row" style="align-items: center;">
              <div v-if="!manageGroupFalg">
                <input type="checkbox" :id="group.u_groupid" :value="group.u_groupid" v-model="checkGroup"/>
                <label :for="group.u_groupid"></label>
              </div>
              <div class="flex-col">
                <input :value="group.u_groupname" :readonly="!group.edit" v-model="group.u_groupname" :style="{'border-color': group.edit ? '#ccc' : '#fff'}">
                <div v-if="group.edit" style="margin-top: 5px">
                  <div class="btn-blue" @click="sureEditGroup(group.u_groupid, group.u_groupname)">确定</div>                        
                  <div class="btn-gray" @click="cancelEditGroup(index)">取消</div>                        
                </div>
              </div>
            </div>
            <div v-if="!group.edit && manageGroupFalg" class="flex-row">
              <span style="color: #458df1; cursor: pointer; margin-right: 20px;" @click="editGroup(index)">修改</span>                        
              <span style="color: #e51c23; cursor: pointer;" @click="deleteGroup(group.u_groupid)">删除</span>                        
            </div>
          </div>
          <!--新建分组-->
          <div style="margin-top: 20px;">
            <div class="btn-gray" style="width: 96px;" :class="{disabled: manageGroupFalg}" @click="newGroupFlag = !newGroupFlag">{{newGroupFlag ? '取消新建' : '新建分组'}}</div>
            <div class="btn-gray" style="width: 96px;" @click="manageGroupFalg = !manageGroupFalg">{{manageGroupFalg ? '完成' : '管理分组'}}</div> 
            <div class="flex-row" v-if="newGroupFlag" style="margin-top: 10px;">
              <input type="text" placeholder="输入新建组名" style="flex: 1;" v-model="newGroupName" @keyup.enter="createGroup"/>
              <label style="background: #468df1; color: #fff; width: 48px; line-height: 38px;text-align: center;" @click="createGroup">新建</label>
            </div>                                            
          </div>
        </div>
        <div slot="footer" v-if="!newGroupFlag && !manageGroupFalg">
          <div class="btn-blue" @click="addPatientGroup">确定</div>
          <div class="btn-gray" @click="shareGroupDialog = false">取消</div>
        </div>
      </DialogBox>
      <!--取消分组对话框-->
      <DialogBox v-if="cancelGroupDialog">
        <h3 slot="header">取消分组</h3>
        <div slot="body" style="padding-left: 32px;">
            您确定要将选定的患者取消分组吗？
        </div>
        <div slot="footer" style="text-align: right;">
          <div class="btn-gray" @click="deletePatientGroup">确定</div>
          <div class="btn-blue" @click="cancelGroupDialog = false">取消</div>
        </div>
      </DialogBox>
    </div>
    <!--患者列表-->
    <div class="table">
      <div class="table-tr table-header">
        <div class="w2">
          <input type="checkbox" id="all" @click="allChecked"/>
          <label for="all"></label>
        </div>
        <div class="w8">姓名</div>
        <div class="w5">性别</div>
        <div class="w6">年龄</div>
        <div class="w10">入组时间</div>
        <div class="w10">更新时间</div>
        <div class="w17">状态</div>
        <div class="w8">录入人</div>
        <div class="w15">所属分中心</div>
        <div class="flex">组</div>
      </div>
      <div class="table-body" :style="{height: tableHeight + 'px'}">
        <div v-if="loading" style="padding: 10px 0 20px;"><Loading></Loading></div>
        <div style="height: 40px;" v-if="!currentPagePatiens.length && !loading"></div>
        <div class="table-tr" v-for="(item,index) in currentPagePatiens" :key="item.u_MDID">
          <div class="w2">
            <input type="checkbox" v-model="checkList" :id="item.u_MDID" :value="item.u_MDID"/>
            <label :for="item.u_MDID"></label>
          </div>
          <!--'/project/'+ projectid + '/patient_list/' + currentGroup + '/' + currentPage +'/patient_detail/'+item.u_MDID-->
          <div class="w8"><router-link :to="{name: 'patientDetail', params:{'projectid': projectid, 'groupid': currentGroup, 'pagenum': currentPage, 'MDID': item.u_MDID}}" v-text="item.u_patientname"></router-link></div>
          <div class="w5" v-text="item.u_gender == '' ? '' : item.u_gender == '1' ? '男' : '女'"></div>
          <div class="w6">{{ item.u_birthday | dateToAge }}岁</div>
          <div class="w10" v-text="item.u_jointime"></div>
          <div class="w10" v-text="item.u_createtime.slice(0,10)"></div>
          <div class="w17">{{ item.follow }}<span :class="{red: item.status == '2'}">（{{ item.status == '2' ? '待审核' : item.status == '3' ? '被退回' : item.status == '4' ? '审核通过' : '-' }}）</span></div>
          <div class="w8" v-text="item.s_username"></div>
          <div class="w15">{{item.center}}</div>
          <div class="flex" v-text="item.group"></div>
        </div>
        <div class="page">
          <Page :total="allPatientNum" :current="currentPage" :pagesize="30" @changepage="changePage"></Page>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  // import $ from 'webpack-zepto'
  import DialogBox from '../lib/dialog.vue'
  import Page from '../lib/page.vue'
  import API from '../api.js'
  import Datepicker from '../lib/Date.vue'
  import Loading from '../lib/loading.vue'
  import { Type } from '../utils/tools.js'
  export default {
    data () {
      return {
        loading: false,
        projectid: this.$route.params.projectid,
        allPatientNum: 0,
        currentPage: 1,
        currentPagePatiens: [],
        patientName: '',
        startJoinTime: '',
        endJoinTime: '',
        checkList: [],
        disabled: true,
        groupList: [],
        editGroupList: [],
        currentIndex: 0,
        checkGroup: [],
        currentGroup: this.$route.params.groupid,
        shareGroupDialog: false,
        groupName: '',
        newGroupName: '',
        newGroupFlag: false,
        manageGroupFalg: false,
        date: new Date().getFullYear(),
        cancelGroupDialog: false,
        tableHeight: 0
      }
    },
    components: {
      DialogBox,
      Page,
      Datepicker,
      Loading
    },
    mounted () {
      if (this.$route.path === '/project/' + this.projectid + '/patient_list') {
        this.currentGroup = '0'
        this.$router.push({path: '/project/' + this.projectid + '/patient_list/0/1'})
      }
      this.getGroupList()
      this.currentPage = parseInt(this.$route.params.pagenum ? this.$route.params.pagenum : 1)
      this.loadPatientList()
      const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
      this.tableHeight = window.innerHeight - tableTop - 20
    },
    watch: {
      '$route.params.pagenum': function (val, oldVal) {
        if (Type(oldVal) === 'undefined') return
        this.currentPage = parseInt(this.$route.params.pagenum ? this.$route.params.pagenum : '1')
        this.loadPatientList()
      },
      'currentGroup': function (val, oldVal) {
        if (Type(oldVal) === 'undefined') return
        // 避免 组和pagenum同时变化，重复调用 this.loadPatientList()
        let flag = true
        if (this.$route.params.pagenum !== '1') flag = false
        this.$router.push({name: 'patientList', params: {groupid: val, pagenum: '1'}})
        if (flag) this.loadPatientList()
      },
      'checkList': function (val) {
        val.length > 0 ? this.disabled = false : this.disabled = true
        val.length !== this.currentPagePatiens.length ? document.getElementById('all').checked = false : ''
      },
      manageGroupFalg (val) {
        if (!val) {
          for (let i in this.groupList[this.currentIndex]) {
            this.editGroupList[this.currentIndex][i] = this.groupList[this.currentIndex][i]
          }
        }
      }
    },
    methods: {
      // 翻页 改变路由
      changePage () {
        this.$router.push({name: 'patientList', params: {pagenum: arguments[0]}})
      },
      // 获取自定义分组列表
      getGroupList () {
        API.GetGroups({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          this.groupList = []
          this.editGroupList = []
          response.map(n => this.groupList.push({
            u_groupid: n.u_groupid,
            u_groupname: n.u_groupname,
            u_projectid: n.u_projectid,
            edit: false
          }))
          response.map(n => this.editGroupList.push({
            u_groupid: n.u_groupid,
            u_groupname: n.u_groupname,
            u_projectid: n.u_projectid,
            edit: false
          }))
        }).catch((err) => {
          console.log(err)
        })
      },
      // 加载该项目患者列表
      loadPatientList () {
        this.loading = true
        this.currentPagePatiens = []
        if (this.currentGroup === '0') {
          // 默认全部患者
          API.GetPatients({userid: this.$root.userid, projectid: this.$route.params.projectid, pagenum: this.currentPage, type: '1'}).then((response) => {
            this.loading = false
            this.allPatientNum = Number(response.allnum)
            response.patientList.map(n => this.currentPagePatiens.push(n))
          }).catch(() => {
            this.loading = false
            this.toast({type: 'error', text: '加载失败！'})
          })
        } else {
          // 获取分组下的患者列表
          API.GetGroupPatients({userid: this.$root.userid, groupid: this.currentGroup, pagenum: this.currentPage, projectid: this.$route.params.projectid}).then((response) => {
            this.loading = false
            this.allPatientNum = Number(response.num)
            if (response.data.length === 0) return
            response.data.map(n => this.currentPagePatiens.push(n))
          }).catch((err) => {
            console.log(err)
          })
        }
      },
      /* 根据姓名搜索患者 */
      searchPatientName () {
        API.SearchPatientName({userid: this.$root.userid, projectid: this.$route.params.projectid, name: this.patientName}).then((response) => {
          this.currentPagePatiens = []
          response.map(n => this.currentPagePatiens.push(n))
          this.allPatientNum = this.currentPagePatiens.length
        }).catch((err) => {
          console.log(err)
        })
      },
      /* 根据入组时间段搜素患者 */
      searchPatientJointime () {
        API.SearchPatientJointime({userid: this.$root.userid, projectid: this.$route.params.projectid, startjoin: this.startJoinTime, endjoin: this.endJoinTime}).then((response) => {
          this.currentPagePatiens = []
          response.map(n => this.currentPagePatiens.push(n))
          this.allPatientNum = this.currentPagePatiens.length
        }).catch((err) => {
          console.log(err)
        })
      },
      // 导出患者数据
      exportPatient () {
        if (!this.checkList.length) return
        let url = '/patient/exportpatient?userid=' + this.$root.userid + '&mdid=' + this.checkList.join(',')
        process.env.NODE_ENV === 'development' ? url = '/api' + url : ''
        window.location.href = url
        this.checkList = []
      },
      // 导出该项目全部患者数据
      exportAllPatient () {
        let url = '/patient/exportpatient?userid=' + this.$root.userid + '&projectid=' + this.$route.params.projectid
        process.env.NODE_ENV === 'development' ? url = '/api' + url : ''
        window.location.href = url
      },
      tip (event) {
        // this.$refs.hide()
        // this.$refs.config.show(event.currentTarget)
      },
      // 搜索分组
      searchGroup () {
        API.SearchGroup({userid: this.$root.userid, projectid: this.$route.params.projectid, groupname: this.groupName}).then((response) => {
          this.groupList = []
          response.map(n => this.groupList.push({
            u_groupid: n.u_groupid,
            u_groupname: n.u_groupname,
            u_projectid: n.u_projectid,
            edit: false
          }))
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 新建分组
      createGroup () {
        API.CreateGroup({userid: this.$root.userid, projectid: this.$route.params.projectid, groupname: this.newGroupName}).then((response) => {
          this.newGroupFlag = false
          this.newGroupName = ''
          this.getGroupList()
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 修改组名
      editGroup (index) {
        this.editGroupList = []
        this.groupList.map(n => this.editGroupList.push({
          u_groupid: n.u_groupid,
          u_groupname: n.u_groupname,
          u_projectid: n.u_projectid,
          edit: false
        }))
        this.editGroupList[index].edit = true
      },
      // 确认修改
      sureEditGroup (groupid, groupname) {
        API.EditGroupName({userid: this.$root.userid, groupid: groupid, groupname: groupname}).then((response) => {
          this.getGroupList()
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 取消修改
      cancelEditGroup (index) {
        for (let i in this.groupList[index]) {
          this.editGroupList[index][i] = this.groupList[index][i]
        }
      },
      // 删除组
      deleteGroup (groupid) {
        API.DeleteGroup({groupid: groupid}).then((response) => {
          this.getGroupList()
          this.loadPatientList()
        }).catch((err) => {
          console.log(err)
        })
      },
      /* 添加patients 到 选中的组 */
      addPatientGroup () {
        this.shareGroupDialog = false
        API.AddGroupPatient({userid: this.$root.userid, groupid: this.checkGroup.join(','), patients: this.checkList.join(',')}).then((response) => {
          this.disabled = true
          this.checkGroup = []
          this.checkList = []
          this.loadPatientList()
          this.toast({
            type: 'success',
            text: '将患者添加到分组成功！',
            placement: 'top'
          })
        }).catch((err) => {
          console.log(err)
        })
      },
      /* 将选中的 patients 从 组中移除 */
      deletePatientGroup () {
        this.cancelGroupDialog = false
        let groupid = this.$route.params.groupid === '0' ? '' : this.$route.params.groupid
        API.DeleteGroupPatient({groupid: groupid, patients: this.checkList.join(',')}).then((response) => {
          this.disabled = true
          this.checkGroup = []
          this.checkList = []
          this.loadPatientList()
          this.toast({
            type: 'success',
            text: '将患者取消分组成功！',
            placement: 'top'
          })
        }).catch((err) => {
          console.log(err)
        })
      },
      /* 全选或全不选 */
      allChecked () {
        if (this.checkList.length !== this.currentPagePatiens.length) {
          this.checkList = []
          for (let n in this.currentPagePatiens) {
            this.checkList.push(this.currentPagePatiens[n].u_MDID)
          }
        } else {
          this.checkList = []
        }
      },
      /* 批量删除患者操作 */
      deletePatients () {
        if (!this.checkList.length) return
        let vm = this
        this.confirm({
          title: '删除患者',
          message: '确定删除选中患者？',
          onConfirm () {
            API.DeletePatients({userid: vm.$root.userid, mdid: vm.checkList.join(',')}).then((response) => {
              vm.loadPatientList(vm.currentPage)
              vm.disabled = true
              vm.checkList = []
              vm.toast({
                type: 'success',
                text: '删除患者成功！',
                placement: 'top'
              })
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '网络异常，重新删除！',
                placement: 'top'
              })
            })
          }
        })
      },
      // 批量审核通过
      checkPass () {
        if (!this.checkList.length) return
        let vm = this
        this.confirm({
          title: '病历审查',
          message: '确定将选中患者通过审查？',
          onConfirm () {
            API.CheckPass({userid: vm.$root.userid, projectid: vm.$route.params.projectid, mdid: vm.checkList.join(',')}).then((response) => {
              vm.loadPatientList(vm.currentPage)
              vm.disabled = true
              vm.checkList = []
              vm.toast({
                type: 'success',
                text: '成功！',
                placement: 'top'
              })
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '网络异常，重新操作！',
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
  /* tools */
  .tools-top {
    margin-top: 32px;
    border-bottom: 1px solid rgba(0,0,0,.12);
    padding: 8px 0;
    background: #fff;
  }
  .tools-top .search {
    padding: 8px 32px;
    flex: 1;
    align-items: center;
    position: relative;
  }
  .tools-top .search input{
    flex: 1;
  }
  .tools-middle {
    padding: 16px 32px;
    justify-content: space-between;
    background: #fff;
  }
  .tools-middle .disabled{
    cursor: not-allowed;
    color: rgba(0,0,0,.26); 
  }
  .table-header{
    padding-right: 24px;
  }
  /* 翻页按钮 */
  .page{
    padding: 16px 32px;
    text-align: right;
    border-top: 1px solid rgba(0,0,0,.12);
  }
</style>