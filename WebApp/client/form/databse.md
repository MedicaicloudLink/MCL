### 数据库设计
```
表单列表 forms

userid  // 所属用户
formid  // 表单id
name  // 表单名称
sourcedata    // form结构，json 类型
create_time  // 创建时间
update_time   // 更新时间
create_userid   // 创建人
update_userid   // 更新人
share_userid    // 分享人
state           // 状态： 1： 未发布， 2： 已发布
```

功能接口
1. 创建表单
2. 更新表单
3. 删除表单
4. 表单列表
5. 分享表单



 <div class="preview-form" v-show="preview">
    <div class="item" v-for="(qa, qaid) in preview_list">
      <div class="section">
        <p v-if="qa.type === 'SECTION'">{{ qa.title }}</p>

        <div class="questions" v-else>

          <div class="q flex-row" v-if="qa.show">
            <p>{{ qa.title }}</p>
          </div>
          <div class="a-item" v-if="qa.show">
            <!--单选-->
            <p-multiple-choice :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'RADIO'" @next="nextBack(arguments[0], qa.id)"></p-multiple-choice>
            <!--单选 end-->

            <!--地址-->
            <p-address v-model="qa.value" :option="qa.answers" v-if="qa.type === 'ADDRESS'"></p-address>
            <!--地址 end-->

            <!--多选-->
            <p-checkboxes :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'CHECKBOX'"></p-checkboxes>
            <!--多选 end-->

            <!--下拉选择-->
            <p-dropdowns :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'DROPDOWN'"></p-dropdowns>
            <!--下拉选择 end-->

            <!--表格选择-->
            <p-table :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'TABLE'"></p-table>
            <!--表格选择 end-->

            <!--线性量表选择-->
            <p-linear-scale :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'LINEARSCALE'"></p-linear-scale>
            <!--线性量表选择 end-->

            <!--时间选择-->
            <p-time :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'TIME'"></p-time>
            <!--时间选择 end-->

            <!--日期选择-->
            <p-date :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'DATE'"></p-date>
            <!--日期选择 end-->

            <!--简短回答-->
            <p-short-text :value.sync="qa.value" :option="qa.answers" v-if="qa.type === 'SHORTTEXT'"></p-short-text>
            <!--简短回答 end-->

            <!--段落回答-->
            <p-long-text :value.sync="qa.value" v-if="qa.type === 'LONGTEXT'"></p-long-text>
            <!--段落回答 end-->
          </div>
        </div>

      </div>
    </div>
  </div>