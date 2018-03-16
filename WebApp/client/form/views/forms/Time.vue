<template>
  <div class="time">
    <span class="hint-text">[--] : [--] : [--] （小时-分-秒 格式／24小时制）</span>
    <div class="flex-row" style="margin-top: 16px;">
      <div class="flex-row">
        <span class="dec">分：</span>
        <m-select v-model="minute" class="option-time">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
      <span class="split-line">|</span>
      <div class="flex-row">
        <span class="dec">秒：</span>
        <m-select v-model="second" class="option-time">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MTime',
  props: {
    value: Object
  },
  data () {
    return {
      minute: true,
      second: true
    }
  },
  mounted () {
    this.minute = this.value.minute
    this.second = this.value.second
  },
  watch: {
    value () {
      this.minute = this.value.minute
      this.second = this.value.second
    },
    minute () {
      this.handleChange()
    },
    second () {
      this.handleChange()
    }
  },
  methods: {
    handleChange () {
      this.$emit('update:value', {minute: this.minute, second: this.second})
    }
  }
}
</script>

<style scoped>
.time .hint-text {
  display: block;
  width: 100%;
  margin: 8px 0;
  color: #999;
  border-bottom: 1px dashed #ddd;
}

.time .dec {
  padding-right: 12px;
  color: #999;
}

.option-time {
  width: 100px;
}

.split-line {
  color: #999;
  padding: 0 20px;
}
</style>

