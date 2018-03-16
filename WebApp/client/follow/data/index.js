var global = {
  data () {
    return {
      userid: window.sessionStorage.getItem('userid'),
      username: '',
      avatar: '',
      shortname: '',
      hospital: '',
      token: '',
      noticeNum: 0, // 新消息提醒
      projectList: [],
      endProject: []
    }
  },
  mounted () {
    if (!window.sessionStorage.getItem('userid')) return
    this.userid = window.sessionStorage.getItem('userid')
    this.projectList = []
    this.endProject = []
    this.GET_USERINFO()
    this.GET_NOTICE_NUM()
    setInterval(() => this.GET_NOTICE_NUM(), 30000)
  },
  methods: {
    GET_USERINFO () {
      this.$http.GetUserinfo({userid: this.userid}).then(response => {
        this.username = response[0].s_username
        this.hospital = response[0].s_workunti
        this.avatar = response[0].s_avatar
        // 获取名字简写
        this.GET_SHORTNAME(response[0].s_username)
      }).catch(err => console.log(err))
    },
    GET_SHORTNAME (str) {
      this.$http.ChineseToShort({string: str}).then(response => {
        this.shortname = response.shortpinyin
      }).catch(err => console.log(err))
    },
    OPEN_PROJECT (id) {
      this.$http.OpenProjectTime({userid: this.userid, projectid: id}).then(rep => {
        console.log('记录打开' + id + '时间成功')
      }).catch(err => console.log(err))
    },
    // 获取通知列表
    GET_NOTICE_NUM () {
      this.$http.GetNoticeNum({userid: this.userid}).then(rep => {
        this.noticeNum = parseInt(rep.count)
      }).catch(err => console.log(err))
    }
  }
}

export default global
