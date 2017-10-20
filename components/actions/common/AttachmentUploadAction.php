<?php

namespace cza\base\components\actions\common;

use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use cza\base\vendor\widgets\plupload\ChunkUploader;

/**
 * handle upload upload images as attachemnts of the entity
 * AttachmentUploadAction class file.
 */
class AttachmentUploadAction extends \cza\base\vendor\widgets\plupload\PluploadAction {

    protected $_organizer;

    /**
     * @var string file input name.
     */
    public $inputName = 'file';

    /**
     * @var string the directory to store temporary files during conversion. You may use path alias here.
     * If not set, it will use the "plupload" subdirectory under the application runtime path.
     */
    public $tempPath = '@runtime/plupload';

    /**
     * @var integer the permission to be set for newly created cache files.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * If not set, the permission will be determined by the current environment.
     */
    public $fileMode;

    /**
     * @var integer the permission to be set for newly created directories.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * Defaults to 0775, meaning the directory is read-writable by owner and group,
     * but read-only for other users.
     */
    public $dirMode = 0775;

    /**
     * @var callable success callback with signature: `function($filename, $params)`
     */
    public $onComplete;
    public $attachementClass;
    public $entityClass;
    public $entityAttribute = 'album';

    /**
     * Initializes the action and ensures the temp path exists.
     */
    public function init() {
        parent::init();

        if (empty($this->attachementClass)) {
            throw new \yii\base\Exception("attachementClass is required!");
        }

        $this->_organizer = Yii::$app->czaHelper->folderOrganizer;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->tempPath = Yii::getAlias($this->tempPath);
        if (!is_dir($this->tempPath)) {
            FileHelper::createDirectory($this->tempPath, $this->dirMode, true);
        }
    }

    /**
     * Runs the action.
     * This method displays the view requested by the user.
     * @throws HttpException if the view is invalid
     */
    public function run() {
        $uploadedFile = UploadedFile::getInstanceByName($this->inputName);
        $params = Yii::$app->request->getBodyParams();
        $filePath = $this->getUnusedPath($this->tempPath . DIRECTORY_SEPARATOR . $uploadedFile->name);

        $isUploadComplete = ChunkUploader::process($uploadedFile, $filePath);

        if ($isUploadComplete) {
            $fileHash = md5(microtime(true) . $filePath);
            $fileType = isset($params['name']) ? strtolower(substr(stristr($params['name'], '.'), 1)) : strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $newFileName = "{$fileHash}.{$fileType}";

            $entityClass = $this->entityClass;
            $entityModel = $entityClass::findOne(['id' => $params['entity_id']]);
            if (is_null($entityModel)) {
                throw new \yii\base\Exception("entity model(id:{$params['entity_id']}) not found!");
            }

            $fileDirPath = $this->_organizer->getFullUploadStoreDir($fileHash, $entityModel);
            $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

            if (!copy($filePath, $newFilePath)) {
                throw new Exception("Cannot copy file! {$filePath}  to {$newFilePath}");
            }
            $attachementClass = $this->attachementClass;
            $attachement = new $attachementClass;
            $attachement->loadDefaultValues();
            $attachement->setAttributes([
//                'name' => pathinfo($filePath, PATHINFO_FILENAME),
//                'type' => $attachement->type,
                'name' => $params['name'],
                'entity_id' => $entityModel->id,
                'entity_class' => $this->entityClass,
                'entity_attribute' => $this->entityAttribute,
                'hash' => $fileHash,
                'size' => filesize($filePath),
                'content' => isset($extras['content']) ? $extras['content'] : "",
                'extension' => $fileType,
                'mime_type' => FileHelper::getMimeType($filePath),
                'logic_path' => $this->_organizer->getUploadLogicPath($fileHash, $entityModel),
            ]);

            if ($attachement->save()) {
                @\unlink($filePath);
            }

            if ($this->onComplete) {
                return call_user_func($this->onComplete, $filePath, $params);
            } else {
                return [
                    'filename' => $filePath,
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * Returns an unused file path by adding a filename suffix if necessary.
     * @param string $path
     * @return string
     */
    protected function getUnusedPath($path) {
        $newPath = $path;
        $info = pathinfo($path);
        $suffix = 1;

        while (file_exists($newPath)) {
            $newPath = $info['dirname'] . DIRECTORY_SEPARATOR . "{$info['filename']}_{$suffix}";
            if (isset($info['extension'])) {
                $newPath .= ".{$info['extension']}";
            }
            $suffix++;
        }

        return $newPath;
    }

}
