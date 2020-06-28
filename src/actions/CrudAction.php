<?php

namespace ttungbmt\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class CrudAction extends Action
{
    /**
     * @var callable a PHP callable that will be called when running an action to determine
     * if the current user has the permission to execute the action. If not set, the access
     * check will not be performed. The signature of the callable should be as follows,
     *
     * ```php
     * function ($model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     *
     * If callable return false then perform standard access control filter behavior
     * (like in [[\yii\filters\AccessControl]]).
     */
    public $checkAccess;

    public $attributes = [];

    public $messages = [];

    public $handler;

    public $title;

    public $modal = [
        'title' => ''
    ];


    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!isset($this->modelClass))
            throw new InvalidConfigException('The "modelClass" property must be set.');
    }

    /**
     * Ensure this action is allowed for current user
     *
     * @param array $params Params to be passed to {$this->checkAccess}
     *
     * @throws ForbiddenHttpException
     */
    protected function ensureAccess($params = [])
    {
        if (!isset($this->checkAccess))
            return;
        $params['action'] = $this;
        if (call_user_func($this->checkAccess, $params))
            return;
        $user = \Yii::$app->user;
        if ($user->getIsGuest())
            $user->loginRequired();
        else
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
    }

    protected function loadModel($params)
    {
//        $finder = ModelFinder::create($this->modelClass);
//        if (!$finder->load($params))
//            throw new BadRequestHttpException($finder->getError());
//        $model = $finder->getModel();
//        if (!$model) {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//        return $model;
    }

    protected function handlerSave($model){
        return is_callable($this->handler) ? call_user_func($this->handler) : call_user_func([$model, $this->handler]);
    }

}