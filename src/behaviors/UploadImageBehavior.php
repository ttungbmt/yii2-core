<?php
namespace ttungbmt\behaviors;

use Illuminate\Support\Str;
use Imagine\Image\ManipulatorInterface;
use ttungbmt\gdal\Gdal;
use Yii;
use yii\base\Model;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;
use function _\internal\parent;

class UploadImageBehavior extends \mohorev\file\UploadImageBehavior
{
    public $scenarios = [Model::SCENARIO_DEFAULT];

    public $attribute = 'file';

    public function getUploadPath($attr = null, $old = false)
    {
        $attribute = $attr ? $attr : $this->attribute;
        return parent::getUploadPath($attribute, $old);
    }

    public function getUploadUrl($attr = null)
    {
        $attribute = $attr ? $attr : $this->attribute;
        return parent::getUploadUrl($attribute);
    }

    public function getThumbUploadPath($attribute = null, $profile = 'thumb', $old = false)
    {
        $attribute = $attribute ? $attribute : $this->attribute;
        return parent::getThumbUploadPath($attribute, $profile, $old);
    }

    protected function getThumbFileName($filename, $profile = 'thumb')
    {
        if($this->isExtension('tif')){
            $filename = pathinfo($filename, PATHINFO_FILENAME).'.jpg';
        }

        return $profile . '-' . $filename;
    }

    public function getThumbUploadUrl($attribute = null, $profile = 'thumb')
    {
        $attribute = $attribute ? $attribute : $this->attribute;
        return parent::getThumbUploadUrl($attribute, $profile);
    }

    protected function isExtension($ext){
        $filename = $this->owner->{$this->attribute};
        return pathinfo($filename, PATHINFO_EXTENSION) === $ext;
    }

    protected function generateImageThumb($config, $path, $thumbPath)
    {
        if($this->isExtension('tif')){
            $gdal = new Gdal();
            $thumbPath =  pathinfo($thumbPath, PATHINFO_DIRNAME) . '/' . pathinfo($thumbPath, PATHINFO_FILENAME) . '.jpg';;
            $gdal->translate($path, $thumbPath);
            $gdal->run();
        } else {
            parent::generateImageThumb($config, $path, $thumbPath);
        }

    }
}