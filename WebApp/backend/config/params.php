<?php
header("Content-type: text/html; charset=utf-8");
return [
    'adminEmail'            => 'admin@example.com',
    'TOKEN_EXPIRES_IN'      =>  604800, 	//TOKEN过期时间,一周失效,单位秒
    'TOKEN_REFRESH_TIME'    =>  604800, 	//TOKEN 24小时续期一次, 单位秒
    'TOKEN_DISTURB_CODE'    =>  'qwerty&*$', //TOKEN干扰因子
    'AES_KEY'               =>  'MYgGnQE2jDFADSFFDSEWsdDdwedfxzse', //AES加密密钥
    
    //'bucket'                => 'hjh',        //ufile空间名   本人test
   
    //'bucket'               => 'medicayun',           //ufile
    'bucket'               => 'medicatest',           //ufile
    'REGIST_TEMPLATE_ID'    => 'SMS_7290882',       //短信注册模板id
    'REGIST_SIGNNAME'       => '注册验证',             //短信注册签名
    
    'UPPWD_TEMPLATE_ID'    => 'SMS_7290880',       //短信修改密码模板id
    'UPPWD_SIGNNAME'       => '身份验证',             //短信注册签名
    //阜外模板信息
    #templateid = 1
    'patientdata'          => [
                               '1'=>'{"备用电话": "", "现居住地": "", "联系电话": ""}',
                               '2'=>'{"BMI": "NaN", "体重": "", "症状": "", "腹围": "", "身高": "", "胸痛会因为服用硝酸甘油有所缓解": "", "胸痛通常是因为运动或情绪激动触发": ""}',
                               '3'=>'{"肾病": "", "家族史": "", "糖尿病": "", "高血压": "", "高血脂症": "", "绝经(女性)": "", "胃食管反流": "", "脑血管疾病": "", "外周血管疾病": "", "已经戒烟时间": "", "每日吸烟频次": "", "累计吸烟时间": "", "糖尿病确诊时间": "", "高血压确诊时间": "", "高血脂症确诊时间": "", "糖尿病是否控制达标": "", "脑血管疾病确诊时间": "", "高血压是否控制达标": "", "外周血管疾病确诊时间": "", "早发心血管疾病家族史": "", "高血脂症是否控制达标": ""}',
                               '4'=>'{"减肥": "", "运动": "", "工作状态": "", "教育程度": "", "饮食习惯": "", "每天工作时长": "", "对心脏病的了解程度": "", "对冠心病CT检查的了解程度": "", "对冠心病CT检查的紧张程度": "", "平常对心脏问题的紧张程度": "", "工作中每天是否久坐超过6小时": ""}',
                               '5'=>'{"药品": ""}',
                               '6'=>'{"尿酸(UA)": "", "肌酐(Cr)": "", "血糖(Glu)": "", "检查日期": "", "尿素氮(BUN)": "", "甘油三酯(TG)": "", "同型半胱氨酸": "", "总胆固醇(TCHO)": "", "脂蛋白(a)(LP(a))": "", "载脂蛋白B(apoB)": "", "载脂蛋白A1(apoA1)": "", "低密度脂蛋白(LDL-C)": "", "糖化血红蛋白(HbA1c)": "", "高密度脂蛋白(HDL-C)": "", "高锰C反应蛋白(hs-CRP)": ""}'
                              ],
    'invitetime'          => '14',//邀请名单中有效期14天
    'registtime'          => '7',//注册有效期7天
    'loginmail'           => 'support@medicayun.cn', //邀请，注册，忘记密码要用的邮箱
    'domainurl'           => 'medicayun.net', //一级域名和后缀
    'inviteexcel'         => 'medicayun.cn-bj.ufileos.com/project-1510971912312b3570835e48eb2538a1c6988278c1.xlsx?UCloudPublicKey=ucloudzhexcel%40163.com14280235690001077411631&Signature=XYoeDldZWV5RrESvK1ZlhQsg2OQ%3D',
    'inviteexcel_name'    => '梅地卡尔临床云批量用户模版.xlsx',
    'mydomain'            =>[
                                'admin',
                                'support',
                                'contact',
                                'product',
                                'service',
                                'hospital',
                                'government',
                                'china',
                                'medicayunchina'
                            ],
];
