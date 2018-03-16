<template>
    <div id="all-patients">
        <div class="tools">
          <div class="tools-left">
            <!--<button :class="{disabled: disabled}" @click="shareGroup">分享到组</button>-->
            <MButton type="blue" @click="allChecked">全选/取消</MButton>            
            <MButton :class="{disabled: disabled}" type="blue" @click="deletePatients">批量删除</MButton>
          </div>
          <!--分享到组对话框-->
          <!--<DialogBox v-if="shareGroupDialog">
            <h3 slot="header">分享到组</h3>
            <div slot="body" class="body">
              <div class="shareGroup" v-for="(item, index) in groupList">
                <div>
                  <input type="checkbox" v-model="checkedGroup" :id="item.u_groupid" :value="item.u_groupid" style="display: none">
                  <label v-text="item.u_groupname" :for="item.u_groupid" @click="addClassChecked"></label>
                </div>
              </div>
            </div>
            <div slot="footer" class="footer">
              <div class="button" :style="{background: shareButton ? '#20a0ff' : '#bbb'}" @click="addPatientGroup">分享</div>
              <div class="button" @click="shareGroupDialog = false">取消</div>
            </div>
          </DialogBox>-->
          <!--end-->
          <div class="tools-right">
            <span>共<i class="sum">{{ allPatientNum }}</i>个患者</span>
						<span class="l"><b style="color: red">1</b>/<i>{{ pageTotal }}</i></span>
            <a class="prev disabled l" href="javascript:;" @click="prevpage()">&lt;</a><a class="next l" href="javascript:;" @click="nextpage()">&gt;</a>
          </div>
        </div>
        <div class="body">
          <div class="show-container">
              <div class="fontBold">
                  <!--<div><span class="el-checkbox" :class="{ 'is-checked': allCheck }" @click="allChecked"><i></i></span><input type="checkbox" class="checkbox" style="visibility: hidden"/></div>-->
                  <input type="checkbox" class="el-checkbox" style="visibility: hidden"/>
                  <div class="patient-name">姓名</div>
                  <div class="patient-sex">性别</div>
                  <div class="patient-age">年龄</div>
                  <div class="patient-birthday">出生年月</div>
                  <div class="join-time">入组时间</div>
                  <div class="update-time">更新时间</div>
                  <div class="creater">创建人</div>
                  <div class="handle">操作</div>
              </div>
          </div>
          <div class="show-container"  v-for="(item,index) in currentPagePatiens" :key="item.u_MDID">
              <div class="recordSheet">
                  <div><span class="el-checkbox" :class="{ 'is-checked': checkList[index] }" @click="isChecked(index)"><i></i></span><input type="checkbox" class="checkbox" v-model="checkList"/></div>
                  <div class="patient-name"><router-link :to="'/home/projectid/'+ projectid + '/all_patients/patient_detail/'+item.u_MDID" v-text="item.u_patientname"></router-link></div>
                  <div class="patient-sex" v-text="item.u_gender == '' ? '' : item.u_gender == '1' ? '男' : '女'"></div>
                  <div class="patient-age"></div>
                  <div class="patient-birthday" v-text="item.u_birthday"></div>
                  <div class="join-time" v-text="item.u_jointime"></div>
                  <div class="update-time" v-text="item.u_createtime"></div>
                  <div class="creater" v-text="item.s_username"></div>
                  <div class="handle" @click="deletePatient(item.u_MDID)">删除</div>
              </div> 
          </div>
        </div>
        <div class="tools">
          <div class="tools-left">
            <!--<button :class="{disabled: disabled}" @click="shareGroup">分享到组</button>-->
            <MButton type="blue" @click="allChecked">全选/取消</MButton>                        
            <MButton :class="{disabled: disabled}" type="blue" @click="deletePatients">批量删除</MButton>
          </div>
        </div>
    </div>
