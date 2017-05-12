<?php

namespace cza\base\modules\Attachments\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use cza\base\models\entity\EntityAttachments;
use cza\base\modules\Attachments\ModuleTrait;
use cza\base\models\ActiveRecord;
use yii\web\HttpException;

class FileController extends Controller {

    use ModuleTrait;

    public function actionUpload() {
        if (is_null($modelClass = \Yii::$app->request->post('entityModelClass'))) {
            throw new HttpException(404, Yii::t('cza', "Parameter 'entityModelClass' is required!"));
        }
        if (is_null($attribute = \Yii::$app->request->post('attribute'))) {
            throw new HttpException(404, Yii::t('cza', "Parameter 'attribute' is required!"));
        }

        if (empty($id = \Yii::$app->request->post('id'))) {
            $model = new $modelClass();
        } else {
            $model = $modelClass::find()->where([ 'id' => $id,])->one();
        }

        $model->scenario = ActiveRecord::SCENARIO_UPLOAD;
        $model->$attribute = UploadedFile::getInstances($model, $attribute);

        if ($model->$attribute && $model->validate([$attribute])) {
            $result['uploadedFiles'] = [];
            if (is_array($model->$attribute)) {
                foreach ($model->$attribute as $file) {
                    $path = $this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $file->name;
                    $file->saveAs($path);
                    $result['uploadedFiles'][] = $file->name;
                    if (!empty($id)) {
                        $model->attachFile($path, $attribute);
                    }
                }
            } else {
                $path = $this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $model->$attribute->name;
                $model->$attribute->saveAs($path);
                $result['uploadedFiles'][] = $model->attach_files->name;
                if (!empty($id)) {
                    $model->attachFile($path, $attribute);
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'error' => $model->getErrors($attribute),
            ];
        }
    }

    public function actionDownload($id) {
        $fileModel = EntityAttachments::findOne(['id' => $id]);
        $filePath = $fileModel->getStorePath();

        return Yii::$app->response->sendFile($filePath, $fileModel->getFileName());
    }

    public function actionDelete($id) {
        $file = EntityAttachments::findOne(['id' => $id]);
        return $file->delete();
    }

    public function actionDownloadTemp($filename) {
        $filePath = $this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $filename;

        return Yii::$app->response->sendFile($filePath, $filename);
    }

    public function actionDeleteTemp($filename) {
        $userTempDir = $this->getModule()->getUserDirPath();
        $filePath = $userTempDir . DIRECTORY_SEPARATOR . $filename;
        \unlink($filePath);
        if (!sizeof(FileHelper::findFiles($userTempDir))) {
            \rmdir($userTempDir);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [];
    }

}
