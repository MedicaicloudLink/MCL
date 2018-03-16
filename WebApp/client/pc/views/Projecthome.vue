<template>
  <div id="projecthome">
    <div class="yesterday color54 flex-row">
      <div class="yesterday-card border-r flex-col">
        <span>昨日新增患者</span>
        <span class="yester-num">{{baselineData.yesterday}}</span>
      </div>
      <div class="yesterday-card flex-col">
        <span>昨日新增随访</span>
        <span class="yester-num">{{followData.yesterday}}</span>
      </div>
    </div>
    <div class="baseline card">
      <div class="card-header">
        <span>登记数据</span>
        <select v-model="baselineCenterId" style="width: 160px;height: 32px;">
          <option value="0">全部</option>
          <option v-for="center in centerList" :value="center.u_centerID">{{ center.u_centername }}</option>
        </select>
      </div>
      <div style="position: relative;">
        <div class="sum color87">
          <span>累计录入患者人数：</span>
          <span v-if="baselineData">{{baselineData.baselineall}}</span>
        </div>
        <div class="date flex-row">
          <span :class="checkedType.week ? 'blue' : 'white'" @click="changeType('week')">周</span>
          <span :class="checkedType.month ? 'blue' : 'white'" @click="changeType('month')">月</span>
          <span :class="checkedType.year ? 'blue' : 'white'" @click="changeType('year')">年</span>
        </div>
      </div>
      <div class="charts">
        <div class="chart" id="baseline-bar" :style="{width: '1000px', height: '400px'}"></div>
      </div>
    </div>
    <div class="follow card">
      <div class="card-header">随访数据</div>
      <div class="sum">累计录入随访数据：{{followData.alreadyfollow + '(' + followData.alreadyper + ')'}}</div>
      <div class="sum-small">应随访总数：{{followData.shouldfollow}}</div>
      <div class="charts border-b" style="margin: 0 16px;">
        <div class="chart" id="follow-pie" :style="{width: '1000px', height: '400px'}"></div>
      </div>
      <div class="sum-small" style="margin-top: 16px;">各随访任务完成情况统计</div>
      <div class="charts">
        <div class="chart" id="follow-bar" :style="{width: '1000px', height: '400px'}"></div>
      </div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  export default {
    data () {
      return {
        centerList: [],
        baselineData: {},
        followData: {},
        baselineCenterId: '0',
        followCenterId: '0',
        checkedType: {
          week: true,
          month: false,
          year: false
        },
        type: 'week',
        baselineX: [],
        baselineY: [],
        followPie: [],
        followBarX: [],
        followBarY1: [],
        followBarY2: [],
        followBarY8: []
      }
    },
    created () {
      this.getCenterList()
      this.getBaselineCount()
      this.getFollowCount()
    },
    watch: {
      baselineCenterId (val) {
        this.getBaselineCount(this.type)
      }
    },
    methods: {
      // 获取项目分中心列表
      getCenterList () {
        API.GetProjectCenter({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          this.centerList = response
          // console.log(response)
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 基线统计
      getBaselineCount () {
        API.BaselineCount({userid: this.$root.userid, projectid: this.$route.params.projectid, centerid: this.baselineCenterId === '0' ? '' : this.baselineCenterId}).then((response) => {
          this.baselineData = response
          this.changeType(this.type)
        }).catch((err) => {
          console.log(err)
        })
      },
      // 随访统计
      getFollowCount () {
        API.FollowCount({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          this.followData = response
          this.followData.followtype.map(n => this.followBarX.push(n.taskname))
          this.followData.everystatus.map(n => this.followPie.push({
            value: n.count,
            name: ''
          }))
          let data = ['已完成随访', '退出', '号码错误', '无人接听', '拒绝随访', '结果正常不需随访', '待随访']
          for (let i = 0; i < this.followPie.length; i++) {
            this.followPie[i].name = data[i]
          }
          this.followData.followtype.map(n => this.followBarY1.push(n.arrdata[1].count))
          this.followData.followtype.map(n => this.followBarY2.push(n.arrdata[0].count))
          this.followData.followtype.map(n => this.followBarY8.push(n.arrdata[2].count))
          this.drawFollowsPie()
          this.drawFollowBar()
        }).catch((err) => {
          console.log(err)
        })
      },
      // 基线柱状图 切换x轴
      changeType (type) {
        let data = {
          week: false,
          month: false,
          year: false
        }
        this.checkedType = data
        this.checkedType[type] = true
        this.type = type
        this.baselineX = []
        this.baselineY = []
        this.baselineData[type].map(n => this.baselineX.push(n.date))
        this.baselineData[type].map(n => this.baselineY.push(n.count))
        this.drawBaselineBar()
      },
      // 画基线新增柱状图
      drawBaselineBar () {
        let baselineBar = this.$echarts.init(document.getElementById('baseline-bar'))
        baselineBar.setOption({
          tooltip: {
            formatter: '{a}<br/>{b}: {c}人'
          },
          xAxis: {
            data: this.baselineX
          },
          legend: {
            data: ['新增患者人数'],
            textStyle: {
              color: 'rgba(0, 0, 0, .54)'
            },
            selectedMode: false
          },
          yAxis: {
          },
          series: [{
            name: '新增患者人数',
            type: 'bar',
            barMaxWidth: '48px',
            label: {
              normal: {
                show: true,
                position: 'outside',
                textStyle: {
                  // color: 'rgb(69, 141, 241)'
                }
              }
            },
            itemStyle: {
              normal: {
                // color: 'rgba(69, 141, 241,.26)'
              }
            },
            data: this.baselineY
          }]
        })
      },
      // 画随访环形图
      drawFollowsPie () {
        let followPie = this.$echarts.init(document.getElementById('follow-pie'))
        followPie.setOption({
          tooltip: {
            trigger: 'item',
            formatter: '{a}<br/>{b}:{c}({d}%)'
          },
          legend: {
            data: ['已完成随访', '退出', '号码错误', '无人接听', '拒绝随访', '结果正常不需随访', '待随访'],
            selectedMode: false,
            orient: 'vertical',
            icon: 'circle',
            textStyle: {
              color: 'rgba(0, 0, 0, .54)'
            },
            left: '10%',
            top: '10px'
          },
          series: [{
            name: '随访数据',
            type: 'pie',
            radius: ['20%', '78%'],
            label: {
              normal: {
                show: true,
                formatter: '{b}:{c}({d}%)'
              }
            },
            data: this.followPie
          }]
        })
      },
      // 画随访任务完成量统计柱状图
      drawFollowBar () {
        let followBar = this.$echarts.init(document.getElementById('follow-bar'))
        followBar.setOption({
          tooltip: {
            formatter: '{b}<br/>{a}: {c}',
            axisPointer: {
              label: {
                show: true
              }
            }
          },
          xAxis: {
            data: this.followBarX
          },
          legend: {
            data: ['退出', '已随访', '待随访'],
            selectedMode: false,
            textStyle: {
              color: 'rgba(0, 0, 0, .54)'
            },
            right: '10%',
            top: '10px'
          },
          yAxis: {},
          series: [
            {
              name: '待随访',
              type: 'bar',
              stack: '随访',
              data: this.followBarY8
            },
            {
              name: '已随访',
              type: 'bar',
              stack: '随访',
              data: this.followBarY2
            },
            {
              name: '退出',
              type: 'bar',
              stack: '随访',
              data: this.followBarY1,
              barWidth: '48px'
            }
          ]
        })
      }
    }
  }
</script>
<style scoped>
  .yesterday {
    height: 120px;
    margin-bottom: 32px;
    background: #fff;
    padding: 8px 0;
    margin-top: 32px;
  }
  .yesterday-card{
    flex: 1;
    align-items: center;
    padding-top: 8px;
    padding-bottom: 32px;
  }
  .yesterday .yester-num {
    color: #458df1;
    font-size: 48px;
  }
  .sum {
    height: 56px;
    line-height: 56px;
    text-align: center;
    font-weight: bold;
    margin-bottom: 16px;
    font-size: 20px;
  }
  .date {
    position: absolute;
    right: 32px;
    top: 12px;
    height: 32px;
    width: 98px;
    line-height: 30px;
    border: 1px solid rgba(0, 0, 0, .12);
  }
  .date span {
    flex: 1;
    text-align: center;
    cursor: pointer;
  }
  .date .blue {
    background: #458df1;
    color: #fff;
  }
  .date .white {
    background: #fff;
    color: rgba(0, 0, 0, .87);
  }
  .sum-small {
    height: 40px;
    line-height: 40px;
    text-align: center;
    font-size: 16px;
    color: rgba(0, 0, 0, .54);
    font-weight: bold;
  }
  .chart {
    margin: 0 auto;
    padding: 0 16px;
  }
</style>