<template>
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
              <span>打开本地文件</span>
            </span>
          </div>
        </div>
        <div v-for="(file,index) in fileList" class="flex-row file">
          <label class="filename">{{file.fname}}</label>
          <i style="margin: 0 8px; cursor: pointer;" @click="deleteFile(file.id, index)">X</i>
          <m-input hintText="请填写文件的说明（可选项）" fontSize="14" class="filenote" v-model="file.note"></m-input>
        </div>
      </div>
    </div>
    <div slot="footer" class="tools">
      <m-button @click="cancelAddFile">取消</m-button>
      <m-button class="add" type="blue" @click="sureAddFile" v-if="fileList.length">确认添加</m-button>
    </div>
  </m-modal>
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
      fileList: [], // 上传的文件列表
      fileNote: [] // 文件及文件说明列表
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
      this.fileNote = []
      this.$emit('close')
    },
    // 上传
    uploadFile (event) {
      let vm = this
      let formData = new window.FormData()
      formData.append('userid', this.$root.userid)
      formData.append('patientid', this.$route.params.mdid)
      for (let i = 0; i < event.target.files.length; i++) {
        formData.append('UploadForm[file][]', event.target.files[i])
      }
      // ajax uplaod iamges
      this.loading = true
      this.$http.UpFile(formData, {
        timeout: 10000,  // 长传时间延长
        onUploadProgress: progressEvent => {
          // 上传进度控制
          console.log('Event:', progressEvent.loaded)
          vm.uploadState = parseInt((progressEvent.loaded / progressEvent.total) * 100)
        }
      }).then(rep => {
        rep.map(i => {
          this.fileList.push({url: i.url, fname: i.fname, note: ''})
        })
        this.loading = false
      }).catch(err => {
        this.loading = false
        console.log(err)
      })
    },
    // 确认上传
    sureAddFile () {
      if (!this.$route.params.recordid) return
      API.SetFile({userid: this.$root.userid, data: JSON.stringify(this.fileList), recordid: this.$route.params.recordid}).then(rep => {
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
      if (this.fileList.length) {
        for (let i in this.fileList) {
          this.deleteFile(this.fileList[i].id)
        }
      }
      this.close()
    },
    // 删除文件
    deleteFile (fileid, index) {
      API.DeleteFile({userid: this.$root.userid, id: fileid}).then((response) => {
        this.fileList.splice(index, 1)
      }).catch((err) => {
        console.log(err)
      })
    }
  }
}
</script>

<style scoped>
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
  .upload-img {
    padding: 8px 15px;
    background: #458df1;
    color: #fff;
    display: inline-block;
    position: relative;
    cursor: pointer;
  }
  .upload-img input {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 2;
    opacity: 0;
    width: 100%;
    height: 100%;
  }
  .file{
    padding: 0 32px;
    margin-bottom: 10px;
  }
  .filename{
    width: 250px;
  }
  .filenote{
    width: 216px;
  }
  .tools{
    padding-right: 24px;
  }

  .tools .add {
    margin-left: 12px;
  }
</style>
