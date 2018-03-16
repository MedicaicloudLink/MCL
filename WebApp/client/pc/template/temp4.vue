<template>
  <div id="temp4">
    <div v-if="recordsData.length === 0" style="color: #ccc;">暂时没有该项记录！</div>
    <table v-else>
      <tr>
        <th>药品名称</th><th>单位剂量</th><th>每次服用剂量</th>
      </tr>
      <tr v-for="item in recordsData">
        <td>{{ item.drugname }}</td>
        <td>{{ item.durgstandard }} {{ item.drugunit}}</td>
        <td>{{ item.drugdose }} {{ item.drugshape}}</td>
      </tr>
    </table>
  </div>
</template>

<script>
  import API from '../api.js'
  export default {
    name: 'temp4',
    props: {
      patientid: String,
      templateid: String
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
  tr{
    border-bottom: 1px #ccc soild;
  }
  td{
    width: 150px;
    text-align: center;
  }
</style>