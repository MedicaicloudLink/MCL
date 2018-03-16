<template>
  <div id="app">
    <Navigation></Navigation>
    <div class="main">
      <div class="header">
        <span class="title">所有通知</span>
        <div>
          <select v-model="type" @change="getNoticeList">
            <option value="all">全部</option>
            <option value="nodo">未处理</option>
          </select>
        </div>
      </div>

      <div class="notice-list">
        <div class="notice-item" v-if="!noticeList.length"></div>

        <div v-for="item in noticeList">
          <!--联系人消息-->
          <div class="flex-row notice-item" v-if="item.type.indexOf('1') == 0">
            <div class="left flex-col">
              <div class="date"> {{ item.createtime | formatDate}} </div>
              <div class="flex-row color87">
                <span class="name" @click="getInviterInfo(item.userid)">{{item.s_username}}</span>
                <span v-if="item.type == '103'">同意了您的联系人邀请</span>
                <span v-else-if="item.type == '104'">拒绝了您的联系人邀请</span>
                <span v-else>想添加您为联系人</span>
              </div>
            </div>
            <div class="color54" v-if="item.type == '105'">已添加</div>
            <div class="color54" v-if="item.type == '106'">已拒绝</div>
            <div class="flex-row" v-if="item.type == '102'">
              <span class="agree" @click="handleContactApply(item.userid, '2')">同意</span>
              <span class="refuse" @click="handleContactApply(item.userid, '3')">拒绝</span>
            </div>
          </div>
          <!--加入项目消息-->
          <div class="flex-row notice-item" v-if="item.type.indexOf('2') == 0">
            <div class="left flex-col">
              <div class="date"> {{ item.createtime | formatDate }} </div>
              <div class="flex-row color87">
                <span class="name" @click="getInviterInfo(item.userid)">{{item.s_username}}</span>
                <span v-if="item.type == '203'">同意加入{{item.projectname}}项目</span>
                <span v-else-if="item.type == '204'">拒绝加入{{item.projectname}}项目</span>
                <span v-else>邀请您加入<span class="name" @click="getProjectInfo(item.projectid)">{{item.projectname}}</span>项目</span>
              </div>
            </div>
            <div class="color54" v-if="item.type == '205'">已添加</div>
            <div class="color54" v-if="item.type == '206'">已拒绝</div>
            <div class="flex-row" v-if="item.type == '202'">
              <span class="agree" @click="handleProjectApply(item.projectid, item.userid, '2')">同意</span>
              <span class="refuse" @click="handleProjectApply(item.projectid, item.userid, '3')">拒绝</span>
            </div>
          </div>
          <!--病例被退回消息-->
          <div class="flex-row notice-item" v-if="item.type.indexOf('3') == 0">
            <div class="left flex-col">
              <div class="date"> {{ item.createtime | formatDate }} </div>
              <div class="flex-row color87">
                <span>
                  您的病例被管理员退回，请到
                  <span class="name" v-if="item.type == '301'">入组登记中被退回的病例</span>
                  <span class="name" v-if="item.type == '302'">随访记录中被退回的病例</span>
                  查看
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="page">
          <Page :total="allNoticeNum" :current="currentPage" :pagesize="30" @changepage="changePage"></Page>
        </div>
      </div>
      <!--新建项目对话框-->
      <DialogBox v-if="inviterInfoDialog">
        <div slot="body" class="body flex-row">
          <img class="avatar" v-if="inviterInfo.s_avatar" :src="'http://' + inviterInfo.s_avatar" alt="userImg">
          <span class="avatar" v-else></span>
          <div class="user flex-col">
            <h3>{{ inviterInfo.s_username }}</h3>
            <p>{{ inviterInfo.s_workunti }}</p>
            <span>性别：{{ inviterInfo.s_sex === '1' ? '男' : '女' }}</span>
            <span>职务/职称：{{ inviterInfo.s_joblevel }}</span>
            <span v-if="inviterInfo.s_mybirthday">年龄：{{ date - Number(inviterInfo.s_mybirthday.slice(0, 4)) }}</span>
            <span>电话：{{ inviterInfo.s_cellphone }}</span>
            <span>科室：{{ inviterInfo.s_department }}</span>
            <span>邮箱：{{ inviterInfo.s_useremail }}</span>
          </div>
        </div>
        <div slot="footer" class="flex-row">
          <div class="close" @click="inviterInfoDialog = false">关闭</div>
        </div>
      </DialogBox>
      <!--end-->
      <!--新建项目对话框-->
      <DialogBox v-if="projectInfoDialog">
        <div slot="body" class="body">
          <p>项目名称：{{ projectInfo.u_projectName }}</p>
          <p class="flex-row">
            <span v-if="projectInfo.starttime" style="margin-right: 50px;">项目开始时间：{{ projectInfo.starttime.slice(0, 10) }}</span>
            <span v-if="projectInfo.endtime">项目结束时间：{{ projectInfo.endtime.slice(0, 10) }}</span>
          </p>
          <p>项目简介：{{ projectInfo.u_projectMem }}</p>
          <p>创建人：{{ projectInfo.s_username }}</p>
          <p>项目成员：<span v-for="name in projectMemberNames" style="margin-right: 10px;">{{ name }}</span></p>
        </div>
        <div slot="footer" class="flex-row">
          <div class="close" @click="projectInfoDialog = false">关闭</div>
        </div>
      </DialogBox>
      <!--end-->
    </div>
  </div>
