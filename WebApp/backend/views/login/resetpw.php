<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>重置密码 - Medicayun</title>
  <link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl?>/css/login.css">
</head>
<body>
  <div class="main">
    <div class="logo">
      <a href="/">
        <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/productlogo.png" alt="MedicaYUN LOGO">
      </a>
      <span class="name"><?php echo $companyname?> </span>
    </div>
    <div class="body">
      <h3 class="center brand">密码重置</h3>
      <p class="hint" id="title_hint">请输入新的密码，进行密码重置</p>
      <span class="hint" id="res_hint"></span>
      <div class="card">
        <p>新的密码</p>
        <input type="password" id="password1" placeholder="新的密码" />
        <p>再次确认新的密码</p>
        <input type="password" id="password2" placeholder="再次确认新的密码" />
        <p><span></span><span class="btn center" id="subBtn">重置</span></p>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    

    $(document).ready(function () {

      $('#password2').keyup(function(e) {
        var pw   = $('#password1').val();
        var pw2   = $('#password2').val();
        
        if (pw.length < 6) {
          showHint('title_hint', '')
          showHint('res_hint', '密码长度不少于6位')
          return
        }
        if (pw !== pw2 && pw2.length > 0) {
          showHint('title_hint', '')
          showHint('res_hint', '两次密码不同')
          return
        }
        if (pw === pw2) {
          showHint('title_hint', '请输入新的密码，进行密码重置')
          showHint('res_hint', '')
        }

        // 回车键事件  
        if(e.which == 13) {  
          $('#subBtn').click();  
        }

      })
        
      // 按钮监听
      $('#subBtn').click(function() {
        if (this.getAttribute('disabled')) return
        var id = getQueryString('mailid')

        showHint('title_hint', '请输入新的密码，进行密码重置')
        showHint('res_hint', '')
        var pw   = $('#password1').val();
        var pw2   = $('#password2').val();
        if (pw.length < 6) {
          showHint('title_hint', '')
          showHint('res_hint', '密码长度不少于6位')
          return
        }
        if (pw !== pw2) {
          showHint('title_hint', '')
          showHint('res_hint', '两次密码不同')
          return
        }

        this.setAttribute("disabled", true);
        reset(id, pw, pw2);
      });

    });

    // 重置密码
    function reset(id, password, repassword) {
      $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/setpasswd']) ?>',
        data: {'mailid': id, 'password': password, 'repassword': repassword},
        type: 'post',
        success: function (e) {
          var response = window.JSON.parse(e); 
          if(response.code == 'err'){
            showHint('title_hint', '')
            showHint('res_hint', '重置失败')
          } else if(response.code == 'succ'){
              window.sessionStorage.setItem('userid', response.data.userid);
              window.sessionStorage.setItem('authorization', response.data.token);

              location.href="<?php echo Yii::$app->urlManager->createUrl(['login/loginmailinput']) ?>";
          }
          eGet('#subBtn').removeAttribute("disabled");
        },
        error: function (e) {
          eGet('#subBtn').removeAttribute("disabled");
          showHint('title_hint', '')
          showHint('res_hint', '重置失败')
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

    </script>
</body>
</html>