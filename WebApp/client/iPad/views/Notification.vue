<template>
  <div id="notification">
    <Navigation></Navigation>
    <div class="main card">
      <div class="card-header">所有通知</div>
      <div class="card-body">
        <div class="option" v-for="item in noticeList">
          <div class="avatar flex-row">
            <img v-if="item.fromimg" :src="'http://' + item.fromimg" :alt="item.fromname" class="avatar-img">
            <span v-else class="avatar-img"></span>
          </div>
          <div class="content flex flex-col">
            <div class="flex-row" style="justify-content: space-between;line-height: 24px;">
              <span>来自 <span v-text="item.fromname"></span></span>
              <div class="date color54"> {{ item.createtime | formatDate }} </div>
            </div>
            <div class="color54" v-text="item.content" style="line-height: 24px;"></div>
            <div v-if="item.status === '0'">
              <div class="btn-blue" @click="handleNotice(item.logid, 1)">同意</div>
              <div class="btn-gray" @click="handleNotice(item.logid, 2)">拒绝</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import Navigation from '../components/navigation.vue'
  export default {
    components: {
      Navigation
    },
    data () {
      return {
        noticeList: [],
        nodispose: false
      }
    },
    created () {
      this.getNoticeList()
    },
    methods: {
      getNoticeList () {
        this.$http.GetNotices({userid: this.$root.userid}).then(response => {
          if (!response.length) { return }
          this.noticeList = response
        }).catch(err => {
          console.log(err)
        })
      },
      handleNotice (id, status) {
        this.$http.HandleNotice({userid: this.$root.userid, noticeid: id, status: status}).then(response => {
          if (!response) { return }
          this.getNoticeList()
        }).catch(err => {
          this.toast({
            text: err,
            type: 'error',
            placement: 'top'
          })
        })
      }
    }
  }
</script>
<style scoped>
  .main{
    width: 630px;
    margin: 96px auto 40px;
    background: #fff;
  }
  .option{
    display: flex;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid rgba(0,0,0,.12);
  }
  .option .avatar {
    width: 48px;
    height: 48px;
    margin-right: 16px;
  }
  .option .avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #eee;
  }
  .card-header {
    line-height: 60px;
    padding: 0 32px;
    font-size: 20px;
    color: rgba(0,0,0,.87);
    border-bottom: 1px solid rgba(0,0,0,.12);
  }
</style>
