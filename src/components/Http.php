<?php
namespace ttungbmt\components;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\Traits\Macroable;

class Http extends \yii\base\Component
{
    use Macroable {
        __call as macroCall;
    }


    /**
     * Execute a method against a new pending request instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }


        return tap(new Factory(), function ($request){
        })->{$method}(...$parameters);
    }

}