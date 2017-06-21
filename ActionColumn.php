<?php
/**
 * Author: dungang
 * Date: 2017/6/21
 * Time: 19:44
 */

namespace dungang\mjax;

use Yii;
use yii\bootstrap\Html;
use yii\grid\Column;
use yii\helpers\Url;

class ActionColumn extends Column
{
    public $enableMjax = true;
    /**
     * @inheritdoc
     */
    public $headerOptions = ['class' => 'action-column'];
    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;
    /**
     * @var string the template used for composing each cell in the action column.
     * Tokens enclosed within curly brackets are treated as controller action IDs (also called *button names*
     * in the context of action column). They will be replaced by the corresponding button rendering callbacks
     * specified in [[buttons]]. For example, the token `{view}` will be replaced by the result of
     * the callback `buttons['view']`. If a callback cannot be found, the token will be replaced with an empty string.
     *
     * As an example, to only have the view, and update button you can add the ActionColumn to your GridView columns as follows:
     *
     * ```php
     * ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
     * ```
     *
     * @see buttons
     */
    public $template = '{view} {update} {delete}';
    /**
     * @var array button rendering callbacks. The array keys are the button names (without curly brackets),
     * and the values are the corresponding button rendering callbacks. The callbacks should use the following
     * signature:
     *
     * ```php
     * function ($url, $model, $key) {
     *     // return the button HTML code
     * }
     * ```
     *
     * where `$url` is the URL that the column creates for the button, `$model` is the model object
     * being rendered for the current row, and `$key` is the key of the model in the data provider array.
     *
     * You can add further conditions to the button, for example only display it, when the model is
     * editable (here assuming you have a status field that indicates that):
     *
     * ```php
     * [
     *     'update' => function ($url, $model, $key) {
     *         return $model->status === 'editable' ? Html::a('Update', $url) : '';
     *     },
     * ],
     * ```
     */
    public $buttons = [];
    /** @var array visibility conditions for each button. The array keys are the button names (without curly brackets),
     * and the values are the boolean true/false or the anonymous function. When the button name is not specified in
     * this array it will be shown by default.
     * The callbacks must use the following signature:
     *
     * ```php
     * function ($model, $key, $index) {
     *     return $model->status === 'editable';
     * }
     * ```
     *
     * Or you can pass a boolean value:
     *
     * ```php
     * [
     *     'update' => \Yii::$app->user->can('update'),
     * ],
     * ```
     * @since 2.0.7
     */
    public $visibleButtons = [];
    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;
    /**
     * @var array html options to be applied to the [[initDefaultButtons()|default buttons]].
     * @since 2.0.4
     */
    public $buttonOptions = [];

    /**
     * @var array
     */
    protected $templateButtons = [];

    /**
     * @var array 设置某个按钮的默认配置
     */
    public $buttonsOption = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (preg_match_all('/\\{([\w\-\/]+)\\}/', $this->template, $matches)) {
            $this->templateButtons = $matches[1];
            $this->initDefaultButtons();
        }
    }

    protected function buttonOption($options, $button)
    {
        if (isset($this->buttonsOption[$button]) && is_array($this->buttonsOption[$button])) {
            $options = array_merge($options, $this->buttonsOption[$button]);
        }
        return $options;
    }

    protected function buttonNotExisted($button){
        return !isset($this->buttons[$button]) && in_array($button, $this->templateButtons);
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if($this->enableMjax) {
            MjaxAsset::register(\Yii::$app->view);
        }

        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $mjaxClass = '';
                if($this->enableMjax) {
                    $mjaxClass = 'mjax';
                }
                $options = $this->buttonOption(
                    array_merge([
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                        'data-mjax-refresh' => 'false',
                        'class' => 'btn btn-xs btn-success ' . $mjaxClass,
                    ], $this->buttonOptions),
                    'update');
                return Html::a('<span class="fa fa-pencil"></span> ' . $options['title'], $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $mjaxClass = '';
                if($this->enableMjax) {
                    $mjaxClass = 'mjax';
                }
                $options = $this->buttonOption(
                    array_merge([
                        'title' => Yii::t('yii', 'Update'),
                        'aria-label' => Yii::t('yii', 'Update'),
                        'data-pjax' => '0',
                        'data-mjax-refresh' => 'true',
                        'class' => 'btn btn-xs btn-primary ' . $mjaxClass,
                    ], $this->buttonOptions),
                    'update');
                return Html::a('<span class="fa fa-pencil"></span> ' . $options['title'], $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-danger ',
                ], $this->buttonOptions);
                return Html::a('<span class="fa fa-trash"></span> ' . $options['title'], $url, $options);
            };
        }
    }

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return Url::toRoute($params);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];

            if (isset($this->visibleButtons[$name])) {
                $isVisible = $this->visibleButtons[$name] instanceof \Closure
                    ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                    : $this->visibleButtons[$name];
            } else {
                $isVisible = true;
            }

            if ($isVisible && isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                return call_user_func($this->buttons[$name], $url, $model, $key);
            } else {
                return '';
            }
        }, $this->template);
    }
}
