<template>
  <span class="radio-option" @click="changeHandler">
    <input type="radio" v-model="value" :value="nativeValue"/>
    <label><slot></slot></label>
  </span>
</template>

<script>
export default {
  name: 'MRadio',
  props: {
    read: {type: Boolean, default: false},
    nativeValue: {},
    value: {}
  },
  data () {
    return {
      val: this.value
    }
  },
  watch: {
    val () { this.val = this.value }
  },
  methods: {
    changeHandler () {
      if (this.read) return
      this.$emit('input', this.nativeValue)
      this.$emit('update:value', this.nativeValue)
    }
  }
}
</script>

<style scoped>
.radio-option {
  display: block;
  height: 20px;
  margin-bottom: 16px;
  position: relative;
}

input[type="radio"] {
  display: none;
}

input[type="radio"] + label {
  display: inline-block;
  cursor: pointer;
  line-height: 20px;
  padding-left: 40px;
  font-size: 16px;
}
input[type="radio"] + label:before, input[type="radio"] + label:after {
  content: "";
  position: absolute;
  border-radius: 50%;
  transition: all 0.3s ease;
  transition-property: transform, border-color;
}
input[type="radio"] + label:before {
  top: 0;
  left: 0;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(0, 0, 0, 0.54);
}
input[type="radio"] + label:after {
  top: 5px;
  left: 5px;
  width: 10px;
  height: 10px;
  transform: scale(0);
  background: #468df1;
}

input[type="radio"]:checked + label:before {
  border-color: #468df1;
  animation: ripple 0.2s linear forwards;
}
input[type="radio"]:checked + label:after {
  transform: scale(1);
}

@keyframes ripple {
  0% {
    box-shadow: 0px 0px 0px 1px transparent;
  }
  50% {
    box-shadow: 0px 0px 0px 15px rgba(0, 0, 0, 0.1);
  }
  100% {
    box-shadow: 0px 0px 0px 15px transparent;
  }
}
</style>

