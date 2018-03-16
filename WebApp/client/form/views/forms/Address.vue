<template>
  <div class="address">
    <span class="hint-text">[省] ／ [市] ／ [具体地址] </span>
    <div class="flex-row" style="margin-top: 16px;">
      <div class="flex-row">
        <span class="dec">省：</span>
        <m-select v-model="province" class="option-time">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
      <span class="split-line">|</span>
      <div class="flex-row">
        <span class="dec">市：</span>
        <m-select v-model="city" class="option-time">
          <m-option value="true">显示</m-option>
          <m-option value="false">隐藏</m-option>
        </m-select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Address',
  props: {
    value: Object
  },
  data () {
    return {
      province: true,
      city: true
    }
  },
  mounted () {
    this.province = this.value.province
    this.city = this.value.city
  },
  watch: {
    value () {
      this.province = this.value.province
      this.city = this.value.city
    },
    province () {
      this.handleChange()
    },
    city () {
      this.handleChange()
    }
  },
  methods: {
    handleChange () {
      this.$emit('update:value', {province: this.province, city: this.city})
    }
  }
}
</script>

<style scoped>
.address .dec {
  padding-right: 12px;
}

.split-line {
  color: #999;
  padding: 0 20px;
}
</style>

