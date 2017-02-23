<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/2/23
 * Time: 10:07
 */

namespace dungang\mjax;


use yii\base\Widget;
use yii\helpers\Json;

class Modal extends Widget
{
    public $selector = '.mjax';

    public $options;

    public function run()
    {
        $view = $this->getView();
        MjaxAsset::register($view);
        $options = empty($this->options) ? '' : Json::htmlEncode($this->options);
        $js = "jQuery('$this->selector').mjax($options);";
        $view->registerJs($js);

    }
}