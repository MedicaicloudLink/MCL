<template>
  <div class="date">
    <span class="hint-text">[----] : [--] : [--] （年-月-日 格式）</span>
    <div class="flex-row" style="margin-top: 16px;">
      <div class="flex-row">
        <span class="dec">年：</span>
        <m-select v-model="year" class="option-date">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
      <span class="split-line">|</span>
      <div class="flex-row">
        <span class="dec">月：</span>
        <m-select v-model="month" class="option-date">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
      <span class="split-line">|</span>
      <div class="flex-row">
        <span class="dec">日：</span>
        <m-select v-model="day" class="option-date">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MDate',
  props: {
    value: Object
  },
  data () {
    return {
      year: true,
      month: true,
      day: true
    }
  },
  mounted () {
    this.year = this.value.year
    this.month = this.value.month
    this.day = this.value.day
  },
  watch: {
    value () {
      this.year = this.value.year
      this.month = this.value.month
      this.day = this.value.day
    },
    year () {
      this.handleChange()
    },
    month () {
      this.handleChange()
    },
    day () {
      this.handleChange()
    }
  },
  methods: {
    handleChange () {
      this.$emit('update:value', {year: this.year, month: this.month, day: this.day})
    }
  }
}
</script>

<style scoped>
.date .dec {
  padding-right: 12px;
  color: #999;
}

.option-date {
  width: 100px;
}

.split-line {
  color: #999;
  padding: 0 20px;
}
</style>

