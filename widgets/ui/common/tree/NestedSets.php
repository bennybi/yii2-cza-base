<?php

/**
 * refer to arogachev\tree\widgets\NestedSets
 */

namespace cza\base\widgets\ui\common\tree;

use Yii;
use cza\base\vendor\assets\jstree\TreeAsset;
use yii\base\Widget as BaseWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class NestedSets extends BaseWidget {

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $updateUrl;
    
    public $controllerPath;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $jsTreeOptions = [];

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        $clientEvents = [
            'create_node' => 'yii.tree.createNode',
            'move_node' => 'yii.tree.moveNode',
            'rename_node' => 'yii.tree.renameNode',
            'delete_node' => 'yii.tree.deleteNode',
        ];

        $model = new $this->modelClass;
        if ($model->saveState) {
            $clientEvents = array_merge($clientEvents, [
                'open_node' => 'yii.tree.openNode',
                'close_node' => 'yii.tree.closeNode',
            ]);
        }

        $items = [
            'create' => ['label' => Yii::t('cza', 'Create')],
            'rename' => ['label' => Yii::t('cza', 'Rename')],
            'remove' => ['label' => Yii::t('cza', 'Remove')],
        ];
        if ($this->updateUrl) {
            $items['update'] = ['label' => Yii::t('cza', 'Update')];
        }

        $this->jsTreeOptions = ArrayHelper::merge([
                    'clientOptions' => [
                        'core' => [
                            'themes' => [
                                'name' => 'proton',
                                'responsive' => true,
                            ],
                            'data' => [
                                'url' => Url::to([$this->controllerPath . '/get-tree', 'modelClass' => $this->modelClass]),
                            ],
                            'check_callback' => true,
                            'strings' => [
                                'New node' => Yii::t('cza', 'New node'),
                            ],
                        ],
                        'plugins' => ['contextmenu', 'dnd', 'checkbox'],
                        'contextmenu' => [
                            'items' => $items,
                        ],
                    ],
                    'clientEvents' => $clientEvents,
                        ], $this->jsTreeOptions);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function run() {
        TreeAsset::register($this->view);

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass;
        $properties = Json::encode([
                    'modelClass' => $this->modelClass,
                    'modelPk' => $model->primaryKey()[0],
                    'controllerUrl' => Url::to($this->controllerPath),
                    'updateUrl' => Url::to($this->updateUrl),
        ]);
        $this->getView()->registerJs("yii.tree.initProperties($properties);");

        echo Html::tag('div', JsTree::widget($this->jsTreeOptions), $this->options);
    }

}
