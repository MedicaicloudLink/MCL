<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>登录 - Medicayun</title>
  <link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl?>/css/login.css">
</head>
<body>
  <div class="main">
    <div class="logo">
    <a href="/">
        <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/productlogo.png" alt="MedicaYUN LOGO">
      </a>
      <span class="name"><?php echo $companyname;?></span>
    </div>
    <div class="body">
      <h2 class="center brand">请输入密码开始登录</h2>
      <p class="hint" id="title_hint">欢迎回来！请输入登录密码开始使用</p>
      <span class="hint" id="res_hint"></span>
      <div class="card">
        <p>邮箱地址</p>
        <input type="text" id="email" placeholder="@yourmailaddress.com" />
        <p><span>登录密码</span><span id="forgetpw" style="color: #458df1;cursor: pointer;">忘记密码？</span></p>
        <input type="password" id="password" placeholder="输入登录密码" />
        <p><span></span><span class="btn" id="subBtn">登录</span></p>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    

    $(document).ready(function () {
      var initEmail = getQueryString('mail')
      if (initEmail) eGet('#email').value = initEmail

      $("#password").keyup(function(e) {  
        // 回车键事件  
        if(e.which == 13) {  
          $('#subBtn').click();  
        }
      });

      $('#forgetpw').click(function() {
          window.location.href = '/login/forgetpassword'
      })
        
      // 登陆按钮监听
      $('#subBtn').click(function() {
        if (this.getAttribute('disabled')) return

        showHint('title_hint', '欢迎回来！请输入登录密码开始使用')
        showHint('res_hint', '')
        window.sessionStorage.clear()
        if (!verifyEmail(eGet('#email').value)) {
          showHint('title_hint', '')
          showHint('res_hint', '邮箱格式不对')
          return
        }
        this.innerHTML = '登录中……'
        var email = eGet('#email').value
        var pw = eGet('#password').value
        
        this.setAttribute("disabled", true);
        emailLogin(email, pw)
      });

    });

    function emailLogin (email, pw) {
      $.ajax({
          url: '<?php echo Yii::$app->urlManager->createUrl(['login/emaillogin']) ?>',
          data: {'mail': email, 'password': pw},
          type: 'post',
          success: function (e) {
              var response = window.JSON.parse(e); 
              if (response.code == 'err') {
                showHint('title_hint', '')
                showHint('res_hint', '您输入的邮箱地址或者密码错误！')
                eGet('#subBtn').innerHTML = '登录'
                eGet('#subBtn').removeAttribute("disabled");
              } else if (response.code == 'succ') {
                window.sessionStorage.setItem('userid', response.data.userid);
                window.sessionStorage.setItem('authorization', response.data.token);

                location.href = "<?php echo Yii::$app->urlManager->createUrl(['user/home']) ?>";

              }
          },
          error: function (e) {
            eGet('#subBtn').removeAttribute("disabled");
            eGet('#subBtn').innerHTML = '登录'
            showHint('title_hint', '')
            showHint('res_hint', '网络错误')
          }
      })
    }

    function eGet (ele) {
      var elements = document.querySelectorAll(ele)
      if (elements.length = 0) return undefined
      if (elements.length = 1) return elements[0]
      return elements
    }

    function showHint (id, text) {
      document.getElementById(id).innerHTML = text
    }

    /** 获取url参数 */
    function getQueryString(name) {
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
      var r = window.location.search.substr(1).match(reg); 
      if (r != null) return unescape(r[2]); return null; 
    }

    /** 验证邮箱 */
    function verifyEmail(email) {
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    }
    </script>
</body>
</html>