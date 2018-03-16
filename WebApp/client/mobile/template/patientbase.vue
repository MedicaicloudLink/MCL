<template>
  <div class="main">
    <div class="form-group" style="display: flex;">
      <label for="name">姓名 </label>
      <input type="text" v-model="patientname" id="patientname" placeholder="姓名">
    </div>
    <div class="form-group" style="display: flex;">
      <label for="phone" style="line-height: 48px;">性别 </label>
      <div style="flex: 1;">
        <input type="radio" value="1" v-model="gender" id="man">
        <label for="man" style="padding-right: 24px;">男</label>
        <input type="radio" value="2" v-model="gender" id="woman">
        <label for="woman">女</label>
      </div>
    </div>
    <div class="form-group" style="display: flex;">
      <label for="birthday">出生日期 </label>
      <datepicker :width="170" v-model="birthday"></datepicker>
    </div>
    <div class="form-group" style="display: flex;">
      <label for="jointime">入组时间 </label>
      <datepicker :width="170" v-model="jointime"></datepicker>
    </div>
  </div>
</template>

<script>
import Datepicker from '../lib/Date.vue'
export default {
  name: 'patientbase',
  components: {
    Datepicker
  },
  props: {
    source: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  data () {
    return {
      patientname: '',
      gender: '',
      birthday: '',
      jointime: ''
    }
  },
  watch: {
    source () {
      this.patientname = this.source.u_patientname
      this.gender = this.source.u_gender
      this.birthday = this.source.u_birthday
      this.jointime = this.source.u_jointime
    }
  },
  created () {
    // 监听所有绑定值，emit 返回父级
    for (let n in this.$data) {
      this.$watch(n, (val, oldVal) => {
        this.$emit('putsever', this.$data)
      })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .form-group {
    padding: 8px 0;
    border-bottom: 1px solid #fff;
  }

  .form-group>label {
    line-height: 32px;
    width: 170px;
    color: #777;
  }
  
</style>