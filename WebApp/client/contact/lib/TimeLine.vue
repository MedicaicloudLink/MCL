<template>
  <ul class="timeline">
    <span class="start li">
      <div class="status">
        <h4>{{ start }}</h4>
      </div>
    </span>
    <li class="li" :class="{complete: h.status === 1}" v-for="(h, index) in value">
      <div class="status" @click="openHistory(index)">
        <h4>{{ h.typename }}</h4>
        <span class="date">{{ h.updatetime | formatDate }}</span>
      </div>
    </li>

    <m-modal :open="dialog" @close="dialog = false">

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
      dialog: false
    }
  },
  methods: {
    openHistory (index) {

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
}

.li {
  position: relative;
  transition: all 200ms ease-in;
  margin-top: 20px;
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
  cursor: pointer;
}
.li.complete .status h4 {
  color: #66DC71;
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
</style>

