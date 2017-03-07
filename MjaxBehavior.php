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

class MjaxBehavior extends Behavior
{
    public function events()
    {
        return [
            Module::EVENT_BEFORE_ACTION=>'mjax'
        ];
    }

    public function mjax()
    {
        if(\Yii::$app->request->isAjax){
            \Yii::$app->layout = '@vendor/dungang/mjax/layout';
        }
    }
}