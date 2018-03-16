<template>
  <div class="main">
    <div class="form-group" style="display: flex;">
      <label for="adress">现居住地 </label>
      <input type="text" v-model="address" id="address" placeholder="省/市">
    </div>
    <div class="form-group" style="display: flex;">
      <label for="phone">联系电话 </label>
      <input type="number" v-model="phone" id="phone" placeholder="手机号码">
    </div>
    <div class="form-group" style="display: flex;">
      <label for="phone2">备用电话 </label>
      <input type="text" v-model="phone2" id="phone" placeholder="固话或手机号">
    </div>
  </div>
</template>

<script>
export default {
  name: 'temp1',
  props: {
    mdid: String,
    templateid: String,
    recordid: String,
    source: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  data () {
    return {
      address: '',
      phone: '',
      phone2: ''
    }
  },
  created () {
    if (JSON.stringify(this.source) !== '{}') {
      this.address = this.source.address
      this.phone = this.source.phone
      this.phone2 = this.source.phone2
    }
    // 监听所有绑定值，emit 返回父级
    for (let n in this.$data) {
      this.$watch(n, (val, oldVal) => {
        this.$emit('putsever', {sourceData: this.$data, showData: this.changeData()})
      })
    }
  },
  methods: {
    // 显示数据
    changeData () {
      let showData = {
        '现居住地': this.address,
        '联系电话': this.phone,
        '备用电话': this.phone2
      }

      return showData
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .form-group {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .form-group>label {
    line-height: 32px;
    width: 170px;
    color: #777;
  }

  
</style>