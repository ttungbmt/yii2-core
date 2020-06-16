<?php

namespace ttungbmt\validators;

use yii\base\DynamicModel;
use yii\validators\EachValidator;
use yii\validators\Validator;

class GeomValidator extends Validator
{
    /**
     * @var boolean whether the attribute value can be null or empty. Defaults to false.
     * If this is true, it means the attribute is considered valid when it is empty.
     */
    public $allowEmpty = true;

    public $geoprocessing;
    public $geometry;

    public function validateAttribute($model, $attribute)
    {
        $value = collect($model->$attribute)->filter(function ($i) {
            return ($i || $i == 0) && trim($i) !== "";
        })->map(function ($i){
            return trim($i);
        });

        if ($this->allowEmpty && $this->isEmpty($value)) {
            $model->$attribute = null;
            return null;
        }


        if (!$this->validateLatLng($value, [$model, $attribute])) {
            return false;
        }

        if($this->geoprocessing && !$this->validateGeoprocessing()){
            return false;
        }

    }


    public function validateLatLng($value, array $params)
    {
        $validator = DynamicModel::validateData(['lat' => $value->get('1'), 'lng' => $value->get('0')], [
            ['lat', 'match', 'pattern' => '/^(\+|-)?(?:90(?:(?:\.0{1,15})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,15})?))$/'],
            ['lng', 'match', 'pattern' => '/^(\+|-)?(?:180(?:(?:\.0{1,15})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,15})?))$/'],
        ]);

        if ($value->count() === 2 && !$validator->hasErrors()) {
            return true;
        }

        $this->addError($params[0], $params[1], $this->implodeMessage($validator));
    }

    public function implodeMessage($validator)
    {
        return collect($validator->getErrors())->flatten()->implode(', ');
    }

    public function validateGeoprocessing()
    {
        if(is_array($this->geoprocessing)){
            return call_user_func($this->geoprocessing);
        }
    }


}