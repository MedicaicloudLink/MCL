<template>
  <div class="checkbox flex-col">
    <div class="option flex-col" v-for="(answer, answerid) in val">
      <div class="flex-row">
        <span class="checkbox-icon"></span>
        <m-input autoHeight hintText="选项描述" v-model="val[answerid].text" @input="handleInput(arguments[0], answerid)" style="flex:1;" full :preview="!isactive"></m-input>
        <i class="iconfont icon-shuoming" @click="handleOther(answerid)" v-show="isactive"></i>
        <span class="delete-icon" @click="deleteAnswer(answerid)" v-show="isactive">×</span>
      </div>

      <span class="hint-text" v-if="val[answerid].other" :preview="!isactive">其它</span>
    </div>

    <span class="add-option flex-row" @click="addAnswers" v-show="isactive"><span class="checkbox-icon"></span><span class="c-gray">点击</span>添加新选项</span>

  </div>
</template>

<script>
export default {
  name: 'Checkboxes',
  props: {
    value: Array,
    isactive: Boolean
  },
  data () {
    return {
      val: [{text: '', other: false}]
    }
  },
  mounted () {
    this.val = this.value
    this.changVersion()
  },
  watch: {
    value () {
      this.val = this.value
      this.changVersion()
    }
  },
  methods: {
    // 旧版本[''], 切换[{text: '', other: false}]
    changVersion () {
      if (typeof this.value[0] === 'string') {
        this.val = []
        this.value.map(v => this.val.push({text: v, other: false}))
        this.handleChange()
      }
    },
    addAnswers () {
      this.val.push({text: '', other: false})
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
    handleInput (val, id) {
      this.val[id] = {text: val, other: false}
      this.handleChange()
    },
    handleOther (id) {
      this.val[id].other = !this.val[id].other
      this.handleChange()
    },
    handleChange () {
      this.$emit('update:value', this.val)
    }
  }
}
</script>

<style scoped>
.checkbox .option {
  margin-bottom: 12px;
  align-items: flex-start;
}

.checkbox .option>.flex-row {
  align-items: flex-start;
  width: 100%;
}

i.icon-shuoming {
  display: inline-block;
  text-align: center;
  padding: 7px 4px 0 12px;
  font-size: 20px;
  color: #555;
  cursor: pointer;
}

.checkbox-icon {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid #bdbdbd;
  border-radius: 2px;
  margin-right: 12px;
  margin-top: 4px;
}

.delete-icon {
  display: inline-block;
  width: 32px;
  font-size: 23px;
  text-align: center;
  cursor: pointer;
}

</style>