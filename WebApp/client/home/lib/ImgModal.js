import Vue from 'vue'
import Modal from './modal.vue'
import mButton from './Button.vue'

const $body = document.querySelector('body')

const createNode = () => {
  const $node = document.createElement('div')
  $body.appendChild($node)
  document.body.style.overflow = 'hidden'
  return $node
}

const removeNode = $node => {
  document.body.style.overflow = 'auto'
  $body.removeChild($node)
}

const imgmodal = (imgurl) => {
  /* eslint-disable no-new */
  const clientHeight = window.innerHeight - (2 * document.getElementById('header').clientHeight) - 200
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
    template: `<Modal ref="modal" class="imgmodal">
      <div slot="body" style="width: 100%;">
        <img src="${imgurl}" alt="" style="display:inline-block;width: 100%;height: auto;max-height: ${clientHeight + 'px'};"/>
      </div>
      <div slot="footer" style="text-align: center;">
        <span @click="destroy" style="font-size: 35px;color: #fff;cursor:pointer;">×</span>
      </div>
    </Modal>`,
    destroyed () {
      removeNode(this.$el)
    },
    methods: {
      destroy () {
        this.$destroy()
      }
    }
  })
}

export default imgmodal
