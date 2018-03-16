<template>
  <transition name="fade">
  	<div 
      ref="dom"
      :class="classObj"
  		@click="handleClick"
  		v-if="show">
  		<h5 v-text="text"></h5>
  	</div>
  </transition>
</template>

<script>
  export default {
    name: 'Toast',
    props: {
      top: {
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
      closeOnClick: {
        type: Boolean,
        default: true
      },
      text: {
        type: String
      },
      width: {
        type: String
      }
    },
    data () {
      return {
        setT: '',
        show: false
      }
    },
    computed: {
      classObj () {
        let {placement, type} = this
        let klass = {}
        klass['toast'] = true
        klass['toast-' + type] = true
        klass['toast-' + placement] = true
        return klass
      }
    },
    watch: {
      show: {
        handler (val, newVal) {
          this.setT = window.clearTimeout(this.setT)
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
        },
        immediate: true
      }
    },
    methods: {
      handleClick () {
        if (this.closeOnClick) {
          this.show = false
        }
      }
    }
  }
</script>

<style scoped>
  .toast {
    position: fixed;
    border-radius: 5px;
    padding: 10px 25px;
    transition-property: opacity;
    transition-duration: 400ms;
    display: inline-block;
    font-size: 16px;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    cursor: default;
    z-index: 1500;
  }

  .toast-default {
    background: #41a8ff;
  }

  .toast-top {
    top: 20px;
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

  .toast-success {
    background: #03d7a3;
    color: #fff;
  }

  .toast-error {
    background: #ff8140;
    color: #fff;
  }

  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s
  }
  .fade-enter, .fade-leave-active {
    opacity: 0
  }
</style>