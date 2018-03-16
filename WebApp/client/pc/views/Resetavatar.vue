<template>
  <div class="card">
    <div class="card-header">修改头像</div>
    <div class="flex-row" style="padding: 32px;">
      <img :src="'http://' + imgurl" alt="图片加载失败" class="old-image">
      <div class="flex-col">
        <div class="add-avatar">
          <input id="my-file" type="file" accept="image/jpeg, image/jpg, image/png" multiple="multiple" name="file" @change="readURL">
          <label for="my-file">{{uploadState}}</label>
        </div>
        <div class="tools" v-if="changeFlag">
          <div class="btn-blue" @click="commit">确定</div>
          <div class="btn-gray" @click="cancel">取消</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import API from '../api.js'
  import MButton from '../lib/button.vue'
  import { resizeMe, dataUrlTOBlod } from '../utils/tools.js'

  export default {
    name: 'Avatar',
    components: {
      MButton
    },
    data () {
      return {
        imgurl: '',
        changeFlag: false,
        uploadState: '修改头像，点击上传图片'
      }
    },
    created () {
      this.imgurl = this.$root.avatar
    },
    watch: {
      '$root.avatar': function (val, oldVal) {
        this.imgurl = this.$root.avatar
      }
    },
    methods: {
      // 压缩, 上传, 预览
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
      uploadImg (image) {
        this.uploadState = '上传中...'
        let formData = new window.FormData()
        let blob = dataUrlTOBlod(image)
        formData.append('UploadForm[file]', blob)
        // ajax uplaod iamges
        API.PutImage(formData).then(response => {
          window.localStorage.setItem('avatarupload', response.ufileurl)
          this.imgurl = response.ufileurl
          this.changeFlag = true
          this.uploadState = '上传成功，确认修改或取消'
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
        let data = {
          s_userid: this.$root.s_userid,
          s_avatar: this.imgurl
        }
        API.EditUserInfo({userInfo: JSON.stringify(data)}).then(response => {
          this.toast({
            text: '修改成功',
            type: 'success',
            placement: 'top'
          })
          this.$root.avatar = this.imgurl
        }).catch(err => {
          this.toast({
            text: '保存失败，重新提交试试',
            type: 'error',
            placement: 'top'
          })
          console.log(err)
        })
        this.changeFlag = false
        document.getElementById('my-file').value = ''
        this.uploadState = '修改头像，点击上传图片'
      },
      // 取消修改头像
      cancel () {
        document.getElementById('my-file').value = ''
        this.imgurl = this.$root.avatar
        this.changeFlag = false
        this.uploadState = '修改头像，点击上传图片'
      }
    }
  }
</script>
<style scoped>
  .old-image{
    width: 160px;
    height: 160px;
    border-radius: 50%;
    margin-right: 16px;
  }
  input[type="file"] {
    position: absolute;
    left: -9999px;
  }
  .add-avatar {
    width: 198px;
    text-align: center;
    color: rgba(0,0,0,.87);
    border: 1px solid rgba(0,0,0,.12);
    padding: 8px;
    cursor: pointer;
    margin-bottom: 16px;
  }
</style>