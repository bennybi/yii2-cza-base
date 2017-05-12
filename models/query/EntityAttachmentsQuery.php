<?php

namespace cza\base\models\query;

/**
 * This is the ActiveQuery class for [[\cza\base\models\entity\EntityAttachments]].
 *
 * @see \cza\base\models\entity\EntityAttachments
 */
class EntityAttachmentsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \cza\base\models\entity\EntityAttachments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \cza\base\models\entity\EntityAttachments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