</template>
<script>
  import $ from 'webpack-zepto'
  import DialogBox from '../components/dialog.vue'
  import MButton from '../components/button.vue'
  import API from '../api.js'
  export default {
    data () {
      return {
        projectid: '',
        shareGroupDialog: false,
        shareButton: false,
        disabled: true,
        groupList: [],
        allPatientNum: 0,
        currentPage: 1,
        pageTotal: 1,
        allCheck: false,
        checkedGroup: [],
        currentPagePatiens: [],
        checkList: [],
        checkedMDIDs: []
      }
    },
    components: {
      DialogBox,
      MButton
    },
    mounted () {
      this.loadPatientList(1)
      this.projectid = this.$route.params.id
      if (this.currentPage === this.pageTotal) {
        $('.tools-right a').eq(1).addClass('disabled')
      }
    },
    watch: {
      'checkedGroup': function (val) {
        if (val.length !== 0) {
          this.shareButton = true
        } else {
          this.shareButton = false
        }
      }
    },
    methods: {
      // 加载该项目患者列表
      loadPatientList (currentpagenum) {
        API.GetPatients({projectid: this.$route.params.id, pagenum: currentpagenum}).then((response) => {
          this.currentPagePatiens = response.patientList
          this.allPatientNum = response.allnum
          this.pageTotal = Math.ceil(this.allPatientNum / 30)
        }).catch((err) => {
          window.alert(err)
        })
      },
      // 上一页
      prevpage () {
        if (this.currentPage > 1) {
          this.currentPage -= 1
          this.loadPatientList(this.currentPage)
          $('.tools-right a').removeClass('disabled')
          if (this.currentPage === 1) {
            $('.tools-right a').eq(0).addClass('disabled')
          }
        }
      },
      // 下一页
      nextpage () {
        if (this.currentPage < this.pageTotal) {
          this.currentPage += 1
          this.loadPatientList(this.currentPage)
          $('.tools-right a').removeClass('disabled')
          if (this.currentPage === this.pageTotal) {
            $('.tools-right a').eq(1).addClass('disabled')
          }
        }
      },
      /* 全选或全不选 */
      allChecked () {
        this.allCheck = !this.allCheck
        if (this.allCheck) {
          for (let i = 0; i < this.currentPagePatiens.length; i++) {
            this.$set(this.checkList, i, true)
            this.disabled = false
          }
        } else {
          for (let i = 0; i < this.currentPagePatiens.length; i++) {
            this.$set(this.checkList, i, false)
            this.disabled = true
          }
        }
      },
      /* patient是否选中 */
      isChecked (index) {
        this.$set(this.checkList, index, !this.checkList[index])
        // 有被选中的patient  则可以编辑
        if (this.checkList.indexOf(true) > -1) {
          this.disabled = false
        } else {
          this.disabled = true
        }
      },
      /* 分享到组Dialog  获取组名 */
      shareGroup () {
        if (!this.disabled) {
          this.shareGroupDialog = true
          API.GetGroup({userid: window.sessionStorage.getItem('userid')}).then((response) => {
            this.groupList = response
          }).catch((err) => {
            window.alert(err)
          })
        }
      },
      /* 组是否选中的style */
      addClassChecked (event) {
        let e = window.event || event
        $(e.target).toggleClass('checked')
      },
      /* 添加patients 到 选中的组 */
      addPatientGroup () {
        this.shareGroupDialog = false
        this.checkedMDIDs = []
        for (let i = 0; i < this.checkList.length; i++) {
          if (this.checkList[i]) {
            this.checkedMDIDs.push(this.currentPagePatiens[i].u_MDID)
          }
          this.$set(this.checkList, i, false)
        }
        API.AddGroupPatient({userid: window.sessionStorage.getItem('userid'), groupid: this.checkedGroup.join(','), patients: this.checkedMDIDs.join(',')}).then((response) => {
          this.disabled = true
          this.checkedGroup = []
          this.checkedMDIDs = []
        }).catch((err) => {
          window.alert(err)
        })
      },
      /* 批量删除患者操作 */
      deletePatients () {
        if (this.disabled === false) {
          if (window.confirm('您确定要删除选定的患者吗？')) {
            this.checkedMDIDs = []
            for (let i = 0; i < this.checkList.length; i++) {
              if (this.checkList[i]) {
                this.checkedMDIDs.push(this.currentPagePatiens[i].u_MDID)
              }
              this.$set(this.checkList, i, false)
            }
            API.DeletePatients({userid: window.sessionStorage.getItem('userid'), mdid: this.checkedMDIDs.join(',')}).then((response) => {
              this.loadPatientList(this.currentPage)
              this.checkedMDIDs = []
              this.disabled = true
            }).catch((err) => {
              // error callback
              window.alert(err)
            })
          }
        }
      },
      /* 删除单个患者操作 */
      deletePatient (mdid) {
        API.DeletePatients({userid: window.sessionStorage.getItem('userid'), mdid: mdid}).then((response) => {
          this.loadPatientList(this.currentPage)
        }).catch((err) => {
          // error callback
          window.alert(err)
        })
      }
    }
  }
