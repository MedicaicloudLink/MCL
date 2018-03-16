<template>
  <div id="project">
    <div class="header-bar">
      <div class="flex-row between">
        <div class="project-name">
          <router-link to="/home" class="back"><i class="iconfont icon-navback"></i></router-link>
          {{projectname}}
        </div>
        <div class="flex-row">
          <span class="search">
            <input placeholder="患者的姓名或电话" v-model="searchText" @focus="goSearch" @keyup.enter="getSearchList"/>
            <i class="iconfont icon-search" @click="getSearchList"></i>
          </span>
        </div>
      </div>
    </div>

    <div class="statebar flex-row">
      <span class="create"><span class="btn" :class="{active: newFlag}" @click="createNew">创建新病例</span></span>
      <div class="breadcrumbs flex-col">
        <div class="flex-row">
          <router-link :to="$route.path" v-if="!$route.query.redirct">{{ $route.meta.title }}</router-link>
          <router-link :class="{active: patientName}" :to="'/project/'+projectid+'/workdata'" v-if="$route.query.redirct === 'workdata'">我的工作日志</router-link>
          <router-link :class="{active: patientName}" :to="'/project/'+projectid+'/savelist'" v-if="$route.query.redirct === 'savelist'">已保存的病例</router-link>
          <router-link :class="{active: patientName}" :to="'/project/'+projectid+'/commitlist'" v-if="$route.query.redirct === 'commitlist'">已提交的病例</router-link>
          <router-link :class="{active: patientName}" :to="'/project/'+projectid+'/backlist'" v-if="$route.query.redirct === 'backlist'">被退回的病例</router-link>
          <router-link :class="{active: patientName}" :to="'/project/'+projectid+'/search'" v-if="$route.query.redirct === 'search'">搜索病例</router-link>
          <span class="flex-row" v-if="$route.query.redirct"><i class="iconfont icon-youxiang" style="color: rgba(0,0,0,.54);"></i>{{patientName}}</span>
        </div>
        <div v-if="$route.name !== 'workdata' && $route.name !== 'workdata-record'" style="color: rgba(0,0,0,.26); font-size: 12px;">同一中心或者小组内，病例记录将被共享</div>
      </div>
    </div>

    <div class="main flex-row" style="align-items: flex-start">
      <div class="side-menu" :style="{height: sideMenuHeight + 'px'}">
        <router-link :to="'/project/'+projectid+'/workdata'" class="flex-row">
          <i class="iconfont icon-work"></i><span>我的工作日志({{worknum}})</span>
        </router-link>
        <router-link :to="'/project/'+projectid+'/savelist'" class="flex-row">
          <i class="iconfont icon-save"></i><span>已保存的病例({{savenum}})</span>
        </router-link>

        <router-link :to="'/project/'+projectid+'/commitlist'" class="flex-row">
          <i class="iconfont icon-commit"></i><span>已提交的病例({{commitnum}})</span>
        </router-link>

        <router-link :to="'/project/'+projectid+'/backlist'" class="flex-row">
          <i class="iconfont icon-back"></i><span>被退回的病例({{backnum}})</span>
        </router-link>
      </div>

      <router-view class="work-area" :projectid="projectid" :searchList="searchList" :projectForm="projectform" @getCaseState="getCaseState"></router-view>
    </div>
  </div>
</template>

<script>
import EventListener from '../tool/EventListener.js'
export default {
  data () {
    return {
      projectid: this.$route.params.projectid,
      projectname: '',
      projectform: [],
      patientName: '',
      newFlag: false,
      worknum: '',
      savenum: '',
      commitnum: '',
      backnum: '',
      searchText: '',
      searchList: [],
      sideMenuHeight: 0
    }
  },
  watch: {
    '$route': function (val) {
      if (val.query.redirct) {
        this.patientName = ''
        this.getPatientName()
      }
      if (val.name === 'newrecord') {
        this.newFlag = true
      } else {
        this.newFlag = false
      }
    }
  },
  created () {
    this.sideMenuHeight = window.innerHeight - 112
    this.openProjectTime()
    this.getProjectInfo()
    this.patientName = ''
    if (this.$route.name === 'newrecord') {
      this.newFlag = true
    } else {
      this.newFlag = false
    }
    this.getPatientName()
    this.getCaseState()
    // 监听窗口变化，重绘侧边栏位置
    this._closeEvent = EventListener.listen(window, 'resize', (e) => {
      this.sideMenuHeight = window.innerHeight - 112
    })
  },
  destroyed () { if (this._closeEvent) this._closeEvent.remove() },
  methods: {
    // 跳转创建新病历
    createNew () {
      if (!this.newFlag) this.$router.push({name: 'newrecord', query: { id: 'new' }})
    },
    // 搜索患者
    goSearch () {
      this.$router.push({name: 'search'})
      this.searchList = []
    },
    getSearchList () {
      this.searchList = []
      this.$http.SearchCase({userid: this.$root.userid, projectid: this.$route.params.projectid, pagenum: 1, search: this.searchText}).then(rep => {
        rep.data.map(item => this.searchList.push(item))
      }).catch(err => console.log(err))
    },
    // 获取项目基本信息
    getProjectInfo () {
      this.$http.GetProjectInfo({userid: this.$root.userid, projectid: this.projectid}).then((response) => {
        this.projectname = response[0].u_projectName
        this.projectform = JSON.parse(response[0].formdata)
      }).catch((err) => {
        console.log(err)
      })
    },
    // 获取患者姓名
    getPatientName () {
      this.$http.GetPatientBase({mdid: this.$route.params.mdid}).then((response) => {
        this.patientName = response[0].u_patientname
      }).catch((err) => {
        console.log(err)
      })
    },
    // 获取项目病历状态
    getCaseState () {
      this.$http.GetCaseState({userid: this.$root.userid, projectid: this.projectid}).then(rep => {
        this.worknum = rep.workdatanum
        this.savenum = rep.savenum
        this.commitnum = rep.commitnum
        this.backnum = rep.returnnum
      }).catch(err => console.log(err))
    },
    // 记录打开项目时间
    openProjectTime () {
      this.$http.OpenProjectTime({userid: this.$root.userid, projectid: this.projectid}).then((response) => {
        console.log('记录打开时间成功')
      }).catch((err) => {
        console.log(err)
      })
    }
  }
}
</script>

