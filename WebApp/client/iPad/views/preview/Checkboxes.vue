<template>
  <div class="checkbox-form flex-col">
    <div class="option" v-for="o in option">
      <!--<mu-checkbox :label="o" :nativeValue="o" v-model="val" @input="handleChange"/>-->
      <m-checkbox :nativeValue="o" v-model="val" @input="handleChange" :read="read">{{ o }}</m-checkbox>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PCheckboxes',
  props: {
    read: {type: Boolean, default: false},
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
      this.$emit('update:answers', this.val)
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