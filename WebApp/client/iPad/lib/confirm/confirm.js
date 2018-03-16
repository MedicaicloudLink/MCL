import Vue from 'vue'
import DialogBox from './dialog.vue'

const $body = document.querySelector('body')

const createNode = () => {
  const $node = document.createElement('div')
  $body.appendChild($node)
  return $node
}

const removeNode = $node => {
  $body.removeChild($node)
}

const confirm = (options) => {
  const {title, message, onConfirm} = options
  /* eslint-disable no-new */
  new Vue({
    el: createNode(),
    components: {
      DialogBox
    },
    template: `<DialogBox ref="dialog" :width="360">
      <div slot="header">${title}</div>
      <div slot="body" style="margin-left: 32px;">
        ${message}
      </div>
      <div slot="footer">
        <div class="btn-blue" @click="handleConfirm">确定</div>
        <div class="btn-gray" @click="destroy">取消</div>
      </div>
    </DialogBox>`,
    destroyed () {
      removeNode(this.$el)
    },
    methods: {
      handleConfirm () {
        onConfirm && onConfirm()
        this.$destroy()
      },
      destroy () {
        this.$destroy()
      }
    }
  })
}

export default confirm
