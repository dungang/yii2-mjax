<?php
namespace dungang\mjax;
use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/2/22
 * Time: 21:53
 */
class AjaxFormAsset extends AssetBundle {

    public $sourcePath = "@bower/jquery-form/dist";
    public $js=['jquery.form.min.js'];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}