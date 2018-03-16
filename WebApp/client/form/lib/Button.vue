<template>
  <span :disabled="disabled" class="button"
    @click="handleClick"
    :autofocus="autofocus"
    :class="[
      type ? 'button-' + type : '',
      size ? 'button-' + size : '',
      {
        'is-disabled': disabled,
        'is-loading': loading,
        'is-plain': plain
      }
    ]">
      <i class="iconfont icon-loading" v-if="loading"></i>
      <span v-if="$slots.default"><slot></slot></span>
      <span ref="effect" class="ink"
        :class="{animate: effect.state}"
        :style="{height: effect.height + 'px', width: effect.height + 'px', top: effect.top + 'px', left: effect.left + 'px'}">
      </span>
    </span>
</template>

<script>
  export default {
    name: 'MButton',
    props: {
      type: {
        type: String,
        default: 'default'
      },
      size: String,
      loading: Boolean,
      disabled: Boolean,
      plain: Boolean,
      autofocus: Boolean
    },
    data () {
      return {
        effect: {
          state: true,
          height: '',
          width: '',
          top: '',
          left: ''
        }
      }
    },
    methods: {
      handleClick (evt) {
        this.effect.state = false
        let d = Math.max(this.$el.offsetWidth, this.$el.offsetHeight)
        this.effect.height = d
        this.effect.width = d
        this.effect.left = evt.pageX - this.$el.offsetLeft - d / 2
        this.effect.top = evt.pageY - this.$el.offsetTop - d / 2
        this.$nextTick(function () {
          this.effect.state = true
        })

        if (!this.loading) { this.$emit('click', evt) }
      }
    }
  }
</script>

<style>
  .button {
    display: inline-block;
    position: relative;
    padding: 6px 18px;
    font-size: 14px;
    border-radius: 2px;
    cursor: pointer;
    line-height: 1.3;
    border: 1px solid rgba(0, 0, 0, 0);
    /*font-family: '宋体';*/
    overflow:hidden;
    -webkit-transition: all 0.2s ease;
    -moz-transition: all 0.2s ease;
    -o-transition: all 0.2s ease;
    transition: all 0.2s ease;
  }

  .button:hover {
    opacity: 0.8;
  }
  /* button background style */
  .button-blue {
    background: #468df1;
    color: #fff;
  }

  .button-gray {
    background: #fff;
    color: #333;
    border: 1px solid rgba(0, 0, 0, 0.4);
  }

  .button.is-disabled {
    color: #c0ccda;
    cursor: not-allowed;
    background-image: none;
    background-color: #eff2f7;
    border-color: #d3dce6;
  }

  .button.is-loading {
    cursor: not-allowed;
  }

  .button.is-loading:before {
    pointer-events: none;
    content: "";
    position: absolute;
    left: -1px;
    top: -1px;
    right: -1px;
    bottom: -1px;
    border-radius: inherit;
    background-color: hsla(0,0%,100%,.35);
  }

  .icon-loading {
    display: inline-block;
    animation: rotating 1s linear infinite;
  }

  /* 点击效果 */
  .ink {
    display: block;
    position: absolute;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 100%;
    -webkit-transform:scale(0);
      -moz-transform:scale(0);
        -o-transform:scale(0);
            transform:scale(0);
  }

  .animate {
    -webkit-animation:ripple 0.65s linear;
    -moz-animation:ripple 0.65s linear;
      -ms-animation:ripple 0.65s linear;
      -o-animation:ripple 0.65s linear;
          animation:ripple 0.65s linear;
  }

  @-webkit-keyframes ripple {
      100% {opacity: 0; -webkit-transform: scale(2.5);}
  }
  @-moz-keyframes ripple {
      100% {opacity: 0; -moz-transform: scale(2.5);}
  }
  @-o-keyframes ripple {
      100% {opacity: 0; -o-transform: scale(2.5);}
  }
  @keyframes ripple {
      100% {opacity: 0; transform: scale(2.5);}
  }


  @keyframes rotating {
    0% {
      transform: rotate(0deg)
    }

    to {
      transform: rotate(1turn)
    }
  }
</style>