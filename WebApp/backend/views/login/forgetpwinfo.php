<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>忘记密码 - Medicayun</title>

  <style>
    * {margin: 0;padding: 0;}
    html, body {
      font-family: "Lucida Grande", Lucida Sans Unicode, Hiragino Sans GB, WenQuanYi Micro Hei, Verdana, Aril, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      width: 100%;
      height: 100%;
      background-size: cover;
      font-size: 16px;
      line-height: 1;
      height: 100%;
    }

    .main {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .logo {
      height: 64px;
      padding: 0 32px;
      display: flex;
      align-items: center;
      background: #fff;
    }

    .logo .name {
      font-size: 20px;
      padding-left: 16px;
    }

    .center {
      text-align: center;
    }

    .login {
      background: #F1F4F9;
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .brand {
      font-size: 32px;
      font-weight: 400;
      margin-top: 80px;
      margin-bottom: 32px;
    }

    .login>p {
      width: 800px;
      line-height: 1.7;
      font-size: 20px;
    }

    p.split {
      margin-top: 24px;
      margin-bottom: 48px;
    }

    .btn {
      height: 40px;
      line-height: 40px;
      font-size: 16px;
      background: #458df1;
      color: #fff;
      border-radius: 2px;
      cursor: pointer;
      text-align: center;
    }

    .btn:hover {
      background: #3C7BD3;
    }

    .btn[disabled] {
      background: #BDBDBD;
    }

    #seed {
      width: 200px;
    }

  </style>
</head>
<body>
  <script>
    var email = window.sessionStorage.getItem('resetpw_email')
    if (!email) location.href = location.protocol + "//" + location.host + '/login/forgetpassword'
  </script>

  <div class="main">
    <div class="logo">
      <a href="/">
        <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/productlogo.png" alt="MedicaYUN LOGO">
      </a>
      <span class="name"><?php echo $companyname;?></span>
    </div>
    <div class="login">
      <h3 class="center brand">密码重置邮件已发送</h3>
      <p>密码重置的邮件已经发送到 “<span id="email"></span>” ，请检查您的收件箱（也包括垃圾邮件文件夹），然后点击邮件中的地址链接重置密码。</p>
      <p class="split">如果您没有收到验证邮件，请点击下面的链接</p>
      <span class="btn" id="seed">重新发送电子邮件</span>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
      eGet('#email').innerText = email

      $('#seed').click(function() {
        if (this.getAttribute('disabled')) return
        time(this, 60)
        sendEmail(email)
      })
    });

    function sendEmail (email) {
      $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/sendforgetpasswd']) ?>',
        data: {'mail': email},
        type: 'post',
        success: function (e) {}
      })
    }

    // 获取元素对象
    function eGet (ele) {
      var elements = document.querySelectorAll(ele)
      if (elements.length = 0) return undefined
      if (elements.length = 1) return elements[0]
      return elements
    }

    function time(o, wait) {
      if (wait > 0) {
        o.setAttribute("disabled", true);
        o.innerText = "重新发送(" + wait + ")";
        wait--;
        setTimeout(function() {
          time(o, wait)
        }, 1000)
      }
      if (wait === 0) {
       o.removeAttribute("disabled");   
       o.innerText = "重新发送电子邮件";
      }
     }

    </script>
</body>
</html>