</template>

<script>
  import DialogBox from './lib/confirm/dialog.vue'
  import Navigation from './components/navigation.vue'
  import Page from './lib/page.vue'
  export default {
    name: 'app',
    components: {
      Navigation,
      DialogBox,
      Page
    },
    data () {
      return {
        type: 'all', // all: 全部 ，nodo: 未处理
        noticeList: [],
        allNoticeNum: 0,
        currentPage: 1,
        inviterInfoDialog: false,
        inviterInfo: {},
        date: new Date().getFullYear(),
        projectInfoDialog: false,
        projectInfo: {},
        projectMemberNames: []
      }
    },
    mounted () {
      // if (!window.sessionStorage.getItem('userid')) this.login()
      this.getNoticeList(this.currentPage)
    },
    watch: {
      currentPage () {
        this.getNoticeList()
      }
    },
    methods: {
      // login () {
      //   let data = {username: '13910111011', password: '123456'}
      //   // let data = {username: '15910952287', password: '123456'}
      //   this.$http.Login(data).then(rep => {
      //     window.sessionStorage.setItem('userid', rep.userid)
      //     window.sessionStorage.setItem('authorization', rep.token)
      //     this.$root.userid = rep.userid
      //     this.$root.GET_USERINFO()
      //   }).catch(err => console.log(err))
      // },
      // 翻页 改变路由
      changePage () {
        this.currentPage = arguments[0]
      },
      // 获取邀请人信息
      getInviterInfo (inviterId) {
        this.inviterInfoDialog = true
        this.inviterInfo = {}
        this.$http.GetInviterInfo({userid: this.$root.userid, touserid: inviterId}).then(response => {
          this.inviterInfo = response
        }).catch(() => this.toast({text: '网络加载失败'}))
      },
      // 获取项目信息
      getProjectInfo (projectId) {
        this.projectInfoDialog = true
        this.projectInfo = {}
        this.projectMemberNames = []
        this.$http.GetProjectInfo({userid: this.$root.userid, projectid: projectId}).then(response => {
          this.projectInfo = response[0]
        }).catch(() => this.toast({text: '网络加载失败'}))
        this.$http.GetProjectMember({userid: this.$root.userid, projectid: projectId}).then(response => {
          response.map(n => this.projectMemberNames.push(n.s_username))
        }).catch(() => this.toast({text: '网络加载失败'}))
      },
      // 获取通知列表
      getNoticeList () {
        this.$http.GetAllNotices({userid: this.$root.userid, page: this.currentPage, type: this.type}).then(response => {
          this.noticeList = response.result
          this.allNoticeNum = Number(response.count)
        }).catch(err => {
          window.alert(err)
        })
      },
      // 处理联系人申请
      handleContactApply (applyid, status) {
        this.$http.HandleContactApply({userid: this.$root.userid, touserid: applyid, status: status}).then(response => {
          console.log(response)
          this.getNoticeList()
          this.$root.GET_NOTICE_NUM()
        }).catch(err => {
          window.alert(err)
        })
      },
      // 处理项目邀请
      handleProjectApply (projectid, applyid, status) {
        this.$http.HandleProjectApply({userid: this.$root.userid, projectid: projectid, touserid: applyid, status: status}).then(response => {
          console.log(response)
          this.getNoticeList()
          this.$root.GET_NOTICE_NUM()
        }).catch(err => {
          window.alert(err)
        })
      }
    }
  }
