<?php
namespace ttungbmt\components;

use Illuminate\Support\Str;

class Settings extends \yii2mod\settings\components\Settings
{
    public function get($section, $key = null, $default = null)
    {
        $path = Str::of($section);
        if($path->contains('.')){
            return parent::get((string)$path->before('.'), (string)$path->after('.'), $default);
        }

        return parent::get($section, $key, $default);
    }

}