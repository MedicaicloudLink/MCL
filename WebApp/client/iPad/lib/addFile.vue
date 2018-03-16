<template>
  <span class="add-img">
    <m-modal :open="dialog" title="添加文件附档" @close="close">
      <div class="content">
        <div class="type">
          <p>本地上传</p>
        </div>
        <div class="type-content">
          <div class="center upload-content" v-if="!fileList.length">
            <m-progressbar :now="uploadState" v-if="loading" type="success" height="20px" :label=true style="margin: 0 32px;"></m-progressbar>
            <div v-if="!loading">
              <p>请选择要上传的本地文件</p>
              <span class="upload-img">
                <input type="file" multiple="multiple" @change="uploadFile">
                <span style="cursor: pointer">打开本地文件</span>
              </span>
            </div>
          </div>
          <div v-for="(file,index) in fileList" class="flex-row file">
            <label class="filename">{{file.fname}}</label>
            <i style="margin: 0 16px; cursor: pointer;" @click="deleteFile(file.id, index)">X</i>
            <m-input autoHeight hintText="请填写文件的说明（可选项）" fontsize=14 class="filenote" v-model="file.note"></m-input>
          </div>
        </div>
      </div>
      <div slot="footer" class="tools">
        <div class="btn-gray" @click="cancelAddFile">取消</div>
        <div class="btn-blue" style="width: 96px;" @click="sureAddFile" v-if="fileList.length">确认添加</div>
      </div>
    </m-modal>
  </span>
</template>

<script>
import API from '../api.js'
export default {
  name: 'Addfile',
  props: {
    open: {type: Boolean, default: false}
  },
  data () {
    return {
      loading: false,
      uploadState: 0,
      dialog: this.open,
      fileList: [] // 上传的文件列表
    }
  },
  watch: {
    open () {
      this.dialog = this.open
    }
  },
  methods: {
    close () {
      this.uploadState = 0
      this.dialog = false
      this.fileList = []
      this.$emit('close')
    },
    // 上传
    uploadFile (event) {
      let vm = this
      let formData = new window.FormData()
      formData.append('userid', this.$root.userid)
      for (let i = 0; i < event.target.files.length; i++) {
        formData.append('UploadForm[file][]', event.target.files[i])
      }
      // ajax uplaod iamges
      this.loading = true
      API.UpFile(formData, {
        timeout: 10000,  // 长传时间延长
        onUploadProgress: progressEvent => {
          // 上传进度控制
          // console.log('Event:', progressEvent.loaded)
          vm.uploadState = parseInt((progressEvent.loaded / progressEvent.total) * 100)
        }
      }).then(rep => {
        rep.map((item) => this.fileList.push({
          fname: item.fname,
          url: item.url,
          note: ''
        }))
        this.loading = false
      }).catch(err => {
        // this.loading = false
        console.log(err)
      })
    },
    // 确认上传
    sureAddFile () {
      if (!this.$route.params.mdid) {
        let fileArr = JSON.parse(window.sessionStorage.getItem('fileList'))
        if (!fileArr) fileArr = []
        this.fileList.map(item => fileArr.push(item))
        window.sessionStorage.setItem('fileList', JSON.stringify(fileArr))
        this.close()
        return
      }
      API.AddFileNote({userid: this.$root.userid, mdid: this.$route.params.mdid, data: JSON.stringify(this.fileList)}).then(rep => {
        this.toast({
          type: 'success',
          text: '上传文件及其备注成功',
          placement: 'top'
        })
        this.loading = false
        this.close()
      }).catch(err => {
        // this.loading = false
        console.log(err)
      })
    },
    // 取消上传
    cancelAddFile () {
      this.close()
    },
    // 删除文件
    deleteFile (fileid, index) {
      if (!this.$route.params.mdid) {
        this.fileList.splice(index, 1)
      }
    }
  }
}
</script>

<style scoped>
  .content{
    padding: 16px 32px 32px;
  }
  .type{
    padding-left: 24px;
    line-height: 32px;
    border: 1px solid rgba(0,0,0,.13);
    border-bottom: none;
    width: 112px;
  }
  .type-content{
    border: 1px solid rgba(0,0,0,.13);
    min-height: 260px;
  }
  .upload-content{
    margin-top: 96px;
  }
  .file{
    padding: 0 32px;
    margin: 10px 0;
  }
  .filename{
    width: 250px;
    overflow: hidden;
    text-overflow:ellipsis;
    white-space: nowrap
  }
  .filenote{
    flex: 1;
  }
  .tools{
    padding-right: 24px;
  }
</style>
