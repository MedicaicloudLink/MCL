<template>
  <div class="about_our">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header">
      <p>为临床试验组织及CRO提供的数据管理方案</p>
    </div>
    <div class="body" style="padding: 0;">
      <div class="des">
        对于临床试验组织和CRO来讲，试验的进度控制以及数据质量都是优先考虑的因素。梅地卡尔临床数据云应用套件为临床试验组织和CRO提供以下的应用：<br/>
        <div style="margin-left: 20px;">
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>报告表编辑<br/>为使用者提供定制化的报告表（CRF）编辑工具，原始的CEF表可以快速地生成互联网化的EDC电子数据捕获系统及后台的数据库</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>患者登记-EDC电子数据捕获<br/>记录患者的初始登记信息，及患者的筛选和入组管理</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>随访记录-EDC电子数据捕获<br/跟踪患者，自动生成任务清单分配给工作人员。根据计划记录所需要的数据，并更新患者数据库</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>患者管理<br/>患者与试验组织者的沟通记录，加强同患者的联络，帮助试验组织者开展患者管理活动</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>患者教育<br/>向患者投送教育材料，开展患者教育活动</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>试验进度管理<br/>使用导航面板（Dashboard）管理项目的进度，让管理者掌控每个项目的状态</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>风险控制管理<br/>不良事件的上报及处理，帮助管理者降低项目风险</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>多中心管理<br/>支持多中心的数据收集，及协作，评价多中心的效率及开展有效管理</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>供应链管理<br/>试验中使用材料的审核及批准流程，材料消耗情况，及库存的管理</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>在线教育与培训<br/>为试验参与者提供在线培训，明确试验职责及提高工作质量</p>
          </div>
        </div>
        <br/>
        梅地卡尔临床数据云应用为临床试验组织及CRO企业带来了明显的收益，如，定制化的CRF工具规范数据收集的范围，并加快项目启动的时间；简单易用的患者登记系统可以有效地缩短筛选入组患者的时间，加快项目实施的进度；多中心的管理可以有效评价不同中心的效率，发现可能出现的问题，及采取对策，以提高效率；同时，强调与患者进行互动，可以降低退出率，节约开支。<br/><br/>
        梅地卡尔为各类临床试验组织提供了可以快速部署实施，并且是低成本的临床数据管理的解决方案，以帮助他们保持和提高在市场上的竞争力。<br/><br/>
        如果您需要了解更多的产品及服务的信息，请联系我们：<br/>
        地址：北京市海淀区北太平庄路25号豪威大厦315A<br/>
        电话：010-56053592<br/>
        电子邮件：contact@medicayun.cn<br/>
      </div>
    </div>
    <foot></foot>
  </div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  import Foot from '../components/foot.vue'
  export default {
    name: 'Home',
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
  .header {
    width: 100%;
    height: 480px;
    background: url(../assets/home/bg1.jpg) no-repeat center;
    background-size: cover;
  }
  .header p{
    font-size: 48px;
    color: #fff;
    width: 680px;
    margin: 0 auto;
    padding-top: 150px;
    line-height: 2;
    text-align: center;
  }
  .body {
    width: 960px;
    margin: 80px auto 120px;
  }
  .body .des{
    font-size: 20px;
    color: rgba(0,0,0,.87);
    line-height: 2;
  }
</style>