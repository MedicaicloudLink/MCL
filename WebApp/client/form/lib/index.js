import Vue from 'vue'
import MInput from './Input.vue'
import MModal from './modal.vue'
import MButton from './Button.vue'
import MSwitch from './Switch.vue'
import Dropdown from './Dropdown.vue'
import Popover from './Popover.vue'
import Typeahead from './Typeahead.vue'
import MSelect from './Select.vue'
import MOption from './Option.vue'
import Addimg from './addImg.vue'
import MRadio from './Radio.vue'
import MCheckbox from './Checkbox.vue'
import MProgressbar from './Progressbar.vue'
import MUpload from './UploadFile.vue'

const Components = {
  MButton,
  MInput,
  MModal,
  MSwitch,
  MSelect,
  MOption,
  Dropdown,
  Popover,
  Typeahead,
  MRadio,
  MCheckbox,
  Addimg,
  MProgressbar,
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

export default Components
