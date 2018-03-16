<template>
  <div>
    <div class="followcard">
      <p><span>计划名称：</span><span class="follow-name" v-text="followInfo.u_follow_name"></span></p>
      <p><span>计划状态：</span><span class="follow-status" v-text="followInfo.u_status"></span></p>
      <p><span>计划开始时间：</span><span class="follow-starttime" v-text="followInfo.u_follow_start"></span></p>
      <p><span>计划结束时间：</span><span class="follow-endtime" v-text="followInfo.u_follow_end"></span></p>
      <p><span>计划创建时间：</span><span class="follow-createtime" v-text="followInfo.u_createtime"></span></p>
      <p><span>计划创建人：</span><span class="follow-createname" v-text="followInfo.u_createname"></span></p>       
    </div>
    <div class="followMember">
      <span>随访成员</span>
      <div class="memberlist">
        <span v-for="item in followMember">{{ item.s_username}}</span>
        <span>[+]</span>
      </div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  export default {
    data () {
      return {
        followInfo: [],
        followMember: []
      }
    },
    created () {
      API.FollowDetail({userid: window.sessionStorage.getItem('userid'), followid: this.$route.params.followid}).then((response) => {
        this.followInfo = response
      }).catch((err) => {
        window.alert(err)
      })
      this.getFollowMember()
    },
    methods: {
      getFollowMember () {
        API.GetFollowMember({userid: window.sessionStorage.getItem('userid'), projectid: this.$route.params.id, followid: this.$route.params.followid}).then((response) => {
          this.followMember = response
        }).catch((err) => {
          window.alert(err)
        })
      }
    }
  }
</script>
<style>
  /* 基本详情 */
  .followcard{
    width: 100%;
    position: relative;
    border: 1px #ccc solid;
    padding: 1em;
  }
  .followcard p{
    line-height: 1.7;
    display: flex;
    display: -wibkit-flex;
    font-size: 14px;
  }
  .memberlist{
    border: 1px #ccc solid;
    padding: 1em;
  }
  .memberlist span{
    padding: 0 10px;
  }
</style>