<template>
  <div>
    <mynav></mynav>
    <h2 class="header-state">
      <div>
        <span :class="{active: !public}" @click="public = false">我的项目</span>
        <span :class="{active: public}" @click="changeView">所有公开项目</span>
      </div>
    </h2>

    <div class="flex-row cards projects" v-if="!public">
      <div class="card bg-blue" v-for="p in projects">
        <router-link :to="'/project/' + p.u_projectID + '/patients/1'">
          <div class="color">
            <i class="iconfont icon-iconfontyiliaoicon" v-if="p.u_projectID === '1'"></i>
            <i class="iconfont icon-xindiantu" v-else-if="p.u_projectID === '2'"></i>
            <i class="iconfont icon-DDoSfanghufuwu" v-else></i>
          </div>
          <div class="content">
            <div class="name">{{ p.u_projectName }}</div>
            <div class="sub">{{ p.u_projectMem }}</div>
          </div>
        </router-link>
      </div>
    </div>

    <div class="flex-row cards projects-public" v-if="public">
      <div class="card bg-blue" v-for="p in publicList">
        <div class="color">
          <i class="iconfont icon-iconfontyiliaoicon" v-if="p.u_projectID === '1'"></i>
          <i class="iconfont icon-xindiantu" v-else-if="p.u_projectID === '2'"></i>
          <i class="iconfont icon-DDoSfanghufuwu" v-else></i>
        </div>
        <div class="content">
            <div class="name">{{ p.u_projectName }}</div>
            <div class="sub">{{ p.u_projectMem }}</div>
            <span v-if="p.is_join">已加入</span>
            <span class="join-btn" v-if="!p.is_join" @click="joinTeam(p.u_projectID)">申请加入</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import API from '../api.js'
  import Mynav from '../components/Header.vue'
  import MButton from '../lib/Button.vue'
  import Datepicker from '../lib/Date.vue'

  export default {
    name: 'Home',
    components: {
      Mynav,
      MButton,
      Datepicker
    },
    data () {
      return {
        public: false,
        publicList: [],
        projects: []
      }
    },
    mounted () {
      // 初始参与项目 List
      this.getProjects()
    },
    methods: {
      changeView () {
        this.public = true
        this.getAllProjects()
      },
      // 获取公开项目
      getAllProjects () {
        this.publicList = []
        API.GetAllProjects().then(res => {
          res.map(n => {
            this.publicList.push({
              u_projectMem: n.u_projectMem,
              u_projectID: n.u_projectID,
              u_projectName: n.u_projectName,
              is_join: false
            })
          })
          this.checkPublic()
        }).catch(error => {
          console.log(error)
        })
      },
      // 已申请判断
      checkPublic () {
        for (let id in this.publicList) {
          // console.log(this.projects[id].u_projectID + '===' + n.u_projectID)
          for (let myid in this.projects) {
            this.projects[myid].u_projectID === this.publicList[id].u_projectID ? this.publicList[id].is_join = true : ''
          }
        }
      },
      // 申请加入项目
      joinTeam (id) {
        console.log(id)
        API.ApplyProject({userid: this.$root.userid, projectid: id}).then(res => {
          this.toast({
            type: 'success',
            text: '申请成功',
            placement: 'bottom-left'
          })
        }).catch(error => {
          this.toast({
            type: 'error',
            text: '申请失败，请重新尝试',
            placement: 'bottom-left'
          })
          console.log(error)
        })
      },
      // 获取我参与的项目
      getProjects () {
        API.GetProjects({userid: this.$root.userid}).then(res => {
          res.map(n => this.projects.push(n))
        }).catch(error => {
          console.log(error)
        })
      }
    }
  }
</script>

<style scoped>
  .header-state {
    background: #fff;
    height: 48px;
    font-size: 16px;
    line-height: 48px;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.12);
    font-weight: 400;
  }

  .header-state>div {
    width: 756px;
    margin: 0 auto;
  }

  .header-state span {
    cursor: pointer;
    display: inline-block;
    height: 45px;
    text-align: center;
    width: 150px;
    color: #777;
  }

  .header-state span.active {
    color: #468df1;
    border-bottom: 3px solid #468df1;
  }

  .describe {
    margin: 24px auto;
    color: #999;
    width: 768px;
    font-size: 18px;
    font-weight: 600;
    /*font-family: "宋体";*/
  }

  .describe p {
    padding-left: 6px;
    line-height: 1;
  }

  .projects, .projects-public {
    margin: 24px auto;
    width: 768px;
    flex-wrap: wrap;
    /*justify-content: space-around;*/
  }

  .card {
    background: #fff;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.2), 0 1px 5px 0 rgba(0,0,0,0.12);
    line-height: 32px;
    width: 244px;
    height: 254px;
    margin: 8px 6px;
  }

  .card:hover {
    box-shadow: 0 2px 8px 0 rgba(0,0,0,.14), 0 3px 4px -5px rgba(0,0,0,.2), 0 1px 8px 0 rgba(0,0,0,.12);
  }

  .card .color {
    height: 136px;
    background-size: cover;
    background-position: center;
    background-color: #468df1;
    text-align: center;
  }

  .color>i {
    line-height: 136px;
    color: #fff;
    font-size: 52px;
  }

  .card .content {
    margin-top: 12px;
    font-size: 14px;
    padding-left: 12px;
    color: rgba(0, 0, 0, 0.8);
  }

  .card .content .name {
    font-size: 16px;
    font-weight: 600;
    line-height: 1;
    padding-bottom: 8px;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 10px;
  }

  .card .content .sub {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    margin-right: 15px;
    font-size: 13px;
    height: 48px;
    line-height: 16px;
    color: #888;
  }

  .join-btn {
    color: #468df1;
  }

</style>