<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->getWidgetNameSpace() . "\\EntityManager" ?>;

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <?= "<?= \n" ?>EntityManager::widget([
        'model' => $model,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
    ]);
    ?>
</div>
