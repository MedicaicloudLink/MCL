<template>
  <ul class="timeline">
    <span class="start li">
      <div class="status">
        <h4>入组</h4>
        <span class="date">{{ start }}</span>
      </div>
    </span>
    <li class="li" :class="{complete: parseInt(h.status) === 1}" v-for="(h, index) in value">
      <div class="status" @click="openHistory(index)">
        <h4>{{ h.typename }}</h4>
        <span class="date">{{ h.updatetime | formatDate }}</span>
      </div>
    </li>

    <m-modal :open="dialog" closeBtn :title="title" @close="dialog = false">
      <div class="item" v-for="i in form">
        <h3 class="form center" v-if="i.title === 'FORM'">{{ i.answer }}</h3>
        <h3 class="section" v-else-if="i.title === 'SECTION'">{{ i.answer }}</h3>
        <div class="qa" v-else>
          <p class="title"><span></span> {{ i.title }}</p>
          <p class="answer">{{ i.answer !== '' ? i.answer : '[未填]'  }}</p>
        </div>
      </div>
    </m-modal>
  </ul>
</template>

<script>
export default {
  name: 'TimeLine',
  props: {
    value: Array,
    start: String
  },
  data () {
    return {
      dialog: false,
      title: '历史记录',
      form: []
    }
  },
  methods: {
    openHistory (index) {
      const form = this.value[index]
      const type = form.type
      this.title = form.typename
      this.dialog = true

      this.form = []
      if (type === 'input') this.getInput(form.u_MDID)
      if (type === 'follow') this.getFollow(form.recordid)
    },
    getInput (mdid) {
      this.$http.GetRegister({patientid: mdid}).then(rep => {
        this.form = JSON.parse(rep[0].patientdata)
      }).catch(err => console.log(err))
    },
    getFollow (recordid) {
      this.$http.GetFollowData({recordid: recordid}).then(rep => {
        this.form = JSON.parse(rep[0].patientdata)
      }).catch(err => console.log(err))
    }
  }
}
</script>


<style scoped>
.timeline {
  list-style-type: none;
  display: flex;
  align-items: flex-start;
  line-height: 1.3;
  /* justify-content: center; */
}

.li {
  position: relative;
  transition: all 200ms ease-in;
  margin-top: 40px;
  padding: 0 40px;
  justify-content: center;
  border-top: 2px solid #D6DCE0;
  transition: all 200ms ease-in;
}

.li:before {
  content: "";
  width: 25px;
  height: 25px;
  background-color: white;
  border-radius: 25px;
  border: 1px solid #ddd;
  position: absolute;
  top: -13px;
  left: 42%;
  transition: all 200ms ease-in;
  z-index: 1;
}

.li:first-child {
  padding-left: 0;
}

.li:first-child::before {
  left: 0%;
}

.li:first-child .status {
  padding-left: 0;
  text-align: left;
}

.li:last-child::after {
  content: '';
  position: absolute;
  top: -2px;
  right: 0;
  width: 50%;
  height: 0;
  border: 1px solid #fff;
}

.status {
  padding: 4px 8px;
  text-align: center;
}
.status h4 {
  font-weight: 600;
  padding-top: 16px;
}

.li.complete {
  /* border-top: 2px solid #66DC71; */
}
.li.complete:before {
  background-color: #66DC71;
  border: none;
  transition: all 200ms ease-in;
  /* cursor: pointer; */
}
.li.complete .status h4 {
  text-decoration: underline;
  color: #66DC71;
  cursor: pointer;
}

.li.start.complete .status h4 {
  text-decoration: none;
  cursor: auto;
}

@media (min-device-width: 320px) and (max-device-width: 700px) {
  .timeline {
    list-style-type: none;
    display: block;
  }

  .li {
    transition: all 200ms ease-in;
    display: flex;
    width: inherit;
  }

  .timestamp {
    width: 100px;
  }

  .status:before {
    left: -8%;
    top: 30%;
    transition: all 200ms ease-in;
  }
}

.item {
}

.qa {
  /* border-bottom: 1px solid #eee; */
  line-height: 1.7;
  padding-bottom: 16px;
}

.section {
  font-size: 22px;
  padding-left: 18px;
  background: #ddd;
  margin-bottom: 10px;
}

.form {
  font-size: 26px;
  margin-bottom: 25px;
  color: #000;
}

.item p.title {
  font-size: 15px;
  color: #1C91DB;
}

.item p.title span {
  display: inline-block;
  width: 0;
  height: 0;
  border-top: 5px solid transparent;
  border-bottom: 5px solid transparent;
  border-left: 5px solid #1C91DB;
  margin-right: 8px;
}

.item p.answer {
  font-size: 15px;
  margin-left: 18px;
}
</style>

