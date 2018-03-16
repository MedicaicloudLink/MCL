<template>
  <div class="table flex-col">
    <div class="header flex-row">
      <span></span>
      <span v-for="c in option.column">{{ c }}</span>
    </div>
    <div class="option flex-row" v-for="(r, index) in val">
      <span style="margin-bottom: 16px;">{{ r.label }}</span>
      <m-radio v-for="c in option.column" :nativeValue="c" v-model="r.value" @input="handleInput(arguments[0], index)"></m-radio>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PTable',
  props: {
    option: Object,
    answers: Array
  },
  data () {
    return {
      val: []
    }
  },
  watch: {
    answers () {
      if (this.answers.length === 0) {
        this.val = []
        this.option.row.map(label => this.val.push({label: label, value: ''}))
      }
    }
  },
  mounted () {
    if (this.answers) {
      if (this.answers.length === 0) {
        this.val = []
        this.option.row.map(label => this.val.push({label: label, value: ''}))
      } else {
        this.val = this.answers
      }
    }
  },
  methods: {
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      let value = ''
      for (let i in this.val) {
        if (this.val[i].value) {
          value += this.val[i].label + ',' + this.val[i].value + '、'
        }
      }
      this.$emit('input', value.substring(0, value.length - 1))
      this.$emit('update:answers', this.val)
    }
  }
}
</script>

<style scoped>
.table .header, .table .option {
  margin-bottom: 16px;
}

.table .header span {
  flex: 1;
}

.table .option span, .table .option label {
  flex: 1;
}
</style>


