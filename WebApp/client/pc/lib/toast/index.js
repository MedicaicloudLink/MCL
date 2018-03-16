import Vue from 'vue'
import ToastComponent from './Toast.vue'
const Toast = Vue.extend(ToastComponent)

export default function (obj) {
  const domNode = document.createElement('div')
  document.body.appendChild(domNode)
  let instance = new Toast({
    el: domNode
  })

  instance.placement = obj.placement || 'center'
  instance.type = obj.type || 'default'
  instance.showClose = obj.showClose
  instance.duration = obj.duration === undefined ? 3000 : obj.duration
  instance.width = obj.width
  instance.text = obj.text
  instance.show = true
}
