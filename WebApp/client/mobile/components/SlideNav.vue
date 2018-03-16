<template>
  <transition name="slide-fade">
    <div id="slide-nav" :style="{height: navHeight + 'px'}" v-if="show">
      <div>medicayun 病历卡</div>
      <router-link to="/">主页</router-link>
      <div class="menu">
        <p><router-link :to="'/project/' + projectid + '/create'">创建新患者</router-link></p>
        <router-link :to="'/project/' + projectid + '/patients'">所有患者</router-link>
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    name: 'SlideNav',
    props: {
      visible: {
        type: Boolean,
        default: false,
        required: true
      }
    },
    data () {
      return {
        show: false,
        broswerHeight: '',
        broswerWidth: '',
        navHeight: ''
      }
    },
    watch: {
      visible (val, oldVal) {
        this.show = val
      }
    },
    mounted () {
      this.show = this.visible
      this.broswerHeight = window.innerHeight
      this.broswerWidth = window.innerWidth
      this.navHeight = this.broswerHeight - 56

      this.clickOther()
    },
    methods: {
      close () {
        this.show = false
        this.$emit('close')
      },
      clickOther () {
        let time = window.setTimeout(() => {
          document.addEventListener('click', (e) => {
            e = e || window.event
            if (this.$el === undefined) { return }
            if (e.clientX > this.$el.offsetWidth) {
              this.close()
              time.clearn
            }
          })
        }, 0)
      }
    }
  }
</script>

<style>
  #slide-nav {
    position: fixed;
    top: 56px;
    left: 0;
    width: 180px;
    background: #fff;
    border-right: 1px solid #fafafa;
    z-index: 100;
  }

  .slide-fade-enter-active {
    transition: all .3s ease;
  }
  .slide-fade-leave-active {
    transition: all .3s ease;
  }
  .slide-fade-enter, .slide-fade-leave-active {
    transform: translateX(20px);
    opacity: 0;
  }

</style>