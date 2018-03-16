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
      <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/logo.svg" alt="MedicaYUN LOGO">
    </div>
    <div class="body">
      <h3 class="brand">欢迎加入<img src="<?php echo Yii::$app->request->baseUrl?>/imgs/biglogo.svg"></h3>
      <p class="hint" id="title_hint">请输入您的电子邮箱地址开始使用</p>
      <span class="hint" id="res_hint"></span>
      <div class="card">
        <p class="des">邮箱地址</p>
        <input type="text" id="email" placeholder="@yourmailaddress.com" />
        <p class="tool"><b></b><span class="btn" id="subBtn">继续</span></p>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
      $('.body').height(document.body.clientHeight - 64 + 'px')

      $("#email").keyup(function(e) {  
        // 回车键事件  
        if(e.which == 13) {  
          $('#subBtn').click();  
        }
      }); 

      // 继续按钮监听
      $('#subBtn').click(function() {
        if (this.getAttribute('disabled')) return
        var email = $('#email').val();

        $('#title_hint').html('请输入您的电子邮箱地址开始使用')
        $('#res_hint').html('')

        // 邮箱验证
        if (!verifyMail(email)) {
          $('#title_hint').html('')
          $('#res_hint').html('邮箱地址格式错误')
          return
        }

        // 登录
        this.setAttribute("disabled", true);
        getEmailStatus(email)
      });
    });

    // 获取邮箱状态
    function getEmailStatus (email) {
      $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/mailstate']) ?>',
        data: {'mail': email},
        type: 'post',
        success: function (rep) {
            var response = window.JSON.parse(rep)
            if (response.status == '1004') {
              sendSignUpEmail(email)
            } else if (response.status == '1005') {
              signIn(email, response.data.domain)
            } else if (response.status == '1006') {
              fistSignUp(email, response.data.domain, response.data.userid)
            } else {
              $('#title_hint').html('')
              $('#res_hint').html('邮箱地址格式错误')
              eGet('#subBtn').removeAttribute("disabled");
            }
        },
        error: function (e) {
          eGet('#subBtn').removeAttribute("disabled");
          $('#title_hint').html('')
          $('#res_hint').html('网络错误')
        }
      })
    }

    // 1004: 未注册，未被邀请
    function sendSignUpEmail (email) {
      window.sessionStorage.setItem('signup_send_email', email)
      $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/sendregistmail']) ?>',
        data: {'mail': email},
        type: 'post',
        success: function (rep) {
          console.log('发送成功')
          location.href = "<?php echo Yii::$app->urlManager->createUrl(['login/loginsendmail']) ?>";
        },
        error: function (e) {
          location.href = "<?php echo Yii::$app->urlManager->createUrl(['login/loginsendmail']) ?>";
        }
      })
    }

    // 1005: 已经注册，直接登录
    function signIn (email, domain) {
      location.href = location.protocol + '//' + domain + '/login/loginform?mail=' + email
    }

    // 1006：被邀请成员，第一次登录完善信息
    function fistSignUp (email, domain, userid) {
      location.href = location.protocol + '//' + domain + '/login/registerpersonal?mail=' + email + '&userid=' + userid
    }

    // 获取元素对象
    function eGet (ele) {
      var elements = document.querySelectorAll(ele)
      if (elements.length = 0) return undefined
      if (elements.length = 1) return elements[0]
      return elements
    }

    // 邮箱验证
    function verifyMail (email) { 
      var reg = new RegExp(/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,5}$/); 
      return reg.test(email)
    } 
  </script>
</body>
</html>