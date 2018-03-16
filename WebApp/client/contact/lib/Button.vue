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
        default: 'gray'
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
          state: false,
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
    padding: 4px 18px;
    font-size: 14px;
    border-radius: 2px;
    cursor: pointer;
     line-height: 1.8; 
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
    transition: all 0.2s ease;
    letter-spacing: 2px;
  }

  .button:hover, .button:active {
    box-shadow: 0 3px 8px 0 rgba(0, 0, 0, 0.38);
  }

  /* button background style */
  .button-blue {
    background: #468df1;
    color: #fff;
  }

  .button-gray {
    background: #f2f3f3;
    color: #333;
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
    background: rgba(0, 0, 0, .2);
    border-radius: 100%;
    transform: scale(0);
  }

  .animate {
    animation: button-wave 0.65s linear;
  }

  @keyframes button-wave {
    100% {
      opacity: 0;
      transform: scale(2.5);
    }
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