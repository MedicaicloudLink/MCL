<template>
  <div class="main">
    <div v-if="recordsData.length === 0" style="color: #ccc; font-size: 14px;">暂时没有该项记录！</div>
    <div class="card" v-for="item in recordsData">
      <div class="form-group">
        <!--<span>检查日期：</span>-->
        <div style="background: #ccc; width: 100%; padding: 3px;text-align: center">{{ item.inspectDate }}</div>
      </div>
      <div class="form-group">
        <span>身高：</span>
        <span>{{ item.height }}</span>
        <span class="unit">cm</span> 
      </div>
      <div class="form-group">
        <span>体重：</span>
        <span>{{ item.weight }}</span>
        <span class="unit">kg</span> 
      </div>
      <div class="form-group">
        <span>BMI：</span>
        <span>{{ (item.weight / ((item.height/100) * (item.height/100))).toFixed(2) }}</span>
      </div>
      <div class="form-group">
        <span>腹围： </span>
        <span>{{ item.abdominalGirth }}</span>
        <span class="unit">cm(厘米)</span> 
      </div>
      <div class="form-group">
        <span>高压：</span>
        <span>{{ item.SBP }}</span>
        <span class="unit">mmHG</span>
      </div>
      <div class="form-group">
        <span>低压：</span>
        <span>{{ item.DBP }}</span>
        <span class="unit">mmHG</span>
      </div>
      <div class="form-group">
        <span>体温：</span>
        <span>{{ item.temperature }}</span>
        <span class="unit">℃</span> 
      </div>
      <div class="form-group">
        <span>心率：</span>
        <span>{{ item.heartbeat }}</span>
        <span class="unit">次/分钟</span> 
      </div>
      <div class="form-group">
        <span>呼吸：</span>
        <span>{{ item.breathe }}</span>
        <span class="unit">次/分钟</span>
      </div>
    </div>
  </div>
</template>

<script>
import API from '../api.js'
export default {
  name: 'temp1',
  props: {
    templateid: String,
    patientid: String
  },
  data () {
    return {
      recordsData: []
    }
  },
  created () {
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
  .main{
    font-size: 0;
  }
  .card {
    line-height: 1.7;
    width: 210px; 
    border: 1px #ccc solid;
    padding: 15px;
    display: inline-block;
    font-size: 14px;
    margin: 17px;
  }
</style>