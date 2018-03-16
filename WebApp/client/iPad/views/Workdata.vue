<template>
<div id="workdata">

  <div class="count flex-row">
    <div class="card flex-col" style="border-right: 1px rgba(0,0,0,.12) solid;"><span>今日提交的病例</span><span class="big-num">{{commitNum}}</span></div>
    <div class="card flex-col"><span>今日保存的病例</span><span class="big-num">{{saveNum}}</span></div>
  </div>

	<div class="table">
		<div class="table-header flex-row">
			<span class="flex-row w14" @click="change('name')">
				<span style="cursor: pointer;">患者姓名</span>
        <img src="../assets/icon_svg/sort_default.svg" v-if="type !== 'name'"/>
        <img src="../assets/icon_svg/sort_down.svg" v-if="type === 'name' && sort"/>
        <img src="../assets/icon_svg/sort_up.svg" v-if="type === 'name' && !sort"/>
			</span>
			<span class="flex-row w10" @click="change('gender')">
				<span style="cursor: pointer;">性别</span>
        <img src="../assets/icon_svg/sort_default.svg" v-if="type !== 'gender'"/>
        <img src="../assets/icon_svg/sort_down.svg" v-if="type === 'gender' && sort"/>
        <img src="../assets/icon_svg/sort_up.svg" v-if="type === 'gender' && !sort"/>
			</span>
			<span class="flex-row w10" @click="change('age')">
				<span style="cursor: pointer;">年龄</span>
        <img src="../assets/icon_svg/sort_default.svg" v-if="type !== 'age'"/>
        <img src="../assets/icon_svg/sort_down.svg" v-if="type === 'age' && sort"/>
        <img src="../assets/icon_svg/sort_up.svg" v-if="type === 'age' && !sort"/>
			</span>
			<span class="w18">电话</span>
			<span class="w16">登记时间</span>
			<span class="flex-row w14" @click="change('status')">
				<span style="cursor: pointer;">状态</span>
        <img src="../assets/icon_svg/sort_default.svg" v-if="type !== 'status'"/>
        <img src="../assets/icon_svg/sort_down.svg" v-if="type === 'status' && sort"/>
        <img src="../assets/icon_svg/sort_up.svg" v-if="type === 'status' && !sort"/>
			</span>
			<span class="flex-row w18" @click="change('updatetime')">
				<span style="cursor: pointer;">上次更新时间</span>
        <img src="../assets/icon_svg/sort_default.svg" v-if="type !== 'updatetime'"/>
        <img src="../assets/icon_svg/sort_down.svg" v-if="type === 'updatetime' && sort"/>
        <img src="../assets/icon_svg/sort_up.svg" v-if="type === 'updatetime' && !sort"/>
			</span>
		</div>
    <up-refresh :elheight="tableHeight - 20" @isdown="getWorkData">
      <div class="table-body">
        <div class="table-tr flex-row" v-if="workDataList.length" v-for="item in workDataList">
          <span class="w14"><router-link :to="{name: 'workdata-record', params: {mdid: item.mdid}, query: {redirct: 'workdata'}}">{{item.u_patientname}}</router-link></span>
          <span class="w10">{{item.u_gender === '1' ? '男' : item.u_gender === '2' ? '女' : ''}}</span>
          <span class="w10">{{ item.u_birthday | dateToAge }}</span>
          <span class="w18">{{item.u_phone}}</span>
          <span class="w16">{{item.u_jointime}}</span>
          <span class="w14">{{item.status === '1' ? '保存' : item.status === '2' ? '提交（待审核）' : item.status === '3' ? '被退回' : item.status === '4' ? '审核通过' : ''}}</span>
          <span class="w18">{{item.updatetime | formatDate}}</span>
        </div>
        <div class="more" v-if="!empty">{{ moreText }}</div>
        <div class="empty" v-if="empty">
          <img src="../assets/empty/workdata_empty.svg" alt="medicayun">
        </div> 
      </div>
    </up-refresh>
	</div>


</div>
</template>

<script>
export default {
  name: 'WorkData',
  props: {
    projectid: String
  },
  data () {
    return {
      empty: false,
      commitNum: 0,
      saveNum: 0,
      oldType: '',
      type: '',
      sort: true,
      workDataList: [],
      pagenum: 1,
      totalPage: 0,
      tableHeight: 0,
      moreText: '加载中...'
    }
  },
  mounted () {
    this.todayCount()
    this.getWorkData()
    const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
    this.tableHeight = window.innerHeight - tableTop
  },
  watch: {
    // 重新排序
    type () {
      this.pagenum = 1
      this.workDataList = []
      this.getWorkData()
    },
    sort () {
      this.pagenum = 1
      this.workDataList = []
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
    // 获取今天的统计量
    todayCount () {
      this.$http.TodayCount({userid: this.$root.userid, projectid: this.projectid}).then(rep => {
        this.commitNum = rep.commitnum
        this.saveNum = rep.savenum
      }).catch(err => console.log(err))
    },
    // 获取我的工作日志数据
    getWorkData () {
      let sort = this.sort ? 'desc' : 'asc'
      this.$http.WorkData({userid: this.$root.userid, projectid: this.projectid, pagenum: this.pagenum, type: this.type, sort: sort}).then(rep => {
        if (this.pagenum === 1 && !rep.result.length) {
          this.empty = true
          return
        }
        this.totalPage = Math.ceil(rep.totalnum / 30)
        if (this.totalPage < this.pagenum) return
        rep.result.map(item => this.workDataList.push(item))
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
