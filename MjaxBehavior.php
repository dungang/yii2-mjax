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
use yii\web\Response;

class MjaxBehavior extends Behavior
{
    public function events()
    {
        return [
            Module::EVENT_BEFORE_ACTION=>'startMjax',
            View::EVENT_BEGIN_PAGE=>'beginPage',
            Response::EVENT_AFTER_PREPARE => 'changeRedirectCode'
        ];
    }

    public function startMjax()
    {
        \Yii::$app->controller->view->attachBehavior('mJaxBehavior',$this);
        \Yii::$app->response->attachBehavior('mJaxBehavior',$this);
        if($this->isMjax()){
            \Yii::$app->controller->layout = '@vendor/dungang/yii2-mjax/layout';
        }
    }

    public function beginPage()
    {
        MjaxAsset::register(\Yii::$app->controller->view);
        \Yii::$app->controller
            ->view->registerJs("$('.mjax').mjax({
                pointForm:function(){ 
                    return this.data('yiiActiveForm') != 'undefined';
                },
                pointEvent: 'beforeSubmit'
            });");
    }

    public function changeRedirectCode(){

        if ($this->isMjax() && \Yii::$app->response->getStatusCode() == 302) {
            \Yii::$app->response->setStatusCode(309,'Mjax Redirect');
            //309状态码没有被使用，所以选择此状态编码作为mjax的跳转编码
            $xRedirect =  \Yii::$app->response->getHeaders()->get('X-Redirect');
            if ($xRedirect) {
                \Yii::$app->response->getHeaders()->set('X-Redirect',null);
                \Yii::$app->response->getHeaders()->set('X-Mjax-Redirect',$xRedirect);
            } else {
                $location =  \Yii::$app->response->getHeaders()->get('Location');
                \Yii::$app->response->getHeaders()->set('Location',null);
                \Yii::$app->response->getHeaders()->set('X-Mjax-Redirect',$location);
            }
        }
    }

    public function isMjax()
    {
        $headers = \Yii::$app->request->getHeaders();
        if (isset($headers['X-Mjax-Request'])) {
            return true;
        }
        return false;
    }
}
