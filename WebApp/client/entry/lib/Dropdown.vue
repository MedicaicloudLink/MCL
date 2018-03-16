<template>
  <div class="dropdown-con">
    <div class="trigger" @click="toggleDropdown">
      <slot name="trigger"></slot>
    </div>

    <transition name="fade">
      <ul class="dropdown-menu" v-show="isShow">
        <slot></slot>
      </ul>
    </transition>

  </div>
</template>
<script>
export default {
  name: 'Dropdown',
  data () {
    return {
      isShow: false
    }
  },
  methods: {
    open () {
      this.isShow = true
    },
    close () {
      this.isShow = false
    },
    toggleDropdown () {
      this.eventListen()
      this.isShow ? this.close() : this.open()
    },
    eventListen () {
      window.addEventListener('click', (e) => {
        if (!this.$el.contains(e.target)) this.isShow = false
      }, false)
    }
  }
}
</script>

<style>

  .dropdown-con {
    position: relative;
  }

  .dropdown-menu {
    position: absolute;
    left: 0;
    top: 100%;
    z-index: 200;
    background: #fff;
    min-width: 100px;
    list-style: none;
    margin: 2px 0 0;
    padding: 6px 0;
    font-size: 14px;
    text-align: left;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2);
    overflow: auto;
  }



  /*动画效果*/
  .fade-enter-active {
    transition: all .3s ease;
  }
  .fade-leave-active {
    transition: all .3s ease;
  }
  .fade-enter, .fade-leave-active {
    transform: translateY(-6px);
    opacity: 0;
  }
</style>
