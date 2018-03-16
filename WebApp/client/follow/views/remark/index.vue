<template>
  <div class="remarks">
    <div class="state">备注：{{ remarks.length }},附件：{{ files.length }}</div>
    <div class="all" :style="{paddingBottom: opreate ? '40px' : '0px'}">
      <div class="list" v-for="i in all">
        <p>
          <span class="name">{{ i.s_username }}：</span>
          <span v-if="i.url">
            上传
            <a :href="'http://' + i.url" target="_blank">{{ i.name }}</a>
            {{ i.remark }}
          </span>
          <span v-else>{{ i.remark }}</span>
        </p>
        <div class="flex-row space">
          <span class="time">{{ i.createtime | formatDate }}</span>
          <span v-if="opreate">
            <i v-if="i.url" class="iconfont icon-delete" @click="del(i.id, 'file')"></i>
            <i v-else class="iconfont icon-delete" @click="del(i.id, 'remark')"></i>
          </span>
        </div>
      </div>
    </div>

    <div class="cret flex-row" v-if="opreate">
      <i class="iconfont icon-upload" @click="fileModel = true"></i>
      <m-input hintText="备注" autoHeight full v-model="text"></m-input>
      <div class="add"><m-button type="blue" @click="add">添加</m-button></div>
    </div>

    <addfile :open="fileModel" @close="fileModel = false"></addfile>
  </div> 
</template>

<script>
export default {
  name: 'Remarks',
  props: { recordid: String, opreate: { type: Boolean, default: true } },
  data () {
    return {
      remarks: [],
      fileModel: false,
      files: [],
      text: '',
      url: {}
    }
  },
  watch: {
    fileModel (val) {
      if (!val) this.getFilesList()
    }
  },
  computed: {
    all () {
      let list = []
      this.remarks.map(i => list.push(i))
      this.files.map(i => list.push(i))
      list.sort((a, b) => {
        return (b.createtime > a.createtime) ? 1 : ((a.createtime > b.createtime) ? -1 : 0)
      })

      return list
    }
  },
  mounted () {
    this.getList()
    this.getFilesList()
  },
  methods: {
    getList () {
      this.$http.GetRemarks({recordid: this.recordid}).then(rep => {
        this.remarks = rep
      }).catch(err => console.log(err))
    },
    getFilesList () {
      this.$http.GetFiles({recordid: this.recordid}).then(rep => {
        this.files = rep
      }).catch(err => console.log(err))
    },
    add () {
      if (this.text.length <= 0) return
      this.$http.SetRemark({recordid: this.recordid, userid: this.$root.userid, remark: this.text}).then(rep => {
        this.text = ''
        this.getList()
      }).catch(err => this.toast({text: err}))
    },
    // 删除备注和附件
    del (id, type) {
      let vm = this
      this.confirm({
        title: type === 'remark' ? '删除备注' : '删除文件',
        message: '删除后不可恢复，是否确认删除？',
        onConfirm () {
          if (type === 'remark') vm.delRemark(id)
          if (type === 'file') vm.delFile(id)
        }
      })
    },
    delRemark (id) {
      this.$http.DelRemark({id: id, userid: this.$root.userid}).then(rep => {
        this.getList()
      }).catch(err => this.toast({text: err}))
    },
    delFile (id) {
      this.$http.DelFile({id: id, userid: this.$root.userid}).then(rep => {
        this.getFilesList()
      }).catch(err => this.toast({text: err}))
    }
  }
}
</script>

<style scoped>
.remarks {
  background: #fff;
  width: 320px;
  box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.2), 0 1px 5px 0 rgba(0,0,0,.12);
}

.remarks .list {
  font-size: 14px;
  color: #555;
}

.name {
  color: #458df1;
}

.time {
  text-align: right;
}

.state {
  margin-top: 12px;
  padding-left: 12px;
  border-bottom: 1px solid #eee;
  text-align: left;
  line-height: 40px;
}

.all {
  max-height: 300px;
  overflow-y: scroll;
  overflow-x: hidden;
}

.all a {
  color: #26b574;
  margin: 0 2px;
}

.all .list:hover {
  background: none;
}

.remarks:hover .all::-webkit-scrollbar-thumb {
  background: #ccc;
}

.all::-webkit-scrollbar-thumb {
  background: #fff;
}

.all::-webkit-scrollbar-track {
  background-color: #fff;
}

.cret {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #fafafa;
  border-top: 1px solid rgba(0,0,0,0.1);
  padding: 6px 0 2px;
}

.cret i.icon-upload {
  margin: 0 8px;
  color: #777;
  font-size: 19px;
  cursor: pointer;
}

.cret i.icon-upload:hover {
  color: #468df1;
}

.cret .add {
  width: 110px;
  margin-left: 8px;
}

@media screen and (max-height: 780px) {
  .all {
    max-height: 200px;
  }
}
</style>
