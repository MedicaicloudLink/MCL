<template>
<div id="follow-list">

	<div class="table" style="margin-top: 20px;">
		<div class="table-header flex-row">
			<span class="w14">患者姓名</span>
			<span class="w10">性别</span>
			<span class="w12">年龄</span>
			<span class="w18">电话</span>
			<span class="w14">状态</span>
			<span class="w14">更新人</span>
			<span class="w18">提交的时间</span>
		</div>
    <up-refresh :elheight="tableHeight" @isdown="getCaseList">
      <div class="table-body">
        <div class="table-tr flex-row" v-for="item in caseList">
          <span class="w14">
            <router-link :to="{name: 'crecord', params: {mdid: item.u_MDID, taskid: item.u_follow_id, recordid: item.recordid}}">{{item.u_patientname}}</router-link>
          </span>
          <span class="w10">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w12">{{ item.u_birthday | dateToAge }}</span>
          <span class="w18">{{item.u_phone}}</span>
          <span class="w14">{{item.u_follow_status | patientState}}</span>
          <span class="w14">{{item.username}}</span>
          <span class="w18">{{item.updatetime | formatDate}}</span>
        </div>
        <div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../../assets/empty/commit.png" alt="medicayun">
        </div> 
      </div>
    </up-refresh>
	</div>


</div>
</template>

<script>
export default {
  name: 'SaveList',
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
      this.$http.GetFollowStateList({userid: this.$root.userid, projectid: this.projectid, pagenum: this.pagenum, type: 'commit'}).then(rep => {
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
