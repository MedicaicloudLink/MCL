var global = {
  data () {
    return {
      userid: window.sessionStorage.getItem('userid'),
      username: '',
      avatar: '',
      shortname: '',
      hospital: '',
      token: '',
      noticeNum: 0 // 新消息提醒
    }
  },
  mounted () {
    if (!window.sessionStorage.getItem('userid')) return
    this.userid = window.sessionStorage.getItem('userid')
    this.GET_USERINFO()
    this.GET_NOTICE_NUM()
    setInterval(() => this.GET_NOTICE_NUM(), 30000)
  },
  methods: {
    // 获取用户信息
    GET_USERINFO () {
      this.$http.GetUserInfo({userid: this.userid}).then(response => {
        this.username = response[0].s_username
        this.hospital = response[0].s_workunti
        this.avatar = response[0].s_avatar
        // 获取名字简写
        this.GET_SHORTNAME(response[0].s_username)
      }).catch(err => this.toast({text: err}))
    },
    // 获取用户姓名首字母缩写
    GET_SHORTNAME (str) {
      this.$http.ChineseToShort({string: str}).then(response => {
        this.shortname = response.shortpinyin
      }).catch(err => this.toast({text: err}))
    },
    GET_NOTICE_NUM () {
      this.$http.GetNoticeNum({userid: this.userid}).then(rep => {
        this.noticeNum = parseInt(rep.count)
      }).catch(err => console.log(err))
    }
  }
}

export default global
