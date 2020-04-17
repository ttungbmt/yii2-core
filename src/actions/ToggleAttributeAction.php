<?php
namespace ttungbmt\actions;

class ToggleAttributeAction extends Action
{
    public function run($attribute, $id){
        //TODO: Restrict owner access
        /* @var $model \yeesoft\db\ActiveRecord */
//        $pk = $this->getPrimaryKey();
        $model = $this->findModel($id);
        $model->{$attribute} = ($model->{$attribute} == 1) ? 0 : 1;
        $model->save(false);
    }
}