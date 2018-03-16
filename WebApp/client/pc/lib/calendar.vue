<style scoped>
.calendar {
    width: 300px;
    padding: 10px;
    background: #fff;
    position: absolute;
    border: 1px solid #DEDEDE;
    border-radius: 2px;
    opacity:.95;
    transition: all .5s ease;
    z-index: 2;
}
.calendar-enter, .calendar-leave {
    opacity: 0;
    transform: translate3d(0,-10px, 0);
}
.calendar:before {
    position: absolute;
    left:30px;
    top: -10px;
    content: "";
    border:5px solid rgba(0, 0, 0, 0);
    border-bottom-color: #DEDEDE;
}
.calendar:after {
    position: absolute;
    left:30px;
    top: -9px;
    content: "";
    border:5px solid rgba(0, 0, 0, 0);
    border-bottom-color: #fff;
}
.calendar-tools{
    height:32px;
    font-size: 20px;
    line-height: 32px;
    color:#5e7a88;
}
.calendar-tools .float.left{
    float:left;
}
.calendar-tools .float.right{
    float:right;
}
.calendar-tools input{
    font-size: 20px;
    line-height: 32px;
    color: #5e7a88;
    width: 70px;
    text-align: center;
    border:none;
    display: inline-block;
    background-color: transparent;
}
.calendar-tools span{
    cursor: pointer;
}
.calendar-prev{
    float:left;
}
.calendar-next{
    float:right;
}
 
.calendar table {
    clear: both;
    width: 100%;
    margin-bottom:10px;
    border-collapse: collapse;
    color: #444444;
}
.calendar td {
    margin:2px !important;
    padding:0px 0;
    width: 14.28571429%;
    height:34px;
    text-align: center;
    vertical-align: middle;
    font-size:14px;
    line-height: 125%;
    cursor: pointer;
}
.calendar td.week{
    pointer-events:none !important;
    cursor: default !important;    
}
.calendar td.disabled {
    color: #c0c0c0;
    pointer-events:none !important;
    cursor: default !important;
}
.calendar td span{
    display:block;
    height:30px;
    line-height:30px;
    margin:2px;
    border-radius:2px;
}
.calendar td span:hover{
    background:#f3f8b3;
}
.calendar td.selected span{
    background-color: #5e7a88;
    color: #fff;
}
.calendar td.selected span:hover{
    background-color: #5e7a88;
    color: #fff;
}
.calendar thead td {
  text-transform: uppercase;
}
.calendar .timer{
    margin:10px 0;
    text-align: center;
}
.calendar .timer input{
    border-radius: 2px;
    padding:5px;
    font-size: 14px;
    line-height: 18px;
    color: #5e7a88;
    width: 50px;
    text-align: center;
    border:1px solid #efefef;
}
.calendar .timer input:focus{
    border:1px solid #5e7a88;
}
.calendar-button{
    text-align: center;
}
.calendar-button span{
    cursor: pointer;
    display: inline-block;
    min-height: 1em;
    min-width: 5em;
    vertical-align: baseline;
    background:#5e7a88;
    color:#fff;
    margin: 0 .25em 0 0;
    padding: .6em 2em;
    font-size: 1em;
    line-height: 1em;
    text-align: center;
    border-radius: .3em;
}
.calendar-button span.cancel{
    background:#efefef;
    color:#666;
}
.calendar .lunar{
     font-size:11px;
     line-height: 150%;
     color:#aaa;   
}
.calendar td.selected .lunar{
     color:#fff;   
}
</style>

