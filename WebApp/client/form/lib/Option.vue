<template>
  <div :value="value" class="m-option">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'Option',
  props: {
    value: {}
  }
}
</script>