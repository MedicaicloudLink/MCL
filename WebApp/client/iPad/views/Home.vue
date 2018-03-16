<template>
  <div id="home">
    <Navigation></Navigation>
    <div class="form-list" v-if="onProjectList.length !== 0 || endProjectList.length !== 0">
      <div class="list-header flex-row">
        <span style="flex: 1;">进行中的项目</span>
        <span class="w160">上次打开的时间</span>
        <span class="w120">已提交病例</span>
        <span class="w120">已保存病例</span>
        <span class="w100">项目管理员</span>
      </div>
      <div class="lists">
        <div class="list flex-row" v-for="(item, index) in onProjectList">
          <router-link :to="'/project/'+item.projectid+'/workdata'" class="flex-row name">
            <i class="iconfont icon-project"></i>
            <span class="name-text">{{ item.u_projectName }}</span>
          </router-link>
          <span class="w160">{{ item.opentime | formatDate }}</span>
          <span class="w120">{{item.commitnum}}</span>
          <span class="w120">{{item.savenum}}</span>
          <span class="w100">{{item.adminname}}</span>
        </div>
      </div>

      <div class="end-team">
        <div class="list-header flex-row" style="margin-top: 50px;">
          <span style="flex: 1;">已结束的项目</span>
          <span class="w160">上次打开的时间</span>
          <span class="w120">已提交病例</span>
          <span class="w120">已保存病例</span>
          <span class="w100">项目管理员</span>
        </div>
        <div class="lists">
          <div class="list flex-row" v-for="(item, index) in endProjectList">
            <span class="flex-row name">
              <i class="iconfont icon-project"></i>
              <span class="name-text">{{ item.u_projectName }}</span>
            </span>
            <span class="w160">{{ item.opentime | formatDate }}</span>
            <span class="w120">{{item.commitnum}}</span>
            <span class="w120">{{item.savenum}}</span>
            <span class="w100">{{item.adminname}}</span>
          </div>
        </div>
      </div>

    </div>

    <div class="empty" v-if="empty" style="margin-top: 160px;">
      <img src="../assets/empty/home_empty.svg" alt="medicayun">
    </div> 
  </div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  export default {
    name: 'Home',
    data () {
      return {
        endProjectList: [],
        onProjectList: [],
        empty: false
      }
    },
    components: {
      Navigation
    },
    created () {
      this.getProjectList()
    },
    methods: {
      getProjectList () {
        this.$http.GetProjects({userid: this.$root.userid}).then((response) => {
          if (!response.length) {
            this.empty = true
            return
          }
          for (let i in response) {
            response[i].type === 1 ? this.onProjectList.push(response[i]) : this.endProjectList.push(response[i])
          }
        }).catch((err) => {
          console.log(err)
        })
      }
    }
  }
</script>
<style scoped>
.form-list {
  width: 1000px;
  margin: 96px auto 20px;
}

.list-header {
  font-weight: 600;
  color: rgba(0,0,0,.54);
  border: none;
  line-height: 48px;
  padding: 0 16px;
}

.lists {
  box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.2), 0 1px 5px 0 rgba(0,0,0,.12);
}

.list {
  justify-content: space-between;
  align-items: center;
  background: #fff;
  height: 56px;
  color: rgba(0, 0, 0, .54);
  border-top: 1px solid rgba(0, 0, 0, .12);
  padding: 0 16px;
}
.list:hover {
  background: #f2f2f2;
}
.name {
  flex: 1;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.name i {
  color: #777;
  font-size: 15px;
}

.list .name .name-text {
  font-size: 16px; 
  color: #333;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
  flex: 1;
  margin-right: 20px;
  margin-left: 16px;
  font-size: 16px;
}

.end-team .list .name-text {
  color: #777;
}
.end-team i {
  color: #999;
}

.w160 {
  width: 160px;
}
.w120 {
  width: 120px;
}
.w100 {
  width: 100px;
}
</style>