</script>
<style scoped>
  .main {
    margin: 0 auto;
    margin-top: 90px;
    width: 800px;
    background: #fff;
  }

  .main .header {
    height: 60px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 32px;
    color: rgba(0,0,0,.87);
    border-bottom: 1px solid rgba(0,0,0,.12);
  }
  .main .header .title {
    font-size: 20px;
  }
  .main .header select{
    width: 80px;
    height: 32px;
    color: rgba(0, 0, 0, 0.87);
    border: 1px solid rgba(0, 0, 0, .12);
    padding-left: 8px;
    padding-right: 16px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance:none;
    position: relative;  
    background: url("./assets/icon_svg/icon_triangle_8.svg") no-repeat right center;
  }
  select:focus{	
    border: 1px solid #458df1; 
    outline: none;
  }

  .main .notice-item {
    padding: 16px 32px;
    border-bottom: 1px solid rgba(0,0,0,.12);
    justify-content: space-between;
    align-items: center;
    height: 80px;
  }
  .main .notice-item .left {
    justify-content: space-between;
    height: 100%;
  }
  .main .notice-item .date {
    font-size: 12px;
    color: rgba(0,0,0,.26);
  }
  .main .notice-item .name {
    color: #458df1;
    text-decoration: underline;
    cursor: pointer;
  }
  .main .notice-item .agree {
    color: #458df1;
    cursor: pointer;
  }
  .main .notice-item .refuse {
    color: rgba(0,0,0,.54);
    cursor: pointer;
  }

  .color54{
    color: rgba(0,0,0,.54);
  }
  .color87{
    color: rgba(0,0,0,.87);
  }

  .page {
    text-align: right;
    padding: 16px 32px;
  }

  /* 按钮样式 */
  .agree, .refuse {
    line-height: 36px;
    padding: 0 16px;
    font-weight: 600;
  }

  .agree:hover, .refuse:hover {
    background: rgba(0, 0, 0, .1);
  }
  .body {
    padding: 20px 32px;
    border-bottom: 1px solid rgba(0,0,0,.12);
    align-items: center;
  }
  .avatar {
    width: 180px;
    height: 180px;
    background: #8fc0ef;
    margin-right: 40px;
  }
  .user h3 {
    font-size: 28px;
    font-weight: normal;
  }
  .user p {
    font-size: 16px;
  }
  .user span {
    line-height: 2;
    color: rgba(0,0,0,.87);
  }
  .close {
    border: 1px solid rgba(0,0,0,.12);
    width: 64px;
    height: 32px;
    line-height: 30px;
    text-align: center;
    cursor: pointer;
  }
  .close:hover {
    background: #458df1;
    border-color: #458df1;
    color: #fff;
  }
  .body p{
    line-height: 3;
  }
</style>
