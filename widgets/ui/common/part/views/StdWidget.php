<?php

use yii\helpers\Html;
?>
<!-- Widget begins -->
<div class="widget">
    <div class="widget-head ">
        <nav class="navbar navbar-default pull-left">
            <div class="navbar-header">
                <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed pull-left" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!--<a href="#" class="navbar-brand">Project name</a>-->
            </div>
            <div class="navbar-collapse collapse navbar-left" id="navbar">
                <?php
                echo cza\base\widgets\navigation\Nav::widget([
                    'options' => [
                        'class' => 'nav navbar-nav navbar-right'
                    ],
                    'items' => [
                        [
                            'label' => Yii::t("app", "Home"),
                            'url' => 'javascript:;',
                            'visible' => true,
                        ],
                        [
                            'label' => 'Select All',
                            'url' => 'javascript:;',
                            'icon' => 'fa fa-check-square-o',
                            'visible' => true,
                        ],
                        [
                            'label' => 'Refresh',
                            'url' => 'javascript:;',
                            'icon' => 'fa fa-refresh',
                            'visible' => true,
                        ],
                        [
                            'label' => 'Create',
                            'url' => 'javascript:;',
                            'icon' => 'fa fa-file-o',
                            'visible' => true,
                        ],
                        [
                            'label' => 'Delete',
                            'url' => 'javascript:;',
                            'icon' => 'fa fa-trash',
                            'visible' => true,
                        ],
                        [
                            'label' => Yii::t("app", "Export"),
                            'url' => 'javascript:;',
                            'icon' => 'fa fa-download',
                            'visible' => true,
                            'items' => [
                                ['label' => Yii::t("app", "Account Settings"), 'url' => ['/backoffice/my-settings'], 'linkOptions' => []],
                                '<li class="divider"></li>',
                                [
                                    'label' => Yii::t("app", "UI Language"),
                                    'url' => 'javascript:;',
                                    'items' => \cza\base\widgets\ui\common\box\LanguageSelectBox::getDropdownItems(),
                                ],
                            ],
                        ],
                    ]
                ]);
                ?>
            </div>
        </nav>
        <div class="widget-icons pull-right">
            <a class="wminimize" href="#"><i class="fa fa-chevron-up"></i></a> 
            <a class="wclose" href="#"><i class="fa fa-times"></i></a>
        </div>  
        <div class="clearfix"></div>
    </div> 

    <!-- Widget content -->
    <div class="widget-content">
        <?php
        echo $this->context->content;
        ?>
    </div>
</div>
<!-- Widget ends -->
