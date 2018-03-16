<template>
  <transition name="modal">
    <div class="modal-mask" v-if="isShow">
      <div class="modal-wrapper">
        <div class="modal-container" :style="{ maxHeight: clientHeight + 'px', width: width + 'px' }">

          <div class="modal-header">
            <slot name="header"></slot>
          </div>

          <div class="modal-body">
            <slot name="body"></slot>
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
  export default {
    name: 'Modal',
    props: {
      width: Number
    },
    data () {
      return {
        isShow: true,
        clientHeight: window.innerHeight - (2 * document.getElementById('header').clientHeight)
      }
    },
    methods: {
      close () {
        this.isShow = false
      },
      open () {
        this.isShow = true
      },
      confirm () {
        this.$emit('confirm')
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
    background-color: rgba(0, 0, 0, .5);
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
    margin: 0px auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;
  }

  .modal-header {
    color: #42b983;
    padding: 0 6px;
    border-bottom: 1px solid #eee;
  }

  .modal-body {
    padding: 0 6px;
  }

  .modal-footer {
    text-align: right;
  }

  .modal-default-button {
    float: right;
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