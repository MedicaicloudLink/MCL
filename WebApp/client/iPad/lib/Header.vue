<template>
  <div class="breadcrumbs flex-col">
    <div class="flex-row">
      <router-link :to="$route.path" v-if="!$route.query.redirct">{{ $route.meta.title }}</router-link>
      <router-link :class="{active: patientName}" :to="'/project/'+projectInfo.u_projectID+'/workdata'" v-if="$route.query.redirct === 'workdata'">我的工作日志</router-link>
      <router-link :class="{active: patientName}" :to="'/project/'+projectInfo.u_projectID+'/savelist'" v-if="$route.query.redirct === 'savelist'">已保存的病例</router-link>
      <router-link :class="{active: patientName}" :to="'/project/'+projectInfo.u_projectID+'/commitlist'" v-if="$route.query.redirct === 'commitlist'">已提交的病例</router-link>
      <router-link :class="{active: patientName}" :to="'/project/'+projectInfo.u_projectID+'/backlist'" v-if="$route.query.redirct === 'backlist'">被退回的病例</router-link>
      <router-link :class="{active: patientName}" :to="'/project/'+projectInfo.u_projectID+'/search'" v-if="$route.query.redirct === 'search'">搜索病例</router-link>
      <span class="flex-row" v-if="$route.query.redirct"><img src="../assets/icon_svg/icon_next.svg" style="margin: 0 8px;"/>{{patientName}}</span>
    </div>
    <div v-if="$route.name !== 'workdata' && $route.name !== 'workdata-record'" style="color: rgba(0,0,0,.26); font-size: 12px;">统一中心或者小组内，病历记录将被共享</div>
  </div>
</template>