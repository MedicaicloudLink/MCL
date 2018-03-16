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
    <input type='hidden' id='userid' value='<?php echo $userid?>'>
    <input type='hidden' id='mail' value='<?php echo $mail?>'>
    <input type='hidden' id='companyid' value='<?php echo $companyid?>'>
    <div class="logo flex-row">
      <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/M.svg" alt="MedicaYUN LOGO">
      <?php echo $companyname?>
    </div>
    <div class="body flex-col">
      <img class="img" src="<?php echo Yii::$app->request->baseUrl?>/imgs/biglogo.svg">
      <p class="des">输入下面的信息，您就可以加入“<?php echo $companyname?>”，和其他同事一起工作</p>
      <div class="form flex-col" style="width: 480px">
        <div class="form-option flex-col" style="margin-bottom: 32px;">
          <label>姓名<span class="res_hint">(请输入不多于20个汉字字符或40个字母字符)</span></label>
          <div class="flex-row" style="position: relative;">
            <input style="width: 100%;" type="text" id="name" placeholder="您的姓名">
            <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
            <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>创建登录密码<span class="res_hint">(请输入不少于6位的密码)</span></label>
          <div class="flex-row" style="position: relative;">
            <input style="width: 100%;" type="password" id="password" placeholder="输入密码，不少于六位">
            <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
            <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
          </div>
        </div>
        <div class="check">
          <input type="checkbox" id="check">
          <label for="check">我已阅读并理解梅地卡尔公司，梅地卡尔临床数据云平台的“<a href="/login/useterms" target="_black">使用条款</a>”和“<a href="/login/privacy" target="_black">隐私条款</a>”，我同意并接受上述协议和条款。</label>
        </div>
      </div>
      <div class="msg" id="msg"></div>
      <div class="tools flex-row"><span class="btn btn-blue disable" id="subBtn">创建帐户</span></div>
      <div class="other">
        <p>如果您不是属于此企业或组织机构的员工，点击下面的链接</p>
        <a href="/login/domain?userid=<?php echo $userid?>&mail=<?php echo $mail?>">登录到另一个企业帐户</a>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
      $('.body').css('min-height', document.body.clientHeight - 64 + 'px')
      // 名字
      $('#name').blur(function () {
        if ((new RegExp(/^[\u4e00-\u9fa5]+$/)).test($(this).val().trim())) {
          verifyInput($(this), 20, 'lt')
        } else if ($(this).val().length > 40) {
          trueOrFalse('err', $(this))
        } else if ($(this).val().length > 0){
          trueOrFalse('succ', $(this))
        }
      });
      // 密码
      $('#password').blur(function () {
        verifyInput($(this), 5, 'gt')
			});
      $('#check').click(function () {
        if ($("input[type='checkbox']").is(':checked')) {
          $('.btn').removeClass('disable')
        } else {
          $('.btn').addClass('disable')
        }
      })
      // 注册
      $('#subBtn').click(function () {
        if ($('.btn').hasClass('disable')) return
        var name      = $('#name').val()
        var password  = $('#password').val()
        var mail      = $('#mail').val()
        var companyid = $('#companyid').val()
        var userid    = $('#userid').val()
        if (!(name && password)) {
          $('#msg').html('姓名和密码不能为空！')
          return
        }
        for (var i = 0; i < $('.res_hint').length; i++) {
          if ($('.res_hint').eq(i).css('display') !== 'none') {
            $('#msg').html('数据格式有误！')
            return
          }
        }
        $('#msg').html('')
        $(this).html('账号创建中...').addClass('disable')
        var that = $(this)
        // 提交
        $.ajax({
          url: '<?php echo Yii::$app->urlManager->createUrl(['company/createuser']) ?>',
          data: {'mail': mail, 'name': name, 'password': password, 'companyid': companyid, 'userid': userid},
          type: 'post',
          success: function (rep) {
            var response = window.JSON.parse(rep); 
            if (response.code == 'succ') {
              window.sessionStorage.setItem('authorization', response.data.token)
                window.sessionStorage.setItem('userid', response.data.userid)
              location.href= 'http://'  + window.location.host + '/company/invitefriends/?userid=' + response.data.userid + '&type=2';
            } else if(response.code == 'err'){
              that.html('账号创建').removeClass('disable')
              $('#msg').html('创建失败')
            }
          },
          error: function () {
            $('#msg').html('创建失败')
            that.html('账号创建').removeClass('disable')
          }
        })
      });
    });
    // 验证输入
    function verifyInput (obj, length, gt) {
      if (!obj.val()) return
      var type
      if (gt == 'gt') {
        type = obj.val().length > length ? 'succ' : 'err'
      } else if (gt == 'lt') {
        type = obj.val().length < length ? 'succ' : 'err'
      }
      trueOrFalse(type, obj)
    }
    function trueOrFalse (type, obj) {
      if (type == 'succ') {
        obj.parent('div').siblings('label').children('.res_hint').hide()
        obj.css('border-color', 'rgba(0,0,0,.12)')
        obj.siblings('.err').hide().siblings('.succ').show()
      } else if (type == 'err') {
        obj.parent('div').siblings('label').children('.res_hint').show()
        obj.css('border-color', '#e51c23')
        obj.siblings('.err').show().siblings('.succ').hide()
      }
    }
  </script>
</body>
</html>
<style>
  .other {
    width: 480px;
    border-top: 1px solid rgba(0,0,0,.12);
    margin: 24px 0;
    text-align: center;
    font-size: 14px;
  }
  .other p{
    margin: 16px 0 8px;
    color: rgba(0,0,0,.54);
  }
</style>