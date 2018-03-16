<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>注册</title>
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
  <style>
    * {margin: 0;padding: 0;}
    html, body {
      font-family: 'Avenir', Helvetica, Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      width: 100%;
      height: 100%;
      background: url("<?php echo Yii::$app->request->baseUrl?>/imgs/bg1.jpg") no-repeat center;
      background-size: cover;
      font-size: 18px;
      line-height: 24px;
    }

    a {
      color: #fff;
    }

    .flex-row {
      display: flex;
      flex-direction: row;
      margin-bottom: 16px;
    }

    .flex-col {
      display: flex;
      flex-direction: column;
      margin-bottom: 16px;
    }

    #mobile {
      flex: 1;
    }

    #password {
      margin-bottom: 16px;
    }

    #password_again {
      margin-bottom: 32px;
    } 

    .flex-row.between {
      justify-content: space-between;
    }

    .logo {
      text-align: center;
      padding-top: 50px;
    }

    .center {
      text-align: center;
    }

    .brand {
      color: #fff;
      font-size: 26px;
      font-weight: 400;
      margin: 30px 0;
      line-height: 37px;
    }

    .card {
      display: flex;
      flex-direction: column;
      width: 300px;
      margin: 0 auto;
      margin-top: 45px;
      background: rgba(255,255,255,.25);
      padding: 0 32px 32px 32px;
    }

    input[type="text"], input[type="number"], input[type="password"] {
      padding: 6px 16px;
      font-size: 16px;
      border: 1px solid #aaa;
      border-radius: 2px;
      height: 30px;
      line-height: 30px;
    }

    input[type="text"]:focus, input[type="password"]:focus, input[type="number"]:focus {
      outline: none;
      border-color: #468df1;
    }
    
    /* remove input[type=number] defult arrow style */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button{
      -webkit-appearance: none !important;
      margin: 0;
    }
    input[type="number"]{-moz-appearance:textfield;}

    .btn {
      height: 44px;
      line-height: 44px;
      font-size: 20px;
      background: #468df1;
      color: #fff;
      cursor: pointer;
    }

    #code {
      width: 140px;
    }

    #msgcode_btn {
      font-size: 14px;
      width: 110px;
    }

    #rep_hint, #mobile_hint {
      color: #f99;
      font-size: 13px;
    }

    .other-link {
      text-align: right;
      color: #fff;
      font-size: 14px;
      margin-top: 8px;
    }
  </style>
</head>
<body>

  <div class="main">
    <div class="logo">
      <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/logo-white.svg" alt="MedicaYUN LOGO">
    </div>
    <div class="login card">
      <h3 class="center brand">注册</h3>
      <span id="rep_hint"></span>
      <div class="flex-col">
        <input type="number" id="mobile" placeholder="手机号码" />
        <span id="mobile_hint"></span>
      </div>
      <div class="flex-row between"><input type="number" id="code" placeholder="验证码" /><span class="btn center" id="msgcode_btn">获取验证码</span></div>
      <input type="password" id="password" placeholder="密码" />
      <input type="password" id="password_again" placeholder="再次输入密码" />
      <input type='hidden'  id='type' value='<?php echo !empty($type)?$type:'';?>'>
      <span class="btn center" id="subBtn" onclick="regist()">注册</span>
      <div class="other-link">
        <p class="signin">已有账号，去<a href="/login/loginform">登录</a></p>
      </div>
    </div>
  </div>

    <script src="<?php echo Yii::$app->request->baseUrl ?>/api/js/jquery.js" type="text/javascript"></script>
    <script>
      $(document).ready(function () {

        // 发送手机验证码
        $('#msgcode_btn').click(function () {
          // 按钮状态判断
          if ($('#msgcode_btn').attr('disabled')) return;
          // 手机格式验证
          if (!verifyPhone($('#mobile').val())) {
            hintMsg('手机号码格式不正确','mobile_hint');
            return ;
          } else {
            hintMsg('','mobile_hint');
          }
          getCode($('#mobile').val());
          settime(this, 60);
        })
      })

      //注册
      function regist() {
          mobile     = $('#mobile').val();
          code       = $('#code').val();
          password   = $('#password').val();
          passwordAgain   = $('#password_again').val();
          type       = $('#type').val();
          console.log(password, passwordAgain)
          if (password !== passwordAgain) {
            document.getElementById('rep_hint').innerHTML = '两次输入的密码不一致'
            return
          }

          $.ajax({
              url: '<?php echo Yii::$app->urlManager->createUrl(['login/register']) ?>',
              data: {'mobile': mobile, 'code': code, 'password': password},
              type: 'post',
              success: function (e) {
                  var response = JSON.parse(e);
                  if (response.code == 'err') {
                      document.getElementById('rep_hint').innerHTML = response.message
                      return false;
                  }
                  if (response.code == 'succ') {
                    if (type === '') {
                        location.href = 'adduserinfo';
                    } else {
                        location.href = 'adduserinfo?type=' + type
                    }

                  }
              }
          });
      }

    //发送验证码
    function getCode(mobile){
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createUrl(['login/getcode']) ?>',     //后台处理验证码方法
            data: {'mobile': mobile},
            type: 'post',
            success: function (e) {
                var response = JSON.parse(e);
                if (response.code == 'err') {
                    document.getElementById('rep_hint').innerHTML = response.message
                    return false;
                }
            }
        });
    }

    /* 倒计时 */
    function settime(dom, count) {
      if (count == 0) {
        clearTimeout(timer)
        dom.removeAttribute("disabled");  
        dom.innerText="再次获取验证码";
      } else {
        dom.setAttribute("disabled", true); 
        dom.innerText="重新发送(" + count + ")"; 
        count--;
        var timer = setTimeout(function() { 
          settime(dom, count)
        },1000) 
      }
    } 

    /* 错误提示 */
    function hintMsg (msg, domId) {
      document.getElementById(domId).innerText = msg;
    }

    /* 验证手机号码 */
    function verifyPhone (phone) {
      if(!(/^1[34578]\d{9}$/.test(phone))){ 
        return false; 
      } else {
        return true;
      }
    }

  </script>
</body>
</html>
