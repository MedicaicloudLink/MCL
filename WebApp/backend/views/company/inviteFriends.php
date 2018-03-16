<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>登录 - Medicayun</title>
  <link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl?>/css/register.css">
  <style>
    input {
      width: 300px;
    }
  </style>
</head>
<body>
  <div class="main">
    <input type='hidden' id='userid' value='<?php echo $userid?>'>
    <input type='hidden' id='companyid' value='<?php echo $companyid?>'>
    <div class="logo flex-row">
      <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/M.svg" alt="MedicaYUN LOGO">
      <?php echo $companyname?>
    </div>
    <div class="body flex-col">
      <img class="img" src="<?php echo Yii::$app->request->baseUrl?>/imgs/biglogo.svg">
      <!-- 邀请同事 -->
      <div class="inviteform" id="inviteform">
        <p class="des flex-col">
          <span><?php echo $username?>，欢迎！</span>
          <span id="company">您已成功创建了“<?php echo $companyname?>”的帐户，现在可以邀请更多同事开始一起工作</span>
          <span id="personal" style="display: none;">您已成功加入了“<?php echo $companyname?>”，现在可以邀请其他同事开始一起工作</span>
        </p>
        <div class="form">
          <div class="row flex-row">
            <div class="form-item flex-col">
              <label>同事姓名</label>
              <input type="text" id="name0" class="name" placeholder="同事的姓名">
            </div>
            <div class="form-item flex-col">
              <label>同事邮箱<span class="res_hint">(请输入正确的邮箱地址！)</span></label>
              <div class="flex-row" style="position: relative;">
                <input type="text" id="mail0" class="mail" placeholder="@workmatemailaddress.com">
                <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
                <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
              </div>
            </div>
            <img class="delete" src="<?php echo Yii::$app->request->baseUrl?>/imgs/delete.svg">
          </div>
        </div>
        <p class="add flex-row">
          <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/add.svg"/>
          <span>添加更多同事</span>
        </p>
        <p id="msg"></p>
        <div class="tools flex-row">
          <span class="jump btn btn-gray">跳过此步</span>
          <span class="sendmail btn btn-blue disable">发送邮件邀请</span>
        </div>
      </div>
      <!-- 邀请结果 -->
      <div class="inviteres flex-col" style="display: none;" id="inviteres">
        <span class="succnum"></span>
        <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/sendsucc.svg">
        <span style="color: rgba(0,0,0,.54); margin: 32px 0;">5s后跳转至个人工作首页，您也可以点击下面的链接直接进入</span>
        <a href="/user/home" class="btn btn-blue start-use">开始使用</a>
      </div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
      $('.body').css('min-height', document.body.clientHeight - 64 + 'px')
      var type = location.search.substr(1).split('&')[1].split('=')[1]
      if (type == '1') {
        $('#company').show()
        $('#personal').hide()
      } else if (type == '2') {
        $('#company').hide()
        $('#personal').show()
      }
      // 改变hash 控制页面显示
      var sendsuccnum = 0
      hashchange()      
      if (window.addEventListener) {
        window.addEventListener("hashchange", hashchange, false);
      } else if(window.attachEvent) {
        window.attachEvent("hashchange", hashchange);
      }
      function hashchange () {
        if (window.location.hash === '#inviteres') {
          $('.inviteform').hide()
          $('.inviteres').show()
          $('.succnum').html('成功发送 ' + sendsuccnum + ' 条邀请！')
        } else {
          $('.inviteform').show()
          $('.inviteres').hide()
        }
      }
      // enter健 => tab健
      var elems = $('input[type=text]')
      var index = 0
      elems.eq(index).focus();
      $('.form').on('keyup', function (e) {
        if (e.keyCode == '13') {
          elems.eq(index).blur()
          if (index == elems.length - 1) return;
          index = index + 1;
          elems.eq(index).focus();
        }
      })
      // 判断是第几个input 获取的焦点
      $('.form').on('focus', 'input[type=text]', function (e) {
        var elemsid = []
        $('input[type=text]').each(function () {
          elemsid.push($(this).attr('id'))
        })
        index = elemsid.indexOf(e.target.id)
      })
      // 动态添加同事
      var num = 0
      $('.add').click(function () {
        num ++;
        var str = '<div class="row flex-row">\
                    <div class="form-item flex-col">\
                      <label>同事姓名</label>\
                      <input type="text" id="name'+ num +'" class="name" placeholder="同事的姓名">\
                    </div>\
                    <div class="form-item flex-col">\
                      <label>同事邮箱<span class="res_hint">(请输入正确的邮箱地址！)</span></label>\
                      <div class="flex-row" style="position: relative;">\
                        <input type="text" id="mail'+ num +'" class="mail" placeholder="@workmatemailaddress.com">\
                        <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>\
                        <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>\
                      </div>\
                    </div>\
                    <img class="delete" src="<?php echo Yii::$app->request->baseUrl?>/imgs/delete.svg">\
                  </div>'
        $('.form').append(str)
        elems = $('input[type=text]')
      })
      // 删除
      $('.form').on('click', '.delete', function () {
        if ($('.delete').length == 1) return
        $(this).parent('div').remove()
        elems = $('input[type=text]')
      })
      // 邮箱验证
      $('.form').on('blur', '.mail', function () {
        // 邮箱判断
        verifymail($(this))
        // 有正确邮箱
        let flag = false
        for (var i = 0; i < $('.succ').length; i++) {
          if ($('.succ').eq(i).css('display') !== 'none') {
            flag = true
            break
          }
        }
        for (var i = 0; i < $('.err').length; i++) {
          if ($('.err').eq(i).css('display') !== 'none') {
            flag = false
            break
          }
        }
        if (flag) $('.sendmail').removeClass('disable')
        else $('.sendmail').addClass('disable')
      })
      // 发送邀请邮件
      $('.sendmail').click(function () {
        if ($(this).hasClass('disable')) return
        
        $(this).addClass('disable').html('发送中...')

        var arr = []
        for (var i = 0; i < $('.name').length; i++) {
          arr.push({'name': $('.name')[i].value, 'mail': $('.mail')[i].value})
        }
        var userid     = $('#userid').val()
        var companyid  = $('#companyid').val()
        var inviteinfo = window.JSON.stringify(arr)
        var that = $(this)
        $.ajax({
          url: '<?php echo Yii::$app->urlManager->createUrl(['company/invitewoker']) ?>',
          data: {'userid': userid, 'companyid': companyid, 'inviteinfo': inviteinfo, 'type': type},
          type: 'post',
          success: function (rep) {
            var response = window.JSON.parse(rep);
            if (response.code == 'succ') {
              sendsuccnum = response.data.count
              window.location.hash = '#inviteres';
              setTimeout(function() {
                location.href = "<?php echo Yii::$app->urlManager->createUrl(['user/home']) ?>"
              }, 5000)
            } else {
              $('#msg').html('邀请失败！')
              that.removeClass('disable').html('发送邮件邀请')
            }
          },
          error: function () {
            $('#msg').html('发送失败，请重新发送！')
            that.removeClass('disable').html('发送邮件邀请')
          }
        })
      })
      $('.jump').click(function () {
        // 跳过邀请
        window.location.hash = '#inviteres';
        setTimeout(function() {
          location.href = "<?php echo Yii::$app->urlManager->createUrl(['user/home']) ?>"
        }, 5000)
      })
    });
    // 邮箱验证
    function verifymail (obj) {
      var reg = new RegExp(/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,5}$/); 
      var resHint = obj.parent('div').siblings('label').children('.res_hint')
      if (!obj.val()) {
        obj.css('border-color', 'rgba(0,0,0,.12)')
        resHint.hide()
        obj.siblings('.err').hide().siblings('.succ').hide()
        return
      }
      if (!reg.test(obj.val())) {
        obj.css('border-color', '#e51c23')
        resHint.show()
        obj.siblings('.err').show().siblings('.succ').hide()
      } else {
        obj.css('border-color', 'rgba(0,0,0,.12)')
        resHint.hide()
        obj.siblings('.err').hide().siblings('.succ').show()
      }
    }
  </script>
</body>
</html>