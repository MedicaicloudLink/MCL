<template>
  <div class="time flex-row">
    <m-input hintText="小时" number v-model="hour" @input="hourHandler"></m-input><span v-if="option.minute">：</span>
    <m-input hintText="分" number v-model="minute" @input="minuteHandler" v-if="option.minute"></m-input><span v-if="option.second">：</span>
    <m-input hintText="秒" number v-model="second" @input="secondHandler" v-if="option.second"></m-input>
  </div>
</template>

<script>
export default {
  name: 'PTime',
  props: {
    option: Object,
    answers: Object
  },
  data () {
    return {
      hour: '',
      minute: '',
      second: '',
      inputArray: []
    }
  },
  watch: {
    answers () {
      this.hour = this.answers.hour
      this.minute = this.answers.minute
      this.second = this.answers.second
    }
  },
  mounted () {
    if (this.answers) {
      this.hour = this.answers.hour
      this.minute = this.answers.minute
      this.second = this.answers.second
    }
    this.inputArray = this.$el.getElementsByTagName('input')
  },
  methods: {
    hourHandler () {
      this.handleChange()
      if (this.hour.length === 2) {
        this.inputArray[0].blur()
        if (this.inputArray.length !== 1) this.inputArray[1].focus()
      }
    },
    minuteHandler () {
      this.handleChange()
      if (this.option.minute) {
        if (this.minute.length === 2) {
          this.inputArray[1].blur()
          if (this.inputArray.length === 3) this.inputArray[2].focus()
        }
      }
    },
    secondHandler () {
      this.handleChange()
      if (this.option.second) {
        if (this.second.length === 2) {
          if (this.inputArray.length === 3) this.inputArray[2].blur()
          else this.inputArray[1].blur()
        }
      }
    },
    handleChange () {
      let arr = []
      arr.push(this.handleValue(this.hour) + '时')
      if (this.option.minute) arr.push(this.handleValue(this.minute) + '分')
      if (this.option.second) arr.push(this.handleValue(this.second) + '秒')
      let str = arr.join('')
      this.$emit('input', str)
      this.$emit('update:answers', {hour: this.hour, minute: this.minute, second: this.second})
    },
    handleValue (val) {
      if (!val) return ''
      else return val
    }
  }
}
</script>

