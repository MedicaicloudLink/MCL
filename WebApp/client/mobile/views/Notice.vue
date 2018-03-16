<template>
  <div>
    <mynav></mynav>
    <div class="list">
      <h5 class="describe"><p>所有通知</p></h5>
      <div class="option flex-row" v-for="item in notices">
        <div class="flex-row">
            <span v-if="item.fromimg.length < 10" class="avatar-bg"></span>
            <img v-else :src="'http://' + item.fromimg" :alt="item.fromname" class="avatar">
        </div>
        <div class="flex-col notices">
          <div class="flex-row line1">
            <span><span v-text="item.fromname"></span></span>
            <div class="date">{{ item.createtime | formatDate }}</div>
          </div>
          <div class="line2" v-text="item.content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../api.js'
  import Mynav from '../components/Header.vue'
  import MButton from '../lib/Button.vue'

  export default {
    name: 'Notices',
    components: {
      Mynav,
      MButton
    },
    data () {
      return {
        notices: []
      }
    },
    mounted () {
      this.getNotices()
    },
    methods: {
      getNotices () {
        api.GetNotices({userid: this.$root.userid}).then(res => {
          res.map(n => this.notices.push(n))
        }).catch(error => {
          console.log(error)
        })
      }
    }
  }
</script>

<style scoped>
  .describe {
    color: #555;
    font-size: 18px;
    font-weight: 600;
    padding-left: 25px;
    border-bottom: 1px solid #eee;
    line-height: 50px;
  }

  .list {
    width: 630px;
    margin: 20px auto 60px;
    background: #fff;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, .1);
    padding-bottom:10px;
  }

  .list .option {
    padding: 16px 20px;
    border-bottom: 1px solid #eee;
    letter-spacing: 1px;
    align-items: center;
  }

  .list .option>.notices {
    flex: 1;
  }

  .list .line1 {
    justify-content: space-between;
    line-height: 32px;
    align-items: center;
  }

  .list .line2 {
    color: #777;
    font-size: 14px;
  }

  .avatar {
    display: inline-block;
    height: 48px;
    width: 48px;
    border-radius: 50%;
    margin-right: 10px;
  }

  .avatar-bg {
    display: inline-block;
    height: 32px;
    width: 32px;
    border-radius: 50%;
    margin-right: 10px;
    background: #38bff3;
  }

  .date {
    color: #777;
    font-size: 12px;
  }
</style>