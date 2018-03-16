<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>忘记密码 - Medicayun</title>
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
      <h3 class="center brand">忘记密码？</h3>
      <p class="hint" id="title_hint">请输入您的邮箱地址，开始密码重置</p>
      <span class="hint" id="res_hint"></span>
      <div class="card">
        <p>邮箱地址</p>
        <input type="text" id="email" placeholder="@yourmailaddress.com"/>
        <p><span></span><span class="btn" id="subBtn">下一步</span></p>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
        window.sessionStorage.clear()

        $("#email").keyup(function(e) {  
          // 回车键事件  
          if(e.which == 13) {  
            $('#subBtn').click();  
          }
        });

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


          var email = eGet('#email').value
          // 验证邮箱有效性
          this.setAttribute("disabled", true);
          verifyAccout(email)
        })
    });

    function verifyAccout (email) {
      $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/forgetpasswdmail']) ?>',
        data: {'mail': email},
        type: 'post',
        success: function (e) {
          var response = window.JSON.parse(e);
          if (response.code == 'succ') sendEmail(email)
          if (response.code == 'err') {
            showHint('title_hint', '')
            showHint('res_hint', '您输入的邮箱地址无效！')
            eGet('#subBtn').removeAttribute("disabled");
          }
        },
        error: function (e) {
          eGet('#subBtn').removeAttribute("disabled");
          showHint('title_hint', '')
          showHint('res_hint', '网络错误')
        }
      })
    }

    function sendEmail (email) {
      $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/sendforgetpasswd']) ?>',
        data: {'mail': email},
        type: 'post',
        success: function (e) {
          var response = window.JSON.parse(e);
          if (response.code == 'err') {
            showHint('title_hint', '')
            showHint('res_hint', '邮件发送失败！')
            return;
          } else if (response.code == 'succ') {
            // 设置 sessionStorage, 下个页面通过缓存重新发送邮件和为空返回此页面 
            window.sessionStorage.setItem('resetpw_email', email);
            location.href="<?php echo Yii::$app->urlManager->createUrl(['login/displayforgetpwuppwd'])?> ";
          }
          eGet('#subBtn').removeAttribute("disabled");
        },
        error: function (e) {
          eGet('#subBtn').removeAttribute("disabled");
          showHint('title_hint', '')
          showHint('res_hint', '网络错误')
        }
      })
    }

    // 获取元素对象
    function eGet (ele) {
      var elements = document.querySelectorAll(ele)
      if (elements.length = 0) return undefined
      if (elements.length = 1) return elements[0]
      return elements
    }

    // 控制提示显示
    function showHint (id, text) {
      document.getElementById(id).innerHTML = text
    }

    /** 验证邮箱 */
    function verifyEmail(email) {
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    }

    </script>
</body>
</html>