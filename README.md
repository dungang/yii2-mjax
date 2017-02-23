# mjax

Bootstrap Modal for Yii2 By Ajax.解决在当前页面弹出编辑页面或者展示信息，不用跳转到其他页面。

![模态框](images/mjax.gif)

## 安装

```
composer require dungang/mjax
```

## 使用

> 标记要绑定模态框的锚点

锚点标签添加 class `mjax`

```
<?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Create'), ['create'],
            ['class' => 'btn btn-primary mjax']) ?>
```

> 注册模态框

```
\dungang\mjax\Modal::widget([
    'selector'=>'.mjax',  //注册对象，默认为`.mjax`
    'options'=>[
        'refresh'=>true //关闭模态框后是否刷新当前页面
    ]
])
```

> 注意事项

`ajax`返回的表当页面的`form`不用用`ActiveForm` 默认生成的`id`编号,请手动指定具体一读唯一的编号，比如：option-form

因为发起`ajax`请求的页面的`widget`可能也是自动生成的元素的`id`，则会跟表单页面的id重复就会被覆盖，导致js事件失效

```
$form = ActiveForm::begin([
        'id'=>'option-form'
    ]); 
```