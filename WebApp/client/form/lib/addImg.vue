<template>
  <span class="add-img">
    <m-modal :open="dialog" title="添加图片" @close="close">
      <h4>本地上传</h4>
      <div class="center upload-content">
        <m-progressbar :now="uploadState" type="success" height="12px" v-if="loading"></m-progressbar>
        <p v-if="!loading">请选择要上传的本地文件</p>
        <span class="upload-img" v-if="!loading">
          <input type="file" accept="image/jpeg, image/jpg, image/png" multiple="multiple" @change="readURL(arguments[0])">
          <span>打开本地文件</span>
        </span>
      </div>

      <div slot="footer">
        <m-button type="gray" @click="close">取消</m-button>
      </div>
    </m-modal>
  </span>
</template>

<script>
import API from '../api.js'
import { resizeMe, dataUrlTOBlod } from '../tool/tools.js'
export default {
  name: 'Addimg',
  props: {
    open: {type: Boolean, default: false}
  },
  data () {
    return {
      loading: false,
      uploadState: 0,
      dialog: this.open
    }
  },
  watch: {
    open () { this.dialog = this.open }
  },
  methods: {
    close (e) {
      this.loading = false
      this.dialog = false
      this.uploadState = 0
      this.$emit('close', e)
    },
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
      let vm = this
      // console.log(image)
      let formData = new window.FormData()
      let blob = dataUrlTOBlod(image)
      formData.append('UploadForm[file]', blob)
      // ajax uplaod iamges
      this.loading = true
      API.PutImage(formData, {
        timeout: 100000,  // 长传时间延长
        onUploadProgress: progressEvent => {
          // 上传进度控制
          console.log('Event:', progressEvent.loaded)
          vm.uploadState = parseInt((progressEvent.loaded / progressEvent.total) * 100)
        }
      }).then(rep => {
        vm.$emit('imgurl', rep.ufileurl)
        vm.close()
        this.loading = false
      }).catch(err => {
        this.loading = false
        console.log(err)
      })
    }
  }
}
</script>

<style scoped>

</style>

