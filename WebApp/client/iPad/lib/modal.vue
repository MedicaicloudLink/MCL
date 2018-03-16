<template>
  <transition name="modal">
    <div class="modal-mask" v-show="open">
      <div class="modal-wrapper">
        <div class="modal-container" :style="{ maxHeight: clientHeight + 'px', width: width + 'px' }">

         <h3 class="dialog-title" style="border-bottom: 1px #ddd solid;">{{ title }}</h3>

          <div class="modal-body">
            <slot></slot>
          </div>

          <div class="modal-footer">
            <slot name="footer"></slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import EventListener from '../tool/EventListener.js'
export default {
  name: 'Modal',
  props: {
    width: Number,
    title: String,
    open: Boolean
  },
  data () {
    return {
      clientHeight: window.innerHeight - (2 * 56)
    }
  },
  mounted () {
    this.cancel()
  },
  watch: {
    open () { this.cancel() }
  },
  beforeDestroy () {
    if (this._closeEvent) this.close()
  },
  methods: {
    close () {
      this.$emit('close')
      if (this._closeEvent) this._closeEvent.remove()
    },
    // 点击模态框非内容区域关闭
    cancel () {
      if (this.open) return
      this._closeEvent = EventListener.listen(this.$el, 'click', (e) => {
        let content = this.$el.getElementsByClassName('modal-container')[0]
        let rect = content.getBoundingClientRect()
        if (e.clientX > rect.left && e.clientX < (rect.left + rect.width) && e.clientY > rect.top && e.clientY < (rect.top + rect.height)) {
          console.log('is click center')
        } else {
          console.log('not click center')
          this.close()
        }
      })
    }
  }
}
</script>

<style>
  .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .37);
    display: table;
    transition: opacity .3s ease;
  }

  .modal-wrapper {
    display: table-cell;
    line-height: 1.7;
    vertical-align: middle;
  }

  .modal-container {
    width: 75%;
    max-width: 768px;
    margin: 0px auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;
  }

  .dialog-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 24px 20px;
    margin: 0;
    font-size: 22px;
    font-weight: 400;
    line-height: 32px;
    color: rgba(0,0,0,.87);
  }

  .modal-body {
    color: rgba(0,0,0,.6);
  }

  .modal-footer {
    min-height: 48px;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }


  /*
  * The following styles are auto-applied to elements with
  * transition="modal" when their visibility is toggled
  * by Vue.js.
  *
  * You can easily play with the modal transition by editing
  * these styles.
  */

  .modal-enter {
    opacity: 0;
  }

  .modal-leave-active {
    opacity: 0;
  }

  .modal-enter .modal-container,
  .modal-leave-active .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
  }

  ::-webkit-scrollbar {
    width: 8px;
  }
  
  ::-webkit-scrollbar-track {
    background-color: #ebebeb;
  }

  ::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background: #999; 
  }
</style>