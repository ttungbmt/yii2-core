<?php
namespace ttungbmt\actions;

use yeesoft\helpers\YeeHelper;
use yeesoft\models\OwnerAccess;
use yeesoft\models\User;
use Yii;

class BulkDeleteAction extends Action
{
    public function run()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelClass;
            $restrictAccess = (YeeHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));

            foreach (Yii::$app->request->post('selection', []) as $id) {
                $where = ['id' => $id];

                if ($restrictAccess) {
                    $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
                }

                $model = $modelClass::findOne($where);

                if ($model) $model->delete();
            }
        }
    }
}