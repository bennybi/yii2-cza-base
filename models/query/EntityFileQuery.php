<?php

namespace cza\base\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\c2\entity\EntityFile]].
 *
 * @see \common\models\c2\entity\EntityFile
 */
class EntityFileQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\c2\entity\EntityFile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\c2\entity\EntityFile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
