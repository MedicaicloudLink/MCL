<template>
  <div class="show-text">
    <m-input hintText="说明" v-model="val" @input="handleInput"></m-input>
    <span>{{ option.unit }}</span>
  </div>
</template>

<script>
export default {
  name: 'PShortText',
  props: {
    option: Object,
    answers: Object
  },
  data () {
    return {
      val: ''
    }
  },
  watch: {
    answers () {
      this.val = this.answers.text
    }
  },
  mounted () {
    if (this.answers) {
      this.val = this.answers.text
    }
  },
  methods: {
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      this.$emit('input', this.val + this.option.unit)
      this.$emit('update:answers', {text: this.val, unit: this.option.unit})
    }
  }
}
</script>

<style scoped>
.hint-text {
  display: inline-block;
  width: 50%;
  color: #999;
  border-bottom: 1px dashed #ddd;
}
</style>


