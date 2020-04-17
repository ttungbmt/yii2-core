<?php

namespace ttungbmt\db;

use Carbon\Carbon;
use common\models\MyModel;
use ttungbmt\behaviors\AttributeCastBehavior;
use Yii;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ActiveRecord extends \yii\db\ActiveRecord
{
    protected $timestamps = false;

    protected $blameables = false;

    protected $casts = [];

    protected $dates = [];

    public function init()
    {
        parent::init();

        $this->initDefaultValues();
    }

    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    public function initDefaultValues()
    {
        return $this;
    }

    public function getColumnType($name)
    {
        return data_get($this->getTableSchema(), "{$name}.type");
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
//        $tableSchema = $this->getTableSchema();


        if ($this->timestamps) {
            $behaviors['timestamp'] = [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ];
        }

        if ($this->blameables) {
            $behaviors['blameable'] = [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ];
        }

        if (!empty($this->casts)) {
            $behaviors['attribute_cast'] = [
                'class' => AttributeCastBehavior::class,
                'attributeTypes' => $this->casts,
                'typecastAfterFind' => true,
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => true,
            ];
        }


        if (!empty($this->dates)) {
//            $behaviors['date_cast'] = [
//                'class' => AttributesBehavior::className(),
//                'attributes' => collect($this->dates)->mapWithKeys(function ($item) {
//                    return [$item => [
//                        ActiveRecord::EVENT_AFTER_FIND => $f1 = function ($event, $attribute) {
//                            return app('formatter')->asDate($this->{$attribute});
//                        },
//                        ActiveRecord::EVENT_BEFORE_INSERT => $fn2 = function ($event, $attribute) {
//                            $date = $this->{$attribute};
//                            try {
//                                return $date ? Carbon::createFromFormat('d/m/Y', $date) : null;
//                            } catch (\Exception $e) {
//                                return $date;
//                            }
//                        },
//                        ActiveRecord::EVENT_BEFORE_UPDATE => $fn2,
//                        ActiveRecord::EVENT_AFTER_INSERT => $f1,
//                        ActiveRecord::EVENT_AFTER_UPDATE => $f1,
//                    ]];
//
//                })->all(),
//            ];
        }

//
//        if ($tableSchema) {
//            $geometryCol = $tableSchema->getColumn($this->geometryColumn);
//            if ($geometryCol && $geometryCol->dbType = 'geometry') {
//                $behaviors['geometry'] = [
//                    'class'                => GeometryBehavior::className(),
//                    'type'                 => $this->geometryType,
//                    'attribute'            => $this->geometryColumn,
//                    'skipAfterFindPostgis' => true,
//                ];
//            }
//
//            $jsonCols = collect($tableSchema->columns)->filter(function ($item) {
//                return $item->dbType == 'json';
//            })->keys();
//            if ($jsonCols->isNotEmpty()) {
//                $behaviors['json'] = [
//                    'class'      => JsonBehavior::className(),
//                    'attributes' => $jsonCols->all(),
//                ];
//            }
//
//            if ($this->positionColumn) {
//                $behaviors['position'] = [
//                    'class'             => PositionBehavior::className(),
//                    'positionAttribute' => 'position',
//                ];
//            }
//        }
//
//

//

//
////        $behaviors['linkall'] = [
////            'class'     => LinkAllBehavior::className(),
////        ];
//
//        $behaviors['sync'] = [
//            'class' => SyncRelationBehavior::className(),
//        ];
//
////        if($this->relations) {
////            $behaviors['saveRelations'] = [
////                'class'     => SaveRelationsBehavior::className(),
////                'relations' => $this->relations
////            ];
////        }

        return $behaviors;
    }

}