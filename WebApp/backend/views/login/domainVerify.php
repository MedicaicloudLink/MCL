<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>登录 - Medicayun</title>
  <link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl?>/css/register.css">
</head>
<body>
  <div class="main">
    <div class="logo flex-row">
      <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/logo.svg" alt="MedicaYUN LOGO">
    </div>
    <div class="body flex-col">
      <img class="img" src="<?php echo Yii::$app->request->baseUrl?>/imgs/biglogo.svg">
      <p class="des">请输入企业账户的域名</p>
      <div class="form flex-row">
        <input type="text" id="domain" class="domain" placeholder="英文字母，数字等的组合">
        <label>.medicayun.com</label>
      </div>
      <div class="msg" id="msg"></div>
      <div class="tools flex-row"><span class="btn btn-blue" id="subBtn">下一步</span></div>
      <p style="font-size: 14px; color: rgba(0,0,0,.54);">如果不清楚企业帐户的域名，请询问需要加入的企业的帐户管理员</p>
      <div class="other">
        <p>或者，您也可以点击下面的链接</p>
        <p id="link" class="link">创建一个新的企业帐户</a>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
      $('.body').css('min-height', document.body.clientHeight - 64 + 'px')
      $('#link').click(function () {
        var search = window.location.search
        var host = window.location.host.split('.').splice(1,2).join('.')
        location.href= location.protocol + '//www.' + host + '/company/registcompany' + search
      });
      // 注册
      $('#subBtn').click(function () {
        var domain = $('#domain').val()
        var search = window.location.search
        var host = window.location.host.split('.').splice(1,2).join('.')
        $('#msg').html('')
        var that = $(this)
        // 提交
        $.ajax({
          url: '<?php echo Yii::$app->urlManager->createUrl(['company/domain']) ?>',
          data: {'domain': domain},
          type: 'post',
          success: function (rep) {
            var response = window.JSON.parse(rep); 
            if (response.code == 'succ') {
              window.sessionStorage.setItem('authorization', response.data.token)
              window.sessionStorage.setItem('userid', response.data.userid)
              location.href = location.protocol + '//' + domain + '.' + host + '/login/registerpersonal' + search;
            } else if(response.code == 'err'){
              $('#msg').html('错误的企业帐户域名，请重新输入！')
            }
          },
          error: function () {
            $('#msg').html('网络错误，请重新验证')
          }
        })
      });
    });
  </script>
</body>
</html>
<style>
  .form {
    align-items: center;
    margin-bottom: 32px;
  }
  label {
    margin: 0;
  }
  .domain { 
    width: 344px;
    margin-right: 8px;
  }
  .tools {
    margin-bottom: 64px;
  }
  .other {
    width: 480px;
    border-top: 1px solid rgba(0,0,0,.12);
    margin: 16px 0;
    text-align: center;
    font-size: 14px;
  }
  .other p{
    margin: 16px 0 8px;
    color: rgba(0,0,0,.54);
  }
  .other .link {
    color: #458df1;
    cursor: pointer;
  }
  .other .link:hover {
    text-decoration: underline;
    color: #3c7bd3;
  }
</style>