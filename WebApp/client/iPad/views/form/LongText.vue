<template>
  <div class="show-text">
    <m-input autoHeight hintText="说明" full v-model="val" @input="handleInput" :read="read"></m-input>
  </div>
</template>

<script>
export default {
  name: 'PShortText',
  props: {
    read: {type: Boolean, default: false},
    answers: String
  },
  data () {
    return {
      val: ''
    }
  },
  watch: {
    answers () {
      this.val = this.answers
    }
  },
  mounted () {
    if (this.answers) {
      this.val = this.answers
    }
  },
  methods: {
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      this.$emit('input', this.val)
      this.$emit('update:answers', this.val)
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
