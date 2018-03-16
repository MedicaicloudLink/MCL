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
    methods: {
      handleClick (evt) {
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

  @keyframes rotating {
    0% {
      transform: rotate(0deg)
    }

    to {
      transform: rotate(1turn)
    }
  }
</style>