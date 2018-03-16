<template>
  <div id="userInfo">
    <Navigation></Navigation>
    <div class="content flex-row">
      <div class="profile">
        <div class="user-info">
          <span v-if="$root.avatar"><img class="avatar" :src="'http://' + $root.avatar" alt="userImg"></span>
          <span class="avatar" v-else>{{ $root.shortName | uppercase }}</span>
          <div class="text flex-col" style="flex: 1;">
            <span class="name">{{ $root.username }}</span>
            <span class="work">{{ $root.hospital }}</span>
          </div>
        </div>
        <div class="link-list flex-col">
          <router-link to="/user/userInfo" class="link-item active">个人信息</router-link>
          <router-link to="/user/resetAvatar" class="link-item">头像修改</router-link>
          <router-link to="/user/resetPw" class="link-item">密码修改</router-link>
        </div>
      </div>
      <div class="show-view">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  export default {
    name: 'User',
    components: {
      Navigation
    }
  }
</script>
<style scoped>
  .content{
    margin: 90px auto;
    justify-content: center;
  }

  .profile {
    width: 216px;
    margin-right: 32px;
    height: 300px;
    background: #fff;
    box-shadow: 3px 3px 5px rgba(0, 0, 0, .13);
  }

  .profile .user-info {
    text-align: center;
    border-bottom: 1px solid rgba(0, 0, 0, .12);
    padding-top: 24px;
  }

  .profile .avatar {
    font-size: 22px;
    height: 72px;
    width: 72px;
    line-height: 72px;
    background: #468df1;
    color: #fff;
    border-radius: 50%;
  }

  .profile .user-info .text {
    padding: 12px 0;
  }

  .profile .user-info .name {
    font-size: 18px;
    line-height: 24px;
  }

  .profile .user-info .work {
   font-size: 14px;
   line-height: 24px;
 }

 .profile .link-list {
   padding: 12px 0;
   font-size: 18px;
   line-height: 24px;
   text-align: center;
 }

 .profile .link-list a{
   font-size: 18px;
   line-height: 2;
   color: rgba(0,0,0,.87);
 }

 .profile .link-list a.router-link-active {
   background: rgba(69, 141, 241, .12);
 }
 .show-view{
   width: 700px;
   background: #fff;
 }
</style>