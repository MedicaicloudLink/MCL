<template>
  <div class="about_our">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header">
      <p>面向小型临床研究团队的数据管理方案</p>
    </div>
    <div class="body" style="padding: 0;">
      <div class="des">
        对于小型的临床研究团队来讲，资金有限、时间紧张、人手不够等等，这些都是影响研究计划乃至最终的研究成果的重要因素。针对研究者面临的困境，梅地卡尔提供的临床数据云应用可以有效地帮助项目管理者全面提升项目管理的水平和效率、扩大产出、缩短研究周期，从而达到获得高质量研究成果的目的。<br/><br/>
        通过使用基于云服务的梅地卡尔临床数据云应用，临床研究团队可以轻松地实现：<br/>
        <div style="margin-left: 20px">
          <i class="iconfont icon-dian-copy"></i>针对不同项目设计CRF表，并快速转换为互联网化的数据捕捉应用，并自动生成后端运行的数据库<br/>
          <i class="iconfont icon-dian-copy"></i>不同项目使用定制化的应用系统收集和更新临床数据<br/>
          <i class="iconfont icon-dian-copy"></i>团队间的成员协作，按照角色分配不同应用的使用权，及相应的数据的访问权限<br/>
          <i class="iconfont icon-dian-copy"></i>多个分中心的管理，及用户权限设置<br/>
          <i class="iconfont icon-dian-copy"></i>随访计划的建立及更新，自动为随访成员分配随访清单并记录工作进度<br/>
          <i class="iconfont icon-dian-copy"></i>随访团队的建立及管理，并设置访问权限<br/>
          <i class="iconfont icon-dian-copy"></i>位于不同地点的成员间的数据收集和处理协作<br/>
          <i class="iconfont icon-dian-copy"></i>数据的审核与批准<br/>
          <i class="iconfont icon-dian-copy"></i>同时管理多个临床研究项目<br/>
          <i class="iconfont icon-dian-copy"></i>项目进度管理：采用导航面板（Dashboard）的形式来展示工作进度<br/>
          <i class="iconfont icon-dian-copy"></i>建立统一的文档管理中心，将从不同渠道收集的纸质，或其他多媒体格式的文件保存、存档并建立索引<br/>
          <i class="iconfont icon-dian-copy"></i>输出标准的Excel格式文件，供SPSS等专业统计学软件使用<br/>
          <i class="iconfont icon-dian-copy"></i>获得相关领域最新的研究动态及进展的报告<br/>
          <i class="iconfont icon-dian-copy"></i>参加各类专业课程的在线培训<br/><br/>
        </div>
        梅地卡尔临床数据云应用为整个研究团队创建了统一视图的数据展示系统，不同角色的研究人员依据统一的数据库进行数据的更新。定制化的CRF工具可以有效地缩短新项目的上线时间，节约了运营成本；统一的数据库降低了人为可能产生的差错，提高了数据的质量；为不同角色分配和设置权限，防止了数据的外泄，降低了项目的管理风险；并且，清晰直观的项目进度管理系统让管理者能够一目了然地掌控项目的状态，及时发现问题和进行调整。<br/><br/>
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
    font-size: 20px;
    color: rgba(0,0,0,.87);
    line-height: 2;
  }
</style>