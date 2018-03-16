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
        <div class="cbOption" :style="{'margin-bottom': '0px'}">
          <input  class="nameortel" type="text" v-model="nameOrTel" placeholder="请输入用户姓名或者手机号" @keyup.enter="searchUser"/>
          <span class="searchuser" @click="searchUser">搜索</span>
        </div>
        <div class="searchResult" v-if="searchFlag">
          <template v-if="searchUserResult.length === 0">
            <span>没有该用户！</span>
          </template>
          <template v-else>
            <span v-for="item in searchUserResult" :class="{active: currentId === item.s_userid}" @click="currentId = item.s_userid">{{item.s_username+'-'+item.s_sex+'-'+item.s_workunti+'-'+item.s_department}}</span>
          </template>
        </div>
        <div class="cbOption">
          <span :style="{'margin-right': '10px'}">管理员</span>
          <span class="adminyorn" :class="{active: adminRank === 'y'}" @click="adminRank = 'y'">是</span><span class="adminyorn" :class="{active: adminRank === 'n'}" :style="{'margin-left': '-1px'}" @click="adminRank = 'n'">不是</span>
        </div>
      </div>
      <div slot="footer" class="tools">
        <MButton type="blue" @click="addProjectMember">添加</MButton>
        <MButton type="gray" @click="addMemberDialog = false, nameOrTel = '', searchFlag = false, adminRank = ''">取消</MButton>
      </div>
    </DialogBox>
    <DialogBox v-if="createCenterDialog">
      <h3 slot="header">创建分中心</h3>
      <div slot="body" class="body">
        <div class="cbOption">
          <label>分中心名称</label>
          <input type="text" v-model="centerName"/>
        </div>
        <div class="cbOption">
          <label >分中心简介</label>
          <input type="text" v-model="centerMem"/>
        </div>
        <div class="cbOption">
          <label >分中心地址</label>
          <input type="text"  v-model="centerAdr"/>
        </div>
        <div class="cbOption">
          <label >分中心电话</label>
          <input type="text" v-model="centerPhone"/>
        </div>
      </div>
      <div slot="footer" class="footer">
        <MButton type="blue" @click="createCenter">保存</MButton>
        <MButton type="gray" @click="createCenterDialog = false">取消</MButton>
      </div>
    </DialogBox>
    <!--end-->
    <!--管理-->
    <div class="management flex-row">
      <!--参与成员-->
      <div class="contant" >
        <div class="mag-name"><span>{{projectInfo.u_projectName}}</span> &gt; 成员管理</div>
        <div class="mag-contant flex-col">
          <div v-for="(item, index) in memberList" class="member flew-row">
            <span class="memberName">{{item.s_username}}</span>
            <!--管理员身份-->
            <span class="choosebtn" :class="{active: item.u_permission === '1'}" @click="editAdministrator(item.u_userid)">管理员</span>
            <!--分配分中心编辑-->
            <div class="addMemberToCenter">
              <span class="choosebtn" :class="{active: item.u_centername}" v-text="item.u_centername ? item.u_centername : '分配分中心'" @click="showCenter"></span>
              <div class="centerDialog">
                <span v-for="option in centerList" v-text="option.u_centername" @click="addMemberToCenter(item, option)"></span>
                <span class="removeCenter" @click="addMemberToCenter(item)">移除分中心</span>
              </div>
            </div> 
            <!--删除项目成员-->
            <span class="choosebtn" @click="deleteMember(item.u_userid)">删除</span>   
          </div>
          <DialogBox v-if="deleteAdminDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将其移除管理员身份吗？
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="deleteAdmin">确定</MButton>
              <MButton type="gray" @click="deleteAdminDialog = false">取消</MButton>
            </div>
          </DialogBox>
          <DialogBox v-if="addAdminDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将其设置为管理员身份吗？
            </div>
            <div slot="footer" class="tools">
              <MButton type="blue" @click="addAdmin">确定</MButton>
              <MButton type="gray" @click="addAdminDialog = false">取消</MButton>
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
        <div class="mag-name"><span>{{projectInfo.u_projectName}}</span> &gt;中心管理</div>
        <div class="mag-contant">
          <!--分中心列表显示-->
          <div class="listOption" v-for="item in centerList">
            <span class="centerName" @click="showCurrentCenterInfo()" v-text="item.u_centername"></span>
            <span class="delete" @click="deleteCenter(item.u_centerID)">X</span>
            <!--分中心具体信息显示-->
            <div class="centerIndfoCard">
              <div class="btn" :style="{'margin-top': '10px'}" v-if="projectInfo.rank" :class="{active: editCenterFlag}" @click="editCenter(item)">编辑中心</div>
              <div class="option"><label>分中心名称：</label><input type="text" v-model="item.u_centername" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none' , marginBottom: editCenterFlag ? '5px' : '0'}"/></div>
              <div class="option"><label>分中心简介：</label><input type="text" v-model="item.u_centermem" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none' , marginBottom: editCenterFlag ? '5px' : '0'}" /></div>
              <div class="option"><label>分中心地址：</label><input type="text" v-model="item.u_centeradr" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none' , marginBottom: editCenterFlag ? '5px' : '0'}" /></div>
              <div class="option"><label>分中心电话：</label><input type="text" v-model="item.u_centerphone" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none' , marginBottom: editCenterFlag ? '5px' : '0'}" /></div>
              <div class="option"><label>创建人：</label><input type="text" :value="item.s_username" readonly :style="{border: 'none' , marginBottom: editCenterFlag ? '5px' : '0'}" /></div>
              <div v-if="editCenterFlag" :style="{margin: '10px 100px'}">
                <div class="btn" @click="editCenter('')">保存</div>
                <div class="btn" @click="editCenterFlag = false">取消</div>
              </div> 
            </div>
          </div> 
          <!--删除分中心对话框-->
          <DialogBox v-if="deleteCenterDialog">
            <h3 slot="header">提示信息</h3>
            <div slot="body" class="body">
              您确定要将删除此中心？
            </div>
            <div slot="footer" class="footer">
              <div class="btn" @click="deleteCenter()">确定</div>
              <div class="btn" @click="deleteCenterDialog = false">取消</div>
            </div>
          </DialogBox>             
          
        </div>
      </div>
    </div>    
  </div>
