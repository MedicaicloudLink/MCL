<template>
<div class="datepicker">
    <input type="text" @click="inputClick" v-model="currentValue" :style="{width: width + 'px'}"/>

    <transition name="fadeDown">
      <div class="datepicker-popup" v-show="displayDayView">
        <div class="datepicker-inner">
          <div class="datepicker-body">
            <div class="datepicker-ctrl">
              <span
                class="month-btn datepicker-preBtn"
                @click="preNextMonthClick(0)">&lt;</span>
              <span
                class="month-btn datepicker-nextBtn"
                @click="preNextMonthClick(1)">&gt;</span>
              <p @click="switchMouthView">
              {{stringifyDayHeader(currDate)}}
              </p>
            </div>
            <div class="datepicker-weekRange">
              <span v-for="w in weekRange">{{w}}</span>
            </div>
            <div class="datepicker-dateRange">
              <span
                v-for="d in dateRange" :class="d.sclass"
                @click="daySelect(d.date, d.sclass)">
                {{d.text}}
              </span>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <div class="datepicker-popup" v-show ="displayMouthView" >
      <div class="datepicker-inner">
        <div class="datepicker-body">
          <div class="datepicker-ctrl">
            <span
              class="month-btn datepicker-preBtn"
              @click="preNextYearClick(0)">&lt;</span>
            <span
              class="month-btn datepicker-nextBtn"
              @click="preNextYearClick(1)">&gt;</span>
            <p @click="switchDecadeView">
            {{stringifyYearHeader(currDate)}}
            </p>
          </div>
          <div class="datepicker-mouthRange">
          	<template v-for="(m, index) in mouthNames">
              <span
                class="monthClassObj(m)"
                @click="mouthSelect(index)">
                {{m.substr(0,3)}}
              </span>
            </template>
          </div>
        </div>
      </div>
    </div>
    <div class="datepicker-popup" v-show ="displayYearView">
      <div class="datepicker-inner">
        <div class="datepicker-body">
          <div class="datepicker-ctrl">
            <span
              class="month-btn datepicker-preBtn"
              @click="preNextDecadeClick(0)">&lt;</span>
            <span
              class="month-btn datepicker-nextBtn"
              @click="preNextDecadeClick(1)">&gt;</span>
            <p>
            {{stringifyDecadeHeader(currDate)}}
            </p>
          </div>
          <div class="datepicker-mouthRange datepicker-decadeRange">
          	<template v-for="decade in decadeRange">
          		<span
                class="yearClassObj(decade)"
                @click.stop="yearSelect(decade.text)">
                {{decade.text}}
             	</span>
		        </template>
          </div>
        </div>
      </div>
    </div>
</div>
</template>

