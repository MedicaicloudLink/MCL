<template>
  <div class="inline">
    <ul class="wrapClasses">
      <span class="page-total">
          <slot>共 {{ total }} 条</slot>
      </span>
      <li
          title="上一页"
          :class="prevClasses"
          @click="prev">
           <i class="iconfont icon-left"></i>
      </li>
      <li title="第一页" :class="firstPageClasses" @click="changePage(1)"><a>1</a></li>
      <li title="向前 5 页" v-if="currentPage - 3 > 1" class="page-item-jump-prev" @click="fastPrev"><a>...</a></li>
      <li :title="currentPage - 2" v-if="currentPage - 2 > 1" class="page-item" @click="changePage(currentPage - 2)"><a>{{ currentPage - 2 }}</a></li>
      <li :title="currentPage - 1" v-if="currentPage - 1 > 1" class="page-item" @click="changePage(currentPage - 1)"><a>{{ currentPage - 1 }}</a></li>
      <li :title="currentPage" v-if="currentPage != 1 && currentPage != allPages" class="page-item page-item-active"><a>{{ currentPage }}</a></li>
      <li :title="currentPage + 1" v-if="currentPage + 1 < allPages" class="page-item" @click="changePage(currentPage + 1)"><a>{{ currentPage + 1 }}</a></li>
      <li :title="currentPage + 2" v-if="currentPage + 2 < allPages" class="page-item" @click="changePage(currentPage + 2)"><a>{{ currentPage + 2 }}</a></li>
      <li title="向后 5 页" v-if="currentPage + 3 < allPages" class="page-item-jump-next" @click="fastNext"><a>...</a></li>
      <li :title="'最后一页:' + allPages" v-if="allPages > 1" :class="lastPageClasses" @click="changePage(allPages)"><a>{{ allPages }}</a></li>
      <li
          title="下一页"
          :class="nextClasses"
          @click="next">
         <i class="iconfont icon-right"></i>
      </li>
    </ul>
  </div>
</template>
<script>
  export default {
    name: 'Page',
    props: {
      current: {
        type: Number,
        default: 1
      },
      pagesize: {
        type: Number,
        default: 10
      },
      total: {
        type: Number,
        default: 0
      }
    },
    watch: {
      current (val) {
        this.currentPage = val
      },
      pagesize (val) {
        this.currentPagesize = val
      },
      currentPage (val) {
        this.$emit('input', val)
      }
    },
    data () {
      return {
        currentPage: this.current,
        currentPagesize: this.pagesize
      }
    },
    computed: {
      allPages () {
        const allPage = Math.ceil(this.total / this.currentPagesize)
        return (allPage === 0) ? 1 : allPage
      },
      wrapClasses () {
        return `page`
      },
      prevClasses () {
        return [
          `page-prev`,
          {
            [`page-disabled`]: this.currentPage === 1
          }
        ]
      },
      nextClasses () {
        return [
          `page-next`,
          {
            [`page-disabled`]: this.currentPage === this.allPages
          }
        ]
      },
      firstPageClasses () {
        return [
          `page-item`,
          {
            [`page-item-active`]: this.currentPage === 1
          }
        ]
      },
      lastPageClasses () {
        return [
          `page-item`,
          {
            [`page-item-active`]: this.currentPage === this.allPages
          }
        ]
      }
    },
    methods: {
      changePage (page, force) {
        page = page * 1
        page = isNaN(page) ? this.currentPage : page
        if (force || (this.currentPage !== page && (page >= 1 && page <= this.allPages))) {
          this.currentPage = page
          this.$emit('change', page, this.currentPagesize)
        }
      },
      prev () {
        const current = this.currentPage
        if (current <= 1) {
          return false
        }
        this.changePage(current - 1)
      },
      next () {
        const current = this.currentPage
        if (current >= this.allPages) {
          return false
        }
        this.changePage(current + 1)
      },
      fastPrev () {
        const page = this.current - 5
        if (page > 0) {
          this.changePage(page)
        } else {
          this.changePage(1)
        }
      },
      fastNext () {
        const page = this.currentPage + 5
        if (page > this.allPages) {
          this.changePage(this.allPages)
        } else {
          this.changePage(page)
        }
      },
      onSize (pagesize) {
        // console.log(pagesize, 1)
        this.currentPagesize = pagesize * 1
        this.changePage(1, true)
      },
      onPage (page) {
        this.changePage(page)
      }
    }
  }
</script>

<style scoped>
  .inline {
    display: inline-block;
  }
  .page-total {
    float: left;
    height: 42px;
    line-height: 42px;
    margin-right: 10px;
  }
  .page-prev, .page-next {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .page-item {
    float: left;
    min-width: 42px;
    height: 42px;
    line-height: 40px;
    margin-right: 4px;
    text-align: center;
    list-style: none;
    background-color: #fff;
    cursor: pointer;
    font-family: Arial;
    border: 1px solid #bbb;
    border-radius: 4px;
    transition:all .24s ease;
  }
  .page-item a {
    margin: 0 6px;
    text-decoration: none;
    color: #555;
  }
  .page-item-active {
    background-color: #468df1;
    border-color: #468df1;
  }
  .page-item-active a, .page-item-active:hover a {
    color: #fff;
  }
  .page-prev, .page-next, .page-item-jump-prev, .page-item-jump-next {
    display: inline-block;
    float: left;
    min-width: 42px;
    height: 42px;
    line-height: 40px;
    list-style: none;
    text-align: center;
    cursor: pointer;
    color: #999;
    font-family: Arial;
    -webkit-transition: all .24s ease;
    transition: all .24s ease;
  }
  .page-prev {
    margin-right: 8px;
  }
</style>