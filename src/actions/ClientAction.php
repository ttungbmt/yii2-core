<?php
namespace ttungbmt\actions;

use yii\httpclient\Client;

class ClientAction extends \yii\base\Action
{
    public function run(){
        $request = \Yii::$app->request;
        $data = collect(array_merge($_POST, $_GET));
        $client = new Client();
        $response = $client->createRequest()->setUrl($data['url']);

        if($request->isGet){
            $response = $response->setMethod('GET')->send();
        } else {
            $response = $response->setMethod('POST')->setData($data['data'])->send();
        }


        if ($response->isOk) {
            if(array_key_exists('application/json', $request->getAcceptableContentTypes())){
                return $this->controller->asJson($response->getData());
            }

            return $response->getContent();

        }
    }
}