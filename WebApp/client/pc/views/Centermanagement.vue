<template>
	<div>
		<div class="btn-blue" style="width: 96px;margin-top: 32px;" @click="createCenterDialog = true">创建分中心</div>   
    <!--创建分中心对话框     -->
		<DialogBox v-if="createCenterDialog">
			<h3 slot="header">创建分中心</h3>
			<div slot="body" class="body">
				<div class="option">
          <label>分中心名称<span style="color: red">*</span></label>
          <input type="text" v-model="centerName"/>
				</div>
        <div class="option">
          <label >负责人<span style="color: red">*</span></label>
          <input type="text" v-model="centerLeader"/>
				</div>
				<div class="option">
          <label >联系电话<span style="color: red">*</span></label>
          <input type="text"  v-model="centerPhone"/>
				</div>
				<div class="option">
          <label >邮箱</label>
          <input type="text" v-model="centerEmail"/>
				</div>
        <div class="option">
          <label >地址</label>
          <input type="text" v-model="centerAdr"/>
				</div>
        <div class="option">
          <label >邮编</label>
          <input type="text" v-model="centerPostcode"/>
				</div>
        <div class="option">
          <label >中心编号<span style="color: red">*</span></label>
          <input type="text" v-model="centerNum"/>
				</div>
			</div>
			<div slot="footer">
        <div class="btn-blue" @click="createCenter">保存</div>
        <div class="btn-gray" @click="createCenterDialog = false">取消</div>
			</div>
		</DialogBox>
    <!--end-->
    <!--分中心列表-->
		<div class="card">
			<div class="card-header">分中心列表</div>
			<div class="table">
				<div class="table-tr table-header">
					<div class="w10">序号</div>
					<div class="w30">中心名称</div>
					<div class="w15">负责人</div>
					<div class="w15">中心人数</div>
					<div class="flex">操作</div>
				</div>
        <div class="table-body" :style="{height: tableHeight + 'px'}">
          <div style="padding-bottom: 10px" v-if="loading"><Loading></Loading></div>
          <div v-else class="table-tr" v-for="(center, index) in centerList">
            <div class="w10">{{ index + 1 }}</div>
            <div class="w30">{{ center.u_centername }}</div>
            <div class="w15">{{ center.u_centerlead }}</div>
            <div class="w15">{{ center.userarr.length }}</div>
            <div class="flex flex-row">
              <span class="blue mg-r-20" @click="showCurrentCenterInfo(center)">查看</span>
              <span class="red" @click="deleteCenter(center.u_centerID, index)">删除</span>
            </div>
          </div>
        </div>
			</div>
		</div>
    <!--end-->
    <!--分中心详情 及编辑对话框-->
    <DialogBox v-if="detailDialog">
      <div slot="header" class="flex-row" style="justify-content: space-between; align-items: center;padding-right: 32px;">
        <span>分中心信息</span>
        <div class="btn-gray" style="font-size: 14px;" v-if="!editCenterFlag" @click="editCenterFlag = true">编辑</div>
      </div>
      <div slot="body">
        <div class="option">
          <label>分中心名称<span style="color: red">*</span></label>
          <input type="text" v-model="editCenterInfo.u_centername" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}"/>
        </div>
        <div class="option">
          <label>负责人<span style="color: red">*</span></label>
          <input type="text" v-model="editCenterInfo.u_centerlead" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}"/>
        </div>
        <div class="option">
          <label>分中心电话<span style="color: red">*</span></label>
          <input type="text" v-model="editCenterInfo.u_centerphone" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
        </div>
        <div class="option">
          <label>邮箱</label>
          <input type="text" v-model="editCenterInfo.u_centeremail" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
        </div>
        <div class="option">
          <label>地址</label>
          <input type="text" v-model="editCenterInfo.u_centeradr" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
        </div>
        <div class="option">
          <label>邮编</label>
          <input type="text" v-model="editCenterInfo.u_centerzipcode" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
        </div>
        <div class="option">
          <label>中心编号<span style="color: red">*</span></label>
          <input type="text" v-model="editCenterInfo.u_centernum" :readonly="!editCenterFlag" :style="{border: editCenterFlag ? '1px #ccc solid' : 'none'}" />
        </div>
        <div v-if="editCenterFlag" style="text-align: right;margin-right: 32px;margin-bottom: 8px;">
          <div class="btn-blue" @click="sureEditCenter">保存</div>
          <div class="btn-gray" @click="cancelEditCenter">取消</div>
        </div>
        <div class="table">
          <div class="table-tr table-header" style="padding-right: 32px;">
            <div class="w10">ID</div>
            <div class="w20">姓名</div>
            <div class="w10">性别</div>
            <div class="w10">年龄</div>
            <div class="flex">任务</div>
          </div>
          <div class="empty-tr" v-if="!editCenterInfo.userarr.length"></div>
          <div class="table-tr" v-for="(user, index) in editCenterInfo.userarr">
            <div class="w10">{{ index + 1 }}</div>
            <div class="w20">{{ user.s_username }}</div>
            <div class="w10">{{ user.s_sex === '1' ? '男' : user.s_sex === '2' ? '女' : ''}}</div>
            <div class="w10">{{ user.s_mybirthday ? (date - Number(user.s_mybirthday.slice(0,4))) : '' }}</div>
            <div class="flex">
              <span>{{ user.task.note }}</span>
            </div>
          </div>
        </div>
      </div>
      <div slot="footer">
        <div class="btn-gray" @click="clooseDetail">关闭</div>
      </div> 
    </DialogBox>
    <!--end-->
	</div>
