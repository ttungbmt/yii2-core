<?php
namespace ttungbmt\actions;

use yeesoft\helpers\YeeHelper;
use yeesoft\models\OwnerAccess;
use yeesoft\models\User;
use Yii;

class BulkActivateAction extends Action
{
    public function run(){
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelClass;
            $restrictAccess = (YeeHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $where = ['id' => Yii::$app->request->post('selection', [])];

            if ($restrictAccess) {
                $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $modelClass::updateAll(['status' => 1], $where);
        }
    }
}