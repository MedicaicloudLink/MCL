<template>
  <div id="avatar">
    <mynav></mynav>
    <div class="content flex-row">
      <div class="profile">
        <div class="user-info">
          <div class="avatar">
            <span v-if="$root.userInfo.s_avatar === ''">{{ $root.shortName | uppercase }}</span>
            <span v-else>
              <img :src="'http://' + $root.userInfo.s_avatar" alt="" class="avatar-img">
            </span>
          </div>
          <div class="text flex-col" style="flex: 1;">
            <span class="name">{{ $root.userInfo.s_username }}</span>
            <span class="work">{{ $root.userInfo.s_workunti }}</span>
          </div>
        </div>
        <div class="link-list flex-col">
          <router-link to="/profile" class="link-item">个人信息</router-link>
          <router-link to="/avatar" class="link-item active">头像修改</router-link>
          <router-link to="/resetpw" class="link-item">密码修改</router-link>
        </div>
      </div>
      <div class="chang-pw">
        <h2>修改头像</h2>
  
        <div class="avatar">
          <input id="my-file" accept="image/jpeg, image/jpg, image/png" multiple="multiple" name="file" type="file" @change="readURL">
          <div class="img-avatar flex-row">
            <img :src="'http://' + imgurl" alt="" class="image-btn">
            <div class="flex-col">
              <label class="add-avatar" for="my-file">
                <i class="iconfont icon-xindenew85"></i>上传图片，修改头像</label>
              <div>
                <m-button type="blue" @click="commit">确定</m-button>
              </div>
            </div>
          </div>
        </div>
  
        <div style="padding: 24px 10px 0 10px;text-align: center;">
        </div>

      </div>
    </div>
  </div>
</template>
<script>
  import API from '../api.js'
  import Mynav from '../components/Header.vue'
  import MButton from '../lib/Button.vue'
  import { resizeMe, dataUrlTOBlod } from '../utils/tools.js'

  export default {
    name: 'Avatar',
    components: {
      MButton,
      Mynav
    },
    data () {
      return {
        imgurl: ''
      }
    },
    created () {
      this.imgurl = this.$root.userInfo.s_avatar
    },
    watch: {
      '$root.userInfo.s_avatar': function (val, oldVal) {
        this.imgurl = this.$root.userInfo.s_avatar
      }
    },
    methods: {
      // 预览, 压缩, 上传
      readURL (event) {
        let vm = this
        if (event.target.files && event.target.files[0]) {
          let reader = new window.FileReader()

          reader.onload = function (e) {
            /** 压缩 */
            let fileSize = event.target.files[0].size / 1024
            // scale 压缩比例
            let scale = (fileSize > 2500 && 0.1) || (fileSize > 1500 && 0.2) || (fileSize > 1000 && 0.3) || (fileSize > 500 && 0.6) || 0.8
            let img = new window.Image()
            img.src = e.target.result
            /** 上传 */
            img.onload = function () {
              // have to wait till it's loaded
              let dataURL = resizeMe(img, scale)
              vm.uploadImg(dataURL)
            }
          }

          reader.readAsDataURL(event.target.files[0])
        }
      },
      // 上传
      uploadImg (image) {
        let formData = new window.FormData()
        let blob = dataUrlTOBlod(image)
        formData.append('UploadForm[file]', blob)
        // ajax uplaod iamges
        API.PutImage(formData).then(response => {
          window.localStorage.setItem('avatarupload', response.ufileurl)
          this.imgurl = response.ufileurl
        }).catch(err => {
          this.toast({
            text: '上传失败，重新上传' + err,
            type: 'error',
            placement: 'bottom-left'
          })
        })
      },
      // 确认修改头像
      commit () {
        console.log(this.imgurl)
        let data = {
          s_userid: this.$root.userInfo.s_userid,
          s_username: this.$root.userInfo.s_username,
          s_sex: this.$root.userInfo.s_sex,
          s_cellphone: this.$root.userInfo.s_cellphone,
          s_userEmail: this.$root.userInfo.s_userEmail,
          s_joblevel: this.$root.userInfo.s_joblevel,
          s_workunti: this.$root.userInfo.s_workunti,
          s_department: this.$root.userInfo.s_department,
          s_avatar: this.imgurl
        }
        API.SetUserinfo({userInfo: JSON.stringify(data)}).then(response => {
          this.toast({
            text: '修改成功',
            type: 'success',
            placement: 'bottom-left'
          })
          this.$root.userInfo.s_avatar = this.imgurl
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