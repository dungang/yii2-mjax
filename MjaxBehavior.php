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
use yii\bootstrap\Alert;
use yii\web\Response;

class MjaxBehavior extends Behavior
{
    public $refresh = 'false';

    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the array:
     *       - class of alert type (i.e. danger, success, info, warning)
     *       - icon for alert AdminLTE
     */
    public $alertTypes = [
        'error' => [
            'class' => 'alert-danger',
            'icon' => '<i class="icon fa fa-ban"></i>',
        ],
        'danger' => [
            'class' => 'alert-danger',
            'icon' => '<i class="icon fa fa-ban"></i>',
        ],
        'success' => [
            'class' => 'alert-success',
            'icon' => '<i class="icon fa fa-check"></i>',
        ],
        'info' => [
            'class' => 'alert-info',
            'icon' => '<i class="icon fa fa-info"></i>',
        ],
        'warning' => [
            'class' => 'alert-warning',
            'icon' => '<i class="icon fa fa-warning"></i>',
        ],
    ];

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
        $view = \Yii::$app->controller->view;
        //when ajax requrest ,not reload assets
        if (!\Yii::$app->request->isAjax) {
            MjaxAsset::register($view);
        }
        //do more,do worse
//        else {
//            $view->clear();
//        }
        \Yii::$app->controller
            ->view->registerJs("$('.mjax').mjax({
                refresh: $this->refresh,
                pointForm:function(){ 
                    return this.data('yiiActiveForm') != undefined;
                },
                pointEvent: 'beforeSubmit'
            });");
    }

    public function changeRedirectCode(){
        $response = \Yii::$app->response;
        if ($this->isMjax()) {
            $response->content = $this->fetchAlter() . $response->content;
            if ($response->getStatusCode() == 302) {
                $response->setStatusCode(309,'Mjax Redirect');
                //309状态码没有被使用，所以选择此状态编码作为mjax的跳转编码
                $xRedirect =  $response->getHeaders()->get('X-Redirect');
                if ($xRedirect) {
                    $response->getHeaders()->set('X-Redirect',null);
                    $response->getHeaders()->set('X-Mjax-Redirect',$xRedirect);
                } else {
                    $location =  $response->getHeaders()->get('Location');
                    $response->getHeaders()->set('Location',null);
                    $response->getHeaders()->set('X-Mjax-Redirect',$location);
                }
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

    public function fetchAlter()
    {
        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes(true);
        $options = [];
        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;
                foreach ($data as $message) {
                    $options['class'] = $this->alertTypes[$type]['class'] ;
                    $options['id'] = 'alert-' . $type;
                    return  Alert::widget([
                        'body' => $this->alertTypes[$type]['icon'] . $message,
                        'options' => $options,
                    ]);
                }
            }
        }
        return null;
    }
}
