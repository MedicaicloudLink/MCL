<template>
  <div class="multiple radio flex-col">
    <div class="flex-col option" v-for="(answer, answerid) in val" v-if="val.length > 0">
      <div v-if="val[answerid].img.length > 10">
        <img :src="'http://' + val[answerid].img" alt="option" class="img-option">
        <span @click="deleteImg(answerid)" v-show="isactive"><i class="iconfont icon-delete2"></i></span>
      </div>
      <div class="flex-row">
        <span class="radio-icon"></span>
        <m-input autoHeight hintText="选项描述" v-model="val[answerid].text" @input="handleInput(arguments[0])" full class="answer" :preview="!isactive"></m-input>
        <i class="iconfont icon-shuoming" @click="val[answerid].other = !val[answerid].other" v-show="isactive"></i>
        <i class="iconfont icon-tupian" @click="openUpdateImg(answerid)" v-show="isactive"></i>
        <span class="delete-icon" @click="deleteAnswer(answerid)" v-show="isactive">×</span>
        <span v-if="isactive && jumpState" class="jump-chioce">
          <i class="iconfont icon-tiaozhuan" @click="choiceJump(answerid)"></i>
        </span>
      </div>
      <span class="jump-span" v-for="item in useQAList" v-if="item.id === answer.next">跳转：{{ item.title }}</span>
      <span class="hint-text" v-if="val[answerid].other" :preview="!isactive">其它</span>
    </div>

    <span class="add-option" @click="addAnswers" v-show="isactive"><span class="radio-icon"></span><span class="c-gray">点击</span>添加新选项</span>

    <div class="flex-row jump-option" v-show="isactive">
      <span>选项跳转：</span>
      <m-select v-model="jumpState" style="width: 100px;" @input="handleChange">
        <m-option value="true">是</m-option>
        <m-option value="false">否</m-option>
      </m-select>
    </div>

    <m-modal :open="jumpDialog" title="选择跳转问题" @close="jumpDialog = false">
      <ul class="jump-lists">
        <li v-for="item, index in useQAList" :value="item.id" @click="jumpQuestion('next', item.id)">{{ item.title }}</li>
        <li @click="jumpQuestion('end')">结束所有问题</li>
      </ul>
      <div slot="footer">
        <m-button type="gray" @click="jumpDialog = false">取消</m-button>
      </div>
    </m-modal>

    <addimg :open="update_img" @imgurl="getImgurl" @close="update_img = false"></addimg>
  </div>
</template>

<script>
export default {
  name: 'MultipleChoice',
  props: {
    value: Array,
    sections: Array,
    isjump: Boolean,
    id: String,
    isactive: Boolean
  },
  data () {
    return {
      val: this.value,
      update_img: false,
      upload_id: '',
      jumpState: this.isjump,
      jumpDialog: false,
      jumpId: ''
    }
  },
  mounted () {
    this.init()
    this.jumpState = this.isjump
  },
  watch: {
    isjump () {
      this.jumpState = this.isjump
    },
    jumpState () {
      if (this.jumpState === false) {
        for (let i in this.val) {
          this.val[i].next = ''
        }
      }
    },
    value () { this.init() }
  },
  computed: {
    useQAList: function () {
      if (this.sections.length < 2 || this.sections[this.sections.length - 1].id === this.id) return ['']
      let index = 0
      for (let sid in this.sections) {
        if (this.sections[sid].id === this.id) {
          index = sid
          break
        }
      }
      return this.sections.slice(parseInt(index) + 1)
    }
  },
  methods: {
    init () {
      if (typeof this.value[0].other === 'boolean') {
        this.val = this.value
        return
      }

      // 处理添加其他属性
      let option = JSON.parse(JSON.stringify(this.value))
      for (const i in option) {
        option[i].other = false
      }
      this.val = []
      option.map(i => this.val.push(i))
      this.handleChange()
    },
    openUpdateImg (id) {
      this.update_img = true
      this.upload_id = id
    },
    getImgurl (img) {
      console.log(img)
      this.val[this.upload_id].img = img
      this.update_img = false
      this.handleChange()
    },
    choiceJump (index) {
      this.jumpDialog = true
      this.jumpId = index
    },
    jumpQuestion (type, id) {
      if (type === 'end') {
        this.val[this.jumpId].next = 'end_form_submit'
      } else {
        this.val[this.jumpId].next = id
      }
      this.jumpDialog = false
      this.jumpId = ''
    },
    addAnswers () {
      this.val.push({text: '', img: '', next: '', other: false})
      // 渲染更新后focus最后一个input
      this.$nextTick(() => {
        const optionsEl = this.$el.getElementsByClassName('option')
        optionsEl[optionsEl.length - 1].getElementsByTagName('textarea')[0].focus()
      })
      this.handleChange()
    },
    deleteAnswer (answerid) {
      if (this.val.length > 1) {
        this.val.splice(answerid, 1)
      }
      this.handleChange()
    },
    deleteImg (answerid) {
      this.val[answerid].img = ''
      this.handleChange()
    },
    handleInput (val) {
      this.handleChange()
    },
    handleChange () {
      this.$emit('update:isjump', this.jumpState)
      this.$emit('update:value', this.val)
    }
  }
}
</script>

<style scoped>
.multiple>.option {
  margin-bottom: 12px;
}

.multiple>.option>.flex-row {
  align-items: flex-start;
}

.answer {
  flex: 1;
}

.next-choice {
  width: 160px;
}

.radio-icon {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid #bdbdbd;
  border-radius: 50%;
  margin-right: 12px;
  margin-top: 4px;
}

.img-option {
  margin-left: 30px;
  display: inline-block;
  width: auto;
  height: auto;
  box-shadow: 0 1px 1px 0 rgba(0,0,0,0.14), 0 2px 1px -1px rgba(0,0,0,0.12), 0 1px 3px 0 rgba(0,0,0,0.2);
  max-width: 180px;
  max-height: 120px;
}

i.icon-tupian, i.icon-shuoming {
  display: inline-block;
  text-align: center;
  padding: 7px 4px 0 12px;
  font-size: 20px;
  color: #555;
  cursor: pointer;
}

.delete-icon {
  display: inline-block;
  width: 32px;
  font-size: 23px;
  text-align: center;
  cursor: pointer;
}

.jump-option {
  margin-top: 20px;
}

.jump-option span{
  color: #777;
  margin-right: 16px;
}

.jump-lists li {
  line-height: 1.8;
  cursor: pointer;
}

.jump-chioce {
  padding-top: 7px;
  text-align: center;
}

.jump-chioce i {
  border-radius: 50%;
  color: #666;
  font-size: 19px;
  cursor: pointer;
}

.jump-lists li:hover, .jump-lists li:active {
  background: #468df1;
  color: #fff;
}

.jump-span {
  margin-left: 32px;
  color: #777;
}

</style>

