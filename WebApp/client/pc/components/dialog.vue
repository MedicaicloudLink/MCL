<template>
  <transition name="dialog">
    <div class="dialog-mask">
      <div class="dialog-wrapper">
        <div class="dialog-container">

          <div class="dialog-header">
            <slot name="header">
              default header
            </slot>
          </div>

          <div class="dialog-body">
            <slot name="body">
              default body
            </slot>
          </div>

          <div class="dialog-footer">
            <slot name="footer">
              default footer
              <!--<button class="dialog-default-button" @click="$emit('close')">
                取消
              </button>-->
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
  .dialog-mask {
    position: fixed;
    z-index: 999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
  }

  .dialog-wrapper {
    display: table-cell;
    vertical-align: middle;
  }

  .dialog-container {
    width: 350px;
    margin: 0 auto;
    padding: 10px 20px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: '-apple-system','SF UI Text',Arial,'PingFang SC','Hiragino Sans GB','Microsoft YaHei','WenQuanYi Micro Hei',sans-serif;
  }

  .dialog-header {
    line-height: 3em;
    /*color: #42b983;*/
    border-bottom: 1px #eee solid;
  }

  .dialog-body {
    margin: 20px 0;
  }
  

  .dialog-enter {
    opacity: 0;
  }

  .dialog-leave-active {
    opacity: 0;
  }

  .dialog-enter .dialog-container,
  .dialog-leave-active .dialog-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
  }
</style>