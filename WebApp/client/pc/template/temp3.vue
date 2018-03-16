<template>
  <div class="main">
    <div v-if="recordsData.length === 0" style="color: #ccc;">暂时没有该项记录！</div>
    <div class="card" style="line-height: 1.7;" v-for="item in recordsData" v-else>
      <div class="form-group">
        <!--<span>诊断日期：</span>-->
        <div style="background: #ccc; width: 100%; padding: 3px;text-align: center">{{ item.diagnoseTime }}</div>
      </div>
      <div class="form-group">
        <span>诊断类型：</span>
        <span>{{ item.diagnoseType }}</span>
      </div>
      <div class="form-group">
        <span>诊断机构：</span>
        <span>{{ item.diagnosePlace }}</span>
      </div>
      <div class="form-group">
        <span>症状及主诉：</span>
        <span>{{ item.symptom }}</span>
      </div>
      <div class="form-group">
        <span>主要诊断名称：</span>
        <span>{{ item.mainDiagnose }}</span>
      </div>
      <div class="form-group">
        <span>诊断代码：</span>
        <span>{{ item.diagnoseCode }}</span>
      </div>
      <div class="form-group">
        <span>诊断意见：</span>
        <span>{{ item.diagnoseOpinion }}</span>
      </div>
      <div class="form-group">
        <span>住院结果：</span>
        <span>{{ item.inhospitalResult }}</span>
      </div>
    </div>
  </div>
</template>

<script>
  import API from '../api.js'
  export default {
    name: 'temp3',
    props: {
      templateid: String,
      patientid: String
    },
    data () {
      return {
        recordsData: []
      }
    },
    mounted () {
      API.GetPatientRecords({mdid: this.patientid, templateid: this.templateid}).then((response) => {
        for (let i = 0; i < response.length; i++) {
          this.recordsData.push(JSON.parse(response[i].u_patientdata))
        }
      }).catch((err) => {
        window.alert(err)
      })
    }
  }
</script>
<style scoped>
  .card{
    width: 400px;
    padding: 15px;
    border: 1px #ccc solid;
    margin: 10px 40px;
    display: inline-block;
  }
</style>