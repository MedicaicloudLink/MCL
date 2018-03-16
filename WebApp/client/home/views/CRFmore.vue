<template>
  <div class="CRF-more">
    <navigation :scrollTop="'416'"></navigation>
    <div class="header flex-row">个性化，按需定制的CRF设计工具</div>
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
      src: require('../assets/CRF/01.svg'),
      title: '按需定制病例表的数据项',
      content: '研究目的与研究方法的不同，需要关注的临床数据项自然也不同，使用梅地卡尔表单设计工具可以让您按照实际需要，自行定义数据项，然后快速生成满足自己需要的电子数据捕获应用（EDC）'
    },
    {
      src: require('../assets/CRF/02.svg'),
      title: '丰富多样的问题类型',
      content: '梅地卡尔表单设计工具提供了多种多样的问题类型来满足收集数据的需要，比如：单选题、多选题、简短回答题、段落回答题、线性量表、时间选项、地址选项、表格式选择等...同时，也支持上传多媒体附件（图片、视频、音频等）'
    },
    {
      src: require('../assets/CRF/03.svg'),
      title: '自定义章节标题和添加说明',
      content: '为您的病例表添加章节标题以及相关的说明，可以使表单的结构更加清晰，让一切看起来整齐有序。添加章节标题及说明不但让自己的思路更加清晰，同时也方便了表单的使用者，提高了数据捕获时的效率，降低了出现差错的可能性'
    },
    {
      src: require('../assets/CRF/04.svg'),
      title: '所见即所得的编辑方式',
      content: '通过添加图片等多媒体格式文件，或展示网站链接，您的病例表看起来会更加生动直观，同时，您可以使用预览功能，随时查看设计的效果，并及时做出调整和优化，梅地卡尔表单设计工具是真正的所见即所得的编辑器'
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