<script>
export default {
  name: 'Datepicker',
  props: {
    value: {
      type: String
    },
    format: {
      default: 'yyyy-MM-dd'
    },
    manual: {
      type: Boolean,
      default: false
    },
    width: {
      type: Number,
      default: 232
    }
  },
  data () {
    return {
      currentValue: this.value,
      today: '',
      weekRange: ['日', '一', '二', '三', '四', '五', '六'],
      dateRange: [],
      decadeRange: [],
      currDate: new Date(),
      displayDayView: false,
      displayMouthView: false,
      displayYearView: false,
      mouthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
    }
  },
  watch: {
    value () {
      this.currentValue = this.value
      this.currDate = this.parse(this.currentValue) || this.parse(new Date())
    },
    currDate () {
      this.getDateRange()
    },
    currentValue (val) {
      this.$emit('input', val)
      this.$emit('change', this.currentValue)
    }
  },
  methods: {
    clean () {
      this.currDate = new Date()
    },
    monthClassObj (m) {
      let {currentValue, mouthNames, parse, currDate} = this
      let klass = {}
      klass['datepicker-dateRange-item-active'] =
      currentValue && parse(currentValue) && mouthNames[parse(currentValue).getMonth()] === m && currDate.getFullYear() === parse(currentValue).getFullYear()
      return klass
    },
    yearClassObj (decade) {
      let {currentValue, parse} = this
      let klass = {}
      klass['datepicker-dateRange-item-active'] =
      currentValue && parse(currentValue) && parse(currentValue).getFullYear() === decade.text
      return klass
    },
    close () {
      this.displayDayView = this.displayMouthView = this.displayMouthView = false
    },
    inputClick () {
      if (this.disabled) return
      if (this.displayMouthView || this.displayYearView) {
        this.displayDayView = false
      } else {
        this.displayDayView = !this.displayDayView
      }
    },
    preNextDecadeClick (flag) {
      const year = this.currDate.getFullYear()
      const mouths = this.currDate.getMonth()
      const date = this.currDate.getDate()
      if (flag === 0) {
        this.currDate = new Date(year - 10, mouths, date)
      } else {
        this.currDate = new Date(year + 10, mouths, date)
      }
    },
    preNextMonthClick (flag) {
      const year = this.currDate.getFullYear()
      const month = this.currDate.getMonth()
      const date = this.currDate.getDate()
      if (flag === 0) {
        const preMonth = this.getYearMonth(year, month - 1)
        this.currDate = new Date(preMonth.year, preMonth.month, date)
      } else {
        const nextMonth = this.getYearMonth(year, month + 1)
        this.currDate = new Date(nextMonth.year, nextMonth.month, date)
      }
    },
    preNextYearClick (flag) {
      const year = this.currDate.getFullYear()
      const mouths = this.currDate.getMonth()
      const date = this.currDate.getDate()
      if (flag === 0) {
        this.currDate = new Date(year - 1, mouths, date)
      } else {
        this.currDate = new Date(year + 1, mouths, date)
      }
    },
    yearSelect (year) {
      this.displayYearView = false
      this.displayMouthView = true
      this.currDate = new Date(year, this.currDate.getMonth(), this.currDate.getDate())
    },
    daySelect (date, klass) {
      if (klass.indexOf('datepicker-item-disable') > -1) {
        return false
      } else {
        this.currDate = date
        this.currentValue = this.stringify(this.currDate)
        this.displayDayView = false
      }
    },
    switchMouthView () {
      this.displayDayView = false
      this.displayMouthView = true
    },
    switchDecadeView () {
      this.displayMouthView = false
      this.displayYearView = true
    },
    mouthSelect (index) {
      this.displayMouthView = false
      this.displayDayView = true
      this.currDate = new Date(this.currDate.getFullYear(), index, this.currDate.getDate())
    },
    getYearMonth (year, month) {
      if (month > 11) {
        year++
        month = 0
      } else if (month < 0) {
        year--
        month = 11
      }
      return {year: year, month: month}
    },
    stringifyDecadeHeader (date) {
      const yearStr = date.getFullYear().toString()
      const firstYearOfDecade = yearStr.substring(0, yearStr.length - 1) + 0
      const lastYearOfDecade = parseInt(firstYearOfDecade, 10) + 10
      return firstYearOfDecade + '-' + lastYearOfDecade
    },
    stringifyDayHeader (date) {
      return this.mouthNames[date.getMonth()] + ' ' + date.getFullYear()
    },
    parseMouth (date) {
      return this.mouthNames[date.getMonth()]
    },
    stringifyYearHeader (date) {
      return date.getFullYear()
    },
    stringify (date, format = this.format) {
      if (isNaN(date.getFullYear())) return ''
      const year = date.getFullYear()
      const month = date.getMonth() + 1
      const day = date.getDate()
      return format
        .replace(/yyyy/g, year)
        .replace(/MMMM/g, month)
        .replace(/MMM/g, month)
        .replace(/MM/g, ('0' + month).slice(-2))
        .replace(/dd/g, ('0' + day).slice(-2))
        .replace(/yy/g, year)
        .replace(/M(?!a)/g, month)
        .replace(/d/g, day)
    },
    parse (str) {
      const date = new Date(str)
      return isNaN(date.getFullYear()) ? null : date
    },
    getDayCount (year, month) {
      const dict = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
      if (month === 1) {
        if ((year % 400 === 0) || (year % 4 === 0 && year % 100 !== 0)) {
          return 29
        }
        return 28
      }
      return dict[month]
    },
    getDateRange () {
      this.dateRange = []
      this.decadeRange = []
      const time = {
        year: this.currDate.getFullYear(),
        month: this.currDate.getMonth(),
        day: this.currDate.getDate()
      }
      const yearStr = time.year.toString()
      const firstYearOfDecade = (yearStr.substring(0, yearStr.length - 1) + 0) - 1
      for (let i = 0; i < 12; i++) {
        this.decadeRange.push({
          text: firstYearOfDecade + i
        })
      }
      const currMonthFirstDay = new Date(time.year, time.month, 1)
      let firstDayWeek = currMonthFirstDay.getDay() + 1
      if (firstDayWeek === 0) {
        firstDayWeek = 7
      }
      const dayCount = this.getDayCount(time.year, time.month)
      if (firstDayWeek > 1) {
        const preMonth = this.getYearMonth(time.year, time.month - 1)
        const prevMonthDayCount = this.getDayCount(preMonth.year, preMonth.month)
        for (let i = 1; i < firstDayWeek; i++) {
          const dayText = prevMonthDayCount - firstDayWeek + i + 1
          this.dateRange.push({
            text: dayText,
            date: new Date(preMonth.year, preMonth.month, dayText),
            sclass: 'datepicker-item-gray'
          })
        }
      }
      for (let i = 1; i <= dayCount; i++) {
        const date = new Date(time.year, time.month, i)
        // const week = date.getDay()
        let sclass = ''
        if (i === time.day) {
          if (this.currentValue) {
            const valueDate = this.parse(this.currentValue)
            if (valueDate) {
              if (valueDate.getFullYear() === time.year && valueDate.getMonth() === time.month) {
                sclass = 'datepicker-dateRange-item-active'
              }
            }
          }
        }
        this.dateRange.push({
          text: i,
          date: date,
          sclass: sclass
        })
      }
      if (this.dateRange.length < 42) {
        const nextMonthNeed = 42 - this.dateRange.length
        const nextMonth = this.getYearMonth(time.year, time.month + 1)
        for (let i = 1; i <= nextMonthNeed; i++) {
          this.dateRange.push({
            text: i,
            date: new Date(nextMonth.year, nextMonth.month, i),
            sclass: 'datepicker-item-gray'
          })
        }
      }
    }
  },
  created () {
    this.today = this.stringify(new Date())
  },
  mounted () {
    this.currDate = this.parse(this.currentValue) || this.parse(new Date())
    this._closeEvent = window.addEventListener('click', e => {
      if (!this.$el.contains(e.target)) this.close()
    })
  },
  beforeDestroy () {
    if (this._closeEvent) this._closeEvent.remove()
  }
}
</script>

