<template>
<div id="serach-table">

	<div class="table">
		<div class="table-header flex-row">
			<span class="flex-row w14">患者姓名</span>
			<span class="flex-row w10">性别</span>
			<span class="flex-row w10">年龄</span>
			<span class="w18">电话</span>
			<span class="w16">ID</span>
			<span class="flex-row w14">状态</span>
			<span class="flex-row w18">上次更新时间</span>
		</div>
    <up-refresh :elheight="tableHeight" @isdown="getCaseList">
      <div class="table-body">
        <div class="table-tr flex-row" v-for="item in caseList">
          <span class="w14">
            <router-link :to="{name: 'wrecord', params: {mdid: item.u_MDID, taskid: item.u_follow_id, recordid: item.recordid}}">{{item.u_patientname}}</router-link>
          </span>
          <span class="w10">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w10">{{ item.u_birthday | dateToAge }}</span>
          <span class="w18">{{item.u_phone}}</span>
          <span class="w16">{{item.u_MDID}}</span>
          <span class="w14">{{item.u_follow_status | patientState}}</span>
          <span class="w18">{{item.updatetime | formatDate}}</span>
        </div>
        <div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../../assets/empty/search.png" alt="medicayun">
        </div> 
      </div>
    </up-refresh>
	</div>


</div>
</template>

<script>
export default {
  name: 'Search',
  props: {
    projectid: String,
    searchText: String
  },
  data () {
    return {
      empty: false,
      caseList: [],
      pagenum: 1,
      totalPage: 0,
      tableHeight: 0,
      moreText: '加载中...'
    }
  },
  watch: {
    searchText (val) {
      if (val) {
        this.moreText = '搜索中...'
        this.pagenum = 1
        this.caseList = []
        this.getCaseList()
        this.empty = false
      } else {
        this.empty = true
        this.caseList = []
      }
    }
  },
  mounted () {
    this.getCaseList()
    const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
    this.tableHeight = window.innerHeight - tableTop
  },
  methods: {
    // 获取我的工作日志数据
    getCaseList () {
      this.$http.GetFollowStateList({userid: this.$root.userid, projectid: this.projectid, pagenum: this.pagenum, type: 'save', search: this.searchText}).then(rep => {
        if (this.pagenum === 1 && !rep.data.length) {
          this.empty = true
          return
        }
        if (rep.data.length > 0) this.pagenum ++
        rep.data.map(item => this.caseList.push(item))
        this.totalPage = Math.ceil(rep.totalnum / 30)
        this.moreText = this.totalPage > this.pagenum - 1 ? '下拉加载更多的记录...' : '没有更多的记录'
      }).catch(err => console.log(err))
    }
  }
}
</script>

<style scoped>
.count {
  margin: 20px 32px;
  justify-content: center;
  background: #fff;
  padding: 16px 0;
  height: 90px;
  color: rgba(0,0,0,.54);
}
.count .card {
  text-align: center;
  flex: 1;
}
.count .card .big-num {
  font-size: 48px;
  color: #468df1;
  font-family: Tahoma;
}
</style>
