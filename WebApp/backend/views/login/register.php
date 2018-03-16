<body>
<center>
注册
<form>
手机号：<input type='text' name='mobile' id='mobile'>
<input id="btnSendCode" type="button" value="发送验证码" onclick="getCode()" /><br> 
验证码：<input type='text' name='code'   id='code'><br>
密码： <input type='password' name='password' id='password'><br>
确认密码： <input type='password' name='repassword' id='repassword'><br>
<input type='hidden'  id='type' value='<?php echo !empty($type)?$type:'';?>'>

<input type='button' value='提交' onclick='regist()'>
</form>
</center>
<p onclick='a()'>点我快点我</p>
<body>
<script src="<?php echo Yii::$app->request->baseUrl ?>/api/js/jquery.js" type="text/javascript"></script>
<script>

//注册
function regist(){
    mobile     = $('#mobile').val();
    code       = $('#code').val();
    password   = $('#password').val();
    repassword = $('#repassword').val();
    type       = $('#type').val();
    if(password != repassword){
        alert('密码不一致');return;
    }
    $.ajax({
        url: 'register',
        data: {'mobile': mobile, 'code': code, 'password': password},
        type: 'post',
        success: function (e) {
            var jsonObj = eval( '(' + e + ')' );  // eval();方法
            if(jsonObj.code == 'err'){
                alert(jsonObj.message);return;
            }
            if(jsonObj.code == 'succ'){
                if(type == ''){
                    location.href='adduserinfo';
                }else{
                    location.href='adduserinfo?type='+type;
                }
                
            }
        }
    });
}
//发送验证码
var InterValObj; //timer变量，控制时间
var count = 60;  //间隔函数，1秒执行
var curCount;    //当前剩余秒数
function getCode(){
    mobile   = $('#mobile').val();
    $.ajax({
        url: 'getcode',     //后台处理验证码方法
        data: {'mobile': mobile},
        type: 'post',
        success: function (e) {
            var jsonObj = eval( '(' + e + ')' );  // eval();方法
            if(jsonObj.code == 'err'){
                alert(jsonObj.message);return;
            }
            else{
                curCount = count;
                //设置button效果，开始计时
                $("#btnSendCode").attr("disabled", "true");
                $("#btnSendCode").val("请在" + curCount + "秒内输入验证码");
                InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
                alert(jsonObj.message);return;
            }
            
        }
    });
}
//timer处理函数
function SetRemainTime() {
    if (curCount == 0) {                
        window.clearInterval(InterValObj);        //停止计时器
        $("#btnSendCode").removeAttr("disabled"); //启用按钮
        $("#btnSendCode").val("重新发送验证码");
    }
    else {
        curCount--;
        $("#btnSendCode").val("请在" + curCount + "秒内输入验证码");
    }
}
function a(){
	$.ajax({
        url: 'http://api.jisuapi.com/area/query',     //后台处理验证码方法
        dataType:'jsonp',
        data: {'parentid': 1,'appkey':'2454fd207da59a85'},
        type: 'get',
        success: function (e) {
            console.log(e)
            
        }
    });
}
</script>
