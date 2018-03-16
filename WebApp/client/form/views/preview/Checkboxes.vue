<template>
  <div class="checkbox-form flex-col">
    <div class="option" v-for="o in option">
      <!--<mu-checkbox :label="o" :nativeValue="o" v-model="val" @input="handleChange"/>-->
      <m-checkbox :nativeValue="o" v-model="val" @input="handleChange">{{ o }}</m-checkbox>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PCheckboxes',
  props: {
    option: Array,
    answers: Array
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
      this.$emit('input', this.val.join(','))
    }
  }
}
</script>

<style scoped>
.checkbox-icon {
  display: inline-block;
  width: 18px;
  height: 18px;
  border: 2px solid #e3e3e3;
  border-radius: 3px;
  margin-right: 8px;
}

</style>