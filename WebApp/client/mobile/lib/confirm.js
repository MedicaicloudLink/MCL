import Vue from 'vue'
import Modal from './modal.vue'
import mButton from './Button.vue'

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
  const {title, message, onConfirm, onHide, onShow} = options
  /* eslint-disable no-new */
  new Vue({
    el: createNode(),
    data () {
      return {
        show: false
      }
    },
    components: {
      Modal,
      mButton
    },
    template: `<Modal ref="modal" :width="380"
      @confirm="handleConfirm"
      @hide="handleHide"
      @show="handleShow"
      @closed="destroy">
      <div slot="header" style="padding: 16px;font-size: 18px;">${title}</div>
      <div slot="body" style="padding: 16px;">
        ${message}
      </div>
      <div slot="footer" style="padding: 8px 16px;">
        <m-button type="gray" @click.native="destroy" style="margin-right: 24px;">取消</m-button>
        <m-button type="blue" @click.native="handleConfirm">确定</m-button>
      </div>
    </Modal>`,
    destroyed () {
      removeNode(this.$el)
    },
    methods: {
      handleShow () {
        onShow && onShow()
      },
      handleConfirm () {
        this.$destroy()
        onConfirm && onConfirm()
      },
      handleHide () {
        this.$destroy()
        onHide && onHide()
      },
      destroy () {
        this.$destroy()
      }
    }
  })
}

export default confirm