</script>
<style scoped>
  /* tools */
  .tools{
    display: flex;
    display: -webkit-flex;
    justify-content: space-between;
  }
  /* 批量删除按钮 不可选中状态 */
  .tools .tools-left .disabled{
    opacity: .2;
    cursor: not-allowed;
  }
  /* 翻页按钮 */
  .tools-right a{
    display: inline;
    border: 1px solid #eee;
    padding: 0 10px;
  }
  .tools-right a.disabled{
    background: #999;
    border: 1px solid #999;
    cursor: default;
  }
  
  /* patient 展示区域*/
  .body {
    margin: 10px 0 20px;
  }
  .show-container{
    border-bottom: 1px #ccc solid;
    width: 100%;
  }
  .recordSheet, .fontBold{
    display: table;
    text-align: center;
    padding: .5em 0;
  }
  .fontBold{
    font-weight: 700;
  }

  .show-container:first-child:hover{
    background: rgb(238, 238, 238);
  }
  .show-container:hover{
    background: #ccc;
  }
  
  input[type=checkbox].checkbox {
    opacity: 0;
    outline: none;
    position: absolute;
    left: -999px;
    margin: 0;
  }
  .el-checkbox{
    display: inline-block;
    position: relative;
    top: .2em;
    border: 1px solid #bfcbd9;
    border-radius: 4px;
    box-sizing: border-box;
    width: 18px;
    height: 18px;
    background-color: #fff;
    margin-left: .5em;
    z-index: 1;
    transition: border-color .25s cubic-bezier(.71,-.46,.29,1.46),background-color .25s cubic-bezier(.71,-.46,.29,1.46);
  }
  .el-checkbox i {
    box-sizing: content-box;
    display: inline-block;
    border: 2px solid #fff;
    border-left: 0;
    border-top: 0;
    height: 8px;
    left: 5px;
    position: absolute;
    top: 1px;
    transform: rotate(45deg);
    width: 4px;
    transition: transform .15s cubic-bezier(.71,-.46,.88,.6) .05s;
    transform-origin: center;
  }
  .el-checkbox.is-checked {
    background-color: #20a0ff;
    border-color: #0190fe;
  }

  .patient-name{
    display: table-cell;
    width: 8em;
  }
  .patient-name a{
    color: #20a0ff;
    text-decoration: underline;
  }
  .patient-sex{
    display: table-cell;
    width: 4em;
  }
  .patient-age{
    display: table-cell;
    width: 4em;
  }
  .patient-birthday{
    display: table-cell;
    width: 8em;
  }
  .join-time{
    display: table-cell;
    width: 10em;
  }
  .update-time{
    display: table-cell;
    width: 12em;
  }
  .creater{
    display: table-cell;
    width: 8em;
  }
  .handle{
    display: table-cell;
    width: 4em;
    cursor: pointer;
    color: #f00;
  }
  
  .fontBold .handle{
    color: #000
  }
  /*分享到组body dialog*/
  .shareGroup {
    display: inline-block;
  }
  .shareGroup label{
    display: inline-block;
    padding: .3em;
    border: 1px #ccc solid;
    border-radius: 5px;
    margin: .2em;
    cursor: pointer;
  }
  .shareGroup label.checked{
    background: #20a0ff;
    border: 1px #0190fe solid;
    color: #fff;
  }
  .footer{
    width: 100%;
    text-align: right;
  }
  /* .button{
    width: 4em;
    line-height: 2em;
    display: inline-block;
    background: #bbb;    
    text-align: center;
    font-size: .9em;
    border-radius: 3px;
    color: #fff;
    margin-left: 2em;
    cursor: pointer;
  } */
</style>