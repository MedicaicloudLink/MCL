<template>
<div id="search-state">
	<div class="table">
		<div class="table-header flex-row">
			<span class="w14">患者姓名</span>
			<span class="w10">性别</span>
			<span class="w10">年龄</span>
			<span class="w18">电话</span>
			<span class="w16">登记时间</span>
			<span class="w14">状态</span>
			<span class="w18">上次更新时间</span>
		</div>
    <up-refresh :elheight="tableHeight - 30">
      <div class="table-body">
        <div class="table-tr flex-row" v-for="item in searchList">
          <span class="w14"><router-link :to="{name: 'record', params: {mdid: item.mdid}, query: {redirct: 'search'}}">{{item.u_patientname}}</router-link></span>
          <span class="w10">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w10">{{ item.u_birthday | dateToAge }}</span>
          <span class="w18">{{item.u_phone}}</span>
          <span class="w16">{{item.u_jointime}}</span>
          <span class="w14">{{item.status === '1' ? '保存' : item.status === '2' ? '提交（待审核）' : item.status === '3' ? '被退回' : item.status === '4' ? '审核通过' : ''}}</span>
          <span class="w18">{{item.updatetime | formatDate}}</span>
        </div>
        <div class="empty" v-if="empty">
          <img src="../assets/empty/search_empty.svg" alt="medicayun">
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
    searchList: Array
  },
  data () {
    return {
      tableHeight: 0,
      empty: true
    }
  },
  watch: {
    searchList (val) {
      if (val.length === 0) {
        this.empty = true
      } else {
        this.empty = false
      }
    }
  },
  mounted () {
    const tableTop = this.$el.getElementsByClassName('table-body')[0].offsetTop
    this.tableHeight = window.innerHeight - tableTop
  }
}
</script>
<style scoped>
  .search-content {
    line-height: 56px;
    padding-left: 32px;
    color: rgba(0,0,0,.87);
    font-size: 16px;
  }
  .table {
    margin-top: 20px;
  }
</style>
