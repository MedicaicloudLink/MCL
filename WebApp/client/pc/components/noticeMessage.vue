<template>
  <transition name="el-message-fade">
    <div class="el-message" v-show="show" @mouseenter="clearTimer" @mouseleave="startTimer">
      <img class="el-message-img" :src="typeImg"/>
      <div class="el-message-group">
        <p>{{ message }}</p>
        <div v-if="showClose" class="el-message-closeBtn icon-x iconfont" @click="$emit('close'), clearTimer"></div>
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    data () {
      return {
        timer: null
      }
    },
    props: {
      show: {
        type: Boolean,
        twoWay: true,
        default: false
      },
      message: {
        type: String,
        default: ''
      },
      duration: {
        type: Number,
        twoWay: true,
        default: 5000
      },
      showClose: {
        type: Boolean,
        twoWay: true,
        default: false
      },
      type: {
        type: String,
        default: 'info'
      }
    },
    computed: {
      typeImg () {
        return require('../assets/svg/' + this.type + '.svg')
      }
    },
    mounted () {
      this.startTimer()
    },
    watch: {
      'show': function (val) {
        if (val === true) {
          this.startTimer()
        }
      }
    },
    methods: {
      clearTimer () {
        clearTimeout(this.timer)
      },
      startTimer () {
        let that = this
        if (this.duration > 0) {
          this.timer = setTimeout(() => {
            that.clearTimer()
            that.$emit('close')
          }, this.duration)
        }
      }
    }
  }
</script>
<style>
  .el-message {
    box-shadow: 0 2px 4px rgba(0,0,0,.12),0 0 6px rgba(0,0,0,.04);
    min-width: 300px;
    padding: 10px 12px;
    box-sizing: border-box;
    border-radius: 3px;
    position: fixed;
    left: 50%;
    top: 40px;
    z-index: 9999;
    -ms-transform: translateX(-50%);
    transform: translateX(-50%);
    background-color: #fff;
    transition: opacity .3s,transform .4s;
    overflow:hidden
  }
  .el-message-img {
    width: 40px;
    height: 40px;
    position: absolute;
    left: 0;
    top: 0;
  }
  .el-message-group {
    margin-left: 38px;
    position: relative;
    height: 20px;
    line-height: 20px
  }
  .el-message-group p {
    font-size: 14px;
    margin: 0 34px 0 0;
    white-space: nowrap;
    color: #8391a5;
    text-align: justify;
  }
  .el-message-closeBtn {
    top: 1px;
    right: 0;
    position: absolute;
    cursor: pointer;
    color: #bfcbd9;
    font-size: 14px;
  }
  .el-message-closeBtn:hover {color:#97a8be}
  .el-message-fade-enter,.el-message-fade-leave-active {
    opacity: 0;
    -ms-transform: translate(-50%,-100%);
    transform: translate(-50%,-100%);
  }
</style>