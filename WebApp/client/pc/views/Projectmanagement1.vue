<template>
  <div>
    <!--项目基本信息-->
    <div class="projectbase">
      <p><i>项目名称： </i><span v-text="projectInfo.u_projectName"></span></p>
      <p><i>项目简介： </i><span v-text="projectInfo.u_projectMem"></span></p>
      <p><i>创建者： </i><span v-text="projectInfo.s_username"></span></p>
      <p><i>创建时间： </i><span v-text="projectInfo.u_projecttime"></span></p>
      <p>
        <i>项目状态： </i>
        <select :class="{active: changeProjectState}" v-model="projectState" :disabled="disabled = !changeProjectState">
          <option value="1">进行中</option>
          <option value="0">已结束</option>
        </select>
        <i class="iconfont icon-bianji" style="margin-left: 20px;" v-if="this.projectInfo.rank && !changeProjectState && projectState !== '0'" @click="changeProjectState = true"></i>
        <div class="tools" v-show="changeProjectState" style="margin-left: 80px; text-align: left;">
          <MButton type="blue" @click="sureEndProject">确定</MButton>
          <MButton type="gray" @click="changeProjectState = false, projectState = projectInfo.u_projectstate">取消</MButton>
        </div>
      </p>
    </div>
    <!--添加成员  创建分中心-->
    <div class="tools" style="text-align: left;">
      <MButton type="blue" :class="{disable: !projectInfo.rank}" @click="addMemberDialog=true, currentId='', searchUserResult=[]">添加成员</MButton>
      <MButton type="blue" :class="{disable: !projectInfo.rank}" @click="createCenterDialog = true && projectInfo.rank">创建分中心</MButton>        
    </div>
    <DialogBox v-if="addMemberDialog && projectInfo.rank">
      <h3 slot="header">添加项目成员</h3>
      <div slot="body" class="body">
        <div class="flex-row">
          <input class="nameortel" type="text" v-model="nameOrTel" placeholder="请输入用户姓名或者手机号" @keyup.enter="searchUser"/>
          <span class="searchuser" @click="searchUser">搜索</span>
        </div>
        <div class="searchResult" v-if="searchUserFlag">
          <template v-if="searchUserResult.length === 0">
            <span>没有该用户！</span>
          </template>
          <template>
            <span v-for="item in searchUserResult" :class="{active: currentId === item.s_userid}" @click="currentId = item.s_userid">{{item.s_username+'-'+item.s_sex+'-'+item.s_workunti+'-'+item.s_department}}</span>
          </template>
        </div>
      </div>
      <div slot="footer" class="tools">
        <MButton type="blue" @click="addProjectMember">添加</MButton>
        <MButton type="gray" @click="addMemberDialog = false, nameOrTel = '', searchUserFlag = false">取消</MButton>
      </div>
    </DialogBox>
    <DialogBox v-if="createCenterDialog">
      <h3 slot="header">创建分中心</h3>
      <div slot="body" class="body">
        <div class="option flex-row">
          <label>分中心名称</label>
          <input type="text" v-model="centerName"/>
        </div>
        <div class="option flex-row">
          <label >分中心简介</label>
          <input type="text" v-model="centerMem"/>
        </div>
        <div class="option flex-row">
          <label >分中心地址</label>
          <input type="text"  v-model="centerAdr"/>
        </div>
        <div class="option flex-row">
          <label >分中心电话</label>
          <input type="text" v-model="centerPhone"/>
        </div>
      </div>
      <div slot="footer" class="tools">
        <MButton type="blue" @click="createCenter">保存</MButton>
        <MButton type="gray" @click="createCenterDialog = false">取消</MButton>
      </div>
    </DialogBox>
    <!--end-->
    <!--管理-->
    <div class="management">
      <!--参与成员-->
      <div class="contant" >
        <div class="mag-name"><span>{{projectInfo.u_projectName}}</span> &gt; 成员管理</div>
        <div class="mag-contant flex-col">
          <div v-for="(item, index) in memberList" class="member flex-row">
            <span class="memberName">{{item.s_username}}</span>
            <span class="choosebtn" :class="{active: item.u_permission === '1'}" v-if="item.u_permission === '1'">管理员</span>
            <span class="choosebtn" :class="{active: item.u_centername}"  v-if="item.u_centername" v-text="item.u_centername"></span>
            <div style="flex: 1; text-align: right" >
              <MButton class="btn" type="blue" :disabled="!projectInfo.rank" @click="setAdminDialog = true, currentId = item.u_userid">设置管理员</MButton>
              <MButton class="btn" type="gray" :disabled="!projectInfo.rank" @click="cancelAdminDialog = true, currentId = item.u_userid">取消管理员</MButton>
              <MButton class="btn" type="blue" :disabled="!projectInfo.rank" @click="addToCenterDialog = true, currentId = item.u_userid">分配分中心</MButton>
              <MButton class="btn" type="gray" :disabled="!projectInfo.rank" @click="removeMemberFromCenter(item.u_userid, item.u_centerid)">移除分中心</MButton>
              <MButton class="btn" type="gray" :disabled="!projectInfo.rank" @click="deleteMemberDialog = true, currentId = item.u_userid">删除</MButton>
            </div>
          </div>
          <DialogBox v-if="addToCenterDialog">
            <h3 slot="header">分配分中心</h3>
            <div slot="body" class="body flex-row">
              <div v-for="option in centerList" class="flex-row" style="align-items: center; margin-right: 10px;">
                <input type="radio" :id="option.u_centerID" v-model="checkedCenterId" :value="option.u_centerID"/>
                <label :for="option.u_centerID">{{ option.u_centername }}</label>
              </div>
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="addMemberToCenter">确定</MButton>
              <MButton type="gray" @click="addToCenterDialog = false">取消</MButton>
            </div>
          </DialogBox>
          <DialogBox v-if="cancelAdminDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将其移除管理员身份吗？
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="cancelAdmin">确定</MButton>
              <MButton type="gray" @click="cancelAdminDialog = false">取消</MButton>
            </div>
          </DialogBox>
          <DialogBox v-if="setAdminDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将其设置为管理员身份吗？
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="setAdmin">确定</MButton>
              <MButton type="gray" @click="setAdminDialog = false">取消</MButton>
            </div>
          </DialogBox> 
          <DialogBox v-if="deleteMemberDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将该成员移除项目吗？
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="deleteMember">确定</MButton>
              <MButton type="gray" @click="deleteMemberDialog = false">取消</MButton>
            </div>
          </DialogBox>      
        </div>
      </div>
      <!--参与中心-->
      <div class="contant">
        <div class="mag-name"><span>{{projectInfo.u_projectName}}</span> &gt; 中心管理</div>
        <div class="flex-row" style="justify-content: space-around;">
          <!--分中心列表显示-->
          <div class="center-list flex-col">
            <div class="list-option flex-row" v-for="item in centerList" style="justify-content: space-between;">
              <span class="center-name" @click="showCurrentCenterInfo(item)" v-text="item.u_centername"></span>
              <MButton type="gray" class="btn" @click="deleteCenterDialog = true, currentId = item.u_centerID">删除</MButton>
            </div>
          </div>
          <!--分中心具体信息显示-->
          <div class="center-info">
            <div class="tools">
              <MButton type="blue" v-if="projectInfo.rank && !editCenterFlag" :class="{active: editCenterFlag}" @click="editCenterFlag = true">编辑中心</MButton>
            </div>
            <div>
              <div class="option flex-row">
                <label>分中心名称：</label>
                <input type="text" v-model="editCenterInfo.u_centername" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}"/>
              </div>
              <div class="option flex-row">
                <label>分中心简介：</label>
                <textarea type="text" style="margin-top: 5px;" v-model="editCenterInfo.u_centermem" :disabled="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}">
                </textarea>
              </div>
              <div class="option flex-row">
                <label>分中心地址：</label>
                <input type="text" v-model="editCenterInfo.u_centeradr" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
              </div>
              <div class="option flex-row">
                <label>分中心电话：</label>
                <input type="text" v-model="editCenterInfo.u_centerphone" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
              </div>
              <div class="option flex-row">
                <label>创建人：</label>
                <input type="text" :value="currentCenterInfo.s_username" readonly :style="{border: 'none'}" />
              </div>
              <div v-if="editCenterFlag" class="tools">
                <MButton type="blue" @click="sureEditCenter">保存</MButton>
                <MButton type="gray" @click="cancelEditCenter">取消</MButton>
              </div> 
            </div>
          </div> 
          <!--删除分中心对话框-->
          <DialogBox v-if="deleteCenterDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将删除此中心？
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="deleteCenter">确定</MButton>
              <MButton type="gray" @click="deleteCenterDialog = false">取消</MButton>
            </div>
          </DialogBox>             
        </div>
      </div>
    </div>    
  </div>
