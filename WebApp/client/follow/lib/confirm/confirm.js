import Vue from 'vue'
import Modal from '../modal/modal'
import MButton from '../Button'

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
    components: { Modal, MButton },
    template: `<Modal open title="${title}" @close="destroy">
      ${message}
      <div slot="footer">
        <m-button @click="destroy">取消</m-button>
        <m-button type="blue" @click="handleConfirm">确定</m-button>
      </div>
    </Modal>`,
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
