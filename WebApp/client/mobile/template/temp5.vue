<template>
  <div class="temp5">

    <div class="form-group flex-row">
      <div class="question">
        <label>冠脉CT检查前，在坚持服用的药物</label>
      </div>
      <div class="flex-col">
        <div class="item" v-for="(d, index) in drugs" :key="index">
          <label class="option-label">{{ index + 1 }}. 药品名称 <span class="del-btn" style="margin-left: 24px;" @click="delect(index)">删除此药物</span></label>
          <typeahead 
            placeholder="药品名称"
            v-model="d.drugname"
            @change="searchDrug(arguments[0], index)"
            :items="search_item"></typeahead>
          <label class="option-label">坚持服药时间</label>
          <div class="flex-row">
            <input type="number" v-model="d.drug_time" @input="putServer" />
            <span class="unit">月</span>
          </div>
          <label class="option-label">服药频次</label>
          <div class="radio">
            <input type="radio" value="超过25天" :id="'time1' + index" v-model="d.durg_month_time" @change="putServer">
            <label :for="'time1' + index">每个月超过25天</label>
          </div>
          <div class="radio">
            <input type="radio" value="15到25天" :id="'time2' + index" v-model="d.durg_month_time" @change="putServer">
            <label :for="'time2' + index">每个月15到25天</label>
          </div>
          <div class="radio">
            <input type="radio" value="小于15天" :id="'time3' + index" v-model="d.durg_month_time" @change="putServer">
            <label :for="'time3' + index">每个月小于15天</label>
          </div>
           
        </div>

        <span class="add-button" @click="add">＋点击添加另一个药物</span>
      </div>
    </div>
    
  </div>
</template>
  
<script>
import Typeahead from '../lib/Typeahead.vue'
import API from '../api.js'
export default {
  name: 'temp5',
  components: {
    Typeahead
  },
  props: {
    mdid: String,
    templateid: String,
    recordid: String,
    source: {
      type: Array
    }
  },
  created () {
    if (this.source.length > 0) {
      this.drugs = []
      this.source.map(n => {
        this.drugs.push(n)
      })
    }
  },
  data () {
    return {
      drugs: [
        {drugname: '', drug_time: '', durg_month_time: ''}
      ],
      search_item: []
    }
  },
  methods: {
    searchDrug (query, index) {
      this.drugs[index].drugname = query
      API.SearchDrugs({drugname: query}).then(response => {
        this.search_item = []
        response.map(n => this.search_item.push(n.u_drugname))
      }).catch(err => {
        console.log(err)
      })

      this.$emit('putsever', {sourceData: this.drugs, showData: this.changeData()})
    },
    add () {
      this.drugs.push({drugname: '', drug_time: '', durg_month_time: ''})
      this.$emit('putsever', {sourceData: this.drugs, showData: this.changeData()})
    },
    putServer () {
      this.$emit('putsever', {sourceData: this.drugs, showData: this.changeData()})
    },
    delect (index) {
      // 删除指定药品
      this.drugs.splice(index, 1)
      this.$emit('putsever', {sourceData: this.drugs, showData: this.changeData()})
    },
    // 显示数据
    changeData () {
      let showData = {}
      for (let d in this.drugs) {
        showData[(parseInt(d) + 1) + '. 药品'] = this.drugs[d].drugname + '，共服用 ' + this.drugs[d].drug_time + ' 月，每月' + this.drugs[d].durg_month_time
      }

      return showData
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  /*form design*/
  .form-group {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .form-group>.question {
    line-height: 32px;
    width: 140px;
    padding-right: 30px;
  }

  .question>label {
    color: #777;
  }

  .item {
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
  }

  .option-label {
    display: block;
    margin: 8px 0;
  }

  .option-label:first-child {
    margin-top: 0;
  }

  .option-label .del-btn {
    color: #ec6d20;
    text-decoration: underline;
    cursor: pointer;
  }

  .add-button {
    margin-top: 8px;
    color: #468df1;
    text-decoration: underline;
    cursor: pointer;
  }

</style>