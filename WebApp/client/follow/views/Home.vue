<template>
<div id="home">
  <mynav class="header-fix"></mynav>

  <div class="form-list" v-if="$root.projectList.length !== 0 || $root.endProject.length !== 0">
    <div class="list-header flex-row">
      <span style="flex: 1;">进行中的项目</span>
      <span class="w160">上次打开的时间</span>
      <span class="w100">应随访</span>
      <span class="w100">我提交</span>
      <span class="w100">已保存</span>
    </div>
    <div class="lists">
      <div class="list flex-row" v-for="(item, index) in $root.projectList">
        <router-link :to="'/project/'+item.projectid+'/work'" class="flex-row name">
          <i class="iconfont icon-project"></i>
          <span class="name-text">{{ item.projectname }}</span>
        </router-link>
        <span class="w160">{{ item.opentime | formatDate }}</span>
        <span class="w100">{{item.shouldnum}}</span>
        <span class="w100">{{item.commitnum}}</span>
        <span class="w100">{{item.savenum}}</span>
      </div>
    </div>

    <div class="end-team">
      <div class="list-header flex-row" style="margin-top: 50px;">
        <span style="flex: 1;">已结束的项目</span>
        <span class="w160">上次打开的时间</span>
        <span class="w100">应随访</span>
        <span class="w100">我提交</span>
        <span class="w100">已保存</span>
      </div>
      <div class="lists">
        <div class="list flex-row" v-for="(item, index) in $root.endProject">
          <span class="flex-row name">
            <i class="iconfont icon-project"></i>
            <span class="name-text">{{ item.projectname }}</span>
          </span>
          <span class="w160">{{ item.opentime | formatDate }}</span>
          <span class="w100">{{item.shouldnum}}</span>
          <span class="w100">{{item.commitnum}}</span>
          <span class="w100">{{item.savenum}}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="empty" v-else style="margin-top: 160px;">
    <img src="../assets/empty/home.png" alt="medicayun">
  </div>
</div>
</template>

<script>
import Mynav from './Header.vue'
export default {
  name: 'Home',
  components: { Mynav },
  mounted () { this.GET_PROJECT_LIST() },
  methods: {
    GET_PROJECT_LIST () {
      this.$http.GetProjects({userid: this.$root.userid}).then((response) => {
        this.$root.projectList = []
        this.$root.endProject = []
        for (let i in response) {
          response[i].type === 1 ? this.$root.projectList.push(response[i]) : this.$root.endProject.push(response[i])
        }
      }).catch(err => console.log(err))
    }
  }
}
</script>

<style>
.form-list {
  width: 1000px;
  margin: 96px auto 20px;
}

.header-fix {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100%;
}

.btn.link {
  display: inline-block;
  background: #468df1;
  padding: 8px 16px;
  color: #fff;
  font-size: 14px;
  font-weight: 400;
}

.lists {
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .2), 0 1px 5px 0 rgba(0, 0, 0, .12);
}

.list a {
  flex: 1;
  color: #333;
  line-height: 1;
  margin-right: 30px;
  overflow: hidden;
}

.name {
  flex: 1;
}

.list a span{
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.list {
  justify-content: space-between;
  align-items: center;
  background: #fff;
  padding: 14px 16px;
  font-size: 16px;
  color: rgba(0, 0, 0, .54);
  border-bottom: 1px solid #eee;
}

.list i {
  color: rgba(0, 0, 0, .54);
  font-size: 16px;
  margin-right: 16px;
}

.list:hover {
  background: #f2f2f2;
}

.end-team .list i {
  color: #888;
}

.form-state {
  width: 142px;
  font-size: 14px;
}

.operate {
  width: 250px;
  font-size: 14px;
}

.list-header {
  font-weight: 600;
  color: rgba(0,0,0,.54);
  border: none;
  line-height: 48px;
  padding: 0 16px;
}

.operate span {
  display: inline-block;
  width: 182px;
}

.icon-delete2, .icon-copy {
  display: inline-block;
  width: 28px;
  text-align: center;
  cursor: pointer;
}

.icon-copy {
  color: #999;
}

.empty {
  text-align: center;
  margin: 80px auto;
}

.w100 {
  width: 100px;
}
.w160 {
  width: 160px;
}

</style>