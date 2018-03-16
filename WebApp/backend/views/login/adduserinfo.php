<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>完善个人信息</title>
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
  <style>
    * {margin: 0;padding: 0;}
    html, body {
      font-family: 'Avenir', Helvetica, Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      width: 100%;
      height: 100%;
      background: rgb(243, 243, 243);
      font-size: 18px;
      line-height: 24px;
    }

    a {
      color: #468df1;
    }

    .card>.flex-row {
      display: flex;
      flex-direction: row;
      align-items: center;
      margin-bottom: 16px;
    }

    .flex-row>input {
      flex: 1;
    }

    .center {
      text-align: center;
    }

    .brand {
      color: #458DF1;
      font-size: 26px;
      font-weight: 400;
      margin: 32px 0;
      line-height: 37px;
    }

    .card {
      display: flex;
      flex-direction: column;
      width: 350px;
      margin: 0 auto;
      padding: 32px;
    }

    input[type="text"], input[type="number"], input[type="password"] {
      padding: 8px 16px;
      font-size: 16px;
      border: 1px solid rgba(0,0,0,.12);
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

    /* radio style*/
	  [type="radio"] {
      position: absolute;
      left: -9999px;
    }
    [type="radio"] + label {
      position: relative;
      cursor: pointer;
      display: inline-block;
      height: 32px;
      padding-left: 28px;
      font-size: 16px;
      line-height: 32px;
    }
    [type="radio"] + label:before {
      content: '';
      position: absolute;
      left: 0;
      top: 6px;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: inline-block;
      border: 1px solid #aaa;
      transition: .28s ease
    }
    [type="radio"]:not(:checked) + label:after, [type="radio"]:checked + label:after {
      content: '';
      position: absolute;
      left: 3px;
      top: 9px;
      width: 12px;
      height: 12px;
      border-radius: 12px;
      transform: scale(1.02);
      transition: all .2s;
    }
    [type="radio"]:checked + label:before {
      border-color: #01adff;
    }
    [type="radio"]:not(:checked) + label:after {
      -webkit-transform: scale(0);
      transform: scale(0)
    }
    [type="radio"]:checked + label:after {
      background: #01adff;
      -webkit-transform: scale(1.02);
      transform: scale(1.02);
    }

    .btn {
      height: 48px;
      line-height: 48px;
      font-size: 20px;
      background: #468df1;
      color: #fff;
      cursor: pointer;
    }
    #subBtn {
      margin-top: 32px;
    }
    #rep_hint{
      color: #f99;
      font-size: 13px;
    }
  </style>
</head>
<body>
  <div class="main">
    <div class="login card">
      <h3 class="center brand">完善个人信息</h3>
      <span id="rep_hint"></span>
      <div class="flex-row"><input type="text" id="name" placeholder="姓名" /></div>
      <div class="flex-row">
        <label style="padding-right: 16px;">性别：</label>
        <div class="flex-row">
          <input type="radio" id="man" name='s_sex' value='1' checked/>
          <label for="man" style="padding-right: 16px;">男</label>
          <input type="radio" id="woman" name='s_sex' value='2'/>
          <label for='woman'>女</label>
        </div>
      </div>
      <div class="flex-row"><input type="text" id="s_workunti" placeholder="所在单位" /></div>
      <div class="flex-row"><input type="text" id="s_department" placeholder="科室" /></div>
      <div class="flex-row"><input type="text" id="s_joblevel" placeholder="职称" /></div>
      <input type='hidden'  id='type' value='<?php echo !empty($type)?$type:'';?>'>
      <span class="btn center" id="subBtn" onclick='adduserinfo()'>提交</span>
    </div>
  </div>

</body>
</html>
<script src="<?php echo Yii::$app->request->baseUrl ?>/api/js/jquery.js" type="text/javascript"></script>
<script>

function adduserinfo(){
    s_username    = $('#name').val();
    s_sex         = $('input[name="s_sex"]:checked').val();
    s_workunti    = $('#s_workunti').val();
    s_department  = $('#s_department').val();
    s_joblevel    = $('#s_joblevel').val();
    type          = $('#type').val();

    $.ajax({
        url: '<?php echo Yii::$app->urlManager->createUrl(['login/adduserinfopro']) ?>',
        data: {'s_username': s_username, 's_sex': s_sex, 's_workunti': s_workunti,'s_department':s_department,'s_joblevel':s_joblevel,'type':type},
        type: 'post',
        success: function (e) {
            var jsonObj = eval( '(' + e + ')' );  // eval();方法
            if(jsonObj.code == 'err'){
              document.getElementById('rep_hint').innerHTML = jsonObj.message
              return false;
            }
            if(jsonObj.code == 'succ') {
                userdata = jsonObj.data;
                window.sessionStorage.setItem('username', userdata.cellphone);
                window.sessionStorage.setItem('userid', userdata.userid);
                window.sessionStorage.setItem('authorization', userdata.token);
                location.href = "<?php echo Yii::$app->urlManager->createUrl(['user/home']) ?>";
            }
        }
    });
}

</script>
