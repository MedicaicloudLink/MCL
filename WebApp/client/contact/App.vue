<template>
  <div id="app">
    <mynav class="header-fix"></mynav>

    <router-view class="main"></router-view>
  </div>
</template>

<script>
import Mynav from './views/Header'
export default {
  name: 'App',
  components: { Mynav }
}
</script>

<style>
.header-fix {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100%;
}

.main {
  margin-top: 56px;
}
</style>
