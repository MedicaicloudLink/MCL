<template>
  <div id="app">
    <mynav></mynav>
    <!-- 路由匹配到的组件将渲染在这里 -->
    <router-view></router-view>
  </div>
</template>

<script>
  import Mynav from './components/Header.vue'
  export default {
    name: 'App',
    components: { Mynav }
  }
</script>

