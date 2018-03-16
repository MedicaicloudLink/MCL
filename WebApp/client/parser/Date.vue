<template>
  <div class="date flex-row">
    <m-input hintText="年份" class="year" num
      v-model="year"
      @keydown="keyYear"
      @input="yearHandler"
      v-if="yearState">
    </m-input><span v-if="yearState">-</span>
    <m-input hintText="月" class="month" num 
      v-model="month"
      @keydown="keyMonth" 
      @input="monthHandler"
      v-if="monthState">
    </m-input><span v-if="monthState">-</span>
    <m-input hintText="日" class="day" num
      v-model="day"
      @keydown="keyDay" 
      @input="dayHandler" 
      v-if="dayState"></m-input>
  </div>
</template>

<script>
export default {
  name: 'PDate',
  props: {
    read: {type: Boolean, default: false},
    value: String,
    id: String,
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
    if (this.now) {
      this.year = new Date().getFullYear().toString()
      this.month = (new Date().getMonth() + 1).toString()
      this.day = new Date().getDate().toString()
      this.handleChange()
    }
    this.inputArray = this.$el.getElementsByTagName('input')
  },
  methods: {
    // 数字键盘 96 - 105
    // 按键检测：1=>49，2=>50 , - => 229, e => 69
    keyYear (e) {
      this.year = this.handleVal(this.year)
      // 日期年份首位只能输入1或2
      if (this.year.length === 0 && e.keyCode !== 49 && e.keyCode !== 50 && e.keyCode !== 97 && e.keyCode !== 98) e.preventDefault()
      if (this.year.length === 4 && e.keyCode !== 8) {
        e.preventDefault()
        this.yearHandler()
      }
    },
    // 按键检测 Backspace => 8 tab => 9
    keyMonth (e) {
      // 日期月份首位只能输入0或1或2
      this.month = this.handleVal(this.month)
      if (this.month.length === 0 && e.keyCode === 8) {
        this.inputArray[0].focus()
        e.preventDefault()
        return
      }

      if (this.month.length === 1) {
        if (this.month === '0' && (e.keyCode === 48 || e.keyCode === 96)) e.preventDefault()
        if (this.month === '1' && e.keyCode !== 48 && e.keyCode !== 49 && e.keyCode !== 50 && e.keyCode !== 96 && e.keyCode !== 97 && e.keyCode !== 98 && e.keyCode !== 8 && e.keyCode !== 9) e.preventDefault()
        if (this.month !== '0' && this.month !== '1' && e.keyCode !== 8) {
          e.preventDefault()
          this.monthHandler()
        }
      }

      if (this.month.length === 2 && e.keyCode !== 8) {
        e.preventDefault()
        this.monthHandler()
      }
    },
    keyDay (e) {
      // 日期天数第一位只能输入0或1或2 或3
      // 闰年 2月=> 29天 平年 28天
      // 1、3、5、7、8、10、12月31天
      // 4、6、9、11月30天
      this.day = this.handleVal(this.day)
      if (this.day.length === 0 && e.keyCode === 8) {
        if (this.inputArray.length === 3) this.inputArray[1].focus()
        else this.inputArray[0].focus()
        e.preventDefault()
        return
      }
      // 00 没有
      if (this.day.length === 1 && this.day === '0' && (e.keyCode === 48 || e.keyCode === 96)) e.preventDefault()
      if (this.day.length === 1 && parseInt(this.day) > 3 && e.keyCode !== 8) e.preventDefault()

      if (this.month && (this.month === '2' || this.month === '02')) {
        if (parseInt(this.day) > 2 && e.keyCode !== 8) e.preventDefault()
        if (!(this.year && this.year % 4 === 0 && (this.year % 100 !== 0 || this.year % 400 === 0))) {
          if (this.day.length === 1 && this.day === '2' && (e.keyCode === 57 || e.keyCode === 105)) e.preventDefault()
        }
      }

      // 限制30， 31号
      let day31 = ['1', '01', '3', '03', '5', '05', '7', '07', '8', '08', '10', '12']
      let day30 = ['4', '04', '6', '06', '9', '09', '11']
      if (this.day.length === 1 && this.day === '3') {
        if (this.month) {
          if (day31.indexOf(this.month) !== -1) {
            if (e.keyCode !== 48 && e.keyCode !== 49 && e.keyCode !== 96 && e.keyCode !== 97 && e.keyCode !== 8) e.preventDefault()
          } else if (day30.indexOf(this.month) !== -1) {
            if (e.keyCode !== 48 && e.keyCode !== 96 && e.keyCode !== 8) e.preventDefault()
          }
        } else {
          if (e.keyCode !== 48 && e.keyCode !== 49 && e.keyCode !== 96 && e.keyCode !== 97 && e.keyCode !== 8) e.preventDefault()
        }
      }
      // tab => 9
      if (this.day.length === 2 && e.keyCode !== 8 && e.keyCode !== 9) {
        e.preventDefault()
      }
    },
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
      if (this.month.length) {
        if (this.month.length === 2 || (this.month !== '0' && this.month !== '1')) {
          if (this.inputArray.length === 1) {
            this.inputArray[0].blur()
          } else if (this.inputArray.length === 2) {
            if (this.yearState) {
              this.inputArray[1].blur()
            } else {
              this.inputArray[1].focus()
            }
          } else if (this.inputArray.length === 3) {
            this.inputArray[2].focus()
          }
        }
      }
    },
    dayHandler () {
      this.handleChange()
    },
    handleChange () {
      let arr = []
      this.year = this.handleVal(this.year)
      this.month = this.handleVal(this.month)
      this.day = this.handleVal(this.day)
      if (this.yearState) arr.push(this.year)
      if (this.monthState) arr.push(this.month)
      if (this.dayState) arr.push(this.day)
      let str = arr.join('-')
      if (str === '--') str = ''
      this.$emit('input', str)
      this.$emit('update:answers', {year: this.year, month: this.month, day: this.day})
    },
    handleVal (val) {
      if (!val) return ''
      else return val.replace(/[^\d]/g, '') // 非数字转化
    }
  }
}
</script>

