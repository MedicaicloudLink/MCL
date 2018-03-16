<template>
  <div class="record-bar">
    <div class="form-group flex-row" v-if="imgshow">
      <div class="question"><label>备注图片</label></div>
      <div class="flex-row content images">
        <div class="img-list center" v-for="(img, index) in images">
          <img :src="'http://' + img" alt="" class="image-btn" v-imgshow />
          <span @click="delect(index)">删除</span>
        </div>
        <div class="add-image-btn">
          <input :id="random" accept="image/jpeg, image/jpg, image/png" multiple="multiple" name="file" type="file" @change="readURL">
          <label :for="random"></label>
        </div>

      </div>
    </div>
    <div class="form-group flex-row" v-if="remarkshow">
      <div class="question"><label>备注</label></div>
      <div class="content remark flex-row">
        <textarea v-model="remark" @input="bubbling" placeholder="备注本条记录" rows="5"></textarea>
      </div>
    </div>
  </div>
</template>

<script>
  import { resizeMe, dataUrlTOBlod } from '../utils/tools.js'
  import API from '../api.js'

  export default {
    name: 'recordBar',
    props: {
      imgshow: {
        type: Boolean
      },
      remarkshow: {
        type: Boolean
      },
      imgs: {
        type: Array
      },
      remarktext: {
        type: String
      }
    },
    data () {
      return {
        random: window.parseInt(Math.random() * 1000000),
        images: [],
        remark: ''
      }
    },
    watch: {
      imgshow (val, oldVal) {
        if (val) this.images = []
      },
      remarkshow (val, oldVal) {
        if (val) this.remark = ''
      },
      imgs (val, oldVal) {
        this.images = this.imgs
      },
      images (val, oldVal) {
        val.length > 0 ? this.bubbling() : ''
      },
      remarktext (val, oldVal) {
        this.remark = val
      }
    },
    created () {
      this.images = this.imgs
      this.remark = this.remarktext
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
        // console.log(image)
        let formData = new window.FormData()
        let blob = dataUrlTOBlod(image)
        formData.append('UploadForm[file]', blob)
        // ajax uplaod iamges
        API.PutImage(formData).then(response => {
          window.localStorage.setItem('uploadImageUrl', response.ufileurl)
          this.images.push(response.ufileurl)
        }).catch(err => {
          window.alert('上传失败，重新上传' + err)
        })
      },
      delect (index) {
        this.images.splice(index, 1)
        this.$emit('putsever', {remark: this.remark, imgs: this.images})
      },
      bubbling () {
        this.$emit('putsever', {remark: this.remark, imgs: this.images})
      }
    }
  }
</script>

<style scoped>
  .form-group {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .form-group>.question {
    padding-top: 4px;
    line-height: 22px;
    width: 140px;
    padding-right: 30px;
  }

  .question>label {
    color: #777;
  }

  .content.images {
    flex: 1;
    flex-wrap: wrap;
  }

  .content textarea {
    outline: none;
    background: #fff;
    flex: 1;
    line-height: 1.3;
  }

  .bar {
    background: #fff;
    padding: 10px 0px;
  }

  .bar>i {
    font-size: 20px;
    color: #2eabc6;
  }

  .image-btn {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 8px 8px 0;
  }

  .img-list span{
    display: block;
    text-align: center;
    border: 1px solid gray;
    margin: 8px 15px;
    border-radius: 3px;
    font-size: 14px;
    line-height: 28px;
  }

  .add-image-btn label {
    display: inline-block;
    width: 80px;
    height: 80px;
    text-align: center;
    border: 1px solid #bfcbd9;
    margin: 0 8px 8px 0;
  }

  .add-image-btn input[type="file"] {
    position: absolute;
    left: -9999px;
  }

  .add-image-btn label::before {
    content: '+';
    line-height: 70px;
    font-size: 30px;
    color: #999;
  }

  .remark {
    flex: 1;
  }
</style>