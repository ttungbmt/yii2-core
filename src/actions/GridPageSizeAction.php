<?php
namespace ttungbmt\actions;

use Yii;
use yii\web\Cookie;

class GridPageSizeAction extends Action
{
    public function run(){
        if (Yii::$app->request->post('grid-page-size')) {
            $cookie = new Cookie([
                'name'   => '_grid_page_size',
                'value'  => Yii::$app->request->post('grid-page-size'),
                'expire' => time() + 86400 * 365, // 1 year
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }
}