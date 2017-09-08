<?php

/**
 * vars
 * $key / $index / $model
 */
use yii\helpers\Html;

//$theme = $this->theme;
//Yii::info($key);
//Yii::info($index);
//Yii::info($model->name);
?>
<div class="widget-user-image">
    <i class="fa fa-file fa-5" aria-hidden="true"></i>
    <?= Html::tag('span', $model->name, ['class' => '']) ?>
    <span class="pull-right">
        <span class="file-footer-buttons">
            <?= Html::button('<i class="glyphicon glyphicon-trash text-danger"></i>', ['data-id' => $model->id, 'title' => Yii::t('app.c2', 'Delete file'), 'class' => 'kv-file-remove btn btn-xs btn-default']) ?>
            <?= Html::button('<i class="glyphicon glyphicon-edit"></i>', ['data-id' => $model->id, 'title' => Yii::t('app.c2', 'Edit'), 'class' => 'kv-file-edit btn btn-xs btn-default']) ?>
        </span>
        <span class="clearfix"></span>
    </span>
</div>
