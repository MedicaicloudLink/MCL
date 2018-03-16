<template>
<div id="workdata">

  <div class="count flex-row">
    <div class="card flex-col" style="border-right: 1px rgba(0,0,0,.12) solid;"><span>今日提交的病例</span><span class="big-num">{{$parent.todaycommit}}</span></div>
    <div class="card flex-col"><span>今日保存的病例</span><span class="big-num">{{$parent.todaysave}}</span></div>
  </div>

	<div class="table">
		<div class="table-header flex-row">
			<span class="flex-row w14">
				<span style="cursor: pointer;">患者姓名</span>
			</span>
			<span class="flex-row w10">
				<span style="cursor: pointer;">性别</span>
			</span>
			<span class="flex-row w10">
				<span style="cursor: pointer;">年龄</span>
			</span>
			<span class="w18">电话</span>
			<span class="w16">ID</span>
			<span class="flex-row w14">
				<span style="cursor: pointer;">状态</span>
			</span>
			<span class="flex-row w18">
				<span style="cursor: pointer;">上次更新时间</span>
			</span>
		</div>
    <up-refresh :elheight="tableHeight" @isdown="getWorkData">
      <div class="table-body">
        <div class="table-tr flex-row" v-for="item in workDataList">
          <span class="w14">
            <router-link :to="{name: 'wrecord', params: {mdid: item.u_MDID, taskid: item.u_follow_id, recordid: item.recordid}}">{{item.u_patientname}}</router-link>
          </span>
          <span class="w10">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w10">{{ item.u_birthday | dateToAge }}</span>
          <span class="w18">{{item.u_phone}}</span>
          <span class="w16">{{item.u_MDID}}</span>
          <span class="w14">{{item.statusnote}}</span>
          <span class="w18">{{item.updatetime | formatDate}}</span>
        </div>
        <div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../../assets/empty/work.png" alt="medicayun">
        </div> 
      </div>
    </up-refresh>
	</div>


</div>
</template>

<script>
export default {
  name: 'Work',
  props: {
    projectid: String
  },
  data () {
    return {
      empty: false,
      workDataList: [],
      pagenum: 1,
      totalPage: 0,
      tableHeight: 0,
      moreText: '加载中...'
    }
  },
  mounted () {
    this.getWorkData()
    const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
    this.tableHeight = window.innerHeight - tableTop
  },
  watch: {
    // 重新排序
    type () {
      this.pagenum = 1
      this.getWorkData()
    },
    sort () {
      this.pagenum = 1
      this.getWorkData()
    }
  },
  methods: {
    change (type) {
      this.type = type
      if (this.oldType === this.type) {
        this.sort = !this.sort
      } else {
        this.oldType = type
        this.sort = true
      }
    },
    // 获取我的工作日志数据
    getWorkData () {
      if (this.pagenum === 1) {
        this.workDataList = []
      }
      // let sort = this.sort ? 'desc' : 'asc'
      this.$http.GetWorkList({userid: this.$root.userid, projectid: this.projectid, pagenum: this.pagenum, type: this.type, sort: ''}).then(rep => {
        if (this.pagenum === 1 && !rep.result.length) {
          this.empty = true
          return
        }
        rep.result.map(item => this.workDataList.push(item))
        this.totalPage = Math.ceil(rep.totalnum / 30)
        if (rep.result.length > 0) this.pagenum ++
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
