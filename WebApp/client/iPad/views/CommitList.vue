<template>
<div id="commit-state">
	<div class="table" style="margin-top: 20px;">
		<div class="table-header flex-row">
			<span class="w14">患者姓名</span>
			<span class="w8">性别</span>
			<span class="w8">年龄</span>
			<span class="w16">电话</span>
			<span class="w14">登记时间</span>
			<span class="w14">提交时间</span>
			<span class="w12">提交人</span>
			<span class="w14">状态</span>
		</div>
		<up-refresh :elheight="tableHeight - 10" @isdown="getCaseList">
			<div class="table-body">
				<div class="table-tr flex-row" v-for="item in caseList">
					<span class="w14"><router-link :to="{name: 'commitlist-record', params: {mdid: item.mdid}, query: {redirct: 'commitlist'}}">{{item.u_patientname}}</router-link></span>
					<span class="w8">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
					<span class="w8">{{ item.u_birthday | dateToAge }}</span>
					<span class="w16">{{item.u_phone}}</span>
					<span class="w14">{{item.u_jointime}}</span>
					<span class="w14">{{item.updatetime | formatDate}}</span>
			    <span class="w12">{{item.username}}</span>
					<span class="w14">{{item.status === '1' ? '保存' : item.status === '2' ? '提交（待审核）' : item.status === '3' ? '被退回' : item.status === '4' ? '审核通过' : ''}}</span>
				</div>
				<div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../assets/empty/commit_empty.svg" alt="medicayun">
        </div>
			</div>
		</up-refresh>
	</div>
</div>
</template>

<script>
export default {
  name: 'CommitList',
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
      this.$http.GetCaseHistory({userid: this.$root.userid, projectid: this.projectid, type: 'commit', pagenum: this.pagenum}).then(rep => {
        if (this.pagenum === 1 && !rep.data.length) {
          this.empty = true
          return
        }
        rep.data.map(item => this.caseList.push(item))
        this.totalPage = Math.ceil(rep.totalnum / 30)
        if (rep.data.length > 0) this.pagenum ++
        this.moreText = this.totalPage > this.pagenum - 1 ? '下拉加载更多的记录...' : '没有更多的记录'
      }).catch(err => console.log(err))
    }
  }
}
</script>
