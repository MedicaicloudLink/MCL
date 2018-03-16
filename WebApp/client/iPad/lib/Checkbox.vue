<template>
  <span class="checkbox-option" @click="changeHandler">
    <input type="checkbox" v-model="inputValue" :value="nativeValue" :disabled="read"/>
    <label><slot></slot></label>
  </span>
</template>

<script>
export default {
  name: 'MCheckbox',
  props: {
    read: {type: Boolean, default: false},
    nativeValue: {},
    value: Array
  },
  data () {
    return {
      inputValue: this.value
    }
  },
  watch: {
    value (val) {
      this.inputValue = val
    },
    inputValue (val) {
      this.$emit('input', val)
    }
  },
  methods: {
    changeHandler () {
      if (this.read) return
      const isCheck = this.inputValue.indexOf(this.nativeValue)
      if (parseInt(isCheck) !== -1) {
        this.inputValue.splice(isCheck, 1)
      } else {
        this.inputValue.push(this.nativeValue)
      }
      this.$emit('input', this.inputValue)
    }
  }
}
</script>

<style scoped>
.checkbox-option {
  display: block;
  height: 20px;
  margin-bottom: 16px;
  position: relative;
}

input[type="checkbox"] {
  display: none;
}

input[type="checkbox"] + label {
  display: inline-block;
  cursor: pointer;
  line-height: 20px;
  padding-left: 40px;
  font-size: 16px;
}
input[type="checkbox"] + label:before, input[type="checkbox"] + label:after {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
}
input[type="checkbox"] + label:before {
  top: 0;
  left: 0;
  width: 16px;
  height: 16px;
  background: #fff;
  border-radius: 2px;
  border: 2px solid rgba(0, 0, 0, 0.54);
  transition: background .3s;
}

input[type="checkbox"] + label:after {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
}

input[type="checkbox"]:checked + label:before {
  border-color: #468df1;
  background: #468df1;
  animation: ripple 0.3s linear forwards;
}
input[type="checkbox"]:checked + label:after {
  transform: rotate(-45deg);
  top: 6px;
  left: 5px;
  width: 10px;
  height: 4px;
  border: 2px solid #fff;
  border-top-style: none;
  border-right-style: none;
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

