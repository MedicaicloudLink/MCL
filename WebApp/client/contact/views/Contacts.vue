<template>
<div id="add">
  <div class="tool flex-row">
    <span @click="$router.go(-1)">返回</span>
  </div>

  <div class="flex-row search">
    <input type="text" v-model="phone" placeholder="手机号" @keydown.enter="search" />
    <i class="iconfont icon-search" @click="search"></i>
  </div>


  <div class="table result">
    <div class="center signup" v-if="result.type === 4">
      <p>他还未注册梅地卡尔临床数据云</p>
      <m-button type="blue" @click="invite">邀请注册</m-button>
    </div>
    <div v-else>
      <div class="table-header flex-row">
        <span class="w15">姓名</span>
        <span class="w25">电话</span>
        <span class="w35">单位</span>
        <span class="w25">操作</span>
      </div>

      <div class="table-body">
        <div class="table-tr flex-row">
          <span class="w15">{{ result.name }}</span>
          <span class="w25">{{ result.phone }}</span>
          <span class="w35">{{ result.work }}</span>
          <span class="w25" v-if="result.type === 1">已在联系人中</span>
          <span class="w25" v-if="result.type === 2">
            <m-button type="blue" @click="addContact">添加联系人</m-button>
          </span>
          <span class="w25" v-if="result.type === 3">本人</span>
        </div>
      </div>
    </div>

  </div>

</div>
</template>

<script>
import Mynav from './Header'
export default {
  name: 'Home',
  components: { Mynav },
  data () {
    return {
      phone: '',
      result: {
        name: '',
        phone: '',
        work: '',
        id: '',
        type: ''  // 1：已是好友；2：不是好友；3：搜的是自己
      },
      notices: []
    }
  },
  methods: {
    search () {
      this.$http.SearchUser({userid: this.$root.userid, mobile: this.phone}).then(rep => {
        console.log(rep)
        this.result.name = rep.s_username
        this.result.id = rep.s_userid
        this.result.phone = rep.s_cellphone
        this.result.work = rep.s_workunti
        this.result.type = parseInt(rep.type)
      }).catch(err => console.log(err))
    },
    addContact () {
      this.$http.addContact({userid: this.$root.userid, touserid: this.result.id}).then(rep => {
        console.log(rep)
        this.toast({text: '已成功发送邀请'})
      }).catch(err => console.log(err))
    },
    invite () {
      this.$http.inviteContact({userid: this.$root.userid, mobile: this.phone}).then(rep => {
        this.toast({text: '已成功发送邀请'})
      }).catch(err => console.log(err))
    },
    handleContactMsg (id, type) {
      this.$http.replyContactMsg({userid: this.$root.userid, touserid: id, status: type}).then(rep => {
        console.log(rep)
        this.getNotices()
      }).catch(err => console.log(err))
    },
    getNotices () {
      this.$http.GetNotices({userid: this.$root.userid, page: 1, type: 'all'}).then(rep => {
        console.log(rep)
        this.notices = rep.result
      }).catch(err => console.log(err))
    }
  }
}
</script>

<style scoped>
#add {
  width: 756px;
  margin: 96px auto 20px;
}

.tool span {
  border: 1px solid #ddd;
  padding: 4px 16px;
  border-radius: 2px;
  cursor: pointer;
  background: #fff;
}

.tool span:hover {
  background: #eee;
}

.search {
  padding: 0 50px;
  margin-top: 50px;
}

.search input {
  outline: none;
  flex: 1;
  height: 36px;
  border: 1px solid #ddd;
  padding-left: 6px;
}

.search input:focus {
  outline: none;
  border-color: #468df1;
}

.search>i {
  height: 38px;
  line-height: 36px;
  width: 38px;
  text-align: center;
  border: 1px solid #ddd;
  border-left: 0;
  cursor: pointer;
  background: #fff;
  color: #468df1;
}

.signup {
  line-height: 3;
}

.result.table {
  margin: 30px 0;
  padding: 20px 0 80px;
  background: #fff;
}

</style>