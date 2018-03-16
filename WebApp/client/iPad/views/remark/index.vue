<template>
  <div class="section text-note">
    <p class="title">备注说明</p>
    <div style="height: 50px" v-if="!textNoteList.length"></div>
    <div class="text-note-item" v-for="item, index in textNoteList">
      <p class="text-note-content">{{ item.remark }}</p>
      <p class="text-note-log flex-row">
        <span>{{ item.s_username }} {{ item.createtime }}</span>
        <i class="iconfont icon-delete" @click="deleteTextNote(item.id, index)"></i>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Remark',
  props: {
    remarkNum: Number
  },
  data () {
    return {
      textNoteList: []
    }
  },
  mounted () { this.getNotes() },
  methods: {
    getNotes () {
      if (!this.$route.params.mdid) {
        this.textNoteList = []
        let arr = JSON.parse(window.sessionStorage.getItem('textNote'))
        if (!arr) arr = []
        arr.map(item => this.textNoteList.push({
          remark: item,
          s_username: this.$root.username,
          createtime: ''
        }))
        this.handleChange()
        return
      }
      this.$http.GetNoteList({mdid: this.$route.params.mdid}).then(rep => {
        this.textNoteList = rep
        this.handleChange()
      }).catch(err => console.log(err))
    },
    // 删除备注
    deleteTextNote (noteid, index) {
      let vm = this
      this.confirm({
        title: '删除备注',
        message: '确定删除此备注？',
        onConfirm () {
          vm.$http.DeleteTextNote({userid: vm.$root.userid, id: noteid}).then((response) => {
            vm.textNoteList.splice(index, 1)
            vm.toast({
              type: 'success',
              text: '删除备注成功！',
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
      this.$emit('update:remarkNum', this.textNoteList.length)
    }
  }
}
</script>

<style>
.text-note {
  padding: 0;
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
</style>


