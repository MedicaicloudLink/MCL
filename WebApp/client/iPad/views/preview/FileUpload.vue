<template>
  <div class="upload">
    <div class="btn-blue" @click="upload_dialog = true">上传</div>
    <p>{{ file.filename }}</p>
    <img v-if="file.fileurl" :src="'http://' + file.fileurl"/>
    <m-upload :open="upload_dialog" @fileurl="getFileurl" @close="upload_dialog = false"></m-upload>
  </div>
</template>

<script>
// import API from '../../api.js'
export default {
  name: 'FileUpload',
  props: {
    answers: Object
  },
  data () {
    return {
      upload_dialog: false,
      file: {
        filename: '',
        fileurl: ''
      }
    }
  },
  mounted () {
    if (this.answers) {
      this.file.filename = this.answers.filename
      this.file.fileurl = this.answers.fileurl
    }
  },
  watch: {
    answers () {
      this.file.filename = this.answers.filename
      this.file.fileurl = this.answers.fileurl
    }
  },
  methods: {
    getFileurl (file) {
      if (file.fileurl.length < 10) return
      this.file.filename = file.filename
      this.file.fileurl = file.fileurl
      this.update_img = false
      this.$emit('input', this.file.filename + ',' + this.file.fileurl)
      this.$emit('update:answers', this.file)
    }
  }
}
</script>
<style scoped>
  img{
    width: 80px;
    height: 80px;
  }
</style>

