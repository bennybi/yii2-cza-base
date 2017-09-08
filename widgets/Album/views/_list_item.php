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
    <?= Html::img($model->getThumbnailUrl(), ['class' => 'img-circle', 'alt' => $model->alt]) ?>
    <div class="file-actions">
        <div class="file-footer-buttons">
            <?= Html::button('<i class="glyphicon glyphicon-trash text-danger"></i>', ['data-id' => $model->id, 'title' => Yii::t('app.c2', 'Delete file'), 'class' => 'kv-file-remove btn btn-xs btn-default']) ?>
            <?= Html::button('<i class="glyphicon glyphicon-edit"></i>', ['data-id' => $model->id, 'title' => Yii::t('app.c2', 'Edit'), 'class' => 'kv-file-edit btn btn-xs btn-default']) ?>
            <?php echo Html::a('<i class="glyphicon glyphicon-zoom-in"></i>', $model->getOriginalUrl(), ['data-id' => $model->id, 'title' => Yii::t('app.c2', 'View details'), 'class' => 'kv-file-zoom btn btn-xs btn-default fancybox', 'data-fancybox'=> "group",  'data-caption'=>$model->alt]) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
