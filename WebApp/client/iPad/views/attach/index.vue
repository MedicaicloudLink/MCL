<template>
  <div class="section text-note">
    <p class="title">文档附件</p>
    <div style="height: 50px" v-if="!fileList.length"></div>
    <div class="text-note-item" v-for="item, index in fileList">
      <p class="text-note-content  flex-row">
        <span class="filename">{{ item.name }}</span>
        <span style="color: rgba(0,0,0,.54);">{{ item.remark }}</span>
      </p>
      <p class="text-note-log flex-row">
        <span>{{ item.s_username }} {{ item.createtime }}</span>
        <i class="iconfont icon-delete" @click="deleteFile(item.id, index)"></i>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AttachFile',
  props: {
    attachFileNum: Number
  },
  data () {
    return {
      fileList: []
    }
  },
  mounted () { this.getAttachFiles() },
  methods: {
    getAttachFiles () {
      if (!this.$route.params.mdid) {
        this.fileList = []
        let fileArr = JSON.parse(window.sessionStorage.getItem('fileList'))
        if (!fileArr) fileArr = []
        fileArr.map(item => this.fileList.push({
          name: item.fname,
          remark: item.note,
          s_username: '',
          createtime: ''
        }))
        this.handleChange()
        return
      }
      this.$http.GetFileList({patientid: this.$route.params.mdid}).then((response) => {
        this.fileList = response
        this.handleChange()
      }).catch(err => console.log(err))
    },
    // 删除文档附件
    deleteFile (noteid, index) {
      let vm = this
      this.confirm({
        title: '删除文档附件',
        message: '确定删除此文档附件？',
        onConfirm () {
          if (!vm.$route.params.mdid) {
            vm.fileList.splice(index, 1)
            let fileArr = JSON.parse(window.sessionStorage.getItem('fileList'))
            fileArr.splice(index, 1)
            window.sessionStorage.setItem('fileList', JSON.stringify(fileArr))
            vm.handleChange()
            console.log(JSON.parse(window.sessionStorage.getItem('fileList')))
            return
          }
          vm.$http.DeleteFile({userid: vm.$root.userid, id: noteid}).then((response) => {
            vm.fileList.splice(index, 1)
            vm.toast({
              type: 'success',
              text: '删除文档附件成功！',
              placement: 'top'
            })
            vm.handleChange()
          }).catch(() => {
            vm.toast({
              type: 'error',
              text: '网络异常，删除失败，重新删除！',
              placement: 'top'
            })
          })
        }
      })
    },
    handleChange () {
      this.$emit('update:attachFileNum', this.fileList.length)
    }
  }
}
</script>

<style>
.text-note {
  background: #fff;
  margin-top: 30px;
}
.text-note .title{
  font-size: 20px;
  height: 60px;
  line-height: 58px;
  padding-left: 32px;
  border-bottom: 2px #ddd solid;
}
.text-note .text-note-item {
  padding: 24px 32px 16px;
  border-bottom: 1px #ddd solid;
}
.text-note-item .text-note-content {
  margin-bottom: 20px;
  color: rgba(0,0,0,.87);
}
.text-note-item .text-note-log {
  color: rgba(0,0,0,.26);
  font-size: 12px;
  justify-content: space-between;
}
.filename {
  width: 250px;
  margin-right: 20px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