<style scoped>
  .header-bar {
    position: fixed;
    background: #468df1;
    color: #fff;
    top: 0;
    left: 0;
    height: 56px;
    z-index: 1000;
    width: 100%;
  }
  .header-bar>div{
    padding-left: 32px;
    font-size: 18px;
    height: 100%;
    align-items: center;
  }
  .project-name {
    max-width: 500px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .back{
    color: #fff;
    margin-right: 16px;
  }
  .back i {
    color: #fff;
  }

  .search {
    position: relative;
    margin-right: 32px;
  }
  .search i {
    position: absolute;
    font-size: 16px;
    right: 0;
    top: 0;
    width: 40px;
    text-align: center;
    line-height: 40px;
    height: 40px;
    color: #fff;
  }
  .search input {
    padding-left: 12px;
    background: rgba(255, 255, 255, .2);
    border: none;
    width: 420px;
    height: 40px;
    border-radius: 3px;
    color: #fff;
  }
  .search ::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color: rgba(255, 255, 255, 0.7);
  }
  .search :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
    color: rgba(255, 255, 255, 0.7);
    opacity:  1;
  }
  .search ::-moz-placeholder { /* Mozilla Firefox 19+ */
    color: rgba(255, 255, 255, 0.7);
    opacity:  1;
  }
  .search :-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: rgba(255, 255, 255, 0.7);
  }
  .search input:focus {
    background: #fff;
    color: #000;
    outline: none;
  }
  .search input:focus+i {
    color: #555;
  }
  .search input:focus::-webkit-input-placeholder {
    color: #888;
  }
  .search input:focus:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
    color: rgba(255, 255, 255, 0.7);
    opacity:  1;
  }
  .search input:focus::-moz-placeholder { /* Mozilla Firefox 19+ */
    color: rgba(255, 255, 255, 0.7);
    opacity:  1;
  }
  .search input:focus:-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: rgba(255, 255, 255, 0.7);
  }

  .avatar {
   display: flex;
   align-items: center;
   justify-content: center;
   width: 56px;
   height: 56px;
   font-size: 12px;
   text-align: center;
   color: #222;
   overflow: hidden;
 }
 .avatar>span, .avatar-img {
   display: inline-block;
   height: 32px;
   width: 32px;
   line-height: 32px;
   background: #fff;
   border-radius: 50%;
 }


.statebar {
  position: fixed;
  height: 56px;
  top: 56px;
  left: 0;
  width: 100%;
  background: #fff;
  display: flex;
  align-items: center;
  z-index: 1000;
  /* box-shadow: 0 4px 5px 0 rgba(0,0,0,0.14), 0 1px 10px 0 rgba(0,0,0,0.12), 0 2px 4px -1px rgba(0,0,0,0.2); */
}

.statebar .create {
  width: 246px;
  height: 56px;
  display: flex;
  padding-left: 24px;
  align-items: center;
  border-bottom: 2px solid rgba(0,0,0,.12);
}
.statebar .create .btn{
  width: 120px;
  color: #fff;
  background: #5d9cf3;
  border: 1px #fff solid;
  text-align: center;
  line-height: 32px;
  cursor: pointer;
  border-radius: 3px;
}
.statebar .create .btn.active{
  color: rgba(255,255,255,.3);
  border: 1px rgba(255,255,255,.3) solid;
  cursor: no-drop;
} 

.breadcrumbs {
  font-size: 16px;
  padding-left: 32px;
  height: 56px;
  flex: 1;
  justify-content: center;
  border-bottom: 2px solid rgba(0,0,0,.12);
}
.breadcrumbs a.active{
  color: rgba(0,0,0,.54);
}
.main {
  padding-top: 112px;
}

.side-menu{
  width: 246px;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 112px;
  padding-top: 16px;
}

.side-menu a{
  height: 64px;
  width: 100%;
  font-size: 16px;
  padding: 10px 0;
  padding-left: 24px;
  color: #333;
}

.side-menu a:hover {
  background: rgba(0, 0, 0, .03);
}

.side-menu a i {
  color: #777;
  margin-right: 16px;
  font-size: 15px;
}
.side-menu a .num {
  margin-left: 32px;
  font-size: 12px;
}
.side-menu a.router-link-active {
  background: rgba(0, 0, 0, .08);
  font-weight: 600;
  color: #222;
  border-radius: 2px;
}
.side-menu a.router-link-active i {
  color: #444;
  font-weight: 400;
}


.work-area {
  margin-left: 246px;
  width: 100%;
  height: 100%;
  overflow-y: scroll;
}
</style>