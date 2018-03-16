<template>
  <transition name="dialog">
    <div class="dialog-mask" v-if="isShow">
      <div class="dialog-wrapper">
        <div class="dialog-container" :style="{width: width + 'px'}">

          <div class="dialog-header">
            <slot name="header">
            </slot>
          </div>

          <div class="dialog-body">
            <slot name="body">
            </slot>
          </div>

          <div class="dialog-footer">
            <slot name="footer"></slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    data () {
      return {
        isShow: true
        // clientHeight: window.innerHeight - 2 * (document.getElementById('nav').clientHeight)
      }
    },
    props: {
      width: {
        type: Number,
        default: 600
      }
    }
  }
</script>
<style scoped>
  .dialog-mask {
    position: fixed;
    z-index: 99999999;
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
    margin: 0 auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: '-apple-system','SF UI Text',Arial,'PingFang SC','Hiragino Sans GB','Microsoft YaHei','WenQuanYi Micro Hei',sans-serif;
  }

  .dialog-header {
    height: 60px;
    line-height: 60px;
    border-bottom: 1px rgba(0, 0, 0, .12) solid;
    padding-left: 32px;
    font-size: 20px;
    color: rgba(0, 0, 0, .87);
    font-weight: 400;
  }

  .dialog-body {
    margin-top: 16px;
  }
  
  .dialog-footer {
    display: flex;
    flex-direction: row-reverse;
    margin-right: 32px;
    margin-top: 40px;
    padding-bottom: 16px;
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