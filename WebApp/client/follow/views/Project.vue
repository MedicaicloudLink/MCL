<template>
  <div id="project">
    <div class="header-bar">
      <div class="flex-row between">
        <div>
          <router-link to="/home" class="back"><i class="iconfont icon-navback"></i></router-link>
          {{projectname}}
        </div>
        <div class="flex-row">
          <span class="search">
            <input placeholder="患者的姓名或电话" v-model="searchText" @focus="goSearch" />
            <i class="iconfont icon-search"></i>
          </span>
          <span class="avatar">
            <span v-if="$root.avatar === ''">{{ $root.shortname.substring(0, 3) | uppercase }}</span>
            <img :src="'http://' + $root.avatar" alt="" class="avatar-img" v-else>
          </span>
        </div>
      </div>
    </div>

    <div class="main flex-row" style="align-items: flex-start;padding-top: 56px;">
      <div class="side-menu" :style="{height: sideMenuHeight + 'px'}">
        <router-link :to="'/project/'+projectid+'/work'" class="flex-row work-log">
          <i class="iconfont icon-work"></i><span>我的工作日志</span>
        </router-link>

        <span class="split"></span>

        <router-link :to="'/project/'+projectid+'/follow'" class="flex-row">
          <i class="iconfont icon-patient"></i><span>应随访患者（{{follownum}}）</span>
        </router-link>

        <router-link :to="'/project/'+projectid+'/save'" class="flex-row">
          <i class="iconfont icon-save"></i><span>已保存（{{savenum}}）</span>
        </router-link>

        <router-link :to="'/project/'+projectid+'/commit'" class="flex-row">
          <i class="iconfont icon-commit"></i><span>已提交（{{commitnum}}）</span>
        </router-link>

        <router-link :to="'/project/'+projectid+'/back'" class="flex-row">
          <i class="iconfont icon-back"></i><span>被退回的记录（{{backnum}}）</span>
        </router-link>
      </div>

      <router-view class="work-area" :style="{height: sideMenuHeight + 'px'}"
       :projectid="projectid"
       :searchText="searchText"
       @getCaseState="getCaseState"></router-view>
    </div>
  </div>
</template>

<script>
import EventListener from '../tool/EventListener.js'
export default {
  data () {
    return {
      projectid: this.$route.params.projectid,
      // projectname: '',
      worknum: '',
      follownum: '',
      savenum: '',
      commitnum: '',
      backnum: '',
      todaycommit: 0,
      todaysave: 0,
      searchText: '',
      sideMenuHeight: 0
    }
  },
  watch: {
    '$route' () {
      this.getCaseState()
    }
  },
  computed: {
    projectname () {
      let name = ''
      this.$root.projectList.map(p => {
        if (p.projectid === this.projectid) name = p.projectname
      })

      return name
    }
  },
  created () {
    // 记录项目打开时间
    this.$root.OPEN_PROJECT(this.projectid)

    this.sideMenuHeight = window.innerHeight - 56
    this.getCaseState()
    // 监听窗口变化，重绘侧边栏位置
    this._closeEvent = EventListener.listen(window, 'resize', (e) => {
      this.sideMenuHeight = window.innerHeight - 56
    })
  },
  destroyed () { if (this._closeEvent) this._closeEvent.remove() },
  methods: {
    goSearch () { this.$router.push({name: 'search'}) },
    // 获取项目病历状态
    getCaseState () {
      this.$http.GetProjectState({userid: this.$root.userid, projectid: this.projectid}).then(rep => {
        this.worknum = rep.mytodaynum
        this.todaycommit = rep.mytodaycommitnum
        this.todaysave = rep.mytodaysavenum
        this.follownum = rep.shouldnum
        this.savenum = rep.savenum
        this.commitnum = rep.commitnum
        this.backnum = rep.returnnum
      }).catch(err => console.log(err))
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
    width: 100%;
  }
  .header-bar>div{
    padding-left: 32px;
    font-size: 18px;
    height: 100%;
    align-items: center;
  }
  .flex-row.between {
    justify-content: space-between;
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
    margin-right: 16px;
  }
  .search i {
    position: absolute;
    font-size: 16px;
    left: 12px;
    top: 12px;
    color: #fff;
  }
  .search input {
    padding-left: 42px;
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
   cursor: pointer;
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


.side-menu {
  width: 226px;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 56px;
  padding-top: 16px;
  /* background: #fff; */
}

.side-menu a{
  font-size: 16px;
  padding: 10px 0;
  padding-left: 24px;
  color: #333;
  height: 36px;
}

.side-menu a:hover {
  background: rgba(0, 0, 0, .03);
}

.side-menu a i {
  color: #777;
  margin-right: 16px;
  font-size: 15px;
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

.work-log {
  height: 36px;
}

.split {
  display: inline-block;
  width: 100%;
  border-top: 1px solid #ddd;
  margin-top: 16px;
  margin-bottom: 16px;
}


.work-area {
  margin-left: 226px;
  width: 100%;
  height: 100%;
  overflow-y: scroll;
}

.work-area::-webkit-scrollbar-track {
  background: #eee;
}

@media screen and (max-width: 1280px) {
  .side-menu {
    width: 180px;
  }

  .side-menu a {
    font-size: 14px;
    padding-left: 12px;
  }

  .side-menu a i {
    margin-right: 12px;
  }

  .work-area {
    margin-left: 180px;
  }
}
</style>