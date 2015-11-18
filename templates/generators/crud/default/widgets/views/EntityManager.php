<?php
echo "<?php\n";
?>

echo $this->context->getTabWidget([
    'options' => ['id' => $this->context->getTabId()],
    'items' => [
        [
            'label' => Yii::t('app', 'Cms Page List'),
            'content' => $this->context->controller->renderPartial('_admin', [
                'model' => $this->context->model,
                'dataProvider' => $this->context->dataProvider,
                'searchModel' => $this->context->searchModel,
            ]),
            'active' => true,
        ],
    ],
]);