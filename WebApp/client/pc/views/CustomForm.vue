<template>
  <div id="customform">
    <Navigation></Navigation>
    <div class="main">
      <!--切换预览和自定义表单的按钮-->
      <MButton :type="'blue'" style="margin-top: 20px" @click="showPreview = !showPreview" v-text="showPreview ? '返回自定义' : '预览'"></MButton>
      <!--自定义表单-->
      <template v-if="!showPreview" >
        <!--自定义表单的名字和说明-->
        <div class="card" @click="addShadow('-1', $event)">
          <div class="title">
            <input class="input1" type="text" placeholder="表单标题" v-model="customFormData.formName" @focus="focus" @blur="blur" style="font-size: 28px"/>
            <div class="line" style="top: 56px"></div>
          </div>
          <div class="title">
            <textarea class="answerOption input1" type="text" placeholder="表单说明" v-model="customFormData.formMem" @focus="focus" @blur="blur" style="height: 28px;"></textarea>
            <div class="line" style="top: 35px;"></div>
          </div>
        </div>
        <!--自定义的表单域-->
        <div class="card" :class="{cardShadow: item.addShadowFlag}" @click="addShadow(index, $event)" v-for="(item, index) in customFormData.formData">
          <div class="card-header">
            <!--问题描述-->
            <div class="title">
              <input type="text" class="input1 question" :placeholder="item.placeholder" @focus="focus" @blur="blur" v-model="item.value" :autofocus="item.autofocus"/>
              <div class="line" style="top: 35px;"></div>
            </div>
            <!--表单域类型选择框 -->
            <div class="formFieldType" @click="showFormFieldDialog(index, $event)">
              <i class="iconfont" :class="item.iconType ? item.iconType : ''"></i>
              <span>{{ item.iconName }}</span>
              <i class="arrow"></i>
            </div>
            <!--表单域类型选择对话框 -->            
            <div class="chooseFormField" v-if="formTypeFlag" :style="{top: formFieldDialogTop, left: formFieldDialogLeft}">
              <div class="chooseOption" @click.stop="changeFromType($event, 'text', '文本框输入')"><i class="iconfont icon-duoxingwenben"></i><span>文本框输入</span></div>
              <div class="chooseOption" @click.stop="changeFromType($event, 'textarea', '文本域输入')"><i class="iconfont icon-duanluo"></i><span>文本域输入</span></div>
              <div class="chooseOption" @click.stop="changeFromType($event, 'radio' , '单选')"><i class="iconfont icon-danxuan"></i><span>单选</span></div>
              <div class="chooseOption" @click.stop="changeFromType($event, 'checkbox', '多选')"><i class="iconfont icon-duoxuanon"></i><span>多选</span></div>
              <div class="chooseOption" @click.stop="changeFromType($event, 'select', '下拉菜单')"><i class="iconfont icon-xialacaidan"></i><span>下拉菜单</span></div>
              <div class="chooseOption" @click.stop="changeFromType($event, 'date','日期')"><i class="iconfont icon-rili"></i><span>日期</span></div>
            </div>
          </div>
          <!--问题的选项-->
          <div class="card-body">
            <template v-if="item.type === 'text'">
              <div class="title">
                <input type="text" class="answerOption input1" placeholder='文本框输入' disabled/>
              </div>
            </template>
            <template v-else-if="item.type === 'textarea'">
              <div class="title">
                <input type="text" class="answerOption input1" placeholder='多行文本输入' disabled/>
              </div>
            </template>
            <template v-else-if="item.type === 'date'">
              <div class="question-option">
                <i class="iconfont icon-left" :class="item.iconType ? item.iconType : ''"></i>
                <div class="title">
                  <input type="text" class="answerOption input1" placeholder='年-月-日' disabled/>
                </div>
              </div>
            </template>
            <template v-else>
              <!--type = radio checkbox select-->
              <div class="question-option" v-for="(questionoption, key) in item.answerOptions">
                <i class="iconfont icon-left" :class="item.iconType ? item.iconType : ''"></i>
                <div class="title">
                  <input type="text" class="answerOption input1" placeholder='选项' @focus="focus" @blur="blur" v-model="questionoption.value"/>
                  <i class="iconfont icon-x icon-right" @click.stop="deleteOption(index, key)"></i>
                  <div class="line"></div>
                </div>
              </div>
              <!--其他选项-->
              <div class="question-option" v-if="item.addOtherOptionFlag">
                <i class="iconfont icon-left" :class="item.iconType ? item.iconType : ''"></i>
                <div class="title">
                  <input type="text" class="answerOption input1" placeholder='其他...' @focus="focus" @blur="blur" disabled/>
                  <i class="iconfont icon-x icon-right" @click.stop="item.addOtherOptionFlag = false"></i>
                  <div class="line"></div>
                </div>
              </div>
              <!--添加选项按钮-->
              <div class="question-option" v-if="item.addShadowFlag">
                <i class="iconfont icon-left" :class="item.iconType ? item.iconType : ''"></i>
                <div class="title" style="font-size: 14px;">
                  <span @click="addOption(index)" style="color: #20a0ff">添加选项</span><span style="cursor: pointer" v-if="item.type !== 'select' && !item.addOtherOptionFlag" @click="item.addOtherOptionFlag = true"> 或 添加其他</span>
                </div>
              </div>
            </template>
          </div>
          <!--表单域底部工具栏-->
          <div class="card-footer" style="text-align: right" v-if="item.addShadowFlag">
            <i class="iconfont icon-shanchu icon-right" @click.stop="deleteFormField(index)"></i> 
          </div>
        </div>
        <div class="tools">
          <MButton :type="'blue'" @click="addFormField">添加问题</MButton>
        </div>
      </template>
      <!--预览自定义的表单-->
      <div class="preview" v-else>
        <div class="card">
          <h2 class="title">{{ customFormData.formName }}</h2>
          <p class="title" style="font-size: 14px;">{{ customFormData.formMem }}</p>
        </div>
        <div class="card" v-for="item in customFormData.formData">
          <label class="title">{{ item.question }}</label>
          <div v-if="item.type === 'select'">
            <select>
              <option v-for="option in item.answerOptions">{{option.value}}</option>
            </select>
          </div>
          <div v-else v-for="option in item.answerOptions">
            <input :type="item.type"/>
            <label style="font-size: 14px;">{{option.value}}</label>
          </div>
          <div v-if="item.addOtherOptionFlag">
            <input :type="item.type"/>
            <label style="font-size: 14px;">其他：</label>
            <input type="text"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import Navigation from '../components/navigation.vue'
  import MButton from '../lib/button.vue'
  import $ from 'webpack-zepto'
  export default {
    data () {
      return {
        activeFlag: false,
        formType: false,
        showPreview: false,
        formTypeFlag: false,    // 控制显示表单域类型选择对话框
        formFieldDialogTop: '', // y轴偏移量
        formFieldDialogLeft: '', // x轴偏移量
        currentIndex: '',
        currentEvent: '',
        customFormData: {
          formName: '未命名的表单',
          formMem: '表单说明',
          formData: [
            {
              type: 'radio',
              question: '未命名的表单问题',
              iconType: 'icon-danxuan',
              iconName: '单选',
              placeholder: '问题',
              answerOptions: [{value: ''}],
              addOtherOptionFlag: false, // 添加其他选项
              // formTypeFlag: false, // 表单域选择面板
              addShadowFlag: false
            }
          ]
        }
      }
    },
    components: {
      Navigation,
      MButton
    },
    methods: {
      // input textarea 获取焦点
      focus (event) {
        $(event.target).parent().addClass('active')
        $(event.target).siblings('.line').css('width', $(event.target).width() + 'px')
      },
      // input textarea 失去焦点
      blur (event) {
        $(event.target).parent().removeClass('active')
      },
      // .card 添加阴影效果
      addShadow (index, event) {
        $(event.currentTarget).addClass('cardShadow').siblings('.card').removeClass('cardShadow')
        for (let i in this.customFormData.formData) {
          this.customFormData.formData[i].addShadowFlag = false
        }
        if (index >= 0) this.customFormData.formData[index].addShadowFlag = true
      },
      // 显示表单域选择对话框
      showFormFieldDialog (index, event) {
        let e = window.event || event
        if (this.currentIndex === index) {
          // 两次点击的事件源是同一个
          this.formTypeFlag = !this.formTypeFlag
        } else {
          this.formTypeFlag = true
        }
        this.formFieldDialogLeft = e.currentTarget.offsetLeft + 'px'
        this.formFieldDialogTop = e.currentTarget.offsetTop + e.currentTarget.offsetHeight + 'px'
        this.currentIndex = index
      },
      // 改变表单域
      changeFromType (event, type, typeName) {
        this.customFormData.formData[this.currentIndex].type = type
        this.customFormData.formData[this.currentIndex].iconType = $(event.currentTarget).find('i').attr('class').split(' ')[1]
        this.customFormData.formData[this.currentIndex].iconName = typeName
        if (type === 'text' || type === 'textarea') {
          this.customFormData.formData[this.currentIndex].answerOptions = [{value: ''}]
        }
        this.formTypeFlag = false
      },
      // 添加选项
      addOption (index) {
        this.customFormData.formData[index].answerOptions.push({value: ''})
        // 自动获取焦点
        setTimeout(function () {
          $('.card').eq(index + 1).find('.answerOption').focus()
        }, 200)
      },
      // 删除选项
      deleteOption (index, key) {
        this.customFormData.formData[index].answerOptions.splice(key, 1)
      },
      // 添加表单域
      addFormField (event) {
        let formField = {
          type: 'radio',
          question: '未命名的表单问题',
          iconType: 'icon-danxuan',
          iconName: '单选',
          placeholder: '问题',
          answerOptions: [{value: ''}],
          addOtherOptionFlag: false, // 添加其他选项
          addShadowFlag: true
        }
        for (let i in this.customFormData.formData) {
          this.customFormData.formData[i].addShadowFlag = false
        }
        this.customFormData.formData.push(formField)
        // window.sessionStorage.setItem('formData', this.formData)
        // 为每次动态添加的表单域  问题描述的input框自动获取焦点
        setTimeout(function () {
          $('.question').focus()
        }, 200)
      },
      // 删除表单域
      deleteFormField (index) {
        this.customFormData.formData.splice(index, 1)
      }
    }
  }
