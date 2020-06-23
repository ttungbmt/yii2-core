<?php
namespace ttungbmt\actions;

use Illuminate\Support\Arr;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

abstract class Action extends \yii\base\Action
{
    public $pjaxContainer = '#crud-datatable-pjax';

    /**
     * Class to use to locate the supplied data ids.
     *
     * @var string
     */
    public $modelClass;
    /**
     * The name of view file.
     *
     * @var string
     */
    public $view = 'default';

    /**
     * The request additional params.
     *
     * @var array
     */
    public $requestParams = [];
    /**
     * The view additional params.
     *
     * @var array
     */
    public $viewParams = [];
    /**
     * The route which will be redirected after the user action.
     *
     * @var string|array|callable
     */
    public $redirectUrl;
    /**
     * The scenario to be assigned to the model before it is validated and updated.
     *
     * @var string
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * The name of the GET parameter that stores the primary key of the model.
     *
     * @var string
     */
    public $primaryKeyParam = 'id';
    /**
     * Is called when a successful result.
     *
     * @var callable|null;
     */
    public $successCallback;
    /**
     * The flash key for success flash message.
     *
     * @var string
     */
    public $flashSuccessKey = 'success';
    /**
     * Is called when a failed result.
     *
     * @var callable|null;
     */
    public $errorCallback;
    /**
     * The flash key for error flash message.
     *
     * @var string
     */
    public $flashErrorKey = 'error';
    /**
     * This method is called right before `run()` is executed.
     * You may override this method to do preparation work for the action run.
     * If the method returns false, it will cancel the action.
     *
     * @var callable|null
     */
    public $beforeRun;


    /**
     * This method is called right after `run()` is executed.
     * You may override this method to do post-processing work for the action run.
     *
     * @var callable|null
     */
    public $afterRun;
    /**
     * The primary key value of current model.
     *
     * @var int|string|callable|bool
     */
    protected $_primaryKey = false;

    /**
     * {@inheritdoc}
     */
    public function beforeRun()
    {
        if (is_callable($this->beforeRun)) {
            return call_user_func($this->beforeRun, $this);
        }
        return parent::beforeRun();
    }
    /**
     * {@inheritdoc}
     */
    public function afterRun()
    {
        if (is_callable($this->afterRun)) {
            call_user_func($this->afterRun, $this);
        }
        parent::afterRun();
    }


    protected function runSuccessHandler($model)
    {
        if (is_callable($this->successCallback)) {
            call_user_func($this->successCallback, $model, $this);
        } elseif (empty($this->flashSuccessKey) === false) {
            Yii::$app->session->setFlash($this->flashSuccessKey);
        }
    }

    protected function runErrorHandler($model)
    {
        if (is_callable($this->errorCallback)) {
            call_user_func($this->errorCallback, $model, $this);
        } elseif (empty($this->flashErrorKey) === false) {
            Yii::$app->session->setFlash($this->flashErrorKey);
        }
    }

    public function getRequestParams(){
        return array_merge(request()->all(), $this->requestParams);
    }

    protected function redirect($model)
    {
        if (is_array($this->redirectUrl)) {
            array_walk($this->redirectUrl, function (&$value) use ($model) {
                if (($pos = strpos($value, ':')) !== false) {
                    $attributeName = substr($value, $pos + 1);
                    $value = ArrayHelper::getValue($model, $attributeName);
                }
            });
        } elseif (is_callable($this->redirectUrl)) {
            $this->redirectUrl = call_user_func($this->redirectUrl, $model);
        }

        if(Yii::$app->request->isAjax){
            return $this->redirectUrl;
        }

        return $this->controller->redirect($this->redirectUrl);
    }

    /**
     * Set model primary key.
     *
     * @param $value
     */
    public function setPrimaryKey($value)
    {
        $this->_primaryKey = $value;
    }

    /**
     * Get primary key of current handling model.
     *
     * @param bool $throwException
     *
     * @throws \yii\web\BadRequestHttpException
     *
     * @return string
     */
    public function getPrimaryKey($throwException = true)
    {
        if ($this->_primaryKey && is_callable($this->_primaryKey)) {
            $this->_primaryKey = call_user_func($this->_primaryKey, $this);
        }
        if ($this->_primaryKey === false) {
            // $primaryKey = head(($this->modelClass)::primaryKey());
            $this->_primaryKey = Yii::$app->request->get($this->primaryKeyParam, null);
        }
        if ($this->_primaryKey === null && $throwException) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => $this->primaryKeyParam,
            ]));
        }
        return $this->_primaryKey;
    }


    /**
     * Finding model by primary key.
     */

    public function findModel($condition, $throwException = true)
    {
        $model = call_user_func(
            [$this->modelClass, 'findOne'],
            $condition
        );

        if (!$model && $throwException) {
            throw new NotFoundHttpException('Not found model id');
        }

        return $model;
    }

    protected function renderAjax($view, $params = [])
    {
        if(!empty($this->viewParams) && !Arr::isAssoc($this->viewParams)){
            $this->viewParams = call_user_func($this->viewParams);
        }

        return $this->controller->renderAjax($view, array_merge($this->viewParams, $params));
    }

    protected function render($view, $params = [])
    {
        if ($this->viewParams instanceof \Closure) {
            $this->viewParams = call_user_func($this->viewParams, $this);
        }

        if($this->viewParams && !Arr::isAssoc($this->viewParams)){
            $this->viewParams = call_user_func($this->viewParams);
        }

        if(is_array($view)){
            return $this->controller->asJson($view);
        }

        $viewParams = array_merge([
            'pjaxContainer' => str_replace( '#', '', $this->pjaxContainer)
        ], $this->viewParams, $params);

        return $this->controller->render($view, $viewParams);
    }


    /**
     * Define redirect page after update, create, delete, etc
     *
     * @param string       $action
     * @param ActiveRecord $model
     *
     * @return string|array
     */
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'delete':
                return ['index'];
                break;
            case 'update':
                return ['view', 'id' => $model->getId()];
                break;
            case 'create':
                return ['view', 'id' => $model->getId()];
                break;
            default:
                return ['index'];
        }
    }


}