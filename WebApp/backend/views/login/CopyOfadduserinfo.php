<body>
<center>
完善基本信息
<form>
姓名：<input type='text' name='s_username' id='s_username'><br>
性别：<input type='text' name='s_sex'   id='s_sex'><br>
所在单位： <input type='text' name='s_workunti' id='s_workunti'><br>
科室：<input type='text' name='s_department' id='s_department'><br>
职称：<input type='text' name='s_joblevel' id='s_joblevel'><br>
<input type='hidden'  id='type' value='<?php echo !empty($type)?$type:'';?>'>
<input type='button' value='提交' onclick='adduserinfo()'>
</form>
</cente>
<body>
<script src="<?php echo Yii::$app->request->baseUrl ?>/api/js/jquery.js" type="text/javascript"></script>
<script>
function adduserinfo(){
    s_username    = $('#s_username').val();
    s_sex         = $('#s_sex').val();
    s_workunti    = $('#s_workunti').val();
    s_department  = $('#s_department').val();
    s_joblevel    = $('#s_joblevel').val();
    type          = $('#type').val();
    $.ajax({
        url: 'adduserinfopro',
        data: {'s_username': s_username, 's_sex': s_sex, 's_workunti': s_workunti,'s_department':s_department,'s_joblevel':s_joblevel,'type':type},
        type: 'post',
        success: function (e) {
            var jsonObj = eval( '(' + e + ')' );  // eval();方法
            if(jsonObj.code == 'err'){
                alert(jsonObj.message)
            }
            if(jsonObj.code == 'succ'){
                alert(jsonObj.message)
            }
        }
    });
}
</script>
