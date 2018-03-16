<template>
  <span class="button"
    @click="handleClick"
    :style="{width: width + 'px'}"
    :class="[
      type ? 'button-' + type : '',
      {
        'is-disabled': disabled,
        'is-loading': loading
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
      },  // 按钮类型 颜色
      loading: Boolean, // 加载动画
      disabled: Boolean,
      width: {
        type: Number,
        default: 64
      }
    },
    methods: {
      handleClick (evt) {
        this.$emit('click', evt)
      }
    }
  }
</script>

<style scoped>
  .button {
    height: 32px;
    width: 64px;
    line-height: 30px;
    display: inline-block;
    position: relative;
    font-size: 14px;
    cursor: pointer;
    text-align: center;
  }
  /* button background style */
  .button-blue {
    color: #fff;
    background: #458df1;
    border: 1px #458df1 solid;
  }
  .button-blue:hover{
    opacity: .8
  }
  .button-gray {
    background: #fff;
    color: rgba(0, 0, 0, .87);
    border: 1px rgba(0, 0, 0, .12) solid;
  }
  .button-gray:hover {
    border: 1px solid #468df1;
    color: #468df1;
  }
  .button.is-disabled {
    color: #ccc;
    cursor: not-allowed;
    background: #eff2f7;
    border-color: #d3dce6;
  }

  .button.is-loading {
    cursor: not-allowed;
  }

  .icon-loading {
    display: inline-block;
    animation: rotating 1s linear infinite;
  }
  .icon-loading:before { content: "\e703"; }
  
  @keyframes rotating {
    0% {
      transform: rotate(0deg)
    }

    to {
      transform: rotate(1turn)
    }
  }
</style>