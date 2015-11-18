<?php

namespace cza\base\components\controllers\backend;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\behaviors\BlameableBehavior;

//use cza\base\filters\CmsMediaFilter;
use cza\base\behaviors\CmsMediaBehavior;

/**
 * service cms content control
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class CmsController extends ModelController {

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'translation-save' => [
                'class' => '\cza\base\components\actions\backend\TranslationSaveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'image-list' => [
                'class' => '\cza\base\components\actions\backend\CmsImageListAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'uploadPath' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(true),
                'uploadUrl' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(),
                'whiteExts' => Yii::$app->params['config']['upload']['imageWhiteExts'],
            ],
            'image-upload' => [
                'class' => '\cza\base\components\actions\backend\CmsImageUploadAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'uploadPath' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(true),
                'uploadUrl' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(),
                'whiteExts' => Yii::$app->params['config']['upload']['imageWhiteExts'],
                'uploadCreate' => true,
            ],
            'file-list' => [
                'class' => '\cza\base\components\actions\backend\CmsFileListAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'uploadPath' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(true),
                'uploadUrl' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(),
                'whiteExts' => Yii::$app->params['config']['upload']['fileWhiteExts'],
            ],
            'file-upload' => [
                'class' => '\cza\base\components\actions\backend\CmsFileUploadAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'uploadPath' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(true),
                'uploadUrl' => Yii::$app->czaHelper->folderOrganizer->getCmsUploadTempPath(),
                'whiteExts' => Yii::$app->params['config']['upload']['fileWhiteExts'],
                'uploadCreate' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return \yii\helpers\ArrayHelper::merge(parent::verbs(), [
                    'index' => ['GET', 'HEAD'],
                    'view' => ['GET', 'HEAD'],
                    'create' => ['GET', 'POST', 'PUT', 'PATCH'],
                    'update' => ['GET', 'POST', 'PUT', 'PATCH'],
                    'delete' => ['GET', 'POST', 'DELETE'],
        ]);
    }

    /**
     * inherit
     * @return array
     */
    protected function getCmsFields() {
        return [];
    }

    /**
     * 
     * @param type $id
     * @param type $allowReturnNew
     * @return \cza\base\components\controllers\backend\modelClass
     * @throws NotFoundHttpException
     */
    public function retrieveModel($id = null, $allowReturnNew = true) {
        $model = parent::retrieveModel($id, $allowReturnNew);

        $model->attachBehavior('BlameableBehavior', [
            'class' => BlameableBehavior::className(),
        ]);

        $cmsFields = $this->getCmsFields();
        if (!empty($cmsFields)) {
            $model->attachBehavior('CmsMediaBehavior', [
                'class' => CmsMediaBehavior::className(),
                'fields' => $cmsFields,
                    //                'options' => array('handleImages' => true),
                    //                'config' => array(
                    //                    'srcBasePath' => Yii::getAlias('@webroot'),
                    //                    'dstBasePath' => Yii::$app->czaHelper->folderOrganizer->getEntityUploadPath(true),
                    //                    'dstUrlBase' => Yii::$app->czaHelper->folderOrganizer->getEntityUploadPath(),
                    //                ),
            ]);
        }
        return $model;
    }

}
