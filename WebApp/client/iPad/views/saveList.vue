<template>
<div id="save-state">

	<div class="table" style="margin-top: 20px;">
		<div class="table-header flex-row">
			<span class="w14">患者姓名</span>
			<span class="w8">性别</span>
			<span class="w10">年龄</span>
			<span class="w16">电话</span>
			<span class="w16">登记时间</span>
			<span class="w14">更新人</span>
			<span class="w14">上次更新时间</span>
      <span class="w8">操作</span>
		</div>
    <up-refresh :elheight="tableHeight - 20" @isdown="getCaseList">
      <div class="table-body">
        <div class="table-tr flex-row" v-for="item, index in caseList">
          <span class="w14"><router-link :to="{name: 'savelist-record', params: {mdid: item.mdid}, query: {redirct: 'savelist'}}">{{item.u_patientname}}</router-link></span>
          <span class="w8">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w10">{{ item.u_birthday | dateToAge }}</span>
          <span class="w16">{{item.u_phone}}</span>
          <span class="w16">{{item.u_jointime}}</span>
          <span class="w14">{{item.username}}</span>
          <span class="w14">{{item.updatetime | formatDate}}</span>
          <span class="w8" v-if="item.u_createuserid != $root.userid">--</span>
          <span class="w8" v-else style="color: #e51c23; cursor: pointer;text-decoration: underline;" @click="deleteSavePatient(index, item.mdid)">删除</span>
        </div>
        <div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../assets/empty/save_empty.svg" alt="medicayun">
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
      this.$http.GetCaseHistory({userid: this.$root.userid, projectid: this.projectid, type: 'save', pagenum: this.pagenum}).then(rep => {
        if (this.pagenum === 1 && !rep.data.length) {
          this.empty = true
          return
        }
        rep.data.map(item => this.caseList.push(item))
        this.totalPage = Math.ceil(rep.totalnum / 30)
        if (rep.data.length > 0) this.pagenum ++
        this.moreText = this.totalPage > this.pagenum - 1 ? '下拉加载更多的记录...' : '没有更多的记录'
      }).catch(err => console.log(err))
    },
    deleteSavePatient (index, mdid) {
      let vm = this
      this.confirm({
        title: '删除保存列表中的患者',
        message: '确定要删除此患者，删除后数据无法恢复？',
        onConfirm () {
          vm.$http.DeleteSavePatient({userid: vm.$root.userid, mdid: mdid}).then(() => {
            vm.toast({type: 'success', text: '删除成功！'})
            vm.caseList.splice(index, 1)
          }).catch((err) => vm.toast({type: 'error', text: err}))
        }
      })
    }
  }
}
</script>
