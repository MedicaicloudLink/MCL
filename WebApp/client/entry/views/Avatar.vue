<template>
  <div id="avatar">
    <div class="content flex-row">
      <div class="profile">
        <div class="user-info">
          <div class="avatar">
            <span v-if="$root.avatar === ''">{{ $root.shortname | uppercase }}</span>
            <span v-else>
              <img :src="'http://' + $root.avatar" alt="" class="avatar-img">
            </span>
          </div>
          <div class="text flex-col" style="flex: 1;">
            <span class="name">{{ $root.username }}</span>
            <span class="work">{{ $root.hospital }}</span>
          </div>
        </div>
        <div class="link-list flex-col">
          <router-link to="/profile" class="link-item">个人信息</router-link>
          <router-link to="/avatar" class="link-item active">头像修改</router-link>
          <router-link to="/resetpw" class="link-item">密码修改</router-link>
        </div>
      </div>
      <div class="chang-pw">
        <h2>头像修改</h2>
  
        <div class="avatar">
          <div class="img-avatar flex-row">
            <img :src="'http://' + imgurl" alt="" class="image-btn">
            <div class="flex-col">
              <span class="add-avatar" for="my-file" @click="update_img = true">
                <i class="iconfont icon-xindenew85"></i>上传图片，修改头像</span>
              <div>
                <m-button type="blue" @click="commit">确定</m-button>
              </div>
            </div>
          </div>
        </div>
  
        <div style="padding: 24px 10px 0 10px;text-align: center;"></div>

      </div>
    </div>

    <addimg :open="update_img" @imgurl="getImgurl" @close="update_img = false"></addimg>
  </div>
</template>
<script>
  import API from '../api.js'

  export default {
    name: 'Avatar',
    data () {
      return {
        userInfo: {},
        update_img: false,
        imgurl: ''
      }
    },
    created () {
      this.getUserinfo()
      this.imgurl = this.$root.avatar
    },
    watch: {
      '$root.avatar': function (val, oldVal) {
        this.imgurl = this.$root.avatar
      }
    },
    methods: {
      getUserinfo () {
        this.$http.GetUserInfo({userid: this.$root.userid}).then(response => {
          this.userInfo = response[0]
          for (let i in this.editInfo) {
            this.editInfo[i] = this.userInfo[i]
          }
        }).catch(err => {
          console.log(err)
        })
      },
      getImgurl (img) {
        this.imgurl = img
      },
      // 确认修改头像
      commit () {
        console.log(this.imgurl)
        let data = {
          s_userid: this.userInfo.s_userid,
          s_username: this.userInfo.s_username,
          s_sex: this.userInfo.s_sex,
          s_cellphone: this.userInfo.s_cellphone,
          s_userEmail: this.userInfo.s_userEmail,
          s_joblevel: this.userInfo.s_joblevel,
          s_workunti: this.userInfo.s_workunti,
          s_department: this.userInfo.s_department,
          s_avatar: this.imgurl
        }
        API.SetUserinfo({userInfo: JSON.stringify(data)}).then(response => {
          this.toast({
            text: '修改成功',
            type: 'success',
            placement: 'bottom-left'
          })
          this.$root.avatar = this.imgurl
        }).catch(err => {
          this.toast({
            text: '保存失败，重新提交试试',
            type: 'success',
            placement: 'bottom-left'
          })
          console.log(err)
        })
      }
    }
  }
</script>
<style scoped>
  .content{
    margin: 50px auto;
    justify-content: center;
  }

  .profile {
    width: 216px;
    margin-right: 16px;
    height: 300px;
    background: #fafafa;
    box-shadow: 3px 3px 5px rgba(0, 0, 0, .13);
  }



  .profile .user-info {
    text-align: center;
    border-bottom: 1px solid #eee;
    margin: 24px 12px 0 12px;
  }

  .profile .avatar span{
    display: inline-block;
    font-size: 22px;
    height: 72px;
    width: 72px;
    line-height: 72px;
    background: #468df1;
    color: #fff;
    border-radius: 50%;
    overflow: hidden;
  }

  .avatar-img {
    display: inline-block;
    height: 72px;
    width: 72px;
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
   overflow: hidden;
   text-overflow: ellipsis;
   white-space: nowrap;
 }

  .profile .link-list {
   font-size: 16px;
   line-height: 40px;
   text-align: center;
 }

 .profile .link-list a{
   font-size: 16px;
 }

 .profile .link-list a.active {
   background: rgba(69, 141, 241, .12);
 }

 .chang-pw {
   background: #fff;
   padding: 24px;
   width: 650px;
   border: 1px solid #ccc;
   align-items: center;
 }

 .chang-pw h2 {
    border-bottom: 1px solid #eee;
    font-size: 22px;
    font-weight: 400;
    color: #777;
    padding-bottom: 24px;
  }

  .avatar .img-avatar {
    display: flex;
  }

  .img-avatar img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
  }

  .img-avatar>div {
    padding-left: 18px;
  }

  .add-avatar {
    text-align: center;
    color: #868381;
    margin: 18px 0 18px;
    border: 1px solid #ccc;
    background: #eee;
    padding: 8px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 2px;
  }

  input[type="file"] {
    position: absolute;
    left: -9999px;
  }

  .tools{
    flex: 1;
    width: 100%;
    text-align: left;
    cursor: pointer;
  }
  .tools>span:first-child{
    margin-right: 24px;
  }
</style>