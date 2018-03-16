<template>
<div id="home">
  <div id="contact">
    <div class="table">

      <div class="table-tool flex-row space" v-if="checkList.length > 0">
        <div class="all-check" @click="allChecked">
          <m-checkbox class="all" v-model="allCheck">全选</m-checkbox>
        </div>
        <span class="btn" @click="delContact('all')">删除</span>
      </div>
      <div v-else class="table-tool flex-row space">
        <div class="flex-row">
          <router-link class="btn" :to="{name: 'contacts'}">添加联系人</router-link>
        </div>

        <div class="seach flex-row">
          <input type="text" placeholder="搜索" v-model="searchText" @change="search(2)">
          <i class="iconfont icon-search" @click="search(2)"></i>
        </div>
      </div>

      <div class="table-tool flex-row filter" v-if="checkList.length === 0">
        <span :class="{active: filter === ''}" @click="search(1, 'all')">全部</span>
        <span :class="{active: filter === String.fromCharCode(n+64)}"
          v-for="n in 26"
          @click="search(1, String.fromCharCode(n+64))">
          {{ String.fromCharCode(n+64) }}
        </span>
      </div>
      <div class="table-header flex-row">
        <span class="w25">姓名</span>
        <span class="w25">电话</span>
        <span class="w35">单位</span>
        <span class="w15">操作</span>
      </div>

      <loading v-if="loading"></loading>

      <div v-else class="table-body">
        <div class="table-tr flex-row" v-for="p in list">
          <span class="w25 flex-row">
            <m-checkbox class="del-check" v-model="checkList" :nativeValue="p.touserid"></m-checkbox>
            <span class="go" @click="goProfile(p.touserid)">{{ p.s_username }}</span>
          </span>
          <span class="w25">{{ p.s_cellphone }}</span>
          <span class="w35">{{ p.s_workunti }}</span>
          <span class="w15 del" @click="delContact('one', p.touserid)">删除</span>
        </div>
      </div>

      <div class="page">
        <m-page :total="total" :current="page" :pagesize="30" @changepage="changePage"></m-page>
      </div>
    </div>
  </div>

</div>
</template>

<script>
import Mynav from './Header'
export default {
  name: 'Home',
  components: { Mynav },
  data () {
    return {
      loading: true,
      page: parseInt(this.$route.params.pagenum),
      total: 0,
      searchText: '',
      list: [],
      checkList: [],
      filter: '',
      allCheck: false
    }
  },
  watch: {
    '$route' (val) {
      this.checkList.splice(0, this.checkList.length)
      this.page = parseInt(this.$route.params.pagenum)
      this.getContacts()
    },
    checkList (val, oldVal) {
      if (this.list.length === val.length && val.length > 0) {
        this.allCheck = true
        return
      }

      this.allCheck = false
    }
  },
  mounted () { this.getContacts() },
  methods: {
    getContacts () {
      this.loading = true
      this.$http.GetContacts({userid: this.$root.userid, page: this.page}).then(rep => {
        this.list = rep.result
        this.total = parseInt(rep.count)
        this.loading = false
      }).catch(err => {
        this.loading = false
        console.log(err)
      })
    },
    goProfile (id) {
      this.$router.push({name: 'profile', params: {id: id}})
    },
    /* 全选或全不选 */
    allChecked () {
      this.checkList.splice(0, this.checkList.length)
      console.log(this.checkList)
      if (!this.allCheck) this.list.map(i => this.checkList.push(i.touserid))
    },
    search (type, value) {
      this.filter = ''
      this.loading = true
      if (parseInt(type) === 1) {
        this.filter = value
        this.searchText = ''
      }
      if (parseInt(type) === 1 && value === 'all') {
        this.filter = ''
        value = ''
        type = 2
      }
      if (parseInt(type) === 2) value = this.searchText

      this.checkList = []
      this.$http.SearchContact({userid: this.$root.userid, type: type, name: value, page: this.page}).then(rep => {
        if (rep.flag === 3 || rep.flag === 2) {
          this.list = []
          this.total = 0
        } else {
          this.list = rep.result
          this.total = parseInt(rep.count)
        }
        this.loading = false
      }).catch(err => {
        this.loading = false
        console.log(err)
      })
    },
    // 翻页 改变路由
    changePage () {
      this.$router.push({name: 'home', params: {pagenum: arguments[0]}})
    },
    delContact (type, id) {
      let vm = this
      if (type === 'all') {
        const touserid = this.checkList
        this.confirm({
          title: '删除联系人',
          message: '是否要删除选中的联系人',
          onConfirm () {
            vm.$http.DelContacts({userid: vm.$root.userid, touserid: touserid.toString()}).then(rep => {
              vm.toast({text: '删除成功'})
              vm.checkList = []
              vm.getContacts()
            }).catch(err => console.log(err))
          }
        })
      } else if (type === 'one') {
        this.confirm({
          title: '删除联系人',
          message: '是否要删除该联系人',
          onConfirm () {
            vm.$http.DelContacts({userid: vm.$root.userid, touserid: id}).then(rep => {
              vm.toast({text: '删除成功'})
              vm.checkList = []
              vm.getContacts()
            }).catch(err => console.log(err))
          }
        })
      }
    }
  }
}
</script>

<style scoped>
#contact {
  width: 1000px;
  margin: 96px auto 20px;
}

.table {
  background: #fff;
}

.table-body .del-check.checkbox-option, .all-check .all.checkbox-option {
  margin-bottom: 0;
}
.all-check .all.checkbox-option {
  padding-right: 20px;
}

.table-tool .btn {
  border: 1px solid #aaa;
  padding: 4px 16px;
  border-radius: 2px;
  cursor: pointer;
}

.table-tool .btn:hover {
  background: #eee;
}

.seach input {
  height: 30px;
  border: 1px solid #ddd;
  padding-left: 6px;
}

.seach i {
  height: 32px;
  line-height: 30px;
  width: 32px;
  text-align: center;
  border: 1px solid #ddd;
  border-left: 0;
  cursor: pointer;
}

span.go {
  cursor: pointer;
}

span.del {
  color: #468df1;
  cursor: pointer;
}

.filter span {
  margin-right: 8px;
  cursor: pointer;
  min-width: 20px;
  line-height: 1;
}

.filter span.active {
  color: #468df1;
  font-weight: 800;
  border-bottom: 1px solid #468df1;
}

.page {
    text-align: right;
    background: #fff;
    padding: 12px 12px 6px;
}

</style>