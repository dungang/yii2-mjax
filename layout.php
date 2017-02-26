<?php
/**
 * User: dungang
 * Date: 2017/2/23
 * Time: 15:00
 * @var $content string
 */
use dungang\mjax\Alert;
$this->beginPage();
$this->head();
echo Alert::widget();
echo $content;
$this->endBody();
$this->endPage(true);