# 接口

#### 登录
user/login
* username
* password

#### 参与项目
user/projectinfo
* userid

#### 项目成员
user/projectmember
* projectid

#### 添加患者
user/addpatient
* name
* gender
* birthday
* projectid
* userid

#### 项目患者列表
user/getprojectlist
* projectid
* userid

#### 患者基本信息
user/getpatientbase
* patientid

#### 项目模板
user/gettemplate
* projectid

#### 获取所有记录
user/getrecords
* patientid

#### 添加记录
user/createrecord
* templateid
* content
* userid
* patientid

#### 添加好友请求
user/addfriend
* addemail
* userid

#### 通知信息
user/getmessage
* userid

#### 确认添加好友
user/confirmrelation
* messageid

#### 所有好友
user/getfriends
* userid
