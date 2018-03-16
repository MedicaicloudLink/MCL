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
         <img src="../assets/icon_svg/icon_back.svg">
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
         <img src="../assets/icon_svg/icon_next.svg">
      </li>
    </ul>
  </div>
</template>
<script>
  export default {
    name: 'Page',
    props: {
      current: { // 当前的要显示的页数
        type: Number,
        default: 1
      },
      pagesize: { // 一页显示的数据的条数
        type: Number,
        default: 10
      },
      total: { // 数据的总条数
        type: Number,
        default: 0
      }
    },
    data () {
      return {
        currentPage: this.current,
        currentPagesize: this.pagesize
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
    computed: {
      allPages () {
        const allPage = Math.ceil(this.total / this.currentPagesize)
        return (allPage === 0) ? 1 : allPage
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
          this.$emit('changepage', page, this.currentPagesize)
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
    height: 32px;
    line-height: 32px;
    margin-right: 10px;
  }
  .page-item, .page-prev, .page-next, .page-item-jump-prev, .page-item-jump-next {
    float: left;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    list-style: none;
    cursor: pointer;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.12);
    border-radius: 2px;
    font-family: Arial;
    -webkit-transition: all .24s ease;
    transition: all .24s ease;
    margin-right: 4px;
  }
  .page-item a {
    text-decoration: none;
    color: rgba(0,0,0,.54);
  }
  .page-item-active {
    background-color: #458df1;
    border-color: #458df1;
  }
  .page-item-active a, .page-item-active:hover a {
    color: #fff;
  }
  .page-next {
    margin-right: 0px;
  }
</style>