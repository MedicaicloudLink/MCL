<template>
  <div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>姓名<span class="keep">*</span></p></div>
        <div class="a-item"><m-input hintText="您的姓名" full v-model="name"></m-input></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>性别<span class="keep">*</span></p></div>
        <div class="a-item">
          <m-radio nativeValue="男" v-model="gender">男</m-radio>
          <m-radio nativeValue="女" v-model="gender">女</m-radio>
        </div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>出生日期<span class="keep">*</span></p></div>
        <div class="a-item"><m-date v-model="birthday"></m-date></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>登记日期<span class="keep">*</span></p></div>
        <div class="a-item"><m-date v-model="join" now></m-date></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>地址<span class="keep">*</span></p></div>
        <div class="a-item"><m-address v-model="address"></m-address></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>联系电话<span class="keep">*</span></p></div>
        <div class="a-item"><m-input hintText="电话号码" full v-model="phone"></m-input></div>
      </div>
    </div>
    <div class="item base-info">
      <div class="questions">
        <div class="q"><p>备用电话</p></div>
        <div class="a-item"><m-input hintText="电话号码" full v-model="phone2"></m-input></div>
      </div>
    </div>
  </div>
</template>

<script>
import MDate from './preview/Date.vue'
import MAddress from './preview/Address.vue'
export default {
  name: 'Baseinfo',
  components: { MDate, MAddress },
  data () {
    return {
      name: '',
      gender: '1',
      address: '',
      birthday: '',
      phone: '',
      phone2: '',
      join: ''
    }
  }
}
</script>

<style scoped>
.item {
  width: 770px;
  margin: 0 auto;
  background: #fff;
}

.section {
  margin-top: 30px;
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #eee;
}

.section p.title {
  font-size: 26px;
  margin: 0;
}

.link-dec {
  display: block;
}
.link-dec a {
  color: #468df1;
}

.questions {
  padding: 16px 24px 24px 42px;
  border-bottom: 1px solid #ddd;
}

/*必答*/
.keep {
  color: red;
  font-size: 18px;
}

.questions .q {
  padding: 8px 0 12px 0;
}

.questions .q p {
  font-size: 16px;
}
</style>

