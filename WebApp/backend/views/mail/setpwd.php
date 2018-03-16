<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <style>
    * {margin: 0;padding: 0;}
    html, body {
      font-family: "Lucida Grande", Lucida Sans Unicode, Hiragino Sans GB, WenQuanYi Micro Hei, Verdana, Aril, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      background-size: cover;
    }

    .main {
      font-size: 16px;
      width: 800px;
      padding: 32px;
      background: #F1F4F9;
    }

    .center {
      text-align: center;
    }

    .brand {
      font-size: 32px;
      font-weight: 400;
      margin-top: 80px;
      margin-bottom: 32px;
    }

    .main p {
      width: 800px;
      line-height: 1.7;
      font-size: 18px;
    }

    .btn {
      display: inline-block;
      height: 40px;
      line-height: 40px;
      font-size: 16px;
      background: #458df1;
      color: #fff;
      border-radius: 2px;
      cursor: pointer;
      text-align: center;
    }

    a.btn {
      text-decoration: none;
      width: 200px;
      margin: 50px auto;
    }

    p.links a {
      font-size: 16px;
      color: #458DF1;
    }

    p.footer {
      margin-top: 40px;
      border-top: 1px solid #ddd;
      font-size: 13px;
      padding-top: 14px;
    }

  </style>
</head>
<body>
  <div class="main">
    <h3 class="center brand"><img src="<?php echo Yii::$app->request->hostInfo?>/imgs/biglogo.svg"></h3>
    <p>请点击下面的按钮，完成您的密码重置。</p>
    <p class="center"><a class="btn" href="http://<?php echo $url;?>">密码重置</a></p>
    <p>您也可以直接点击下面的链接，完成密码重置：</p>
    <p class="link"><a href="http://<?php echo $url;?>"><?php echo $url;?></a></p>

    <p class="footer">邮件来自系统自动发送，无需回复。
    如果您有任何关于梅地卡尔产品和服务的问题和建议，请发送邮件到：<a href="mailto:support@medicayun.cn">support@medicayun.cn</a> </p>
  </div>
</body>
</html>