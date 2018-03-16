<template>
  <div class="foot">
    <p class="flex-row">
      <span class="mg-r-32">地址：北京市海淀区北太平庄路25号豪威大厦315A</span>
      <span >电话/传真：010-56053592</span>
    </p>
    <p class="flex-row">
      <span class="mg-r-32">邮政编码：100088</span>
      <span>电子邮件：contact@medicayun.cn</span>
    </p>
    <ul class="flex-row">
      <li><router-link to="/privacy">隐私条款</router-link></li>
      <li style="margin: 0 8px;">|</li>
      <li><router-link to="/use">使用条款</router-link></li>
      <li style="margin: 0 8px;">|</li>
      <li>版权声明</li>
    </ul>
    <p class="flex-row" style="color: rgba(255,255,255,.5);">
      <span class="mg-r-32">Copyright 2017 ©北京梅地卡尔科技有限公司 </span>
      <span>京ICP备 151028673</span>
    </p>
  </div>
</template>
<script>
  export default {
    name: 'Foot'
  }
</script>
<style scoped>
  .foot{
    background: #000;
    text-align: center;
    width: 100%;
    height: 180px;
    padding-top: 30px;
  }
  .foot p{
    color: #fff;
    justify-content: center;
    line-height: 32px;
  }
  .mg-r-32 {
    margin-right: 32px;
  }
  .foot ul{
    color: rgba(255,255,255,.5);
    justify-content: center;
    margin-top: 30px;
  }
  .foot ul li a{
    color: rgba(255,255,255,.5);
  }
  .foot ul li a:hover{
    color: #458df1;
  }
</style>