<style>
  .datepicker {
    position: relative;
    display: inline-block;
    font-size: 13px;
  }

  .datepicker>input[type="text"] {
    cursor: pointer;
    width: 232px;
    border-radius: 2px;
    border: 1px solid #ddd;
    font-size: 16px;
    height: 30px;
  }

  .datepicker>input[type="text"]:hover {
    border: 1px solid #468df1;
  }

  .datepicker-popup {
    position: absolute;
    border: 1px solid #ccc;
    background: #fff;
    margin-top: 2px;
    z-index: 1000;
    box-shadow: 3px 3px 3px rgba(0, 0, 0, .1);
  }

  .datepicker-inner {
    width: 250px;
  }

  .datepicker-ctrl {
    position: relative;
    height: 35px;
    line-height: 35px;
    font-weight: bold;
    text-align: center;
  }

  .datepicker-ctrl p, .datepicker-ctrl span, .datepicker-body span {
    display: inline-block;
    width: 35px;
    line-height: 35px;
    height: 35px;
    border-radius: 35px;
  }

  .datepicker-ctrl p {
    width: 65%;
    margin: 0px;
  }

  .datepicker-ctrl span {
    position: absolute;
  }

  .datepicker-body span {
    text-align: center;
  }

  .datepicker-mouthRange span {
    width: 44px;
    height: 44px;
    line-height: 44px;
    margin: 2px 18px;
  }

  .datepicker-item-disable {
    background-color: white !important;
    cursor: not-allowed !important;
  }

  .datepicker-datepicker-decadeRange span:first-child,
  .datepicker-datepicker-decadeRange span:last-child,
  .datepicker-item-disable,
  .datepicker-item-gray{
    color: #ccc;
  }

  .datepicker-dateRange-item-active:hover,
  .datepicker-dateRange-item-active {
    background: #468df1 !important;
    color: #fff !important;
  }

  .datepicker-mouthRange {
    margin-bottom: 10px;
    margin-left:10px;
  }

  .datepicker-mouthRange span,
  .datepicker-ctrl span,
  .datepicker-ctrl p,
  .datepicker-dateRange span {
    transition: all .3s ease;
    cursor: pointer;
  }
  .datepicker-mouthRange span:hover,
  .datepicker-ctrl p:hover,
  .datepicker-ctrl i:hover,
  .datepicker-dateRange span:hover,
  .datepicker-dateRange-item-hover {
      background-color : #f5f9ff;
  }
  .datepicker-weekRange{
    border-bottom: 1px solid #eee;
    border-top: 1px solid #eee;
  }

  .datepicker-weekRange span{
      font-weight: bold;
  }

  .datepicker-month-btn{
    font-weight: bold;
    user-select:none;
  }
  .datepicker-preBtn{
    left: 2px;
  }
  .datepicker-nextBtn{
    right: 2px;
  }

  /*fadeDown*/
  .fadeDown-enter-active,.fadeDown-leave-active {
    transition: all .3s ease;
  }
  .fadeDown-enter, .fadeDown-leave-active {
    transform:translateY(-10px);
    opacity: 0;
  }
</style>
