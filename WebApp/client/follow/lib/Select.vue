<template>
  <div class="select">
    <m-input type="text" v-model="text" @focus="_onFocus(read)" read>
      <span slot="iconright" class="arrow-bottom"></span>
    </m-input>
    <transition name="fade">
      <ul class="drop-option" ref="menu" v-show="show" 
        :style="{minWidth: menuMinWidth + 'px', maxHeight: maxOptionHeight}">
        <template v-if="currentOptions.length">
          <li v-for="option in currentOptions" 
            @click.prevent="select(option)" 
            :class="{active: val === option.value}">
            <span v-html="option.label"></span>
          </li>
        </template>
        
        <slot v-else></slot>
      </ul>
    </transition>
  </div>
</template>

<script>
import EventListener from '../tool/EventListener.js'
import { checkNum } from '../tool/tools.js'
export default {
  name: 'MSelect',
  props: {
    hintText: String,
    read: {type: Boolean, default: false},
    value: {},
    item: {},
    maxOptionHeight: {
      type: String,
      default: 'auto'
    }
  },
  data () {
    return {
      text: '',
      val: this.value,
      currentOptions: [],
      show: false,
      menuMinWidth: ''
    }
  },
  watch: {
    value () {
      this.val = this.value
      this.currentOptions.map(i => {
        if (this.val === i.value) this.text = i.label
      })
    },
    item () {
      this.currentOptions = []
      this.updateList()
      this.$emit('input', this.val)
    },
    currentOptions () {
      this.currentOptions.map(i => {
        if (this.val === i.value) this.text = i.label
      })
    }
  },
  mounted () {
    this.updateList()
  },
  beforeDestroy () {
    if (this._closeEvent) this._closeEvent.remove()
  },
  methods: {
    updateList () {
      this.$nextTick(() => {
        this.menuMinWidth = this.$el.offsetWidth
        if (!this.currentOptions.length) {
          var options = this.$refs.menu.getElementsByClassName('m-option')
          var ret = []
          for (var i = 0, l = options.length; i < l; i++) {
            var label = options[i].innerHTML
            var value = options[i].getAttribute('value')
            // 非空字符串
            if (value.length > 0) {
              // 特殊类型修改, 有返回值为0可能，所以判断===false
              if (checkNum(value) !== false) value = checkNum(value)
              if (value === 'true') value = true
              if (value === 'false') value = false
            }

            ret.push({value: value, label: label})
          }
          this.currentOptions = ret
        }

        this._closeEvent = EventListener.listen(window, 'click', (e) => {
          if (!this.$el.contains(e.target)) this.show = false
        })
      })
    },
    _onFocus (read) {
      this.show = !read
    },
    select (option) {
      this.val = option.value
      this.text = option.label
      this.show = false
      this.$emit('input', this.val)
    }
  }
}
</script>

<style>
.select {
  display: inline-block;
  position: relative;
}

.arrow-bottom {
  display: inline-block;
  width: 0;
  height: 0;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-top: 5px solid #ddd;
}

.drop-option {
  display: block;
  position: absolute;
  top: 30px;
  left: 0;
  background: #fff;
  min-width: 120px;
  overflow-y: auto;
  z-index: 1000;
  text-align: left;
  box-shadow: 0 1px 6px rgba(0,0,0,.117647), 0 1px 4px rgba(0,0,0,.117647);
  cursor: pointer;
}

.drop-option>li {
  list-style: none;
  padding: 7px 25px 7px 20px;
  color: #555;
}

.drop-option>li:hover {
  background: #f5f5f5;
}

.drop-option .active {
  color: #468df1;
}


/*动画效果*/
  .fade-enter-active {
    transition: all .3s ease;
  }
  .fade-leave-active {
    transition: all .3s ease;
  }
  .fade-enter, .fade-leave-active {
    transform: translateY(-6px);
    opacity: 0;
  }
</style>
