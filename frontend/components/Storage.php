<?php


namespace frontend\components;


use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class Storage
 *
 * @package frontend\components
 */
class Storage extends Component implements StorageInterface{

    /**
     * @var
     */
    private $fileName;

    /**
     * @param \yii\web\UploadedFile $file
     *
     * @return mixed
     */
    public function saveUploadedFile(UploadedFile $file){
        $path = $this->preparePath($file);

        if($path && $file->saveAs($path)){
            return $this->fileName;
        }
    }

    /**
     * @param \yii\web\UploadedFile $file
     *
     * @return string
     * @throws \yii\base\Exception
     */
    protected function preparePath(UploadedFile $file){
        $this->fileName = $this->getFileName($file);

        $path = $this->getStoragePath() . $this->fileName;

        $path = FileHelper::normalizePath($path);
        if(FileHelper::createDirectory(dirname($path))){
            return $path;
        }
    }

    /**
     * @param \yii\web\UploadedFile $file
     *
     * @return string
     */
    protected function getFileName(UploadedFile $file){
        $hash = sha1_file($file->tempName);

        $name = substr_replace($hash, '/', 2, 0);
        $name = substr_replace($name, '/', 5, 0);

        return $name .'.'. $file->extension;
    }

    /**
     * @return bool|string
     */
    protected function getStoragePath(){
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }

    /**
     * @param string $filename
     *
     * @return mixed|string
     */
    public function getFile(string $filename){
        return Yii::$app->params['storageUri'] . $filename;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function deleteFile(string $filename){
        $file = $this->getStoragePath().$filename;
        if(file_exists($file)) return unlink($file);

        return true;
    }
}