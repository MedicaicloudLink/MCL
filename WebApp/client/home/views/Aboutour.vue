<template>
  <div class="about_our">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header">
      <p>将数据从简单的信息展示转变为深刻的洞察</p>
    </div>
    <div class="body" style="padding: 0;">
      <div class="des">
        <p>在这个充满了变革的时代，企业每时每刻都要应对各种挑战。对于医疗服务提供者来说，提升临床疗效、控制医疗支出、体感疾病预测能力、开展人群健康管理等等，都是艰巨而富有挑战性的工作。梅地卡尔为医疗服务组织提供全面的临床数据管理解决方案，帮助他们在数据驱动的时代更好的应对各种挑战。</p>
        <p>梅地卡尔研究、设计、开发、实施面向医疗服务领域的大数据管理软件并提供专业服务，我们的使命是帮助用户更好的挖掘医疗数据中潜藏的价值、验证医疗活动中的最佳实践、改善患者体检和诊疗效果、提高资源的使用效率、减少社会资源的浪费及促进人群健康水平提升。梅地卡尔努力为用户提供一流的产品和服务，通过持续不断的改进和完善使用体验，为用户创造价值。我们勇于认知，勇于实践，求真务实，帮助用户将数据从信息的展示转变为深刻的洞察。</p>
      </div>
      <div class="msg">
        <h3>联系信息</h3>
        <p>地址：北京市海淀区北太平庄路25号豪威大厦315A</p>
        <p>电话/传真：010-56053592</p>
        <p>邮政编码：100088</p>
        <p>电子邮件：contact@medicayun.cn</p>
        <p>Linkedin：北京梅地卡尔科技有限公司</p>
        <p>Facebook：北京梅地卡尔科技有限公司</p>
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
  .body p{
    font-size: 20px;
    color: rgba(0,0,0,.87);
    line-height: 2;
  }
  .body .des p{
    margin-bottom: 80px;
  }
  .body .msg h3{
    font-size: 32px;
    color: rgba(0,0,0,.87);
    margin-bottom: 24px;
  }
</style>