<?php
/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/2/23
 * Time: 13:30
 */

namespace dungang\mjax;


use yii\web\AssetBundle;

class MjaxAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery.mjax/src';

    public $js = ['jquery.mjax.js'];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'dungang\mjax\AjaxFormAsset'
    ];
}
