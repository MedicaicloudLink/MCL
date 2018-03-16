import Vue from 'vue'
import ToastComponent from './Toast.vue'
const Toast = Vue.extend(ToastComponent)

export default function (obj) {
  const domNode = document.createElement('div')
  document.body.appendChild(domNode)
  let instance = new Toast({
    el: domNode
  })

  instance.placement = obj.placement || 'top'
  instance.type = obj.type || 'default'
  instance.closeOnClick = obj.closeOnClick
  instance.duration = obj.duration === undefined ? 2500 : obj.duration
  instance.width = obj.width
  instance.text = obj.text

  instance.show = true

  if (instance.duration > 0) {
    setTimeout(() => {
      instance.show = false
      Vue.nextTick(() => {
        instance.$destroy()
      })
    }, instance.duration)
  }
}
