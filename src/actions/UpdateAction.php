<?php
namespace ttungbmt\actions;

use kartik\form\ActiveForm;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class UpdateAction extends CrudAction
{
    /**
     * {@inheritdoc}
     */
//    public $redirectUrl = ['view', 'id' => ':primaryKey'];
    public $redirectUrl = ['index'];
    /**
     * {@inheritdoc}
     */
    public $view = 'update';
    /**
     * Enable or disable ajax validation handler.
     *
     * @var bool
     */
    public $enableAjaxValidation = true;

    /**
     * A callback which defines the logic of the update of the object.
     *
     * @var callable;
     */
    public $handler = 'save';

    /**
     * The flash key for success flash message.
     *
     * @var string
     */
    public $flashSuccessKey = 'update:success';
    /**
     * The flash key for error flash message.
     *
     * @var string
     */
    public $flashErrorKey = 'update:error';

    public function run()
    {
        $pk = $this->getPrimaryKey();
        $model = $this->findModel($pk);
        $model->scenario = $this->scenario;
        $model->setAttributes($this->attributes);

        $request = Yii::$app->request;
        $params = $this->getRequestParams();

        $btnClose = Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]);
        $btnSave = Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"]);
        $btnCreateMore = Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote']);

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $resp = ['title'   => Yii::t('app', $this->title, ['id' => $pk, 'gid' => $pk]),];
            $message =  data_get($this->messages, 'success', 'Đã cập nhật thành công');

            if ($model->load($request->post())) {

                if ($this->enableAjaxValidation && empty($params['ajax']) === false) {
                    return ActiveForm::validate($model);
                }

                if ($this->handlerSave($model)) {
                    $this->runSuccessHandler($model);

                    $forceReload = $this->pjaxContainer;

                    if ($this->redirectUrl != false) {
                        return [
                            'forceReload' => $forceReload,
                            'redirectUrl' => Url::to($this->redirect($model)),
                            'forceClose' => true,
                        ];
                    }

                    return array_merge($resp, [
                        'forceReload' => $forceReload,
                        'content' => "<span class='text-success'>{$message}</span>",
                        'message' => $message,
                        'footer'      => $btnClose . $btnCreateMore
                    ]);
                } elseif ($model->hasErrors() === true) {
                    $this->runErrorHandler($model);
                    $resp['errors'] = $model->getErrors();
                    $resp['message'] = data_get($this->messages, 'error', 'Cập nhật thất bại');
                }
            }


            return array_merge($resp, [
                'content' => $this->renderAjax($this->view, ['model' => $model]),
                'footer'  => $btnClose . $btnSave
            ]);
        }


        if ($request->isPost && $model->load($params)) {
            if ($this->enableAjaxValidation && request()->isAjax && empty($params['ajax']) === false) {
                return $this->render(ActiveForm::validate($model));
            }

            if ($this->handlerSave($model)) {
                $this->runSuccessHandler($model);
                if ($this->redirectUrl !==  false) {
                    return $this->redirect($model);
                }
            } elseif ($model->hasErrors() === false) {
                $this->runErrorHandler($model);
            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}