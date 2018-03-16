<template>
  <div class="multiple radio flex-col">
    <div class="option flex-col" v-for="(o, index) in option" :key="index">
      <img :src="'http://' + o.img" @click="imgRadio(o.text)" v-if="o.img.length > 10">
      <m-radio :nativeValue="o.text" v-model="val" @input="handleChange">{{ o.text }}</m-radio>
      <m-input class="other" hintText="其它" v-model="other" v-if="o.other && o.text === val"></m-input>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PMultipleChoice',
  props: {
    option: Array,
    answers: {type: String, default: ''},
    otherVal: Object
  },
  data () {
    return {
      val: this.answers,
      other: ''
    }
  },
  watch: {
    answers () { this.init() },
    otherVal () { this.init() },
    val (val, old) {
      // 修改值的时候清空其他
      this.$emit('update:otherVal', {title: this.val, text: ''})
      if (val !== old) this.other = ''
    },
    other () {
      this.$emit('update:otherVal', {title: this.val, text: this.other})
    }
  },
  mounted () { this.init() },
  methods: {
    init () {
      this.val = this.answers
      if (this.otherVal && this.answers === this.otherVal.title) this.other = this.otherVal.text
    },
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
.other {
  margin-top: -12px;
  margin-bottom: 16px;
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

