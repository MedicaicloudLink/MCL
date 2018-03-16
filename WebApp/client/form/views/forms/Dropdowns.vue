<template>
  <div class="dropdown flex-col">
    <div class="flex-col option" v-for="(answer, answerid) in val">
      <div class="flex-row">
        <span class="dropdown-icon">{{ answerid + 1 }}.</span>
        <m-input autoHeight hintText="选项描述" v-model="val[answerid].text" @input="handleInput(arguments[0])" full :preview="!isactive"></m-input>
        <span class="delete-icon" @click="deleteAnswer(answerid)" v-show="isactive">×</span>
        <span v-if="isactive && jumpState" class="jump-chioce">
          <i class="iconfont icon-tiaozhuan" @click="choiceJump(answerid)"></i>
        </span>
      </div>
      <span class="jump-span" v-for="item in useQAList" v-if="item.id === answer.next">跳转：{{ item.title }}</span>
    </div>

    <span class="add-option" @click="addAnswers" v-show="isactive"><span class="dropdown-icon">{{ val.length + 1 }}.</span><span class="c-gray">点击</span>添加新选项</span>

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

  </div>
</template>

<script>
export default {
  name: 'Dropdowns',
  props: {
    value: Array,
    sections: Array,
    isjump: Boolean,
    id: String,
    isactive: Boolean
  },
  data () {
    return {
      val: [
        {text: '', next: ''}
      ],
      jumpState: this.isjump,
      jumpDialog: false,
      jumpId: ''
    }
  },
  mounted () {
    this.val = this.value
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
    value () {
      this.val = this.value
    }
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
      this.val.push({text: '', next: ''})
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
.dropdown-icon {
  display: inline-block;
  margin-right: 12px;
  font-size: 16px;
  color: #aaa;
  margin-top: 4px;
}

.dropdown>.option {
  margin-bottom: 12px;
}

.dropdown>.option>.flex-row {
  align-items: flex-start;
}

.delete-icon {
  display: inline-block;
  width: 32px;
  font-size: 23px;
  text-align: center;
  cursor: pointer;
}

.next-option {
  margin-left: 12px;
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

