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
    <input type='hidden' id='mail' value='<?php echo $mail?>'>
    <div class="logo flex-row">
      <img src="<?php echo Yii::$app->request->baseUrl?>/imgs/logo.svg" alt="MedicaYUN LOGO">
    </div>
    <div class="body flex-col">
      <img class="img" src="<?php echo Yii::$app->request->baseUrl?>/imgs/biglogo.svg">
      <p class="des">请输入下面的信息，完成企业或组织机构帐户的创建</p>
      <div class="form flex-col" style="width: 480px;">
        <div class="form-option flex-col">
          <label>姓名<span class="keep">*</span><span class="res_hint">(请输入不多于20个汉字字符或者40个英文字符)</span></label>
          <div class="flex-row" style="position: relative;">
            <input type="text" style="width: 100%;" id="name" placeholder="您的姓名">
            <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
            <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>创建登录密码<span class="keep">*</span><span class="res_hint">(请输入不少于6位的密码)</span></label>
          <div class="flex-row" style="position: relative;">
            <input style="width: 100%;" type="password" id="password" placeholder="请输入不少于6位的密码">
            <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
            <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>企业或组织机构名称<span class="keep">*</span><span class="res_hint">(请输入不多于30个汉字字符或者60个英文字符)</span></label>
          <div class="flex-row" style="position: relative;">
            <input type="text" style="width: 100%;" id="companyName" placeholder="您所在的单位的名称">
            <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
            <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>设置企业的二级域名<span class="keep">*</span><span class="res_hint"></span></label>
          <div class="flex-row">
            <div class="flex-row" style="position: relative; flex: 1;">
              <input type="text" style="width: 100%;" id="secondName"  placeholder="可以输入英文，数字，横线‘-’，和下划线‘_’">
              <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
              <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
            </div>
            <span class="fixed-content" id="secondName" style="width: 152px;" >.medicayun.com</span>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>所在城市或地区<span class="keep">*</span></label>
          <div class="flex-row between">
            <select id="province"><option value="">-请选择省份-</option></select>
            <select id="city"><option value="">-请选择市(区)-</option></select>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>手机号码<span class="keep">*</span><span class="res_hint">(请输入正确的11位手机号)</span></label>
          <div class="flex-row">
            <span class="fixed-content" style="width: 120px;">+86(中国)</span>
            <div class="flex-row" style="position: relative; flex: 1;">
              <input type="text" id="phone" style="width: 100%;" placeholder="您的手机电话号码">
              <img class="msg-icon succ" src="<?php echo Yii::$app->request->baseUrl?>/imgs/succ.svg"/>
              <img class="msg-icon err" src="<?php echo Yii::$app->request->baseUrl?>/imgs/err.svg"/>
            </div>
          </div>
        </div>
        <div class="form-option flex-col">
          <label>所属部门<span class="keep">*</span></label>
          <input type="text" id="department" placeholder="您所属的部门">
        </div>
        <div class="check">
          <input type="checkbox" id="check">
          <label for="check">我已阅读并理解梅地卡尔公司，梅地卡尔临床数据云平台的“<a href="/login/useterms" target="_black">使用条款</a>”和“<a href="/login/privacy" target="_black">隐私条款</a>”，我代表所在的企业（或组织机构）同意并接受上述协议和条款。</label>
        </div>
      </div>
      <span id="msg"></span>
      <div class="tools flex-row"><span class="btn btn-blue disable" id="subBtn">创建账户</span></div>
    </div>
  </div>

  <script src="<?php echo Yii::$app->request->baseUrl?>/api/js/jquery.js" type="text/javascript"></script>

  <script>
    $(document).ready(function () {
      // enter健 => tab健
      var elems = $('input[type=text], input[type=password], select')
      var index = 0
      elems.eq(index).focus();
      $('.form').on('keyup', function (e) {
        if (e.keyCode == '13') {
          index ++;
          if (index == elems.length) elems.eq(index - 1).blur();
          else elems.eq(index).focus();
        }
      })
      // 判断是第几个input 获取的焦点
      $('.form').on('focus', ('input[type=text], input[type=password], select'), function (e) {
        var elemsid = []
        $('input[type=text], input[type=password], select').each(function () {
          elemsid.push($(this).attr('id'))
        })
        index = elemsid.indexOf(document.activeElement.id)
      })
      // 地址数据
      var adderssData = [
        {
          'province': '北京',
          'city': ['东城区', '西城区', '崇文区', '宣武区', '朝阳区', '丰台区', '石景山区', '海淀区', '门头沟区', '房山区', '通州区', '顺义区', '昌平区', '大兴区', '怀柔区', '平谷区', '密云县', '延庆县']
        },
        {
          'province': '上海',
          'city': ['黄浦区', '卢湾区', '徐汇区', '长宁区', '静安区', '普陀区', '闸北区', '虹口区', '杨浦区', '闵行区', '宝山区', '嘉定区', '浦东新区', '金山区', '松江区', '青浦区', '南汇区', '奉贤区', '崇明县']
        },
        {
          'province': '天津',
          'city': ['和平区', '河东区', '河西区', '南开区', '河北区', '红桥区', '塘沽区', '汉沽区', '大港区', '东丽区', '西青区', '津南区', '北辰区', '武清区', '宝坻区', '宁河县', '静海县', '蓟县']
        },
        {
          'province': '重庆',
          'city': ['万州区', '涪陵区', '渝中区', '大渡口区', '江北区', '沙坪坝区', '九龙坡区', '南岸区', '北碚区', '万盛区', '双桥区', '渝北区', '巴南区', '黔江区', '长寿区', '綦江县', '潼南县', '铜梁县', '大足县', '荣昌县', '璧山县', '梁平县', '城口县', '丰都县', '垫江县', '武隆县', '忠　县', '开　县', '云阳县', '奉节县', '巫山县', '巫溪县', '石柱土家族自治县', '秀山土家族苗族自治县', '酉阳土家族苗族自治县', '彭水苗族土家族自治县', '江津市', '合川市', '永川市', '南川市']
        },
        {
          'province': '四川',
          'city': ['成都市', '自贡市', '泸州市', '德阳市', '绵阳市', '广元市', '遂宁市', '内江市', '乐山市', '南充市', '眉山市', '宜宾市', '广安市', '达州市', '雅安市', '巴中市', '资阳市', '阿坝藏族羌族自治州', '甘孜藏族自治州', '凉山彝族自治州']
        },
        {
          'province': '贵州',
          'city': ['贵阳市', '六盘水市', '遵义市', '安顺市', '铜仁地区', '黔西南布依族苗族自治州', '毕节', '黔东南苗族侗族自治州', '黔南布依族苗族自治州']
        },
        {
          'province': '云南',
          'city': ['昆明市', '曲靖市', '玉溪市', '保山市', '昭通市', '丽江市', '思茅市', '临沧市', '楚雄彝族自治州', '红河哈尼族彝族自治州', '文山壮族苗族自治州', '西双版纳傣族自治州', '大理白族自治州', '德宏傣族景颇族自治州', '怒江傈僳族自治州', '迪庆藏族自治州']
        },
        {
          'province': '西藏',
          'city': ['拉萨市', '昌都', '山南', '日喀则', '那曲', '阿里', '林芝']
        },
        {
          'province': '河南',
          'city': ['郑州市', '开封市', '洛阳市', '平顶山市', '安阳市', '鹤壁市', '新乡市', '焦作市', '濮阳市', '许昌市', '漯河市', '三门峡市', '南阳市', '商丘市', '信阳市', '周口市', '驻马店市']
        },
        {
          'province': '湖北',
          'city': ['武汉市', '黄石市', '十堰市', '宜昌市', '襄樊市', '鄂州市', '荆门市', '孝感市', '荆州市', '黄冈市', '咸宁市', '随州市', '恩施土家族苗族自治州', '省直辖行政单位']
        },
        {
          'province': '湖南',
          'city': ['长沙市', '株洲市', '湘潭市', '衡阳市', '邵阳市', '岳阳市', '常德市', '张家界市', '益阳市', '郴州市', '永州市', '怀化市', '娄底市', '湘西土家族苗族自治州']
        },
        {
          'province': '广东',
          'city': ['广州市', '韶关市', '深圳市', '珠海市', '汕头市', '佛山市', '江门市', '湛江市', '茂名市', '肇庆市', '惠州市', '梅州市', '汕尾市', '河源市', '阳江市', '清远市', '东莞市', '中山市', '潮州市', '揭阳市', '云浮市']
        },
        {
          'province': '广西',
          'city': ['南宁市', '柳州市', '桂林市', '梧州市', '北海市', '防城港市', '钦州市', '贵港市', '玉林市', '百色市', '贺州市', '河池市', '来宾市', '崇左市']
        },
        {
          'province': '陕西',
          'city': ['西安市', '宝鸡市', '咸阳市', '渭南市', '延安市', '汉中市', '榆林市', '安康市', '商洛市']
        },
        {
          'province': '甘肃',
          'city': ['兰州市', '嘉峪关市', '金昌市', '白银市', '天水市', '武威市', '张掖市', '平凉市', '酒泉市', '庆阳市', '定西市', '陇南市', '临夏回族自治州', '甘南藏族自治州']
        },
        {
          'province': '青海',
          'city': ['西宁市', '海东地区', '海北藏族自治州', '黄南藏族自治州', '海南藏族自治州', '果洛藏族自治州', '玉树藏族自治州', '海西蒙古族藏族自治州']
        },
        {
          'province': ' 宁夏',
          'city': ['银川市', '石嘴山市', '吴忠市', '固原市', '中卫市']
        },
        {
          'province': '新疆',
          'city': ['乌鲁木齐市', '克拉玛依市', '吐鲁番', '哈密', '昌吉回族自治州', '博尔塔拉蒙古自治州', '巴音郭楞蒙古自治州', '阿克苏', '克孜勒苏柯尔克孜自治州', '喀什', '和田', '伊犁哈萨克自治州', '塔城', '阿勒泰', '石河子市', '阿拉尔市', '图木舒克市', '五家渠市']
        },
        {
          'province': '河北',
          'city': ['石家庄市', '唐山市', '秦皇岛市', '邯郸市', '邢台市', '保定市', '张家口市', '承德市', '沧州市', '廊坊市', '衡水市']
        },
        {
          'province': '山西',
          'city': ['太原市', '大同市', '阳泉市', '长治市', '晋城市', '朔州市', '晋中市', '运城市', '忻州市', '临汾市', '吕梁市']
        },
        {
          'province': '内蒙古',
          'city': ['呼和浩特市', '包头市', '乌海市', '赤峰市', '通辽市', '鄂尔多斯市', '呼伦贝尔市', '巴彦淖尔市', '乌兰察布市', '兴安盟', '锡林郭勒盟', '阿拉善盟']
        },
        {
          'province': '江苏',
          'city': ['南京市', '无锡市', '徐州市', '常州市', '苏州市', '南通市', '连云港市', '淮安市', '盐城市', '扬州市', '镇江市', '泰州市', '宿迁市']
        },
        {
          'province': '浙江',
          'city': ['杭州市', '宁波市', '温州市', '嘉兴市', '湖州市', '绍兴市', '金华市', '舟山市', '台州市', '丽水市']
        },
        {
          'province': '安徽',
          'city': ['合肥市', '芜湖市', '蚌埠市', '淮南市', '马鞍山市', '淮北市', '铜陵市', '安庆市', '黄山市', '滁州市', '阜阳市', '宿州市', '巢湖市', '六安市', '亳州市', '池州市', '宣城市']
        },
        {
          'province': '福建',
          'city': ['福州市', '厦门市', '莆田市', '三明市', '泉州市', '漳州市', '南平市', '龙岩市', '宁德市']
        },
        {
          'province': '江西',
          'city': ['南昌市', '景德镇市', '萍乡市', '九江市', '新余市', '鹰潭市', '赣州市', '吉安市', '宜春市', '抚州市', '上饶市']
        },
        {
          'province': '山东',
          'city': ['济南市', '青岛市', '淄博市', '枣庄市', '东营市', '烟台市', '潍坊市', '济宁市', '泰安市', '威海市', '日照市', '莱芜市', '临沂市', '德州市', '聊城市', '滨州市', '荷泽市']
        },
        {
          'province': '辽宁',
          'city': ['沈阳市', '大连市', '鞍山市', '抚顺市', '丹东市', '锦州市', '营口市', '阜新市', '辽阳市', '盘锦市', '铁岭市', '朝阳市', '葫芦岛市']
        },
        {
          'province': '吉林',
          'city': ['长春市', '吉林市', '四平市', '辽源市', '通化市', '白山市', '松原市', '白城市', '延边朝鲜族自治州']
        },
        {
          'province': '黑龙江',
          'city': ['哈尔滨市', '齐齐哈尔市', '鸡西市', '鹤岗市', '双鸭山市', '大庆市', '伊春市', '佳木斯市', '七台河市', '牡丹江市', '黑河市', '绥化市', '大兴安岭地区']
        },
        {
          'province': '海南',
          'city': ['海口市', '三亚市', '五指山市', '琼海市', '儋州市', '文昌市', '万宁市', '东方市', '定安县', '屯昌县', '澄迈县', '临高县', '白沙黎族自治县', '昌江黎族自治县', '乐东黎族自治县', '陵水黎族自治县', '保亭黎族苗族自治县', '琼中黎族苗族自治县', '西沙群岛', '南沙群岛', '中沙群岛的岛礁及其海域']
        },
        {
          'province': '台湾',
          'city': ['台湾']
        },
        {
          'province': '香港',
          'city': ['香港']
        },
        {
          'province': '澳门',
          'city': ['澳门']
        }
      ]
      // 省份
      for(var i = 0; i < adderssData.length; i++){
        var option = new Option(adderssData[i].province, adderssData[i].province);
        $('#province').append(option);
      }
      // 改变省份，显示相对应的市
      $('#province').change(function () {
        $('#city').empty().append('<option value="">-请选择市(区)-</option>')
        for(var i = 0; i < adderssData.length; i++) {
          if ($('#province').val() == adderssData[i].province) {
            for(var j = 0; j < adderssData[i].city.length; j++) {
              var option = new Option(adderssData[i].city[j], adderssData[i].city[j]);
              $('#city').append(option);
            }
            break;
          }
        }
      });
      // 名字
      $('#name').blur(function () { valLength($(this), 40) });
      // 密码
      $('#password').blur(function () {
        verifyInput($(this), 5, 'gt')
      });
      // 企业名称
      $('#companyName').blur(function () { valLength($(this), 60) });
      // 企业二级域名
      $('#secondName').blur(function () {
        if (!$(this).val()) return
        var reg = new RegExp(/^[A-Za-z\d-_]+$/);
        var value = $(this).val()
        var that = $(this)
        var obj = $(this).parent().parent().siblings().children('.res_hint')
        if (!reg.test(value)) {
          trueOrFalse('err', that, that.parent())
          obj.html('(二级域名只能输入字母、数字、‘-’、‘_’)');
          return
        }
        // 验证是否被占用
        $.ajax({
          url: '<?php echo Yii::$app->urlManager->createUrl(['company/domainstate']) ?>',
          data: {'domain': value},
          type: 'post',
          success: function (rep) {
            var response = window.JSON.parse(rep); 
            if (response.code == 'err') {
              trueOrFalse('err', that, that.parent())
              obj.html('(此二级域名已被占用)');
            } else if (response.code == 'succ') {
              trueOrFalse('succ', that, that.parent())
              obj.html('');
            }
          }
        })
      })
      // phone
      $('#phone').keydown(function (event) {
        var e = event || window.event;
        var value = e.target.value
        // 第一位只能输入‘1’
        if (value.length === 0 && e.keyCode !== 49 && e.keyCode !== 97) e.preventDefault()
        // maxLength = 11
        if (value.length > 10 && e.keyCode !== 8) e.preventDefault()
      });
      $('#phone').blur(function() {
        verifyInput($(this), 10, 'gt',$(this).parent())
      });
      // 阅读协议
      $('#check').click(function () {
        if ($("input[type='checkbox']").is(':checked')) {
          $('#subBtn').removeClass('disable')
        } else {
          $('#subBtn').addClass('disable')
        }
      })
      // 注册
      $('#subBtn').click(function () {
        if ($(this).hasClass('disable')) return
        var name     = $('#name').val()
        var password = $('#password').val()
        var company = $('#companyName').val()
        var secondWWW = $('#secondName').val()
        var province = $('#province').val()
        var city = $('#city').val()
        var phone = $('#phone').val()
        var depart = $('#department').val()
        var mail = $('#mail').val()
        if (!(name && password && company && secondWWW && province && city && phone && depart)) {
          $('#msg').html('*为必填项')
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
          url: '<?php echo Yii::$app->urlManager->createUrl(['company/createcompany']) ?>',
          data: {'mail': mail, 'username': name, 'password': password, 'companyname': company, 'domain': secondWWW, 'city': JSON.stringify({province: province, city: city}), 'mobile': phone, 'department': depart},
          type: 'post',
          success: function (rep) {
            var response = window.JSON.parse(rep); 
            $('#msg').show();
            if(response.code == 'succ'){
              window.sessionStorage.setItem('token', response.data.token)
              location.href= 'http://' + response.data.domain + '/company/invitefriends/?userid=' + response.data.userid + '&type=1';
            } else if(response.code == 'err'){
              that.html('账号创建').removeClass('disable')
              if (response.status == '1050') {
                $('#msg').html('此邮箱已被注册')
              } else if (response.status == '1051') {
                $('#msg').html('此邮箱在邀请名单中')
              } else {
                $('#msg').html('创建失败')
              }
            }
          },
          error: function () {
            $('#msg').html('创建失败')
            that.html('账号创建').removeClass('disable')
          }
        })
      });
    });
    // 汉字 字母长度的判断
    function valLength (obj, length) {
      // if (!obj.val()) return
      if ((new RegExp(/^[\u4e00-\u9fa5]+$/)).test(obj.val().trim())) {
        verifyInput(obj, Math.ceil(length/2), 'lt')
      } else if (obj.val().length > length) {
        trueOrFalse('err', obj)
      } else if (obj.val().length > 0){
        trueOrFalse('succ', obj)
      }
    }
    // 验证输入
    function verifyInput (obj, length, gt, resObj) {
      var type
      if (gt == 'gt') {
        type = obj.val().length > length ? 'succ' : 'err'
      } else if (gt == 'lt') {
        type = obj.val().length < length ? 'succ' : 'err'
      }
      trueOrFalse(type, obj, resObj)
    }
    function trueOrFalse (type, obj, resObj) {
      if (!obj.val()) return
      if (!resObj) resObj = obj
      if (type == 'succ') {
        resObj.parent('div').siblings('label').children('.res_hint').hide()
        obj.css('border-color', 'rgba(0,0,0,.12)')
        obj.siblings('.err').hide().siblings('.succ').show()
      } else if (type == 'err') {
        resObj.parent('div').siblings('label').children('.res_hint').show()
        obj.css('border-color', '#e51c23')
        obj.siblings('.err').show().siblings('.succ').hide()
      }
    }
  </script>
</body>
</html>
<style>
  select {
    width: 232px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance:none;
    position: relative;  
    background: #fff url("<?php echo Yii::$app->request->baseUrl?>/imgs/icon_triangle_8.svg") no-repeat right center;
  }
</style>