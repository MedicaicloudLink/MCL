<template>
<div id="home">
  <mynav class="header-fix"></mynav>

  <div class="form-list">
    <div class="list-header flex-row">
      <span style="flex: 1;"><router-link :to="{ name: 'neweditor', query: { id: 'new' }}" class="btn link">创建新表单</router-link></span>
      <span class="form-state" v-if="$root.formlist.length > 0">状态</span>
      <span class="operate" v-if="$root.formlist.length > 0">上次打开的时间</span>
    </div>
    <div class="lists" v-if="$root.formlist.length > 0">
      <div class="list flex-row" v-for="(f, index) in $root.formlist">
        <router-link :to="{name: 'editor', params: {formid: f.formid}}" class="flex-row"><i class="iconfont icon-wendang"></i><span>{{ f.name }}</span></router-link>
        <span class="form-state">{{ f.state === '2' ? '已发布' : '未发布' }}</span>
        <div class="operate">
          <span>{{ f.update_time | formatDate }}</span>
          <i class="iconfont icon-copy" @click="copyDialog(index)"></i>
          <i class="iconfont icon-delete2" @click="openDelect(index)"></i>
        </div>
        
      </div>
    </div>

    <div class="empty" v-else>
      <img src="../assets/empty.png" alt="medicayun">
    </div>
  </div>
  

  <m-modal :open="delect_state" title="删除表单" @close="delect_state = false">
    是否确定删除该表单？

    <div slot="footer">
      <m-button type="gray" @click="delect_state = false">取消</m-button>
      <m-button type="blue" @click="delForm">确定</m-button>
    </div>
  </m-modal>

  <m-modal :open="copy_dialog" title="复制表单" @close="copy_dialog = false">
    <div class="public_dialog" style="height: 50px;line-height: 3;">
      <p>复制新建一份此表单</p>
    </div>

    <div slot="footer">
      <m-button type="gray" @click="copy_dialog = false">取消</m-button>
      <m-button type="blue" @click="createForm">确认复制</m-button>
    </div>
  </m-modal>

</div>
</template>

<script>
import Mynav from './Header.vue'
export default {
  name: 'Home',
  components: { Mynav },
  data () {
    return {
      delect_state: false,
      delect_id: '',
      copy_id: '',
      copy_dialog: false
    }
  },
  mounted () {
    window.localStorage.removeItem('new_form_id')
    this.$root.GET_FORM_LIST()
  },
  methods: {
    openDelect (index) {
      this.delect_state = true
      this.delect_id = index
    },
    copyDialog (index) {
      this.copy_dialog = true
      this.copy_id = index
    },
    createForm () {
      let name = this.$root.formlist[this.copy_id].name
      let copyIndex = 1
      for (let i of this.$root.formlist) {
        if (i.name.substr(0, name.length) === name) {
          try {
            let behind = i.name.substr(name.length)
            let index = parseInt(behind.substr(3))
            copyIndex = copyIndex <= index ? index + 1 : copyIndex
          } catch (error) {
            console.log('name err')
          }
        }
      }
      // console.log(name + '[副本' + copyIndex + ']')

      name = name + '[副本' + copyIndex + ']'
      const formDate = this.$root.formlist[this.copy_id].sourcedata
      this.$http.createForm({userid: this.$root.userid, name: name, sourcedata: formDate}).then(rep => {
        this.toast({ text: '复制成功' })
        this.$root.GET_FORM_LIST()
        this.copy_dialog = false
      }).catch(err => {
        this.toast({text: err})
      })
    },
    delForm () {
      const formid = this.$root.formlist[this.delect_id].formid
      this.$http.delForm({formid: formid, userid: this.$root.userid}).then(rep => {
        this.$root.GET_FORM_LIST()
        this.delect_state = false
      }).catch(err => {
        this.delect_state = false
        console.log(err)
      })
    }
  }
}
</script>

<style>


.form-list {
  margin-top: 56px;
  width: 900px;
  margin: 76px auto 20px;
}

.header-fix {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100%;
}

.btn.link {
  display: inline-block;
  background: #468df1;
  padding: 8px 16px;
  color: #fff;
  font-size: 14px;
  font-weight: 400;
}

.lists {
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .2), 0 1px 5px 0 rgba(0, 0, 0, .12);
}

.list a {
  flex: 1;
  color: #333;
  line-height: 1;
  margin-right: 30px;
  overflow: hidden;
}

.list a span{
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.list {
  justify-content: space-between;
  align-items: center;
  background: #fff;
  padding: 14px 16px;
  font-size: 16px;
  color: rgba(0, 0, 0, .54);
  border-bottom: 1px solid #eee;
}

.list i.icon-wendang {
  color: #888;
  font-size: 18px;
  margin-right: 16px;
}

.list:hover {
  background: #eee;
}

.form-state {
  width: 142px;
  font-size: 14px;
}

.operate {
  width: 250px;
  font-size: 14px;
}

.list-header {
  font-weight: 600;
  background: rgba(0, 0, 0, 0);
  border: none;
  margin-right: 15px;
  margin-bottom: 12px;
}

.operate span {
  display: inline-block;
  width: 182px;
}

.icon-delete2, .icon-copy {
  display: inline-block;
  width: 28px;
  text-align: center;
  cursor: pointer;
}

.icon-copy {
  color: #999;
}

.empty {
  text-align: center;
  margin: 80px auto;
}

</style>