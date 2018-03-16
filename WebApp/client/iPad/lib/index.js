import Vue from 'vue'
import MButton from './button.vue'
import MInput from './Input.vue'
import MModal from './modal.vue'
import Dropdown from './Dropdown.vue'
import Typeahead from './Typeahead.vue'
import MSelect from './Select.vue'
import MOption from './Option.vue'
import Addfile from './addFile.vue'
import MRadio from './Radio.vue'
import MCheckbox from './Checkbox.vue'
import MProgressbar from './Progressbar.vue'
import UpRefresh from './upRefresh.vue'
import MUpload from './UploadFile.vue'

const Components = {
  MButton,
  MInput,
  MModal,
  MSelect,
  MOption,
  Dropdown,
  Typeahead,
  MRadio,
  MCheckbox,
  Addfile,
  MProgressbar,
  UpRefresh,
  MUpload
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