</template>
<script>
  import $ from 'webpack-zepto'
  import API from '../api.js'
  import DialogBox from '../lib/dialog.vue'
  import MButton from '../lib/button.vue'
  export default {
    data () {
      return {
        projectInfo: [],
        projectState: '',
        changeProjectState: false,
        centerList: [],
        createCenterDialog: false,
        centerName: '',
        centerMem: '',
        centerAdr: '',
        centerPhone: '',
        currentCenterInfo: {},
        deleteCenterDialog: false,
        editCenterFlag: false,
        memberList: [],
        deleteAdminDialog: false,
        addAdminDialog: false,
        deleteMemberDialog: false,
        addMemberDialog: false,
        currentId: '',
        currentEvent: {},
        nameOrTel: '',
        searchFlag: false,
        searchUserResult: [],
        adminRank: ''
      }
    },
    components: {
      DialogBox,
      MButton
    },
    created () {
      // 项目详情
      API.GetProjectInfo({userId: this.$root.userid, projectId: this.$route.params.projectid}).then((response) => {
        // success callback
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
    methods: {
      sureEndProject () {
        this.changeProjectState = false
        API.EndProject({userid: this.$root.userid, name: this.$route.params.projectid}).then((response) => {
          console.log(response)
        }).catch((err) => {
          this.projectState = this.projectInfo.u_projectstate
          window.alert(err)
        })
      },
      // 分中心列表
      getCenterList () {
        API.GetProjectCenter({userId: this.$root.userid, projectId: this.$route.params.projectid}).then((response) => {
          this.centerList = response
        }).catch((err) => {
          window.alert(err)
        })
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
      // 展示当前项目分中心详情
      showCurrentCenterInfo (event) {
        this.editCenterMemberFlag = false
        let e = window.event || event
        if ($(e.target).hasClass('active')) {
          $('.centerName').removeClass('active')
          $('.centerName').siblings('div').css({'height': '0px', 'border': 'none'})
        } else {
          $('.centerName').removeClass('active')
          $('.centerName').siblings('div').css({'height': '0px', 'border': 'none'})
          $(e.target).addClass('active')
          $(e.target).siblings('div').css({'height': 'auto', 'border': '1px #20a0ff solid'})
        }
      },
      /* 删除分中心 */
      deleteCenter (flag) {
        this.deleteCenterDialog = true
        if (flag) {
          this.currentId = flag
        } else {
          API.DeleteCenter({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, centerId: this.currentId}).then((response) => {
            window.alert('删除分中心成功')
            this.getCenterList()
            this.deleteCenterDialog = false
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      // 编辑分中心
      editCenter (item) {
        if (item) {
          this.editCenterFlag = true
          this.currentCenterInfo = item
        } else {
          API.EditCenter({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, centerMsg: JSON.stringify({u_centerID: this.currentCenterInfo.u_centerID, u_centername: this.currentCenterInfo.u_centername, u_centermem: this.currentCenterInfo.u_centermem, u_centeradr: this.currentCenterInfo.u_centeradr, u_centerphone: this.currentCenterInfo.u_centerphone})}).then((response) => {
            this.editCenterFlag = false
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      // 获取项目参与成员列表
      getMemberList () {
        API.GetMember({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid}).then((response) => {
          this.memberList = response
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* 搜索用户信息 */
      searchUser () {
        if (this.nameOrTel) {
          this.searchFlag = true
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
      /* 添加项目成员 */
      addProjectMember () {
        if (this.currentId) {
          API.AddProjectMember({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, memberId: this.currentId, admin: this.adminRank}).then((response) => {
            window.alert('添加成功')
            this.getMemberList()
            this.addMemberDialog = false
            this.nameOrTel = ''
            this.searchFlag = false
            this.adminRank = ''
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      /* 删除项目成员 */
      deleteMember (flag) {
        this.deleteMemberDialog = true
        if (flag) {
          this.currentId = flag
        } else {
          this.$http.post('/api/project/removememberinproject', {userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, memberId: this.currentId}).then((response) => {
            $(this.currentEvent.target).removeClass('active')
            this.getMemberList()
            this.deleteMemberDialog = false
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      /* 展示项目包含分中心列表框 */
      showCenter (event) {
        let e = window.event || event
        if ($(e.target).hasClass('show')) {
          $(e.target).removeClass('show')
          $(e.target).siblings('div').css({'height': '0px', 'border': 'none'})
        } else {
          $(e.target).addClass('show')
          $(e.target).siblings('div').css({'height': 'auto', 'border': '1px #20a0ff solid'})
        }
      },
      // 编辑项目成员所在的分中心列表
      addMemberToCenter (item, option, event) {
        let e = window.event || event
        // 移除分中心
        if (item.u_centername !== '') {
          API.RemoveMemberInCenter({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, centerId: item.u_centerid, memberId: item.u_userid}).then((response) => {
            item.u_centername = ''
            item.u_centerid = ''
            $(e.target).parent().css({'height': '0px', 'border': 'none'})
          }).catch((err) => {
            window.alert(err)
          })
        }
        // 添加到分中心
        if (option) {
          API.AddMemberToCenter({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, centerId: option.u_centerID, memberId: item.u_userid}).then((response) => {
            item.u_centername = option.u_centername
            item.u_centerid = option.u_centerID
            $(e.target).parent().css({'height': '0px', 'border': 'none'})
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      /* 编辑管理员 */
      editAdministrator (memberID, event) {
        this.currentId = memberID
        let e = window.event || event
        this.currentEvent = e
        if ($(e.target).hasClass('active')) {
          this.deleteAdminDialog = true
        } else {
          this.addAdminDialog = true
        }
      },
      /* 移除项目管理员 */
      deleteAdmin () {
        // console.log(this.currentId)
        API.DeleteAdmin({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, memberId: this.currentId}).then((response) => {
          window.alert('移除管理员成功')
          $(this.currentEvent.target).removeClass('active')
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* 添加项目管理员 */
      addAdmin () {
        API.AddAdmin({userId: window.sessionStorage.getItem('userid'), projectId: this.$route.params.projectid, memberId: this.currentId}).then((response) => {
          window.alert('添加管理员成功')
          $(this.currentEvent.target).addClass('active')
          this.addAdminDialog = false
        }).catch((err) => {
          // error callback
          window.alert(err)
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
  /*管理*/
  .management{
    width: 100%;
    margin: 20px 0;
    justify-content: space-around;
  }
  .management .contant{
    border: 1px #ccc solid;
    font-size: 14px;
    width: 45%;
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
    line-height: 3;
    padding-left: 12px;
    border-bottom: 1px #eee solid;
  }
  .mag-contant .member .memberName {
    min-width: 80px;
    display: inline-block;
  }
  .mag-contant .member .choosebtn{
    font-size: 10px;
    border: 1px #ccc solid;
    border-radius: 3px;
    padding: 1px 3px;
    cursor: pointer;
  }
  .member .choosebtn.active{
    background: #20a0ff;
    border-color: #20a0ff;
    color: #fff;
  }

  .addMemberToCenter{
    position: relative;
    display: inline-block;
    font-size: 12px;
  }
  .addMemberToCenter .centerDialog{
    position: absolute;
    background: #fff;
    min-width: 200px;
    left: 0px;
    top: 27px;
    z-index: 999;
    height: 0;
    overflow: hidden;
    line-height: 1.5;
  }
  .addMemberToCenter .centerDialog span{
    padding: 0 3px;
    cursor: pointer;
    margin: 3px;
    display: inline-block;
  }
  .centerDialog span.removeCenter{
    border: 1px #eee solid;
  }

 .centerIndfoCard{
    margin-left: 10px;
    font-size: 14px;
    height: 0;
    padding: 0px 10px;
    overflow: hidden;
  }
  .centerIndfoCard .option{
    display: flex;
  }
  .centerIndfoCard .option label{
    padding: 3px;
  }
  .centerIndfoCard .option input, .centerIndfoCard .option textarea{
    padding: 3px;
    background: #f4f6f8;
    border-radius: 3px;
    font-size: 14px;
  }
  .centerIndfoCard span{
    margin-right: 10px;
  }

  .delete{
    cursor: pointer;
    font-size: 16px;
  }
  .listOption span.centerName{
    cursor: pointer;
    padding: 0 10px;
    margin: 5px 5px 0 10px;
    display: inline-block;
  }
  .listOption span.centerName.active{
    background: #20a0ff;
    color: #fff;
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
  .adminyorn{
    font-size: 14px;
    border: 1px solid #ccc;
    padding: 0 3px;
    background: #fff;
    cursor: pointer;
  }
  .adminyorn.active{
    background: #20a0ff;
    border-color: #20a0ff;
    color: #fff;
  }
  
  
  .selectOption span.checked{
    background: #20a0ff;
    color: #fff;
    cursor: pointer;
  }
  .selectOption span.canJoin{
    cursor: pointer;
    color: #000;
  }
  /* 创建分中心对话框 */
  .cbOption{
    display: flex;
    width: 100%;
    align-items: center;
    margin: 3px 0;
  }
  .cbOption label{
    padding: 5px 0;
    font-size: 12px;
    font-family: -webkit-body;
    margin-right: 5px;
  }
  .cbOption label ~ input[type=text], .cbOption label ~ textarea{
    flex: 1;
    padding: 2px;
    border: none;
    border: 1px #ccc solid;
    border-radius: 3px;
  }
  .footer {
    text-align: right;
  }
  .footer .btn{
    background: #eee;
    border: 1px #ccc solid;
    cursor: pointer;
    padding: 0 10px;
    border-radius: 3px;
    color: #000;
  }
</style>