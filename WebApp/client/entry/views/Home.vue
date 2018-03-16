<template>
  <div id="home">
    <div class="items flex-row">
      <a href="/user/mobilehome" class="item flex-col">
        <img src="../assets/mednotes.svg" alt="患者登记"/>
        <p>患者登记</p>
      </a>
      <a href="/user/pchome" class="item flex-col">
        <img src="../assets/mag.svg" alt="临床研究管理"/>
        <p>临床研究管理</p>
      </a>
      <a href="/user/follow" class="item flex-col">
        <img src="../assets/follow.svg" alt="随访记录"/>
        <p>随访记录</p>
      </a>
      <a href="/user/eform" class="item flex-col">
        <img src="../assets/form.svg" alt="报告表编辑"/>
        <p>报告表编辑</p>
      </a>
      <a class="item flex-col no-drop">
        <img src="../assets/img_gray.svg" alt="影像中心"/>
        <p style="color: #aaa;">影像中心</p>
      </a>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Home'
  }
</script>

<style scoped>
#home {
  margin: 80px auto 0;
  width: 928px;
}

.items {
  flex-wrap: wrap;
}

.item {
  box-sizing: border-box;
  background: #fff;
  align-items: center;
  justify-content: space-between;
  width: 200px;
  height: 200px;
  padding: 40px 0;
  margin: 0 16px 32px;
  color: rgba(0,0,0,.87);
  font-size: 16px;
  cursor: pointer;
}

.item:hover {
  -webkit-box-shadow: rgba(0,0,0,.24) 0px 0px 20px;
  -moz-box-shadow: rgba(0,0,0,.24) 0px 0px 20px;
  box-shadow: rgba(0,0,0,.24) 0px 0px 20px;
}
.item.no-drop {
  cursor: no-drop;
  box-shadow: none;
}
</style>