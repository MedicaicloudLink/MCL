<template>
    <DialogBox>
        <h3 slot="header">创建新病历</h3>
        <div slot="body" class="body">
            <div class="choose">
                <label for="name" class="label">患者姓名</label>
                <input type="text" id="name" class="input" v-model="patientName"/>
            </div>
            <div class="choose">
                <label for="sex" class="label">性别</label>
                <div>
                    <label for="boy" class="sex">男</label>
                    <input type="radio" id="boy" class="input" v-model="sex" value="1"/>
                </div>
                <div>
                    <label for="girl" class="sex">女</label>
                    <input type="radio" id="girl" class="input" v-model="sex" value="2" />
                </div>
            </div>
            <div class="choose">
                <label for="birth" class="label">出生年月</label>
                <input type="date" v-model="birthday" id="birth" class="input"/>
            </div>
            <!--<div class="choose">
                <label for="project" class="label">项目选择</label>
                <select class="input">
                    <option v-for="item in projectRecord" v-text="item.projectid"></option>
                </select>
            </div>-->
        </div>
        <div slot="footer" class="footer">
            <div class="button" :style="{background: newRecordButton ? '#42b983' : '#bbb'}" @click="addNewPatient">创建</div>
            <div class="button" @click="$emit('close')">取消</div>
        </div>
    </DialogBox>
</template>
<script>
    import DialogBox from './dialog.vue'
    export default{
      data () {
        return {
          patientName: '',
          sex: '1',
          birthday: '',
          newRecordButton: false
        }
      },
      components: {
        DialogBox
      },
      methods: {
        addNewPatient () {
          this.$emit('close')
        }
      }
    }
</script>
<style scoped>
    /*弹出框 dialog*/
  .choose{
    display: flex;
    width: 100%;
    padding: .5em 0;
  }
  .label,.sex{
    width: 5em;
    padding: .5em 0;
    font-size: .5em;
    font-family: -webkit-body;
  }
  .label ~ .input{
    flex: 1;
    padding: .2em;
    border: none;
    border: 1px #ccc solid;
    border-radius: 3px;
  }
  .label ~ div{
    padding: .2em;
  }
  .sex{
    width: 2em;
  }
  .sex ~ input{
    margin-left: .3em;
    margin-right: 1.5em;
  }
  .recordTemplate{
    flex: 1;
    padding: 0 !important;
  }
  .recordTemplate span{
    padding: .2em .5em;
    margin: .2em;
    border: 1px #ccc solid;
    border-radius: 3px;
    font-size: .5em;
    float: left;
  }
  .footer{
    width: 100%;
    text-align: right;
  }
  .button{
    width: 4em;
    line-height: 2em;
    display: inline-block;
    background: #bbb;    
    text-align: center;
    font-size: .9em;
    border-radius: 3px;
    color: #fff;
    margin-left: 2em;
  }
</style>