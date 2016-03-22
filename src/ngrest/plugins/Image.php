<?php

namespace admin\ngrest\plugins;

/**
 * Single Image-Upload
 * 
 * @author nadar
 */
class Image extends \admin\ngrest\base\Plugin
{
    public $noFilters = false;

    public function renderList($id, $ngModel)
    {
        return $this->createTag('storage-image-thumbnail-display', null, ['image-id' => "{{{$ngModel}}}"]);
    }

    public function renderCreate($id, $ngModel)
    {
        return $this->createFormTag('zaa-image-upload', $id, $ngModel, ['options' => json_encode(['no_filter' => $this->noFilters])]);
    }

    public function renderUpdate($id, $ngModel)
    {
        return $this->renderCreate($id, $ngModel);
    }
}
