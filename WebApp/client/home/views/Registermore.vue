<template>
  <div class="reg-more">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header flex-row">简单便捷的患者登记应用</div>
    <div class="body">
      <div class="item" v-for="item in data">
        <img class="item-img" :src="item.src">
        <div class="item-text">
          <p class="text-title">{{ item.title }}</p>
          <p class="text-content">{{ item.content }}</p>
        </div>
      </div>
      <a class="start-user" href="/login/loginform" target="_blank">开始使用</a>
    </div>
    <foot></foot>
  </div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  import Foot from '../components/foot.vue'
  let data = [
    {
      src: require('../assets/reg/01.svg'),
      title: '轻松地为不同的项目分配不同的、有针对性的患者登记应用',
      content: '通过加载已经定制好并发布的病例表表单，您就可以快速地为不同的研究项目部署不同的，并且是有针对性的患者登记应用，即使同时开展多个项目，也都轻而易举'
    },
    {
      src: require('../assets/reg/02.svg'),
      title: '内置的患者基本信息模块，轻松调用',
      content: '患者登记应用内置了通用的患者基本信息模块，包括姓名、性别、出生日期、联系电话、备用电话、居住地址、登记日期等等'
    },
    {
      src: require('../assets/reg/03.svg'),
      title: '多人间的团队协作',
      content: '无论是一个人或是多人间的团队协作，患者登记应用都能满足要求。团队成员可以分布在不同的地点，利用互联网基于同一个数据库开展工作，添加、修改、更新并汇总患者的首次登记信息'
    },
    {
      src: require('../assets/reg/04.svg'),
      title: '数据的质量控制及审查',
      content: '应用系统会基于一定的规则对录入的数据进行判断并给予输入者提示，要求检查所录入数据项的正确性。同时，应用系统也设置了人工审查的选项，即数据录入者提供的登记信息必须经过一位有资格的管理员进行最后的审查批准才能正式生效'
    },
    {
      src: require('../assets/reg/05.svg'),
      title: '实时的工作进度汇总数据',
      content: '无论是新增加的患者数据还是已提交待审核的患者数据都会实时的进行数据的汇总并通过图表等形式反应出来，让录入人员以及项目的管理员能够及时把控工作进度'
    }
  ]
  export default {
    name: 'Homemore',
    components: {
      Navigation,
      Foot
    },
    data () {
      return {
        data: data
      }
    },
    mounted () {
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    }
  }
</script>