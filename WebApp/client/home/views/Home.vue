<template>
  <div>
    <navigation :scrollTop="'656'"></navigation>
    <div class="home-header">
      <p class="title-english">Medicayun Clinical Cloud</p>
      <p class="title">梅地卡尔临床数据云</p>
      <p class="content">完全定制化、支持多中心及跨学科合作、满足各种医学临床研究目的的数据共享与协作平台</p>
      <a href="/login/loginform" target="_blank" class="link-more">开始使用</a>
    </div>
    <div class="home-middle flex-col">
      <p class="title">构建数据驱动的策略，洞悉未来</p>
      <p class="content">我们致力于帮助各类医疗服务提供者，以及从事生命科学领域的组织机构轻松地构建属于自己的，定制化的数据管理和应用系统，真正的实现“数据民主化”</p>
      <router-link to="/home_more" class="link-more">了解更多</router-link>
    </div>  
    <div class="home-body">    
      <div class="item">
        <img class="action-img" src="../assets/home/01.svg">
        <div class="item-content">
          <p class="title">个性化，按需定制的CRF设计工具</p>
          <p class="dec">梅地卡尔表单设计工具帮助用户按照需求定制自己专有的病例表，并迅速自动生成互联网化的数据采集应用界面和数据库，真正实现所见即所得，而实现这一切都无需使用者具有任何专业的计算机软件开发能力</p>
          <router-link to="/CRF_more" class="link-more">了解更多<img src="../assets/home/icon_link_more.svg" style="margin-left: 4px;"></router-link>
        </div>
      </div>
      <div class="item">
        <div class="item-content">
          <p class="title">简单便捷的患者登记应用</p>
          <p class="dec">无需繁琐的操作，梅地卡尔患者登记应用帮助使用者快速的完成患者的入组登记工作，有效地缩短了信息获取时间</p>
          <router-link to="/register_more" class="link-more">了解更多<img src="../assets/home/icon_link_more.svg" style="margin-left: 4px;"></router-link>
        </div>
        <img class="action-img-right" src="../assets/home/02.svg">
      </div>
      <div class="item">
        <img class="action-img" src="../assets/home/03.svg">
        <div class="item-content">
          <p class="title">轻而易举地管理临床研究项目</p>
          <p class="dec">使用梅地卡尔临床研究管理应用，让临床研究项目的管理工作变得简单高效，即使同时管理多个项目、多个中心，也都轻而易举</p>
          <router-link to="mag_more" class="link-more">了解更多<img src="../assets/home/icon_link_more.svg" style="margin-left: 4px;"></router-link>
        </div>
      </div>
      <div class="item">
        <div class="item-content">
          <p class="title">让随访工作变得井井有条</p>
          <p class="dec">无论随访的要求和内容多么的复杂，智能化的梅地卡尔随访记录应用都能让这一切变得井井有条，让随访成员掌握合适的信息来开展工作，随访工作就是这么的简单</p>
          <router-link to="/follow_more" class="link-more">了解更多<img src="../assets/home/icon_link_more.svg" style="margin-left: 4px;"></router-link>
        </div>
        <img class="action-img" src="../assets/home/04.svg">
      </div>
      <div class="item">
        <img class="action-img" src="../assets/home/05.svg">
        <div class="item-content">
          <p class="title">文档与内容管理</p>
          <p class="dec">被海量的医疗文档淹没而找不到自己需要的信息？使用梅地卡尔文档与内容管理应用，问题迎刃而解。采用OCR、人工智能分类、自然语言处理等技术...梅地卡尔会帮助您在需要的时候得到想要的信息</p>
        </div>
      </div>
    </div>
    <div class="home-header about-our">
      <p class="title">关于我们</p>
      <p style="font-size: 36px; margin-top: 64px;">面向生命科学和医疗服务组织的大数据管理方案提供商</p>
      <p class="content">这是个数据驱动的时代，充满着挑战和不确定性。梅地卡尔致力于为用户提供完整全面的大数据处理技术和服务，<br/>帮助用户更好地收集、整合、存储和分析所拥有的数据，让数据成为组织内部最宝贵的资产</p>
      <router-link to="/about_our" class="link-more">了解更多</router-link>
    </div>
    <div class="our-client">
      <p class="title">我们的客户</p>
      <p class="content">我们为一流的医疗机构提供产品与服务，包括：</p>
      <div class="client-logo">
        <img :src="item.url" :alt="item.alt" v-for="item in data">
      </div>
    </div>
    <foot></foot>
  </div>
</template>

