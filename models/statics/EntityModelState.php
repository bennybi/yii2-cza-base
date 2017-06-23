<?php

namespace cza\base\models\statics;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Entity Model State
 *
 * @author ben
 */
class EntityModelState {

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    protected static $_data;

    /**
     * 
     * @param type $id
     * @param type $attr
     * @return string|array
     */
    public static function getData($id = '', $attr = '') {
        if (is_null(static::$_data)) {
            static::$_data = [
                static::STATUS_ENABLE => ['id' => static::STATUS_ENABLE, 'label' => Yii::t('app.c2', 'Enable')],
                static::STATUS_DISABLE => ['id' => static::STATUS_DISABLE, 'label' => Yii::t('app.c2', 'Disable')],
            ];
        }
        if (!empty($id) && !empty($attr)) {
            return static::$_data[$id][$attr];
        }
        if (!empty($id) && empty($attr)) {
            return static::$_data[$id];
        }
        return static::$_data;
    }

    public static function getLabel($id) {
        return static::getData($id, 'label');
    }

    public static function getHashMap($keyField, $valField) {
        $key = __CLASS__ . Yii::$app->language . $keyField . $valField;
        $data = Yii::$app->cache->get($key);

        if ($data === false) {
            $data = ArrayHelper::map(static::getData(), $keyField, $valField);
            Yii::$app->cache->set($key, $data);
        }

        return $data;
    }

}