<template>
    <div @click.stop=""  class="calendar" v-show="show" :style="{'left':x+'px','top':y+'px'}" transition="calendar" transition-mode="out-in">
        <div>
            <div class="calendar-tools">
                <span class="calendar-prev" @click="prevMonth">
                    <svg width="16" height="16" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.015625, 0.015625)"><path d="M671.968 912c-12.288 0-24.576-4.672-33.952-14.048L286.048 545.984c-18.752-18.72-18.752-49.12 0-67.872l351.968-352c18.752-18.752 49.12-18.752 67.872 0 18.752 18.72 18.752 49.12 0 67.872l-318.016 318.048 318.016 318.016c18.752 18.752 18.752 49.12 0 67.872C696.544 907.328 684.256 912 671.968 912z" fill="#5e7a88"></path></g></g></svg>
                </span>
                <span class="calendar-next"  @click="nextMonth">
                    <svg width="16" height="16" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.015625, 0.015625)"><path d="M761.056 532.128c0.512-0.992 1.344-1.824 1.792-2.848 8.8-18.304 5.92-40.704-9.664-55.424L399.936 139.744c-19.264-18.208-49.632-17.344-67.872 1.888-18.208 19.264-17.376 49.632 1.888 67.872l316.96 299.84-315.712 304.288c-19.072 18.4-19.648 48.768-1.248 67.872 9.408 9.792 21.984 14.688 34.56 14.688 12 0 24-4.48 33.312-13.44l350.048-337.376c0.672-0.672 0.928-1.6 1.6-2.304 0.512-0.48 1.056-0.832 1.568-1.344C757.76 538.88 759.2 535.392 761.056 532.128z" fill="#5e7a88"></path></g></g></svg>
                </span>
                <div class="text center">
                    <input type="text" v-model="year" @change="render(year,month)" min="1970" max="2100" maxlength="4">
                     / 
                    {{monthString}}
                </div>
            </div>
            <table cellpadding="5">
              <thead>
                <tr>
                  <td v-for="week in weeks" class="week">{{week}}</td>
                </tr>
              </thead>
              <tr v-for="day in days">
                <td 
                v-for="child in day" 
                :class="{'selected':child.selected,'disabled':child.disabled}"
                @click="select(child.value)">
                <span>{{child.value}}</span>                                                                   
                </td>
              </tr>
            </table>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      twoWay: true,
      default: false
    },
    picker: {
      type: String,
      default: ''
    },
    value: {
      type: String,
      twoWay: true,
      default: ''
    },
    x: {
      type: Number,
      default: 0
    },
    y: {
      type: Number,
      default: 0
    },
    sep: {
      type: String,
      twoWay: true,
      default: ''
    },
    weeks: {
      type: Array,
      default: function () {
        return ['日', '一', '二', '三', '四', '五', '六']
      }
    },
    months: {
      type: Array,
      default: function () {
        return ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']
      }
    }
  },
  data () {
    return {
      year: 0,
      month: 0,
      days: [], // 相当于一个二维数组 days[[day],[day],...]
      monthString: ''
    }
  },
  created () {
    this.init()
  },
  watch: {
    'show': function (val) {
      if (val === true) {
        this.init()
      }
    }
  },
  methods: {
    // 日期补零
    zero (n) {
      return n < 10 ? '0' + n : n
    },
    // 初始化一些东西
    init () {
      let now = new Date()
      this.year = now.getFullYear()
      this.month = now.getMonth()
      this.day = now.getDate()
      this.monthString = this.months[this.month]
      this.render(this.year, this.month)
    },
    // 渲染日期
    render (y, m) {
      this.days.splice(0)
      let firstDayOfWeek = new Date(y, m, 1).getDay()         // 当月第一天所在的星期 （0 ~ 6，日~六）
      let lastDateOfMonth = new Date(y, m + 1, 0).getDate()    // 当月最后一天 当月的天数
      let lastDateOfLastMonth = new Date(y, m, 0).getDate()     // 上个月的最后一天
      let lastDayOfWeek = new Date(y, m + 1, 0).getDay()    // 当月最后一天所在的星期
      // this.year = y
      // this.currentMonth = this.months[m]
      let line = 1
      let day = []
      let oneday = {value: '', select: '', disable: ''} // 存储日历每一行的7个数字
      let firstLineStrat = lastDateOfLastMonth - firstDayOfWeek + 1 // 日历第一行的开始
      // 第一行填充上个月的最后几天
      for (let j = 0; j < firstDayOfWeek; j++) {
        oneday.value = firstLineStrat // 值
        oneday.selected = false
        oneday.disabled = true
        day[j] = oneday
        oneday = {value: '', select: '', disable: ''}
        firstLineStrat++
      }
      // 当前月份的日历显示
      for (let i = 1; i <= lastDateOfMonth; i++) {
        // day.push(i)
        oneday.value = i // 值
        oneday.selected = false
        if (i === new Date().getDate() && y === new Date().getFullYear() && m === new Date().getMonth()) {
          oneday.selected = true
        }
        oneday.disabled = false
        day.push(oneday)
        oneday = {value: '', select: '', disable: ''}
        if ((i + firstDayOfWeek) % 7 === 0) {
          // 日历规格 七列 从（日~六）
          this.days[line - 1] = day
          line++
          day = []
        }
      }
      // 将当前月份的最后一行日历用下个月补齐
      for (let z = 1; day.length <= 7; z++) {
        oneday.value = z // 值
        oneday.selected = false
        oneday.disabled = true
        day.push(oneday)
        oneday = {value: '', select: '', disable: ''}
        if (lastDayOfWeek + z === 6) {
          this.days[line - 1] = day
          break
        }
      }
      // 日历的规格的六行 不足六行用下个月的日历补齐
      if (this.days.length !== 6) {
        let z = day[6].value + 1
        day = []
        for (; day.length <= 7; z++) {
          oneday.value = z // 值
          oneday.selected = false
          oneday.disabled = true
          day.push(oneday)
          oneday = {value: '', select: '', disable: ''}
          if (day.length === 7) {
            this.days[5] = day
            break
          }
        }
      }
    },
    // 上月
    prevMonth (e) {
      e.stopPropagation()
      if (this.month === 0) {
        this.month = 11
        this.year = parseInt(this.year) - 1
      } else {
        this.month = parseInt(this.month) - 1
      }
      this.monthString = this.months[this.month]
      this.render(this.year, this.month)
    },
    //  下月
    nextMonth (e) {
      e.stopPropagation()
      if (this.month === 11) {
        this.month = 0
        this.year = parseInt(this.year) + 1
      } else {
        this.month = parseInt(this.month) + 1
      }
      this.monthString = this.months[this.month]
      this.render(this.year, this.month)
    },
    // 选中日期
    select (child) {
      let value = this.year + '-' + this.zero(this.month + 1) + '-' + this.zero(child)
      this.$emit('select', value, this.picker)
    }
  }
}
</script>