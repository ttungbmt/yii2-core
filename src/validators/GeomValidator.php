<?php
namespace ttungbmt\validators;

use yii\base\DynamicModel;
use yii\validators\EachValidator;
use yii\validators\Validator;

class GeomValidator extends Validator {
    public function validateAttribute($model, $attribute) {
        $mesage = 'Toạ độ không hợp lệ';
        $valid1 = new EachValidator(['rule' => ['number']]);
        $value = collect($model->$attribute)->filter(function ($i){
            return ($i || $i == 0) && trim($i) !== "" ;
        });

        if($value->count() === 0) {
            $model->$attribute = null;
            return;
        }

        if($value->count() === 2 && $valid1->validate($value->all())){

            $valid2 = DynamicModel::validateData(['lat' => $value->get('1'), 'lng' => $value->get('0')], [
                ['lat', 'match', 'pattern' => '/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/'],
                ['lng', 'match', 'pattern' => '/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/'],
            ]);

            if(!$valid2->hasErrors()){
                $model->$attribute = $value->sortKeys()->all();
                return;
            }
            $this->addError($model, $attribute, collect($valid2->getErrors())->flatten()->implode(', '));
        }

        $this->addError($model, $attribute, $mesage);
    }
}