<script>
  import Navigation from '../components/navigation.vue'
  import Foot from '../components/foot.vue'
  let imgData = [
    {
      url: require('../assets/client_logo/01四川华西.png'),
      alt: '四川华西医院'
    },
    {
      url: require('../assets/client_logo/02阜外医院.png'),
      alt: '阜外医院'
    },
    {
      url: require('../assets/client_logo/03同济医院.png'),
      alt: '同济医院'
    },
    {
      url: require('../assets/client_logo/04山东齐鲁.png'),
      alt: '山东齐鲁'
    },
    {
      url: require('../assets/client_logo/05上海第六.png'),
      alt: '上海第六人民医院'
    },
    {
      url: require('../assets/client_logo/06协和医院.png'),
      alt: '北京协和医院'
    },
    {
      url: require('../assets/client_logo/07上海瑞金.png'),
      alt: '上海瑞金'
    },
    {
      url: require('../assets/client_logo/08广东人民医院.png'),
      alt: '广东人民医院'
    },
    {
      url: require('../assets/client_logo/09包头中心医院.png'),
      alt: '包头中心医院'
    },
    {
      url: require('../assets/client_logo/10深圳市福田区人民医院.png'),
      alt: '深圳市福田区人民医院'
    },
    {
      url: require('../assets/client_logo/11安贞医院.png'),
      alt: '安贞医院'
    },
    {
      url: require('../assets/client_logo/12北大医院.png'),
      alt: '北大医院'
    },
    {
      url: require('../assets/client_logo/13中国医科大学附属盛京医院.png'),
      alt: '中国医科大学附属盛京医院'
    },
    {
      url: require('../assets/client_logo/14西京医院.png'),
      alt: '西京医院'
    },
    {
      url: require('../assets/client_logo/15上海中山.png'),
      alt: '复旦大学附属中山医院'
    },
    {
      url: require('../assets/client_logo/16中国医科大学附属第一医院.png'),
      alt: '中国医科大学附属第一医院'
    },
    {
      url: require('../assets/client_logo/17朝阳医院.png'),
      alt: '北京朝阳医院'
    },
    {
      url: require('../assets/client_logo/18天津总医院.png'),
      alt: '天津总医院'
    }
  ]
  export default {
    name: 'Home',
    data () {
      return {
        data: imgData
      }
    },
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
  .home-header {
    width: 100%;
    height: 720px;
    background: url(../assets/home/bg1.jpg) no-repeat center;
    background-size: cover;
    color: #fff;
    text-align: center;
  }
  .home-header.about-our {
    background: url(../assets/home/bg2.jpg) no-repeat center;
    height: 640px;
  }
  .home-header .title-english {
    padding-top: 200px;
    font-size: 36px;
  }
  .home-header .title{
    font-size: 48px;
  }
  .home-header.about-our .title{
    padding-top: 80px;
  }
  .home-header .content{
    line-height: 2;
    margin: 32px auto 64px;
    font-size: 20px;
  }
  .home-header .link-more {
    background: #458df1;
    color: #fff;
    width: 160px;
    height: 48px;
    line-height: 46px;
    margin: 0 auto;
    border: 1px solid #458df1;
    border-radius: 24px;
    cursor: pointer;
    display: inline-block;
    font-size: 20px;
  }
  .home-middle {
    background: rgba(0,0,0,.04);
    text-align: center;
    color: rgba(0,0,0,.87);
    padding: 80px 0;
  }
  .home-middle .title {
    font-size: 48px;
  }
  .home-middle .content{
    line-height: 2;
    width: 800px;
    margin: 32px auto 64px;
    font-size: 20px;
  }
  .home-middle .link-more {
    width: 160px;
    height: 48px;
    line-height: 46px;
    margin: 0 auto;
    color: #458df1;
    border: 1px solid #458df1;
    border-radius: 24px;
    cursor: pointer;
    display: inline-block;
    font-size: 20px;
  }
  .home-middle .link-more:hover{
    background: #458df1;
    color: #fff;
  }

  .home-body {
    margin: 120px auto 0;
    width: 1000px;
  }
  .home-body .item{
    margin-bottom: 120px;
    display: flex;
    justify-content: space-between;
    font-size: 20px;
  }
  .home-body .item .action-img{
    width: 300px;
    height: 300px;
  }
  .home-body .item .item-content {
    width: 620px;
  }
  .home-body .item .item-content .title{
    font-size:32px;
    color: rgba(0,0,0,.87);
    margin: 24px 0 32px;
  }
  .home-body .item .item-content .dec{
    color: rgba(0,0,0,.54);
    line-height: 2;
    margin-bottom: 16px;
  }
  .home-body .item .item-content .link-more {
    color: #458df1;
    cursor: pointer;
  }

  .our-client{
    margin: 80px auto 100px;
    text-align: center;
    width: 1000px;
    overflow: hidden;
  }
  .our-client .title {
    color: rgba(0,0,0,.87);
    font-size: 48px;
  }
  .our-client .content {
    color: rgba(0,0,0,.54);
    font-size: 20px;
    margin: 32px 0 64px;
  }
  .our-client .client-logo {
    width: 1080px;
    display: flex;
    flex-wrap: wrap;
  }
  .our-client .client-logo img{
    width: 100px;
    height: 100px;
    margin-right: 80px;
    margin-bottom: 48px;
  }
</style>