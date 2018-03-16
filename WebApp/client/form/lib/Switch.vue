<template>
  <!--TODO-->
  <div class="switch">
    <input type="checkbox" :value="val" @change="handleChange">
    <div class="switch-box-slider"></div>
  </div>
</template>

<script>
export default {
  name: 'MSwitch',
  props: {
    value: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      val: false
    }
  },
  methods: {
    handleChange (e) {
      this.val = e.target.vaule
      this.$emit('input', this.val)
    }
  }
}
</script>

<style>
.switch {
  position: relative;
  display: inline-block;
  height: 24px;
  line-height: 24px;
  cursor: pointer;
  user-select: none;
}

.switch input[type=checkbox] {
  opacity: 0;
  display: block;
  height: 100%;
  width: 100%;
}

.switch-box-slider {
  position: absolute;
  display: inline-block;
  height: 8px;
  width: 32px;
  background: #d5d5d5;
  border-radius: 8px;
  cursor: pointer;
  -webkit-transition: all 0.2s ease;
  transition: all 0.2s ease;
}

.switch-box-slider:after {
  position: absolute;
  left: -8px;
  top: -8px;
  display: block;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #eeeeee;
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.2);
  content: '';
  -webkit-transition: all 0.2s ease;
  transition: all 0.2s ease;
}

.switch input[type=checkbox]:checked:not(:disabled) ~ .switch-box-slider {
  background: #28e1bd;
}
</style>
