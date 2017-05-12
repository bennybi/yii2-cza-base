<?php

namespace cza\base\components\controllers\backend;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
//use yii\web\Controller;
use cza\base\filters\CmsMediaFilter;
use cza\base\behaviors\CmsMediaBehavior;
use cza\base\models\statics\ResponseDatum;

/**
 * for model CRUD usage.
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ModelController extends Controller {

    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;

    public function actions() {
        return \yii\helpers\ArrayHelper::merge(parent::actions(), [
                    'editColumn' => [                                       // identifier for your editable action
                        'class' => \kartik\grid\EditableColumnAction::className(), // action class name
                        'modelClass' => $this->modelClass, // the update model class
                    ],
                    'translation-save' => [
                        'class' => '\cza\base\components\actions\backend\TranslationSaveAction',
                        'modelClass' => $this->modelClass,
                        'checkAccess' => [$this, 'checkAccess'],
                    ],
        ]);
    }

    /**
     * inherit
     * @param type $id
     * @return Model
     */
    protected function findModel($id) {
        return null;
    }

    /**
     * accept $id or $expandRowKey as model PK params
     * compatible with Karik grid detail request params
     * @return string
     */
    public function actionDetail() {
        $request = Yii::$app->request;
        if (!is_null($id = $request->post('id', $request->post('expandRowKey')))) {
            $model = $this->retrieveModel($id);
            return $this->renderPartial('_detail', ['model' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }

    public function actionMultipleDelete(array $ids) {
        $model = $this->retrieveModel();
        $model->multipleDeleteByIds($ids);
        if (true) {
            $responseData = ResponseDatum::getSuccessDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $ids);
        } else {
            $responseData = ResponseDatum::getErrorDatum(['message' => Yii::t('cza', 'Error: operation can not finish!!')], $ids);
        }
        return $this->asJson($responseData);
    }

    public function actionDelete($id) {
        if ($this->findModel($id)->delete()) {
            $responseData = ResponseDatum::getSuccessDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $id);
        } else {
            $responseData = ResponseDatum::getErrorDatum(['message' => Yii::t('cza', 'Error: operation can not finish!!')], $id);
        }
        return $this->asJson($responseData);
    }

}
