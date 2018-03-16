<template>
  <div class="linear-scale">
    <div class="flex-row">
      <m-select v-model="left" @input="handleChange">
        <m-option :value="(n - 1)" v-for="n in 10">{{ (n - 1) }}</m-option>
      </m-select>
      <span class="split-text">到</span>
      <m-select v-model="right" @input="handleChange">
        <m-option :value="n" v-for="n in 10">{{ n }}</m-option>
      </m-select>
    </div>

    <div class="flex-col" style="margin-top: 20px;">
      <div class="flex-row">
        <span class="dec">{{ left }}</span>
        <m-input :value.sync="leftText" @input="handleChange" hintText="输入解释性说明（可选项）" :preview="!isactive" :fontsize="14"></m-input>
      </div>
      <div class="flex-row">
        <span class="dec">{{ right }}</span>
        <m-input :value.sync="rightText" @input="handleChange" hintText="输入解释性说明（可选项）" :preview="!isactive":fontsize="14"></m-input>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LinearScale',
  props: {
    value: Object,
    isactive: Boolean
  },
  data () {
    return {
      left: 0,
      right: 5,
      leftText: '',
      rightText: ''
    }
  },
  mounted () {
    this.left = this.value.left
    this.right = this.value.right
    this.leftText = this.value.leftText
    this.rightText = this.value.rightText
  },
  watch: {
    value () {
      this.left = this.value.left
      this.right = this.value.right
      this.leftText = this.value.leftText
      this.rightText = this.value.rightText
    }
  },
  methods: {
    handleChange () {
      this.$emit('update:value',
        {
          left: this.left,
          right: this.right,
          leftText: this.leftText,
          rightText: this.rightText
        })
    }
  }
}
</script>
<style scoped>
.linear-scale .dec {
  font-size: 14px;
  line-height: 30px;
  width: 20px;
}

.split-text {
  margin: 0 12px;
}
</style>

