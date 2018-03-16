<template>
  <div class="about_our">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header">
      <p>面向诊所及医疗服务机构的患者关系管理</p>
    </div>
    <div class="body" style="padding: 0;">
      <div class="des">
        良好的医患关系是一个双赢的结果，所以，开展患者关系管理对于医疗服务提供者来讲就变得十分必要和迫切了，梅地卡尔临床数据云应用可以帮助中小型医疗服务提供者在提高患者关系管理的水平及效果方面有所突破。<br/><br/>
        梅地卡尔的解决方案包含了以下的应用：<br/>
        <div style="margin-left: 20px;">
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>患者登记<br/>患者的初始登记</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>报告表编辑<br/>为不同的科室定制不同的患者临床数据收集应用</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>医患沟通<br/>加强同患者的信息交互，密切医患关系，提高患者满意度</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>患者教育<br/>向患者递送教育材料，开展患者教育，降低患者不切实际的期望值</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>预约管理<br/>管理预约时间及分配相应的资源</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>随访管理<br/>建立和管理随访计划，开展随访工作</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>健康档案管理<br/>为患者建立统一的档案资料</p>
          </div>
          <div style="display: flex;">
            <i class="iconfont icon-dian-copy"></i>
            <p>报表与分析<br/>运营分析的导航面板（Dashboard）展示，实时掌握运营状况</p>
          </div>
        </div>
        <br/>
        从患者的初次登记，到诊中管理，随访管理，以及患者教育等各个环节，梅地卡尔都有相应的解决方案。这个解决方案帮助医疗服务提供者在预算有限，以及人手不足的情况下，依旧能够开展有效的患者管理工作，加强同患者的沟通，提高患者满意度，实现既定的运营目标。<br/><br/>
        如果您需要了解更多的产品及服务的信息，请联系我们：<br/>
        地址：北京市海淀区北太平庄路25号豪威大厦315A<br/>
        电话：010 56053592<br/>
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