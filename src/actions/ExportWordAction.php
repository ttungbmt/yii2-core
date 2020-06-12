<?php
namespace ttungbmt\actions;

use Yii;
use yii\base\Action;
use yii\base\ViewNotFoundException;

class ExportWordAction extends Action {
    public $fileName = 'Form_Word';
    public $view = 'template-word';
    public $getData;

    public function run() {
        # Html to Word: https://docconverter.pro/Home/Dashboard
        # Html viewer: https://codebeautify.org/htmlviewer
        $data = call_user_func($this->getData);
        try {
            $this->addHeaders();
            $content = $this->controller->renderPartial($this->view, $data);
            return $content;
        } catch (ViewNotFoundException $e) {
            dump($e);
        }

    }

    protected function addHeaders() {
        $headers = Yii::$app->response->headers;
        $headers
            ->add('Content-Type', 'application/vnd.ms-word')
            ->add('Content-Disposition', "attachment;Filename={$this->fileName}.doc");
    }
}