<template>
  <div class="input-form" :class="{fullwidth: full, dark: bgDark}">

    <template v-if="!autoHeight">
      <input type="text" v-model="val" :placeholder="hintText" v-if="!number" :readonly="read"
      @input="handleInput"
      @focus="handleFocus"
      @keydown="handlekeydown"
      @keyup="handleKeyup"
      @blur="handleBlur"
        :style="{fontSize: fontsize + 'px', width: autoWidth ? width + 'px' : '', maxWidth: maxWidth}"
        :class="{preview: preview}"/>
      <input type="number" v-model="val" :placeholder="hintText" :readonly="read" v-if="number"
      @input="handleInput"
      @focus="handleFocus"
      @keydown="handlekeydown"
      @keyup="handleKeyup"
      @blur="handleBlur" />
    </template>

    <template v-else> 
      <textarea :value="val" :placeholder="hintText" @input="autoInput" @keypress="handleKey" rows="1"
        :class="{preview: preview}"
        :style="{fontSize: fontsize + 'px', maxWidth: maxWidth}"
        ></textarea>
    </template>


    <span class="i-right"><slot name="iconright"></slot></span>
    
    <span class="bar"></span>

    <span class="autowidth_temp" :style="{fontSize: fontsize + 'px'}"></span>
  </div>
</template>

<script>
export default {
  name: 'MInput',
  props: {
    value: String,
    hintText: String,
    fontsize: {type: String, default: '16'},
    preview: {type: Boolean, default: false},
    full: {type: Boolean, default: false},
    num: {type: Boolean, default: false}, // 纯数字
    number: {type: Boolean, default: false},
    read: {type: Boolean, default: false},
    autoLinefeed: {type: Boolean, default: false},
    bgDark: {type: Boolean, default: false},
    autoWidth: {type: Boolean, default: false},
    maxWidth: String,
    autoHeight: {type: Boolean, default: false}
  },
  data () {
    return {
      val: this.value,
      width: 10
    }
  },
  watch: {
    value () {
      this.val = this.value
      this.$nextTick(() => {
        if (this.autoWidth) {
          this.changWidth(this.val)
        }
        if (this.autoHeight) this.changHeight()
      })
    },
    // 非数字用''代替
    val () {
      if (this.val && this.num) {
        this.val = this.val.replace(/[^\d]/g, '')
      }
    },
    autoHeight () {
      this.changHeight()
    }
  },
  mounted () {
    this.$nextTick(() => {
      if (this.autoWidth) this.changWidth(this.val)
      if (this.autoHeight) this.changHeight()
    })
  },
  methods: {
    // 自动宽度实现
    changWidth (text) {
      // 传入text参数解决输入法this.val未赋值的问题
      let el = this.$el.getElementsByClassName('autowidth_temp')[0]
      el.innerHTML = text
      this.width = el.offsetWidth
      if (text === '') {
        el.innerHTML = this.hintText
        this.width = el.offsetWidth
      }
    },
    // 初始化自动高度
    changHeight (e) {
      let el = this.$el.getElementsByTagName('textarea')[0]
      if (el.scrollHeight) el.style.height = el.scrollHeight + 'px'
    },
    autoInput (e) {
      e.target.style.height = '1px'
      e.target.style.height = e.target.scrollHeight + 'px'
      this.val = e.target.value
      this.$emit('update:value', this.val)
      this.$emit('input', this.val)
    },
    // 禁止回车换行
    handleKey (e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        return
      }
    },
    handleFocus (event) {
      this.$emit('focus', event)
    },
    handleBlur (event) {
      this.$emit('blur', event)
    },
    handlekeydown (event) {
      this.$emit('keydown', event)
    },
    handleKeyup (event) {
      this.$emit('keyup', event)
    },
    handleChange (e) {
      this.changWidth()
      this.$emit('change', e, e.target.value)
    },
    handleInput (e) {
      this.changWidth(e.target.value)
      this.$emit('update:value', this.val)
      this.$emit('input', this.val)
    }
  }
}
</script>

<style scoped>
.input-form {
  position: relative;
  display: inline-block;
  min-width: 12px;
  max-width: 100%;
}

.input-form.fullwidth {
  display: block;
  width: 100%;
}

input, textarea {
  font-size: 16px;
  display: block;
  width: 100%;
  border: none;
  background: none;
  border-bottom: 1px solid rgba(0,0,0,.12);
  line-height: 1.8;
}

textarea{
  resize: none;
  min-height: 28px;
}

::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color: rgba(0,0,0,.36);
}
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color: rgba(0,0,0,.36);
   opacity:  1;
}
::-moz-placeholder { /* Mozilla Firefox 19+ */
   color: rgba(0,0,0,.36);
   opacity:  1;
}
:-ms-input-placeholder { /* Internet Explorer 10-11 */
   color: rgba(0,0,0,.46);
}
::-ms-input-placeholder { /* Microsoft Edge */
   color: rgba(0,0,0,.36);
}

input.preview, textarea.preview {
  border-bottom: 1px solid rgba(0,0,0,0);
}

input:focus, textarea:focus {
  outline: none;
  border-bottom: 1px solid rgba(0,0,0,0);
}

.i-right {
  position: absolute;
  right: 0;
  top: 0;
  height: 100%;
}


/* BOTTOM BARS ================================= */

.bar {
  position: relative;
  display: block;
  width: 100%;
}

.bar:before,
.bar:after {
  content: '';
  height: 2px;
  width: 0;
  bottom: 1px;
  position: absolute;
  background: #4285f4;
  transition: 0.2s ease all;
  -moz-transition: 0.2s ease all;
  -webkit-transition: 0.2s ease all;
}

.bar:before {
  left: 50%;
}

.bar:after {
  right: 50%;
}

.input-form.dark input {
  color: #fff;
}

.input-form.dark .bar:before,
.input-form.dark .bar:after {
  background: #fff;
}

.input-form.dark ::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color: rgba(255, 255, 255, 0.7);
}
.input-form.dark :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color: rgba(255, 255, 255, 0.7);
   opacity:  1;
}
.input-form.dark ::-moz-placeholder { /* Mozilla Firefox 19+ */
   color: rgba(255, 255, 255, 0.7);
   opacity:  1;
}
.input-form.dark :-ms-input-placeholder { /* Internet Explorer 10-11 */
   color: rgba(255, 255, 255, 0.7);
}
::-ms-input-placeholder { /* Microsoft Edge */
   color: rgba(255, 255, 255, 0.7);
}

/* active state */

input:focus~.bar:before,
input:focus~.bar:after,
textarea:focus~.bar:before,
textarea:focus~.bar:after {
  width: 50%;
}

.autowidth_temp {
  position: absolute;
  left: -9999px;
  top: -9999px;
  display: inline-block;
  padding-right: 6px;
}

/* remove input[type=number] defult arrow style */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button{
  -webkit-appearance: none !important;
  margin: 0;
}
input[type="number"]{-moz-appearance:textfield;}

textarea::-webkit-scrollbar {
  display: none;
}
</style>