</script>
<style scoped>
  .main{
    width: 400px;
    margin: 64px auto;
  }
  .card{
    padding: 10px 20px;
  }
  .card.cardShadow{
    -webkit-box-shadow: 0 0 4px rgba(0,0,0,.24), 0 2px 8px rgba(0,0,0,.48);
    box-shadow: 0 0 4px rgba(0,0,0,.24), 0 2px 8px rgba(0,0,0,.48);
  }

  .card-header, .question-option, .title{
    display: flex;
    display: -webkit-flex;
    align-items: center;
  }
  
  .title{
    padding: 10px 0;
    position: relative;
    flex: 1;
  }
  .title .line{
    position: absolute;
    height: 3px;
    background: #f00;
    top: 33px;
    transition: all .2s;
    transform: scaleX(0);
  }
  .title.active .line{
    transform: scaleX(1);
    transform-origin: center;
  }
  .input1{
    border: none;
    outline: none;
    font-weight: 400;
    font-size: 16px;
    color: #000;
    border-bottom: #ccc 1px solid;
    width: 100%;  
  }
  .answerOption{
    font-size: 14px;
  }
  .formFieldType{
    padding: 5px;
    margin-left: 10px;
    background: #ddd;
    position: relative;
    cursor: pointer;
    color: #333;
    min-width: 130px;
  }
  .formFieldType span {
    margin-left: 5px;
    margin-right: 30px;
  }
  .formFieldType .arrow {
    height: 0;
    width: 0;
    border-top:10px solid #aaa;
    border-left:10px solid transparent;
    border-bottom:10px solid transparent;
    border-right:10px solid transparent;
    position: absolute;
    right: 5px;
    top: 13px;
  }
  /* 表单域类型选择框 */
  .chooseFormField{
    position: absolute;
    background: #fff;
    z-index: 999;
    padding: 5px;
    color: #666;
    line-height: 2;
    cursor: pointer;
  }
  .chooseOption span{
    margin-left: 10px;
    margin-right: 30px;
  }

  .tools{
    position: fixed;
    right: 300px;
    top: 50%;
  }
  .icon-left{
    margin-right: 10px;
    font-size: 14px;
    color: #999;
  }
  .icon-right{
    margin-left: 10px;
    font-size: 14px;
    color: #999;
    cursor: pointer;
  }
  .icon-duanluo:before { content: "\e62e"; }
  .icon-danxuan:before { content: "\e6ae"; }
  .icon-rili:before { content: "\e6eb"; }
  .icon-iconfont-copy:before { content: "\e604"; }
  .icon-duoxingwenben:before { content: "\e7e1"; }
  .icon-duoxuanon:before { content: "\e731"; }
  .icon-xialacaidan:before { content: "\e65f"; }
  .icon-danhangwenben:before { content: "\e611"; }
  .icon-danxuan1:before { content: "\e695"; }
  .icon-x:before { content: "\e629"; }
  .icon-shanchu:before { content: "\e609"; }
</style>