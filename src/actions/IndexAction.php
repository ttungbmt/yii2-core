<?php
namespace ttungbmt\actions;
use Yii;

class IndexAction extends CrudAction
{
    public $dataProvider;
    /**
     * @var string View file name
     */
    public $view = 'index';

    public function run()
    {
        $this->ensureAccess();

        $model = new $this->modelClass;
        $model->scenario = $this->scenario;
        $params = $this->getRequestParams();

        if(method_exists($model, 'search')){
            $this->viewParams['searchModel'] = $model;
            $this->viewParams['dataProvider'] = $this->dataProvider ? $this->dataProvider : $model->search($params);
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }

}