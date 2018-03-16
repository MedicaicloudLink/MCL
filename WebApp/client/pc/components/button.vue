<template>
  <span class="button"
    @click="handleClick"
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
      disabled: Boolean
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
    display: inline-block;
    position: relative;
    padding: 8px 15px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    line-height: 1.3;
  }
  /* button background style */
  .button-blue {
    background: #20a0ff;
    color: #fff;
  }
  .button-blue:hover{
    opacity: .8
  }
  .button-gray {
    background: #fff;
    color: #333;
    border: 1px solid #bbb;
  }
  .button-gray:hover {
    border: 1px solid #20a0ff;
    color: #20a0ff;
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