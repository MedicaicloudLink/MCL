<template>
  <div class="multiple radio flex-col">
    <div class="option flex-col" v-for="o in option">
      <img :src="'http://' + o.img" @click="imgRadio(o.text)" v-if="o.img.length > 10">
      <m-radio :nativeValue="o.text" v-model="val" @input="handleChange" :read="read">{{ o.text }}</m-radio>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PMultipleChoice',
  props: {
    read: {type: Boolean, default: false},
    option: Array,
    answers: {type: String, default: ''}
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
    imgRadio (val) {
      this.val = val
      this.handleChange()
    },
    // next 和 val 是否有对应
    nextDetection () {
      for (let v in this.option) {
        if (this.option[v].text === this.val && this.option[v].next.length > 2) return this.option[v].next
      }
      return false
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
.option {
  margin-bottom: 8px;
}

.multiple img {
    margin-left: 30px;
    display: inline-block;
    width: auto;
    height: auto;
    box-shadow: 0 1px 1px 0 rgba(0,0,0,0.14), 0 2px 1px -1px rgba(0,0,0,0.12), 0 1px 3px 0 rgba(0,0,0,0.2);
    max-width: 180px;
    max-height: 120px;
}

</style>

