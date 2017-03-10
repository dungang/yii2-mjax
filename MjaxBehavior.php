<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/2/23
 * Time: 15:10
 */

namespace dungang\mjax;


use yii\base\Behavior;
use yii\base\Module;
use yii\base\View;

class MjaxBehavior extends Behavior
{
    public function events()
    {
        return [
            Module::EVENT_BEFORE_ACTION=>'mJax',
            View::EVENT_BEGIN_PAGE=>'beginPage',
        ];
    }

    public function mJax()
    {
        \Yii::$app->controller->view->attachBehavior('mJaxBehavior',$this);
        if(\Yii::$app->request->isAjax){
            \Yii::$app->layout = '@vendor/dungang/mjax/layout';
        }
    }

    public function beginPage()
    {
        MjaxAsset::register(\Yii::$app->controller->view);
        \Yii::$app->controller->view->registerJs("$('.mjax').mjax();");
    }
}