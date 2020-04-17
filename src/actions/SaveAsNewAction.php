<?php
namespace ttungbmt\actions;
use Yii;

class SaveAsNewAction extends UpdateAction
{
    public function run(){
        $model = new $this->modelClass;

        if (Yii::$app->request->post('_asnew') != '1') {
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('crudMessage', Yii::t('yee', 'Your item has been updated.'));
            return $this->redirect($this->getRedirectPage('update', $model));
        }

        return $this->renderIsAjax($this->updateView, compact('model'));
    }
}