</template>
<script>
  // import $ from 'webpack-zepto'
  import API from '../api.js'
  import DialogBox from '../lib/dialog.vue'
  import MButton from '../lib/button.vue'
  export default {
    data () {
      return {
        projectInfo: [],
        centerList: [],
        memberList: [],
        projectState: false,
        changeProjectState: false,
        currentId: '',
        addMemberDialog: false,
        nameOrTel: '',
        searchUserFlag: false,
        searchUserResult: [],
        createCenterDialog: false,
        centerName: '',
        centerMem: '',
        centerAdr: '',
        centerPhone: '',
        setAdminDialog: false,
        cancelAdminDialog: false,
        checkedCenterId: '',
        addToCenterDialog: false,
        deleteMemberDialog: false,
        currentCenterInfo: {},
        editCenterInfo: {
          u_centerID: '',
          u_centername: '',
          u_centermem: '',
          u_centeradr: '',
          u_centerphone: ''
        },
        deleteCenterDialog: false,
        editCenterFlag: false
      }
    },
    components: {
      DialogBox,
      MButton
    },
    created () {
      this.getProjectInfo()
    },
    methods: {
      // 项目详情
      getProjectInfo () {
        API.GetProjectInfo({userId: this.$root.userid, projectId: this.$route.params.projectid}).then((response) => {
          this.projectInfo = response[0]
          this.projectState = this.projectInfo.u_projectstate
          // 用户权限
          if (this.projectInfo.rank === '1') {
            this.projectInfo.rank = true
          } else if (this.projectInfo.rank === '2') {
            this.projectInfo.rank = false
          }
          this.getCenterList()
          this.getMemberList()
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 结束项目
      sureEndProject () {
        this.changeProjectState = false
        API.EndProject({userid: this.$root.userid, name: this.$route.params.projectid}).then((response) => {
          console.log(response)
        }).catch((err) => {
          this.projectState = this.projectInfo.u_projectstate
          window.alert(err)
        })
      },
      // 获取项目参与成员列表
      getMemberList () {
        API.GetMember({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid}).then((response) => {
          this.memberList = response
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 获取项目分中心列表
      getCenterList () {
        API.GetProjectCenter({userId: this.$root.userid, projectId: this.$route.params.projectid}).then((response) => {
          this.centerList = response
          this.showCurrentCenterInfo(this.centerList[0])
        }).catch((err) => {
          window.alert(err)
        })
      },
      //
      //
      /* 添加项目成员 */
      addProjectMember () {
        if (this.currentId) {
          API.AddProjectMember({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, memberId: this.currentId, admin: this.adminRank}).then((response) => {
            window.alert('添加成功')
            this.getMemberList()
            this.addMemberDialog = false
            this.nameOrTel = ''
            this.searchUserFlag = false
            this.adminRank = ''
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      /* 搜索用户信息 */
      searchUser () {
        if (this.nameOrTel) {
          this.searchUserFlag = true
          API.FindUserInfo({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, nameOrTel: this.nameOrTel}).then((response) => {
            this.searchUserResult = response
            for (let i = 0; i < this.searchUserResult.length; i++) {
              if (this.searchUserResult[i].s_sex === '1') {
                this.searchUserResult[i].s_sex = '男'
              } else if (this.searchUserResult[i].s_sex === '2') {
                this.searchUserResult[i].s_sex = '女'
              }
            }
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      // 创建分中心
      createCenter () {
        API.CreateCenter({userId: this.$root.userid, projectId: this.$route.params.projectid, centerName: this.centerName, centerMem: this.centerMem, centerAdr: this.centerAdr, centerPhone: this.centerPhone}).then((response) => {
          this.createCenterDialog = false
          window.alert('创建成功')
          this.centerName = ''
          this.centerMem = ''
          this.centerAdr = ''
          this.centerPhone = ''
          this.getCenterList()
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* 添加项目管理员 */
      setAdmin () {
        API.AddAdmin({userId: this.$root.userid, projectId: this.$route.params.projectid, memberId: this.currentId}).then((response) => {
          this.toast({
            type: 'success',
            text: '添加项目管理员成功！',
            placement: 'top'
          })
          this.getMemberList()
          this.setAdminDialog = false
        }).catch(() => {
          this.toast({
            type: 'error',
            text: '添加失败，请再次尝试！',
            placement: 'top'
          })
        })
      },
      /* 移除项目管理员 */
      cancelAdmin () {
        // console.log(this.currentId)
        API.DeleteAdmin({userId: this.$root.userid, projectId: this.$route.params.projectid, memberId: this.currentId}).then((response) => {
          this.toast({
            type: 'success',
            text: '移除项目管理员成功！',
            placement: 'top'
          })
          this.getMemberList()
          this.cancelAdminDialog = false
        }).catch((err) => {
          let type = err === '不能将自己移除' ? 'default' : 'error'
          err = err === '不能将自己移除' ? err : '添加失败，请重新尝试！'
          this.toast({
            type: type,
            text: err,
            placement: 'top'
          })
          this.cancelAdminDialog = false
        })
      },
      /* 添加项目成员到分中心 */
      addMemberToCenter (item, option, event) {
        API.AddMemberToCenter({userId: this.$root.userid, projectId: this.$route.params.projectid, centerId: this.checkedCenterId, memberId: this.currentId}).then((response) => {
          this.toast({
            type: 'success',
            text: '添加项目成员到分中心成功！',
            placement: 'top'
          })
          this.getMemberList()
          this.addToCenterDialog = false
          this.checkedCenterId = ''
        }).catch((err) => {
          let type = err === '用户已被分配到中心' ? 'default' : 'error'
          err = err === '用户已被分配到中心' ? '用户已被分配到中心, 请先移除该分中心' : '添加失败，请重新尝试！'
          this.toast({
            type: type,
            text: err,
            placement: 'top'
          })
        })
      },
      /* 移除分中心 */
      removeMemberFromCenter (memberid, centerid) {
        API.RemoveMemberInCenter({userId: this.$root.userid, projectId: this.$route.params.projectid, centerId: centerid, memberId: memberid}).then((response) => {
          this.getMemberList()
          this.toast({
            type: 'success',
            text: '从分中心移除成功！',
            placement: 'top'
          })
        }).catch((err) => {
          let type = err === '用户不存在该中心中' ? 'default' : 'error'
          err = err === '用户不存在该中心中' ? err : '添加失败，请重新尝试！'
          this.toast({
            type: type,
            text: err,
            placement: 'top'
          })
        })
      },
      /* 删除项目成员 */
      deleteMember () {
        API.DeleteProjectMember({userId: this.$root.userid, projectId: this.$route.params.projectid, memberId: this.currentId}).then((response) => {
          this.getMemberList()
          this.deleteMemberDialog = false
          this.toast({
            type: 'success',
            text: '删除成功！',
            placement: 'top'
          })
        }).catch(() => {
          this.toast({
            type: 'error',
            text: '删除失败，请再次尝试！',
            placement: 'top'
          })
        })
      },
      //
      // 展示当前项目分中心详情
      showCurrentCenterInfo (item) {
        this.currentCenterInfo = item
        for (let i in this.currentCenterInfo) {
          this.editCenterInfo[i] = this.currentCenterInfo[i]
        }
      },
      // 确认编辑分中心
      sureEditCenter () {
        API.EditCenter({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, centerMsg: JSON.stringify({u_centerID: this.editCenterInfo.u_centerID, u_centername: this.editCenterInfo.u_centername, u_centermem: this.editCenterInfo.u_centermem, u_centeradr: this.editCenterInfo.u_centeradr, u_centerphone: this.editCenterInfo.u_centerphone})}).then((response) => {
          this.editCenterFlag = false
          this.toast({
            type: 'success',
            text: '编辑分中心成功！',
            placement: 'top'
          })
        }).catch((err) => {
          this.toast({
            type: 'success',
            text: err,
            placement: 'top'
          })
        })
      },
      // 取消编辑分中心
      cancelEditCenter () {
        this.editCenterFlag = false
        for (let i in this.currentCenterInfo) {
          this.editCenterInfo[i] = this.currentCenterInfo[i]
        }
      },
      /* 删除分中心 */
      deleteCenter () {
        this.deleteCenterDialog = false
        API.DeleteCenter({userId: this.$root.userid, projectId: this.$route.params.projectid, centerId: this.currentId}).then((response) => {
          this.toast({
            type: 'success',
            text: '删除分中心成功！',
            placement: 'top'
          })
          this.getCenterList()
        }).catch(() => {
          this.toast({
            type: 'error',
            text: '删除分中心失败！',
            placement: 'top'
          })
        })
      }
    }
  }
</script>
<style scoped>
  /* 项目基本信息 */
  .projectbase{
    border: 1px #ccc solid;
    padding-left: 12px;
    font-size: 14px;
    line-height: 2;
    margin-bottom: 20px;
  }
  .projectbase p select{
    border: none;
    -moz-appearance: none;    
    -webkit-appearance: none;
    appearance: none;
    color: #000;
    border: 1px #fff solid;
    border-radius: 3px;
    padding: 2px;
    width: 80px;
  }
  .projectbase p select.active{
    border-color: #eee;
  }
  /* tools */
  .tools{
    padding: 10px 0;
    text-align: right;
  }
  .tools .disable{
    opacity: .2;
  }

  /* 添加项目成员style */
  input.nameortel{
    border: none;
    border: 1px #ccc solid;
    border-right: none;
    border-radius: 3px;
    padding: 3px;
    flex: 1;
  }
  .searchuser{
    width: 50px;
    padding: 4px;
    background: #20a0ff;
    text-align: center;
    color: #fff;
    margin-left: -1px;
    cursor: pointer;
  }
  .searchResult{
    border: 1px #ccc solid;
    border-top: none;
  }
  .searchResult span{
    font-size: 14px;
    padding: 5px;
    width: 100%;
    display: inline-block;
    cursor: pointer;
  }
  .searchResult span:hover, .searchResult span.active{
    background: #eee;
  }
  
  /* 创建分中心对话框 */
  .option{
    margin: 5px 0;
  }
  .option label{
    padding: 5px 0;
    font-size: 12px;
    font-family: -webkit-body;
    margin-right: 5px;
    width: 85px;
  }
  .option input[type=text]{
    flex: 1;
    padding: 3px 5px;
    border: none;
    border: 1px #ccc solid;
    border-radius: 3px;
  }

  /*管理*/
  .management{
    width: 100%;
    margin: 20px 0;
  }
  .management .contant{
    border: 1px #ccc solid;
    font-size: 14px;
    margin-bottom: 20px;
  }
  .contant .mag-name{
    padding: 8px 12px;
    border-bottom: 1px #ccc solid;
  }
  .contant .mag-contant {
    background: #fafafa;
    margin: 12px;
    border: 1px #ccc solid;
    border-radius: 3px; 
  }

  /* 成员style */
  .mag-contant .member {
    padding: 12px;
    border-bottom: 1px #eee solid;
    align-items: center;
  }
  .mag-contant .member .memberName {
    min-width: 90px;
    display: block;
  }
  .mag-contant .member .choosebtn{
    font-size: 12px;
    border: 1px #ccc solid;
    border-radius: 3px;
    padding: 0 3px;
    margin-right: 5px;
  }
  .member .choosebtn.active{
    background: #20a0ff;
    border-color: #20a0ff;
    color: #fff;
  }
  .btn{
    padding: 2px 12px;
    font-size: 12px;
  }
  /* 分中心 */ 
  .center-list, .center-info{
    width: 45%;
    background: #fafafa;
    margin: 12px;
    border: 1px #ccc solid;
    border-radius: 3px; 
  }
  .center-name{
    border-bottom: 1px #000 solid;
  }
  .center-list .list-option {
    padding: 12px;
    border-bottom: 1px #eee solid;
    align-items: center;
    cursor: pointer;
  }
  /* 分中心分中心具体信息显示 */
 .center-info{
    padding: 0px 12px;
 }
 .center-info .option label{
   font-size: 14px;
   width: 100px;
 }
  .center-info .option input, .center-info .option textarea{
    padding: 3px 5px;
    background: #fafafa;
    border-radius: 3px;
    font-size: 14px;
    flex: 1;
  }
  .center-info span{
    margin-right: 10px;
  } 
</style>