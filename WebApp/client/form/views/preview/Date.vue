<template>
  <div class="date flex-row">
    <m-input hintText="年份" class="year" number v-model="year" @input="yearHandler" v-if="yearState"></m-input><span v-if="yearState">-</span>
    <m-input hintText="月" class="month" number v-model="month" @input="monthHandler" v-if="monthState"></m-input><span v-if="monthState">-</span>
    <m-input hintText="日" class="day" number v-model="day" @input="dayHandler" v-if="dayState"></m-input>
  </div>
</template>

<script>
export default {
  name: 'PDate',
  props: {
    value: String,
    option: Object,
    answers: Object,
    now: {type: Boolean, default: false}
  },
  data () {
    return {
      year: '',
      month: '',
      day: '',
      yearState: true,
      monthState: true,
      dayState: true,
      inputArray: []
    }
  },
  watch: {
    answers () {
      this.year = this.answers.year
      this.month = this.answers.month
      this.day = this.answers.day
    }
  },
  mounted () {
    if (this.now) {
      this.year = new Date().getFullYear().toString()
      this.month = (new Date().getMonth() + 1).toString()
      this.day = new Date().getDate().toString()
    }
    if (this.option) {
      this.yearState = this.option.year
      this.monthState = this.option.month
      this.dayState = this.option.day
    }
    if (this.answers) {
      this.year = this.answers.year
      this.month = this.answers.month
      this.day = this.answers.day
    }
    this.inputArray = this.$el.getElementsByTagName('input')
  },
  methods: {
    yearHandler (event) {
      this.handleChange()
      if (this.year.length === 4) {
        if (this.inputArray.length === 1) {
          this.inputArray[0].blur()
        } else {
          this.inputArray[1].focus()
        }
      }
    },
    monthHandler () {
      this.handleChange()
      if (this.month.length === 2) {
        if (this.inputArray.length === 1) {
          this.inputArray[0].blur()
        } else if (this.inputArray.length === 2) {
          if (this.yearState) {
            this.inputArray[1].blur()
          } else {
            this.inputArray[1].focus()
          }
        } else {
          this.inputArray[2].focus()
        }
      }
    },
    dayHandler () {
      this.handleChange()
      if (this.day.length === 2) {
        this.inputArray[this.inputArray.length - 1].blur()
      }
    },
    handleChange () {
      let arr = []
      this.year = this.handleValue(this.year)
      this.month = this.handleValue(this.month)
      this.day = this.handleValue(this.day)
      let str = arr.join('-')
      this.$emit('input', str)
      this.$emit('update:answers', {year: this.year, month: this.month, day: this.day})
    },
    handleValue (val) {
      if (!val) return ''
      else return val
    }
  }
}
</script>

