import Vue from 'vue'
import MInput from './Input.vue'
import MModal from './modal/modal'
import Dropdown from './Dropdown.vue'
import MOption from './Option.vue'
import MCheckbox from './Checkbox.vue'
import Popover from './Popover.vue'
import MButton from './Button'
import MPage from './Page'
import Loading from './loading'

const Components = {
  MButton,
  MInput,
  MModal,
  MOption,
  Dropdown,
  MCheckbox,
  Popover,
  MPage,
  Loading
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
import Confirm from './confirm/confirm.js'
Vue.prototype.confirm = Confirm

export default Components
