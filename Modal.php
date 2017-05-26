<?php
/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/2/23
 * Time: 10:07
 */

namespace dungang\mjax;


use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JsExpression;

class Modal extends Widget
{
    public $selector = '.mjax';

    public $options;

    public function run()
    {
        $view = $this->getView();
        $this->options['pointForm'] = new JsExpression("function(){ 
                    return this.data('yiiActiveForm') != 'undefined';
                }");
        $this->options['pointEvent'] = 'beforeSubmit';
        MjaxAsset::register($view);
        $options = empty($this->options) ? '' : Json::htmlEncode($this->options);
        $js = "jQuery('$this->selector').mjax($options);";
        $view->registerJs($js);
    }
}