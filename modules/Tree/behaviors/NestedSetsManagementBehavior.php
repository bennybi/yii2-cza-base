<?php

/**
 * refer to arogachev\tree\behaviors\NestedSetsManagementBehavior
 */

namespace cza\base\modules\Tree\behaviors;

use yii\base\Behavior;

class NestedSetsManagementBehavior extends Behavior {

    /**
     * @var string
     */
    public $nameAttribute = 'name';

    /**
     * @var string
     */
    public $isOpenedAttribute = 'is_opened';

    /**
     * @var boolean
     */
    public $saveState = false;

    /**
     * @return array
     */
    public function getHierarchicalArray() {
        /* @var $modelClass \yii\db\ActiveRecord */
        $modelClass = $this->owner->className();
        /* @var $baseModel \creocoder\nestedsets\NestedSetsBehavior */
        $baseModel = new $this->owner;
        $models = $modelClass::find()
                ->orderBy($baseModel->leftAttribute)
                ->all();

        $depth = 0;
        $c = 0;
        $result = [];
        $path = [];

        foreach ($models as $model) {
            $depthValue = $model->{$baseModel->depthAttribute};

            if ($depthValue > $depth) {
                $c = 0;
            } elseif ($depthValue < $depth) {
                for ($i = $depth - $depthValue; $i; $i--) {
                    unset($path[count($path) - 1]);
                }

                $c = $path[count($path) - 1] + 1;
            }

            $path[$depthValue] = $c;

            $value = [
                'id' => $model->primaryKey,
                'text' => $model->{$model->nameAttribute},
            ];

            if ($this->saveState) {
                $value['state'] = [
                    'opened' => $model->{$model->isOpenedAttribute},
                    'disabled' => $model->isRoot(),
                ];
            }

            $this->saveByPath($result, $path, $value);

            $depth = $depthValue;

            $c++;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getFullPathsList() {
        return $this->fillPath($this->getHierarchicalArray());
    }

    /**
     * @param array $items
     * @param array $path
     * @param array $result
     * @return array
     */
    protected function fillPath($items, &$path = [], &$result = []) {
        foreach ($items as $item) {
            $result[$item['id']] = implode(' / ', array_merge($path, [$item['text']]));

            if (isset($item['children'])) {
                $path[] = $item['text'];
                $this->fillPath($item['children'], $path, $result);
            }

            if ($item === end($items)) {
                array_pop($path);
            }
        }

        return $result;
    }

    protected function saveByPath(&$array, $path, $value, $childrenNodeName = 'children') {
        $temp = &$array;
        $keysCount = count($path);
        $c = 1;

        foreach ($path as $key) {
            if ($c == $keysCount || $childrenNodeName === false) {
                $temp = &$temp[$key];
            } else {
                $temp = &$temp[$key][$childrenNodeName];
            }

            $c++;
        }

        $temp = $value;
        unset($temp);
    }

}
