<template>
  <transition name="fade">
    <div ref="dom" class="toast" :class="placement ? 'toast-' + placement : ''" :style="{width: width + 'px'}" v-show="show" @mouseenter="clearTimer" @mouseleave="startTimer">
      <img class="toast-img" :src="typeImg" />
      <div class="toast-group">
        <p>{{ text }}</p>
        <div v-if="showClose" class="toast-closeBtn icon-x iconfont" @click="close"></div>
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    name: 'Toast',
    props: {
      show: {
        type: Boolean,
        default: false
      },
      placement: {
        type: String,
        default: 'center'
      },
      type: {
        type: String,
        default: 'default'
      },
      duration: {
        type: Number,
        default: 2500
      },
      showClose: {
        type: Boolean,
        default: false
      },
      text: {
        type: String,
        default: ''
      },
      width: {
        type: String
      }
    },
    data () {
      return {
        timer: null
      }
    },
    computed: {
      typeImg () {
        return require('../../assets/warning_svg/' + this.type + '.svg')
      }
    },
    watch: {
      show: function (val) {
        if (val) {
          this.$nextTick(function () {
            if (this.placement === 'top' || this.placement === 'bottom') {
              this.$refs.dom.style.marginLeft = -1 * this.$refs.dom.offsetWidth / 2 + 'px'
            } else if (this.placement === 'center') {
              this.$refs.dom.style.marginLeft = -1 * this.$refs.dom.offsetWidth / 2 + 'px'
              this.$refs.dom.style.marginTop = -1 * this.$refs.dom.offsetHeight / 2 + 'px'
            }
          })
        }
      }
    },
    mounted () {
      this.startTimer()
    },
    methods: {
      clearTimer () {
        clearTimeout(this.timer)
      },
      startTimer () {
        let that = this
        if (this.duration > 0) {
          this.timer = setTimeout(() => {
            that.close()
          }, this.duration)
        }
      },
      close () {
        this.show = false
        this.clearTimer()
      }
    }
  }
</script>

<style scoped>
  .toast {
    width: 300px;
    overflow: hidden;
    padding: 10px 12px;
    display: inline-block;     
    box-shadow: 0 2px 4px rgba(0,0,0,.12),0 0 6px rgba(0,0,0,.04);    
    border-radius: 3px;
    font-size: 16px;
    line-height: 1;
    color: #000;
    background: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    transition: opacity .3s;
    position: fixed;
    z-index: 9999;
    cursor: default;
  }

  /* position */
  .toast-top {
    top: 70px;
    margin: 0 auto;
    left: 50%;
  }
  .toast-bottom {
    bottom: 20px;
    margin: 0 auto;
    left: 50%;
    top: initial;
  }
  .toast-center {
    margin: 0 auto;
    left: 50%;
    top: 50%;
  }
  .toast-top-right {
    top: 20px;
    right: 30px;
  }
  .toast-top-left {
    top: 20px;
    left: 30px;
  }
  .toast-bottom-right {
    bottom: 20px;
    right: 30px;
  }
  .toast-bottom-left {
    bottom: 30px;
    left: 30px;
  }

  .toast-img {
    width: 40px;
    height: 40px;
    position: absolute;
    left: 0;
    top: 0;
  }
  .toast-group {
    margin-left: 38px;
    position: relative;
    height: 20px;
    line-height: 20px
  }
  .toast-group p {
    font-size: 14px;
    margin: 0 34px 0 0;
    color: #8391a5;
    text-align: justify;
  }
  .toast-closeBtn {
    top: 1px;
    right: 0;
    position: absolute;
    cursor: pointer;
    color: #bfcbd9;
    font-size: 14px;
  }
  .toast-closeBtn:hover {color: #000}
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s
  }
  .fade-enter, .fade-leave-active {
    opacity: 0
  }
</style>