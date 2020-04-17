<?php

namespace ttungbmt\actions;

use Yii;
use yii\helpers\Html;

class ViewAction extends CrudAction
{
    /**
     * @var string View file name
     */
    public $view = 'view';

    public function run()
    {
        $primaryKey = $this->getPrimaryKey();
        $model = $this->findModel($primaryKey);

        if (request()->isAjax) {
            $modal = collect($this->modal);
            $title = $modal->get('title', 'View');
            $footer = $modal->get('footer', '{close}');

            return $this->render([
                'title' => Yii::t('app', $title, ['id' => $primaryKey]),
                'content' => $this->controller->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer' => Yii::t('app', $footer, [
                    'close' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']),
                    'edit' =>  Html::a('Edit', ['update', $this->primaryKeyParam => $primaryKey], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ])
            ]);
        }

        return $this->render($this->view, [
            'model' => $model
        ]);
    }
}