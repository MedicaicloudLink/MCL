<template>
  <div class="short-text">
    <span class="hint-text">填写回复的文本（30个字以内）</span>
    <div class="flex-row" style="margin-top: 16px;">
      <span class="dec">单位：</span>
      <m-select v-model="unit" extra class="short-text-option" maxOptionHeight="300px">
        <m-option value="">无</m-option>
        <m-option value="kg">kg</m-option>
        <m-option value="g">g</m-option>
        <m-option value="cm">cm</m-option>
        <m-option value="mg">mg</m-option>
        <m-option value="mm">mm</m-option>
        <m-option value="L">L</m-option>
        <m-option value="ml">ml</m-option>
        <m-option value="mmol/L">mmol/L</m-option>
        <m-option value="mmHg">mmHg</m-option>
        <m-option value="Kpa">Kpa</m-option>
        <m-option value="bpm">bpm</m-option>
        <m-option value="mg/L">mg/L</m-option>
        <m-option value="g/L">g/L</m-option>
        <m-option value="IU/L">IU/L</m-option>
        <m-option value="μmol/L">μmol/L</m-option>
        <m-option value="%">%</m-option>
        <m-option value="mic/s">mic/s</m-option>
        <m-option value="Hz">Hz</m-option>
        <m-option value="μm/s">μm/s</m-option>
        <m-option value="vol/dl">vol/dl</m-option>
        <m-option value="ml/dl">ml/dl</m-option>
        <m-option value="miu/ml">miu/ml</m-option>
        <m-option value="μg">μg</m-option>
        <m-option value="mol/L">mol/L</m-option>
        <m-option value="片">片</m-option>
        <m-option value="次">次</m-option>
        <m-option value="天">天</m-option>
        <m-option value="月">月</m-option>
        <m-option value="年">年</m-option>
        <m-option value="丸">丸</m-option>
        <m-option value="粒">粒</m-option>
        <m-option value="分钟">分钟</m-option>
        <m-option value="小时">小时</m-option>
        <m-option value="秒">秒</m-option>
      </m-select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ShortText',
  props: {
    value: {type: String, default: ''}
  },
  data () {
    return {
      unit: ''
    }
  },
  mounted () {
    this.unit = this.value
  },
  watch: {
    value () {
      this.unit = this.value
    },
    unit () {
      this.$emit('update:value', this.unit)
    }
  }
}
</script>

<style scoped>
.short-text .dec {
  padding-right: 12px;
}

.short-text-option {
  width: 150px;
}
</style>


