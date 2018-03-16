<template>
	<div id="project">
		<Navigation></Navigation>
    <!--左边导航栏-->
    <ul class="left-menu" :style="{height: leftHeight + 'px'}">
      <li class="flex">
        <router-link :to="'/project/'+projectid+'/project_home'">
          <img src="../assets/left_menu_svg/icon_home.svg" />
          <span>首页</span>
        </router-link>
      </li>
      <li>
        <router-link :to="'/project/'+projectid+'/patient_list'">
          <img src="../assets/left_menu_svg/icon_list.svg" />
          <span>患者病例</span>
        </router-link>
      </li>
      <li>
        <router-link :to="'/project/'+projectid+'/member_management'">
          <img src="../assets/left_menu_svg/icon_member.svg" />
          <span>成员管理</span>
        </router-link>
      </li>
      <li>
        <router-link :to="'/project/'+projectid+'/center_management'">
          <img src="../assets/left_menu_svg/icon_centre.svg" />
          <span>分中心管理</span>
        </router-link>
      </li>
      <li>
        <router-link :to="'/project/'+projectid+'/follow_plans'">
          <img src="../assets/left_menu_svg/icon_visit.svg" />
          <span>随访计划</span>
        </router-link>
      </li>
      <!--end-->
    </ul>
    <!--end-->
    <div class="main">
      <div class="flex-col">
        <!--面包屑导航条 当前位置-->
        <div class="breadcrumb flex-row">
          <a href="#/home" class="project-home"><i>{{projectName}}</i><img src="../assets/icon_svg/icon_next.svg" /></a>
          <a href="#"><i></i><img src="../assets/icon_svg/icon_next.svg" /></a>
          <a href="javascript:;"><i></i><img src="../assets/icon_svg/icon_next.svg" /></a>
        </div>
        <router-view class="show-area"></router-view>
      </div>
    </div>
	</div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  import $ from 'webpack-zepto'
  import API from '../api.js'
  export default {
    data () {
      return {
        projectid: this.$route.params.projectid,
        projectName: '',
        patientName: '',
        leftHeight: window.innerHeight - 56
      }
    },
    components: {
      Navigation
    },
    mounted () {
      // 项目详情
      API.GetProjectInfo({userid: this.$root.userid, projectid: this.$route.params.projectid}).then((response) => {
        this.projectName = response[0].u_projectName
        this.currentCatalogue()
      }).catch((err) => {
        console.log(err)
      })
      // 面包屑导航 的 patientname
      if (this.$route.params.MDID) {
        this.patientName = ''
        API.GetPatientBase({mdid: this.$route.params.MDID}).then((response) => {
          this.patientName = response[0].u_patientname
          this.currentCatalogue()
        }).catch((err) => {
          console.log(err)
        })
      }
    },
    watch: {
      '$route': function (val) {
        this.currentCatalogue()
        if (this.$route.params.MDID) {
          this.patientName = ''
          API.GetPatientBase({mdid: this.$route.params.MDID}).then((response) => {
            this.patientName = response[0].u_patientname
            this.currentCatalogue()
          }).catch((err) => {
            console.log(err)
          })
        }
      }
    },
    methods: {
      /* 当前目录位置 面包屑导航条 */
      currentCatalogue () {
        if (this.$route.path.indexOf('/patient_detail/') !== -1) {
          // 进入患者病历详情页
          $('.breadcrumb a:last-child').show().addClass('active').siblings().removeClass('active')
          $('.breadcrumb a:last-child').find('i').html(this.patientName)
          $('.breadcrumb a').eq(1).attr('href', '#' + this.$route.path.split('/patient_detail/')[0])
        } else {
          $('.breadcrumb a:last-child').hide()
          $('.breadcrumb a').eq(1).addClass('active').attr('href', '#' + this.$route.path)
        }
        setTimeout(function () {
          $('.breadcrumb a').eq(1).find('i').html($('.left-menu li').find('.router-link-active span').html())
        }, 200)
      }
    }
  }
</script>
<style scoped>
   /*左边导航栏*/
  .left-menu{
    width: 184px;
    background: #5d9cf3;
    position: fixed;
    top: 56px;
    left: 0;  
    padding-top: 32px;
    height: 500px;
  }
  .left-menu li{
    position: relative;
  }
  .left-menu li a{
    display: inline-block;
    color: #fff;
    height: 56px;
    width: 100%;
    display: flex;
    align-items: center;
  }
  .left-menu li a img{
    width: 16px;
    height: 16px;
    margin-left: 24px;
    margin-right: 16px;
  }
  .left-menu li a.router-link-active{
    background: rgba(0, 0, 0, .12);
    z-index: 1;
  }

  .main{
    margin-top: 56px;
    margin-left: 184px;
  }
  /*面包屑导航条*/
  .breadcrumb{
    background: #fff;
    height: 40px;
    align-items: center;
    padding-left: 32px;
    position: fixed;
    width: 100%;
    border-bottom: 2px solid rgba(0,0,0,.12);
    z-index: 99;
  }
  .breadcrumb a{
    color: rgba(0, 0, 0, .54);
    display: flex;
    align-items: center;
  }
  .breadcrumb .project-home i{
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .breadcrumb a img {
    margin: 0 4px;
  }
  .breadcrumb a.active{
    color: #e51c23;
  }
  .breadcrumb a.active img{
    display: none;
  }
  
  /*病例展示区域style*/
  .show-area{
    margin: 0 32px;
    margin-top: 40px;
  }
</style>