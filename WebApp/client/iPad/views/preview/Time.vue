<template>
  <div class="time flex-row">
    <m-input hintText="小时" num 
     v-model="hour"
     @keydown="keyHour" 
     @input="hourHandler">
    </m-input><span v-if="option.minute">：</span>
    <m-input hintText="分" num 
     v-model="minute"
     @keydown="keyMinute"
     @input="minuteHandler"
     v-if="option.minute">
    </m-input><span v-if="option.second">：</span>
    <m-input hintText="秒" num 
     v-model="second"
     @keydown="keySecond"
     @input="secondHandler"
     v-if="option.second"></m-input>
  </div>
</template>

<script>
export default {
  name: 'PTime',
  props: {
    read: {type: Boolean, default: false},
    option: Object,
    answers: Object,
    id: String
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
    keyHour (e) {
      // 时间小时第一位只能输入0或1或2
      this.hour = this.handleVal(this.hour)
      if (this.hour.length === 0 && e.keyCode !== 48 && e.keyCode !== 49 && e.keyCode !== 50 && e.keyCode !== 96 && e.keyCode !== 97 && e.keyCode !== 98) e.preventDefault()
      if (this.hour.length === 1 && this.hour === '2' && e.keyCode !== 8 && e.keyCode !== 9) {
        if (e.keyCode < 48 || (e.keyCode > 51 && e.keyCode < 96) || e.keyCode > 99) e.preventDefault()
      }
      if (this.hour.length === 2 && e.keyCode !== 8) {
        e.preventDefault()
        this.hourHandler()
      }
    },
    // minute second 范围 00 ~ 59
    keyMinute (e) {
      this.minute = this.handleVal(this.minute)
      if (this.minute.length === 0 && e.keyCode === 8) {
        this.inputArray[0].focus()
        e.preventDefault()
        return
      }
      if (this.minute.length === 1 && e.keyCode !== 8) {
        if (parseInt(this.minute) > 5 || parseInt(this.minute) < 0) {
          e.preventDefault()
          this.minuteHandler()
        }
      }
      if (this.minute.length === 2 && e.keyCode !== 8) {
        e.preventDefault()
        this.minuteHandler()
      }
    },
    keySecond (e) {
      this.second = this.handleVal(this.second)
      if (this.second.length === 0 && e.keyCode === 8) {
        if (this.inputArray.length === 3) this.inputArray[1].focus()
        else this.inputArray[0].focus()
        e.preventDefault()
        return
      }
      if (this.second.length === 1 && e.keyCode !== 8) {
        if (parseInt(this.second) > 5 || parseInt(this.second) < 0) {
          e.preventDefault()
        }
      }
      if (this.second.length === 2 && e.keyCode !== 8) e.preventDefault()
    },
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
        if (this.minute.length === 2 || (parseInt(this.minute) > 5 || parseInt(this.minute) < 0)) {
          this.inputArray[1].blur()
          if (this.inputArray.length === 3) this.inputArray[2].focus()
        }
      }
    },
    secondHandler () {
      this.handleChange()
    },
    handleChange () {
      let arr = []
      this.hour = this.handleVal(this.hour)
      this.minute = this.handleVal(this.minute)
      this.second = this.handleVal(this.second)
      arr.push(this.hour + '时')
      if (this.option.minute) arr.push(this.minute + '分')
      if (this.option.second) arr.push(this.second + '秒')
      let str = arr.join('')
      this.$emit('input', str)
      this.$emit('update:answers', {hour: this.hour, minute: this.minute, second: this.second})
    },
    handleVal (val) {
      if (!val) return ''
      else {
        return val.replace(/[^\d]/g, '')
      }
    }
  }
}
</script>

