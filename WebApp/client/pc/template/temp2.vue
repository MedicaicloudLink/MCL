<template>
  <div class="temp4">
    <div v-if="recordsData.length === 0" style="color: #ccc; font-size: 14px;">暂时没有该项记录！</div>
    <div class="content" style="line-height: 1.7;" v-for="item in recordsData">
      <div class="form-group">
        <span>1.症状：</span>
        <span v-for="option in item.symptom">
          {{ option }}
          <span v-if="option === '胸痛'">
            <span v-if="item.trigger === '是'">(通常是因为运动或情绪激动触发)</span>
            <span v-else>(通常不是因为运动或情绪激动触发)</span>            
          </span>
        </span>
        <span v-if="item.symptomNone">{{ item.symptomNone }}</span>
      </div>
      <div class="form-group">
        <span v-if="item.symptom.indexOf('胸痛') !== -1">
          <span>2.胸痛是否会因为服用硝酸甘油有所缓解：</span>
          <span>{{ item.remission }}</span>
        </span>
      </div>
      <div class="form-group">
        <div>3.主要危险因素：</div>
        <div>
          <span style="marginLeft: 20px;">a).高血压：</span>
          <span v-if="item.hypertension === '有'">
            <span>{{item.hypertensionDate}}(确诊时间)</span>
            <span v-if="item.hypertensionLine ==='是'">已控制达标</span>
            <span v-else>没有控制达标</span>
          </span>
          <span v-else>{{ item.hypertension }}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">b).高血脂：</span>
          <span v-if="item.hyperlipidemia === '有'">
            <span>{{item.hyperlipidemiaDate}}(确诊时间)</span>
            <span v-if="item.hyperlipidemiaLine ==='是'">已控制达标</span>
            <span v-else>没有控制达标</span>
          </span>
          <span v-else>{{ item.hyperlipidemia }}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">c).高血糖：</span>
          <span v-if="item.diabetes === '有'">
            <span>{{item.diabetesDate}}(确诊时间)</span>
            <span v-if="item.diabetesLine ==='是'">已控制达标</span>
            <span v-else>没有控制达标</span>
          </span>
          <span v-else>{{ item.diabetes }}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">d).外周血管疾病：</span>
          <span v-if="item.peripheralVascular === '有'">
            <span>{{item.peripheralVascularDate}}(确诊时间)</span>
            <span v-if="item.peripheralVascularLine ==='是'">已控制达标</span>
            <span v-else>没有控制达标</span>
          </span>
          <span v-else>{{ item.peripheralVascular }}</span>
        </div>
         <div>
          <span style="marginLeft: 20px;">e).脑血管疾病：</span>
          <span v-if="item.cerebrovascular === '有'">
            <span>{{item.cerebrovascularDate}}(确诊时间)</span>
            <span v-if="item.cerebrovascularLine ==='是'">已控制达标</span>
            <span v-else>没有控制达标</span>
          </span>
          <span v-else>{{ item.cerebrovascular }}</span>
        </div>
      </div>
      <div class="form-group">
        <div>4.其他危险因素：</div>
        <div>
          <span style="marginLeft: 20px;">a).早发心血管疾病家族史：</span>
          <span v-if="item.family === '有'">
            <span v-for="option in item.familyGroup"> {{ option }} </span>
          </span>
          <span v-else>{{ item.family }}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">b).吸烟史：</span>
          <span v-if="item.smoke === '有'">
            <span>{{ item.smokeyear }}年(烟龄)</span>
            <span>{{ item.smoketime }}(每日吸烟频次)</span>
          </span>
          <span v-else>{{ item.smoke }}</span>
          <span v-if="item.smoke === '已经戒烟'">(戒烟{{stopsmokeyear}}年)</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">c).绝经(女性)：</span>
          <span>{{item.menopause}}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">d).胃食管反流：</span>
          <span>{{item.menopause}}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">e).肾病病史：</span>
          <span>{{item.nephropathy}}</span>
        </div>
      </div>
      <div class="form-group">
        <div>5.生活：</div>
        <div>
          <span style="marginLeft: 20px;">a).教育程度：</span>
          <span>{{ item.education }}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">b).工作状态：</span>
          <span v-if="item.work === '还在工作'">
            <span> 每天工作{{ item.workHours }} </span>
            <span v-if="item.workSit6 === '是'"> (工作中每天会久坐超过6小时)</span>
            <span v-else>(工作中每天不会久坐超过6小时)</span>
          </span>
          <span v-else>{{ item.work }}</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">c).生活习惯：</span>
          <span v-if="item.nobadEat">{{ item.nobadEat }}</span>
          <span v-else v-for="option in item.eat"> {{ option }} </span>
        </div>
        <div>
          <span style="marginLeft: 20px;">d).运动：</span>
          <span>{{ item.sport }} (至少是一小时以上的有氧运动)</span>
        </div>
        <div>
          <span style="marginLeft: 20px;">e).减肥：</span>
          <span>{{ item.weight }} (减肥目标是降低体重的5%)</span>
        </div>
      </div>
      <div class="form-group">
        <div>6.认知和心理：</div>
        <div>
          <span style="marginLeft: 20px;">a).心脏病：</span>
          <span>{{ item.diseaseUnderstand }} (对心脏病或冠心病的了解程度)</span>
          <span>{{ item.diseasePsychology }} (平常对心脏问题的紧张程度)</span>          
        </div>
        <div>
          <span style="marginLeft: 20px;">b).冠心病CT：</span>
          <span>{{ item.CTUnderstand }} (对冠心病CT检查的了解程度)</span>
          <span>{{ item.CTPsychology }} (对冠心病CT检查的紧张程度)</span>          
        </div>
      </div>
      <div class="form-group">
        <div>7.服用药物：</div>
        <div v-if="item.durgs.length === 0" style="color: #ccc; marginLeft: 20px;">暂时没有服用药物！</div>
        <table style="marginLeft: 20px;" border='1' cellspacing="0" borderColor="#ccc" v-else>
          <tr>
            <th>药品名称</th><th>服用时长(月)</th><th>每月服药频次</th><th>每次服用剂量</th>
          </tr>
          <tr v-for="drug in item.durgs">
            <td>{{ drug.drugname }}</td>
            <td> {{drug.drugMonth}} </td>
            <td>{{ drug.durgMonthTime }}</td>
            <td>{{ drug.drugDose }}</td>
          </tr>
        </table>
      </div>
      <div class="form-group">
        <div>8.化验单照片：</div>
        <img :src="'http://'+item.uploadImage" width="200px" style="marginLeft: 20px; cursor: pointer" @click="zoomImgDialog = true"/>
        <div class="imgDialog" v-if="zoomImgDialog" @click="zoomImgDialog = false">
          <img class="zoomImg" :src="'http://'+item.uploadImage"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import API from '../api.js'
  export default {
    name: 'temp2',
    props: {
      patientid: String,
      templateid: String
    },
    data () {
      return {
        recordsData: [],
        zoomImgDialog: false
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

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  tr:hover{
    background: #ddd;
  }
  tr:first-child:hover{
    background: #f4f6f8;
  }
  td, th{
    width: 150px;
    text-align: center;
    padding: 3px;
  }
  .imgDialog{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    background: rgba(0,0,0,.5);
    width: 100%;
    height:  100%;
    display: flex;
    justify-content: center;
    align-items: center; 
  }
  .zoomImg{
    width: 600px;
  }
</style>
