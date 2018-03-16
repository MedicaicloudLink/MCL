import Vue from 'vue'
import MModal from './modal.vue'
import Dropdown from './Dropdown.vue'
import Addimg from './addImg.vue'
import MProgressbar from './Progressbar.vue'
import Datepicker from './Date'
import MButton from './Button'
import Popover from './Popover'

const Components = {
  MButton,
  MModal,
  Dropdown,
  Addimg,
  MProgressbar,
  Datepicker,
  Popover
}

const install = function () {
  for (let i in Components) {
    Vue.component(i, Components[i])
  }
}

if (typeof window !== 'undefined' && window.Vue) install(window.Vue)

Components.install = install

// 全局提示
import Toast from './toast'
Vue.prototype.toast = Toast

// 全局对话框提示
import Confirm from './confirm.js'
Vue.prototype.confirm = Confirm

export default Components
