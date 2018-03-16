<template>
  <div id="profile">
    <div class="tool flex-row">
      <span @click="$router.go(-1)">返回</span>
      <span @click="delContact">删除联系人</span>
    </div>

    <div class="card flex-row info">
      <div class="left avatar">
        <span v-if="avatar.length < 10">{{ name.substr(0, 1) }}</span>
        <img v-else :src="'http://' + avatar" />
      </div>
      <div class="flex-col base">
        <h3>{{ name }}</h3>
        <p>{{ work }}</p>
        <div class="flex-row">
          <span>性别：{{ gender === '1' ? '男' : '女' }}</span>
          <span>职务/职称：{{ duty }}</span>
        </div>
        <div class="flex-row">
          <span>年龄：{{ birthday | dateToAge }}</span>
          <span>电话：{{ phone }}</span>
        </div>
        <div class="flex-row">
          <span>科室：{{ department }}</span>
          <span>邮箱：{{ email }}</span>
        </div>
      </div>
    </div>

    <h3>进行中的项目</h3>
    <div class="lists project">
      <div class="item" v-for="t in join_team" v-if="parseInt(t.type) === 1">
        <i class="iconfont icon-project"></i>
         {{ t.projectname }}
      </div>
    </div>

    <h3>已结束的项目</h3>
    <div class="lists project">
      <div class="item" v-for="t in join_team" v-if="parseInt(t.type) === 2">
        <i class="iconfont icon-project"></i>
         {{ t.projectname }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data () {
    return {
      id: this.$route.params.id,
      name: '',
      avatar: '',
      phone: '',
      gender: '',
      email: '',
      birthday: '',
      work: '',
      duty: '',
      department: '',
      join_team: []
    }
  },
  mounted () { this.getProfile() },
  methods: {
    getProfile () {
      this.$http.GetContactInfo({userid: this.$root.userid, touserid: this.id}).then(rep => {
        this.name = rep.s_username
        this.phone = rep.s_cellphone
        this.avatar = rep.s_avatar
        this.gender = rep.s_sex
        this.email = rep.s_userEmail
        this.birthday = rep.s_mybirthday
        this.work = rep.s_workunti
        this.duty = rep.s_joblevel
        this.department = rep.s_department
        this.join_team = rep.project
      }).catch(err => console.log(err))
    },
    delContact () {
      let vm = this
      this.confirm({
        title: '删除联系人',
        message: '是否要删除该联系人',
        onConfirm () {
          vm.$http.DelContacts({userid: vm.$root.userid, touserid: vm.id}).then(rep => {
            vm.toast({text: '删除成功'})
            vm.$router.go(-1)
          }).catch(err => console.log(err))
        }
      })
    }
  }
}
</script>

<style scoped>
#profile {
  width: 75%;
  max-width: 768px;
  margin: 30px auto;
  margin-top: 90px;
}

.tool {
  justify-content: space-between;
}

.tool span {
  border: 1px solid #ddd;
  padding: 4px 16px;
  border-radius: 2px;
  cursor: pointer;
  background: #fff;
}

.tool span:hover {
  background: #eee;
}

.info {
  height: 200px;
  margin: 30px 0;
  background: #fff;
  padding: 20px 35px;
}

.info .avatar {
  width: 200px;
  height: 200px;
  margin-right: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar span {
  display: block;
  width: 180px;
  height: 180px;
  line-height: 180px;
  background: #8fc0ef;
  color: #fff;
  text-align: center;
  font-size: 80px;
}

.avatar img {
  display: inline-block;
  width: 180px;
  height: 180px;
}

.info .base{
  align-items: flex-start;
  flex: 1;
}

.info .base h3 {
  font-size: 28px;
  margin-bottom: 8px;
}

.info .base p {
  font-size: 16px;
  margin-bottom: 12px;
}

.base .flex-row {
  width: 100%;
  margin-top: 12px;
  font-size: 14px;
}

.base .flex-row span, .base .flex-row {
  flex: 1;
}

#profile>h3 {
  font-size: 18px;
  margin-bottom: 8px;
  margin-top: 26px;
}

.project {
  background: #fff;
}

.project .item {
  height: 46px;
  line-height: 46px;
  font-size: 16px;
  padding: 0 16px;
}

.icon-project {
  color: #777;
  margin-right: 12px;
  font-size: 15px;
}
</style>

