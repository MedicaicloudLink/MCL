<template>
  <span class="add-img">
    <m-modal :open="dialog" title="上传文件" @close="close">
      <h4>本地上传</h4>
      <div class="center upload-content">
        <m-progressbar :now="uploadState" type="success" height="12px" v-if="loading"></m-progressbar>
        <p v-if="!loading">请选择要上传的本地文件</p>
        <span class="upload-img" v-if="!loading">
          <input type="file" multiple="multiple" @change="readURL(arguments[0])">
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
export default {
  name: 'Addimg',
  props: {
    open: {type: Boolean, default: false}
  },
  data () {
    return {
      loading: false,
      uploadState: 0,
      dialog: this.open,
      filename: ''
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
      if (event.target.files && event.target.files[0]) {
        console.log(event.target.files[0])
        this.filename = event.target.files[0].name
        this.uploadFile(event.target.files[0], this.filename)
      }
    },
    // 上传
    uploadFile (file, name) {
      let vm = this
      let formData = new window.FormData()
      formData.append('UploadForm[file]', file)
      // ajax uplaod
      this.loading = true
      API.PutImage(formData, {
        timeout: 100000,  // 长传时间延长
        onUploadProgress: progressEvent => {
          // 上传进度控制
          console.log('Event:', progressEvent.loaded)
          vm.uploadState = parseInt((progressEvent.loaded / progressEvent.total) * 100)
        }
      }).then(rep => {
        vm.$emit('fileurl', {fileurl: rep.ufileurl, filename: name})
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