</template>
<script>
  import API from '../api.js'
  import DialogBox from '../lib/dialog.vue'
  import Loading from '../lib/loading.vue'
  export default {
    data () {
      return {
        loading: false,
        createCenterDialog: false,
        centerName: '',
        centerLeader: '',
        centerPhone: '',
        centerEmail: '',
        centerAdr: '',
        centerPostcode: '',
        centerNum: '',
        checkedCenterId: '',
        centerList: [],
        currentCenterInfo: {},
        editCenterInfo: {
          u_centerID: '',
          u_centername: '',
          u_centerlead: '',
          u_centerphone: '',
          u_centeremail: '',
          u_centerzipcode: '',
          u_centeradr: '',
          u_centernum: '',
          userarr: []
        },
        date: new Date().getFullYear(),
        editCenterFlag: false,
        detailDialog: false,
        tableHeight: 0
      }
    },
    components: {
      DialogBox,
      Loading
    },
    mounted () {
      this.getCenterList()
      const tableTop = this.$el.getElementsByClassName('table-body')[0].getBoundingClientRect().top
      this.tableHeight = window.innerHeight - tableTop - 30
    },
    computed: {
      required () {
        return this.centerName && this.centerLeader && this.centerPhone && this.centerNum
      },
      requiredEdit () {
        return this.editCenterInfo.u_centername && this.editCenterInfo.u_centerlead && this.editCenterInfo.u_centerphone && this.editCenterInfo.u_centernum
      }
    },
    methods: {
      // 获取项目分中心列表
      getCenterList () {
        this.loading = true
        API.GetProjectCenter({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
          this.centerList = response
          this.loading = false
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 创建分中心
      createCenter () {
        if (this.required) {
          API.CreateCenter({
            userid: this.$root.userid,
            projectid: this.$route.params.projectid,
            centername: this.centerName,
            centerlead: this.centerLeader,
            centerphone: this.centerPhone,
            centeremail: this.centerEmail,
            centeradr: this.centerAdr,
            centerzipcode: this.centerPostcode,
            centernum: this.centerNum
          }).then((response) => {
            this.createCenterDialog = false
            this.toast({
              type: 'success',
              text: '创建分中心成功！',
              placement: 'top'
            })
            this.centerName = ''
            this.centerLeader = ''
            this.centerPhone = ''
            this.centerEmail = ''
            this.centerAdr = ''
            this.centerPostcode = ''
            this.centerNum = ''
            this.getCenterList()
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '网络异常，请重新提交试试！',
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'error',
            text: '保存失败，*为必选项',
            placement: 'top'
          })
        }
      },
      // 展示当前项目分中心详情
      showCurrentCenterInfo (item) {
        this.detailDialog = true
        this.currentCenterInfo = item
        for (let i in this.currentCenterInfo) {
          this.editCenterInfo[i] = this.currentCenterInfo[i]
        }
      },
      // 确认编辑分中心
      sureEditCenter () {
        if (this.requiredEdit) {
          API.EditCenter({
            userid: window.sessionStorage.getItem('userid'),
            projectid: this.$route.params.projectid,
            centerid: this.editCenterInfo.u_centerID,
            centername: this.editCenterInfo.u_centername,
            centerlead: this.editCenterInfo.u_centerlead,
            centeradr: this.editCenterInfo.u_centeradr,
            centerphone: this.editCenterInfo.u_centerphone,
            centeremail: this.editCenterInfo.u_centeremail,
            centerzipcode: this.editCenterInfo.u_centerzipcode,
            centernum: this.editCenterInfo.u_centernum
          }).then((response) => {
            this.editCenterFlag = false
            this.toast({
              type: 'success',
              text: '编辑分中心成功！',
              placement: 'top'
            })
          }).catch(() => {
            this.toast({
              type: 'error',
              text: '网络异常，请重新保存试试！',
              placement: 'top'
            })
          })
        } else {
          this.toast({
            type: 'error',
            text: '保存失败，*为必填项！',
            placement: 'top'
          })
        }
      },
      // 取消编辑分中心
      cancelEditCenter () {
        this.editCenterFlag = false
        for (let i in this.currentCenterInfo) {
          this.editCenterInfo[i] = this.currentCenterInfo[i]
        }
      },
      /* 删除分中心 */
      deleteCenter (centerId, index) {
        let vm = this
        this.confirm({
          title: '删除分中心',
          message: '确定删除此分中心？',
          onConfirm () {
            API.DeleteCenter({userid: vm.$root.userid, projectid: vm.$route.params.projectid, centerid: centerId}).then((response) => {
              vm.toast({
                type: 'success',
                text: '删除分中心成功！',
                placement: 'top'
              })
              vm.centerList.splice(index, 1)
            }).catch(() => {
              vm.toast({
                type: 'error',
                text: '删除分中心失败！',
                placement: 'top'
              })
            })
          }
        })
      },
      clooseDetail () {
        this.detailDialog = false
        this.cancelEditCenter()
      }
    }
  }
</script>
<style scoped>
  /* 创建分中心对话框 */
  .option{
    height: 40px;
    align-items: center;
    margin-bottom: 16px; 
    display: flex;
    margin-right: 72px;
  }
  .option label{
    width: 120px;
    text-align: right;
    margin-right: 32px;
    color: rgba(0,0,0,.54);
  }
  .option input[type=text]{
    flex: 1;
  }
  .table-tr{
    padding: 0 32px;
  }
  .table-tr.table-header{
    padding-right: 40px;
  }
  .empty-tr {
    height: 40px;
  }
</style>