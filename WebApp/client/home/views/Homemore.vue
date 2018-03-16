<template>
  <div class="home-more">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header flex-row">构建数据驱动的策略，洞悉未来</div>
    <div class="body" style="padding: 0">
      <div class="item flex-col">
        <router-link to="/personal_mag" class="title">为个人研究者提供免费的临床数据管理方案</router-link>
        <p class="flex-row">
          <span>患者登记</span>
          <span class="line">|</span>
          <span>临床研究项目管理</span>
          <span class="line">|</span>
          <span >随访记录</span>
          <span class="line">|</span>
          <span>文档与内容管理</span>
          <span class="line">|</span>
          <span>在线课程</span>
        </p>
      </div>
      <div class="item flex-col">
        <router-link to="/group_mag" class="title">面向小型临床研究团队的数据管理方案</router-link>
        <p class="flex-row" style="margin-bottom: 16px;">
          <span>患者登记</span>
          <span class="line">|</span>
          <span>临床研究项目管理</span>
          <span class="line">|</span>
          <span >随访记录</span>
          <span class="line">|</span>
          <span>审核与批准</span>
          <span class="line">|</span>
          <span>患者管理</span>
        </p>
        <p class="flex-row">
          <span>医患沟通</span>
          <span class="line">|</span>
          <span>在线课程</span>
          <span class="line">|</span>
          <span >文档与内容管理</span>
        </p>
      </div>
      <div class="item flex-col">
        <router-link to="/CRO_mag" class="title">为临床试验组织及CRO提供的数据管理方案</router-link>
        <p class="flex-row" style="margin-bottom: 16px;">
          <span>患者筛选与入组</span>
          <span class="line">|</span>
          <span>EDC电子数据捕获</span>
          <span class="line">|</span>
          <span>患者管理</span>
          <span class="line">|</span>
          <span>患者教育</span>
          <span class="line">|</span>
          <span>试验进度管理</span>
        </p>
        <p class="flex-row">
          <span>风险控制管理</span>
          <span class="line">|</span>
          <span>多中心管理</span>
          <span class="line">|</span>
          <span>供应链管理</span>
          <span class="line">|</span>
          <span>在线教育和培训</span>
        </p>
      </div>
      <div class="item flex-col">
        <router-link to="/patient_mag" class="title">面向诊所及医疗服务机构的患者关系管理</router-link>
        <p class="flex-row" style="margin-bottom: 16px;">
          <span>患者登记</span>
          <span class="line">|</span>
          <span>医患沟通</span>
          <span class="line">|</span>
          <span>患者教育</span>
          <span class="line">|</span>
          <span>预约管理</span>
          <span class="line">|</span>
          <span>随访管理</span>
        </p>
        <p class="flex-row">
          <span>健康档案管理</span>
          <span class="line">|</span>
          <span>报表与分析</span>
        </p>
      </div>
    </div>
    <foot></foot>
  </div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  import Foot from '../components/foot.vue'
  export default {
    name: 'Homemore',
    components: {
      Navigation,
      Foot
    },
    mounted () {
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    }
  }
</script>
<style scoped>
  .body .item{
    height: 400px;
    align-items: center;
    justify-content: center;
  }
  .body .item:nth-child(even) {
    background: rgba(0,0,0,.04);
  }
  .body .item .title {
    font-size: 32px;
    color: #458df1;
    margin-bottom: 32px;
  }
  .body .item p.flex-row {
    justify-content: center;
  }
  .body .item p span {
    font-size: 20px;
    color: rgba(0,0,0,.87);
  }
  .body .item p span.line{
    color: #458df1;
    margin: 0 16px;
    font-weight: bolder;
  }
</style>