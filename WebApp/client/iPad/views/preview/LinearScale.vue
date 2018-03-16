<template>
  <div class="linear-scale flex-row">
    <span class="dec">{{ option.leftText }}</span>
    <div class="flex-row option-item">
      <div class="flex-col option" v-for="n in (option.right - option.left + 1)">
        <span>{{ option.left + n - 1}}</span>
        <m-radio :nativeValue="(option.left + n -1).toString()" v-model="val" @input="handleInput(arguments[0])" class="linear-opton"></m-radio>
      </div>
    </div>
    <span class="dec">{{ option.rightText }}</span>
  </div>
</template>

<script>
export default {
  name: 'PLinearScale',
  props: {
    option: Object,
    answers: String
  },
  data () {
    return {
      val: this.answers
    }
  },
  watch: {
    answers () {
      this.val = this.answers
    }
  },
  methods: {
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      this.$emit('input', this.val)
      this.$emit('update:answers', this.val)
    }
  }
}
</script>
<style scoped>
.dec {
  width: 80px;
  color: #888;
}

.option-item {
  flex: 1;
}

.linear-scale .option {
  flex: 1;
  text-align: center;
  align-items: center;
}

.linear-opton {
  width: 20px;
  margin-top: 8px;
}
</style>

