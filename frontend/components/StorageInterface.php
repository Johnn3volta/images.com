<?php

namespace frontend\components;
use yii\web\UploadedFile;

/**
 * Interface StorageInterface
 *
 * @package frontend\components
 */
interface StorageInterface{

    /**
     * @param \yii\web\UploadedFile $file
     *
     * @return mixed
     */
    public function saveUploadedFile(UploadedFile $file);

    /**
     * @param string $filename
     *
     * @return mixed
     */
    public function getFile(string $filename);
}
