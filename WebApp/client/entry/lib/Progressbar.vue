<template>
  <div class="progress">
    <div :class="classObj" :style="{width: now + '%', height: height}">
      {{label ? now + '%':'' }}
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Progressbar',
    props: {
      height: {
        type: String,
        default: '20px'
      },
      now: {
        type: Number,
        require: true
      },
      label: {
        type: Boolean,
        default: false
      },
      type: {
        type: String
      },
      striped: {
        type: Boolean,
        default: false
      },
      animated: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      classObj () {
        let {type, striped, animated} = this
        let klass = {}
        klass['progress-bar'] = true
        klass['progress-bar-' + type] = true
        klass['progress-bar-striped'] = striped
        klass['progress-bar-animated'] = animated
        return klass
      }
    }
  }
</script>

<style>
.progress {
  overflow: hidden;
  border-radius: 4px;
  box-shadow: none;
  background: #f9f9f9;
}

.progress-bar {
  float: left;
  width: 0;
  height: 100%;
  font-size: 12px;
  line-height: 20px;
  color: #fff;
  text-align: center;
  background-color: #468df1;
  transition: width .6s ease;
}
.progress-bar-striped {
  background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
  background-size: 40px 40px;
}
.progress-bar-animated {
    animation: progress-bar-stripes 2s linear infinite;
}
.progress-bar-success {
  background-color: #468df1;
}

@-webkit-keyframes progress-bar-stripes {
  from {
    background-position: 40px 0;
  }
  to {
    background-position: 0 0;
  }
}
@-o-keyframes progress-bar-stripes {
  from {
    background-position: 40px 0;
  }
  to {
    background-position: 0 0;
  }
}
@keyframes progress-bar-stripes {
  from {
    background-position: 40px 0;
  }
  to {
    background-position: 0 0;
  }
}


@-webkit-keyframes loading-spin {
  from {
    -webkit-transform: rotate(0deg);
  }
  to {
    -webkit-transform: rotate(360deg);
  }
}
@-moz-keyframes loading-spin {
  from {
    -moz-transform: rotate(0deg);
  }
  to {
    -moz-transform: rotate(360deg);
  }
}
@-ms-keyframes loading-spin {
  from {
    -ms-transform: rotate(0deg);
  }
  to {
    -ms-transform: rotate(360deg);
  }
}
@keyframes loading-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

</style>
