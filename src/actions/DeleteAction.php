<?php

namespace ttungbmt\actions;

use kartik\form\ActiveForm;
use sergeymakinen\facades\Session;
use Yii;
use yii\web\Response;

class DeleteAction extends CrudAction
{
    /**
     * {@inheritdoc}
     */
    public $redirectUrl = ['index'];
    /**
     * {@inheritdoc}
     */
    public $view = false;
    /**
     * A callback which defines the logic of the removal of the object.
     *
     * @var callable;
     */
    public $handler;
    /**
     * Is called when a throw exception.
     *
     * @var callable|bool;
     */
    public $exceptionCallback;
    /**
     * The flash key for success flash message.
     *
     * @var string
     */
    public $flashSuccessKey = 'delete:success';
    /**
     * The flash key for error flash message.
     *
     * @var string
     */
    public $flashErrorKey = 'delete:error';
    /**
     * The flash key for exception flash message.
     *
     * @var string
     */
    public $flashExceptionKey = 'delete:exception';

    public function run()
    {
        $pk = $this->getPrimaryKey();
        $request = Yii::$app->request;

        $model = $this->findModel($pk);
        $model->scenario = $this->scenario;

        try {
            if (is_callable($this->handler)) {
                $result = (bool)call_user_func($this->handler, $model, $this);
            } else {
                $handler = is_string($this->handler) ? $this->handler : 'delete';
                $result = (bool)$model->{$handler}();
            }
            if ($result) {
                $this->runSuccessHandler($model);
            } else {
                $this->runErrorHandler($model);
            }
        } catch (\Exception $ex) {
            if (is_callable($this->exceptionCallback)) {
                call_user_func($this->exceptionCallback, $model, $this, $ex);
            } elseif ($this->exceptionCallback !== false) {
                Session::setFlash($this->flashExceptionKey);
            }
        }

        if ($request->isAjax) {
            return $this->render(['forceClose' => true, 'forceReload' => $this->pjaxContainer]);
        }

        if ($this->view === false) {
            return $this->redirect($model);
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}