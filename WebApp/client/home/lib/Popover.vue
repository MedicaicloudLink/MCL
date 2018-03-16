<template>
  <transition name="slide-fade">
    <div class="popover" v-show="state" :style="{top: position.top + 'px', right: position.right + 'px'}">
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
        // console.log(el)
        this.setPosition(el)
        this.clickEvent()
        this.state = true
      },
      setPosition (el) {
        const broswerWidth = window.innerWidth
        const p = {
          w: el.offsetWidth,
          h: el.offsetHeight,
          t: el.offsetTop,
          l: el.offsetLeft
        }

        this.position.top = p.h + p.t
        this.position.right = broswerWidth - p.l - p.w
        if (document.body.scrollHeight > window.innerHeight) {
          this.position.right = this.position.right - 8
        }
      },
      hide () {
        this.state = false
      },
      clickEvent () {
        window.addEventListener('click', (e) => {
          if (!this.$el.contains(e.target)) this.state = false
        }, false)
      }
    }
  }
</script>

<style>
  .popover {
    position: absolute;
    background: #fafafa;
    color: #222;
    border: 1px solid rgba(204, 204, 204, 1);
    z-index: 2000;
  }

  .slide-fade-enter-active {
    transition: all .3s ease;
  }
  .slide-fade-leave-active {
    transition: all .3s ease;
  }
  .slide-fade-enter, .slide-fade-leave-active {
    transform: translate(5px);
    opacity: 0;
  }
</style>