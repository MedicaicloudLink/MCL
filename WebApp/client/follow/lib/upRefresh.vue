<template>
  <div class="refresh" :style="{height: elheight + 'px'}">
    <slot></slot>
  </div>
</template>

<script>
import EventListener from '../tool/EventListener'
import { throttle } from '../tool/tools'
export default {
  name: 'Refresh',
  props: {
    elheight: Number
  },
  mounted () {
    this._closeEvent = EventListener.listen(this.$el, 'scroll', (e) => {
      // this.handleDown()
      throttle(this.handleDown(), 10000)
    })
  },
  destroyed () { if (this._closeEvent) this._closeEvent.remove() },
  methods: {
    handleDown () {
      if ((this.elheight + this.$el.scrollTop) >= this.$el.scrollHeight) {
        this.$emit('isdown')
      }
    }
  }
}
</script>

<style>
.refresh {
  background: #fff;
  overflow-y: scroll;
}

.refresh::-webkit-scrollbar {
  width: 8px;
}
  
.refresh::-webkit-scrollbar-track {
  background-color: #fff;
}

.refresh::-webkit-scrollbar-thumb {
  border-radius: 0px;
  background: #b4b4b4;
}
</style>

