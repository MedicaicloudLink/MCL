<template>
  <div class="checkbox-form flex-col">
    <div class="option flex-col" v-for="o in option_other">
      <m-checkbox :nativeValue="o.text" v-model="val" @input="handleChange">{{ o.text }}</m-checkbox>
      <m-input hintText="其它" full v-model="o.otherText" class="other" v-if="o.other && val.indexOf(o.text) !== -1"></m-input>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PCheckboxes',
  props: {
    option: Array,
    answers: Array,
    otherVal: Array
  },
  data () {
    return {
      option_other: [],
      val: this.answers,
      other: []
    }
  },
  watch: {
    option () { this.init() },
    answers (val) { this.val = val },
    option_other: {
      handler (val) {
        this.other = []
        val.map(i => {
          if (i.other) this.other.push({title: i.text, text: i.otherText})
        })
        this.handleChange()
      },
      deep: true
    }
  },
  mounted () { this.init() },
  methods: {
    init () {
      this.val = this.answers
      this.option_other = []

      if (typeof this.option[0] === 'string') {
        // 旧版本【‘’， ‘’】
        this.option_other = []
        this.option.map(i => {
          this.option_other.push({text: i, other: false})
        })
      } else {
        for (let i in this.option) {
          // 处理新版本的[{text: '', other: false}]
          // 初始化赋值其他other
          let item = JSON.parse(JSON.stringify(this.option[i]))

          let isHaveOther = false
          let havaIndex = 0
          for (const i in this.otherVal) {
            if (this.otherVal[i].title === item.text) {
              isHaveOther = true
              havaIndex = i
            }
          }
          isHaveOther ? item.otherText = this.otherVal[havaIndex].text : item.otherText = ''

          this.option_other.push(item)
        }
      }
    },
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      this.$emit('update:otherVal', this.other)
      this.$emit('update:answers', this.val)
    }
  }
}
</script>

<style scoped>
.checkbox-icon {
  display: inline-block;
  width: 18px;
  height: 18px;
  border: 2px solid #e3e3e3;
  border-radius: 3px;
  margin-right: 8px;
}

.other {
  margin-top: -12px;
  margin-bottom: 16px;
}

</style>