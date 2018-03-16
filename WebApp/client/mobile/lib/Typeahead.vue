<template>
<div class="search-tip">
  <input type="text" 
    :placeholder="placeholder"
    v-model="query" 
    @focus="_onFocus"
    @blur="_onBlur" 
    @input="update" />

  <div class="results" :style="{width: input_width + 'px', top: input_height + 'px'}" v-if="tipState && citems.length > 0">
    <div class="item" v-for="(i, index) in citems" @click="confirm(index)">{{i}}</div>
  </div>
</div>
</template>

<script>
export default {
  name: 'Typeahead',
  props: {
    placeholder: {
      type: String
    },
    value: {
      type: String
    },
    items: {
      type: Array
    }
  },
  data () {
    return {
      input_width: '',
      input_height: '',
      tipState: false,
      query: this.value,
      citems: this.items
    }
  },
  watch: {
    items () {
      this.citems = this.items
    }
  },
  mounted () {
    this.input_width = this.$el.offsetWidth - 2
    this.input_height = this.$el.offsetHeight
  },
  methods: {
    _onFocus () {
      this.tipState = true
    },
    _onBlur () {
      // 延迟隐藏检索提示
      window.addEventListener('click', (e) => {
        if (!this.$el.contains(e.target)) this.tipState = false
      }, false)
    },
    update () {
      setTimeout(() => {
        if (!this.query) {
          return false
        }
        this.$emit('change', this.query)
      }, 200)
    },
    clearnState () {
      this.tipState = false
      this.citems = []
    },
    confirm (index) {
      this.query = this.citems[index]
      this.$emit('change', this.citems[index])
      this.clearnState()
    }
  }
}
</script>

<style>
input {
  outline: none;
  border-radius: 2px;
}
/*搜索提示*/

.search-tip {
  position: relative;
  height: auto;
}

.search-tip .results {
  position: absolute;
  left: 0px;
  height: auto;
  max-height: 120px;
  width: 250px;
  padding: 5px 0;
  background: #fff;
  border: 1px solid #e4e4e4;
  z-index: 100;
}

.search-tip .results.show {
  display: block;
}

.search-tip .results .item {
  height: 30px;
  padding: 0 8px;
  line-height: 30px;
  font-size: 1em;
  cursor: pointer;
  color: #333;
}

.search-tip .results .item:hover {
  background: #eaeefb;
}

.search-tip .results .item:active {
  background: #468df1;
  color: #fff;
}

</style>
