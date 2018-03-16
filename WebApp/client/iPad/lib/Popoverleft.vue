<template>
  <transition name="slide-fade">
    <div class="popover" v-show="state" :style="{top: position.top + 'px', left: position.left + 'px'}">
      <slot></slot>
    </div>
  </transition>
</template>

<script>
  export default {
    name: 'Popover',
    data () {
      return {
        state: false,
        position: {top: 0, left: 0, right: 0, bottom: 0}
      }
    },
    methods: {
      show (el) {
        this.state = !this.state
        this.setPosition(el)
        this.clickEvent()
      },
      setPosition (el) {
        const p = {
          w: el.offsetWidth,
          h: el.offsetHeight,
          t: el.offsetTop,
          l: el.offsetLeft
        }
        this.position.top = p.h + p.t
        this.position.left = p.l
      },
      hide () {
        this.state = false
      },
      clickEvent () {
        document.addEventListener('click', (e) => {
          e = e || window.event
          if (this.$el === undefined) { return }
          // 判断是否点中所选区域
          if (this.clickRegion(this.$el, e.clientX, e.clientY)) {
            this.state = false
          } else {
            this.state = false
          }
        }, false)
      },
      /** 点击点和所选区域是否重叠， true点击所选区域，false点击点位于所选区域外 */
      clickRegion (el, x, y) {
        // p => el postion, get {width, height, top, left}
        let p = {
          w: el.offsetWidth,
          h: el.offsetHeight,
          t: el.offsetTop,
          l: el.offsetLeft
        }
        if (x < (p.l + p.w) && x > p.l && y < (p.t + p.h) && y > p.t) {
          return false
        } else {
          return false
        }
      }
    }
  }
</script>

<style scoped>
  .popover {
    position: absolute;
    background: #fff;
    color: #222;
    border: 1px solid rgba(204, 204, 204, 1);
    z-index: 1000;
  }
  
  .slide-fade-enter-active {
    transition: all .3s ease;
  }
  .slide-fade-leave-active {
    transition: all .3s ease;
  }
  .slide-fade-enter, .slide-fade-leave-active {
    transform: translate(-5px);
    opacity: 0;
  }
</style>