<template>
  <div class="table flex-row">
    <div class="row flex-col">
      <div class="flex-row" v-for="(r, rid) in row">
        <span class="dec">第{{ rid + 1 }}行</span>
        <m-input placeholder="选项描述" :value.sync="row[rid]" @input="handleInput" :preview="!isactive"></m-input>
        <span @click="deleteOption('row', rid)" v-show="isactive">×</span>
      </div>
      <span class="add-option" @click="addOption('row')" v-show="isactive">添加新的一行</span>
    </div>

    <div class="column flex-col">
      <div class="flex-row" v-for="(c, cid) in column">
        <span class="dec">第{{ cid + 1 }}列</span>
        <m-input placeholder="选项描述" :value.sync="column[cid]" @input="handleInput" :preview="!isactive"></m-input>
        <span @click="deleteOption('column', cid)" v-show="isactive">×</span>
      </div>
      <span class="add-option" @click="addOption('column')" v-show="isactive">添加新的一列</span>
    </div>
  </div>
</template>

<script>
import MInput from '../../lib/Input.vue'
export default {
  name: 'MTable',
  components: {
    MInput
  },
  props: {
    value: Object,
    isactive: Boolean
  },
  data () {
    return {
      row: [''],
      column: ['']
    }
  },
  mounted () {
    this.row = this.value.row
    this.column = this.value.column
  },
  watch: {
    value () {
      this.row = this.value.row
      this.column = this.value.column
    }
  },
  methods: {
    handleInput (e) {
      this.$emit('update:value', {row: this.row, column: this.column})
    },
    deleteOption (type, id) {
      if (type === 'row' && this.row.length > 1) this.row.splice(id, 1)
      if (type === 'column' && this.column.length > 1) this.column.splice(id, 1)
    },
    addOption (type) {
      if (type === 'row') this.row.push('')
      if (type === 'column') this.column.push('')
      // 渲染更新后focus最后一个input
      this.$nextTick(() => {
        const optionsEl = this.$el.getElementsByClassName(type)[0].getElementsByTagName('input')
        optionsEl[optionsEl.length - 1].focus()
      })
    }
  }
}
</script>

<style scoped>
.table.flex-row {
  align-items: flex-start;
}

.table .dec {
  font-size: 12px;
}

.flex-row {
  display: flex;
  flex-direction: row;
  flex: 1;
  justify-content: flex-start;
  align-items: center;
  align-content: flex-start;
  width: 100%;
}

.flex-col {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: flex-start;
  align-content: flex-start;
}

.row>div, .column>div {
  margin-bottom: 8px;
}

.row {
  margin-right: 20px;
}

.row .input-form, .column .input-form {
  flex: 1;
}
</style>


