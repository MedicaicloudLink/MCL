<template>
<div id="follow-list">

	<div class="table" style="margin-top: 20px;">
		<div class="table-header flex-row">
			<span class="w14"><b style="margin-left: 20px;">患者姓名</b></span>
			<span class="w10">性别</span>
			<span class="w12">年龄</span>
			<span class="w18">电话</span>
			<span class="w14">随访逾期</span>
			<span class="w14">前一次随访</span>
			<span class="w18">提示说明</span>
		</div>
    <up-refresh :elheight="tableHeight" @isdown="getCaseList">
      <div class="table-body">
        <div class="table-tr flex-row" v-for="item in caseList">
          <span class="w14">
            <span v-if="item.type === 'new'" class="new"></span>
            <span v-if="item.type === 'wait'" class="wait"></span>
            <router-link :to="{name: 'frecord', params: {mdid: item.u_MDID, taskid: item.taskid}}">{{item.u_patientname}}</router-link>
          </span>
          <span class="w10">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w12">{{ item.u_birthday | dateToAge }}</span>
          <span class="w18">{{item.u_phone}}</span>
          <span class="w14">{{item.overday}}</span>
          <span class="w14">{{item.lastfollow | formatDate}}</span>
          <span class="w18" v-if="item.follow_status === '2'">{{item.other_reason}}</span>
          <span class="w18" v-else>{{item.follow_status | patientState}}</span>
        </div>
        <div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../../assets/empty/follow.png" alt="medicayun">
        </div> 
      </div>
    </up-refresh>
	</div>


</div>
</template>

<script>
export default {
  name: 'FollowList',
  props: {
    projectid: String
  },
  data () {
    return {
      empty: false,
      caseList: [],
      totalPage: 0,
      pagenum: 1,
      tableHeight: 0,
      moreText: '加载中...'
    }
  },
  mounted () {
    this.getCaseList()
    const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
    this.tableHeight = window.innerHeight - tableTop
  },
  methods: {
    getCaseList () {
      this.$http.GetFollowList({userid: this.$root.userid, projectid: this.projectid, pagenum: this.pagenum}).then(rep => {
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
#follow-list .table-tr {
  min-height: 30px;
  padding-top: 8px;
  padding-bottom: 8px;
  height: auto;
}
.new, .wait {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 8px;
}

.new {
  background: #24AE69;
}

.wait {
  background: #FCBC24;
}

</style>

