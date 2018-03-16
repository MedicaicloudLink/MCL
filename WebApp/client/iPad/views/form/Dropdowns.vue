<template>
  <div class="multiple dropdown">
    <m-select v-model="val" maxOptionHeight="300px" @input="handleInput" :read="read">
      <m-option v-for="item, index in option" :key="index" :value="item.text">{{ item.text }}</m-option>
    </m-select>
  </div>
</template>

<script>
export default {
  name: 'PDropdowns',
  props: {
    read: {type: Boolean, default: false},
    option: Array,
    answers: String
  },
  data () {
    return {
      val: this.answers
    }
  },
  watch: {
    answers () {
      this.val = this.answers
    }
  },
  methods: {
    // next 和 val 是否有对应
    nextDetection () {
      for (let v in this.option) {
        if (this.option[v].text === this.val && this.option[v].next.length > 2) return this.option[v].next
      }

      return false
    },
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      const nextResult = this.nextDetection()
      this.$emit('next', nextResult)
      this.$emit('input', this.val)
      this.$emit('update:answers', this.val)
    }
  }
}
</script>

<style scoped>
.dropdown-icon {
  display: inline-block;
  margin-right: 8px;
  font-size: 16px;
  color: #aaa;
}

</style>

