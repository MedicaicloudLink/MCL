<template>
  <div class="personal-mag">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header">
      <p>为个人研究者提供的免费的临床数据管理方案</p>
    </div>
    <div class="body" style="padding: 0;">
      <div class="des">
        在临床研究中，约80%的时间和经历都花费在了数据的收集和处理过程中，特别是对于个人临床研究者，在缺少资金的支持，以及可支配的时间有限的情况下，数据的处理更成为了一个艰巨的任务。针对这些难题，梅地卡尔为从事医学和临床领域的研究者提供了一个简便易行，且高效的解决方案。<br/><br/>
        通过使用基于云服务的梅地卡尔临床数据云应用，研究者可以轻松地实现：<br/>
        <div style="margin-left: 20px">
          <i class="iconfont icon-dian-copy"></i>将自己设计的CRF快速转换为互联网化的数据捕捉应用，并自动生成后端运行的数据库<br/>
          <i class="iconfont icon-dian-copy"></i>使用为自己定制的应用收集和更新患者数据<br/>
          <i class="iconfont icon-dian-copy"></i>与其他人协作，共同进行患者的档案建立、以及开展随访工作并及时更新数据<br/>
          <i class="iconfont icon-dian-copy"></i>可以管理多个不同的临床研究项目<br/>
          <i class="iconfont icon-dian-copy"></i>将自己收集的纸质，或其他多媒体格式的文件保存、存档并建立索引<br/>
          <i class="iconfont icon-dian-copy"></i>输出标准的Excel格式文件，供SPSS等专业统计学软件使用<br/>
          <i class="iconfont icon-dian-copy"></i>获得相关领域最新的研究动态及进展的报告<br/>
          <i class="iconfont icon-dian-copy"></i>参加各类专业课程的在线培训<br/><br/>
        </div>
        梅地卡尔临床数据云应用的设计理念是以“用户友好性和易用性”为重点的，它给使用者带来的好处是显而易见的。使用互联网化的数据收集应用不仅可以为研究者节约大量宝贵的时间，还大幅降低了数据处理的负荷，并且使用统一的后台数据库系统，使得数据的处理更加准确，避免了重复工作和可能出现的差错。<br/><br/>
        不同的研究目的会要求采集不同的数据项，梅地卡尔为使用者提供自助式的CRF设计工具来满足这种定制化的需求。使用者只需要使用字处理（Word）或表格软件（Excel）设计出CRF方案，梅地卡尔报告表编辑应用就可以快速将其转换为互联网化的数据捕获应用，并自动生成后台的数据库。<br/><br/>
        梅地卡尔临床数据云应用还支持多人间的协作，使用者可以分配不同的角色，如患者登记录入员，或随访员，来满足数据收集工作的要求。无论团队的成员在什么地点，他们都可以通过互联网随时随地完成数据收集工作。同时，项目的管理者还可以为不同的角色分配不同的访问权限，比如，承担随访工作的人员不会被允许访问存有全部患者信息的主数据库。<br/><br/>
        告别使用纸笔，以及Excel表格来管理患者临床数据的时代吧！梅地卡尔临床数据云应用，让您的研究如虎添翼！
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