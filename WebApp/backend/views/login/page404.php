<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>链接失效</title>

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

    .logo img {
      height: 32px;
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
      width: 450px;
      line-height: 1.7;
      font-size: 20px;
      text-align: center;
    }

    img.icon {
      margin: 32px;
    }

    p.links {
      display: flex;
      justify-content: space-between;
    }

    p.links a {
      font-size: 16px;
      color: #458DF1;
    }

  </style>
</head>
<body>
  <div class="main">
    <div class="logo">
      <a href="/">
        <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/logonew.png" alt="MedicaYUN LOGO">
      </a>
    </div>
    <div class="login">
      <h3 class="center brand">页面无法显示</h3>
      <p>链接已经失效，或者页面已被移除</p>
      <p>
        <img src="/imgs/icon404.png" alt="" class="icon">
      </p>
      <p class="links">
        <a href="javascript:history.back(-1)">返回上一页</a>
        <a href="/">Medicayun 首页</a>
        <a href="/">前往帮助中心</a>
      </p>
      
    </div>
  </div>
</body>
</html>