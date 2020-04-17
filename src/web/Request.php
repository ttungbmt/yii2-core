<?php

namespace ttungbmt\web;

use Illuminate\Support\Traits\Macroable;

class Request extends \yii\web\Request
{
    use Macroable {
        __call as macroCall;
    }

    public function instance()
    {
        return new \Illuminate\Http\Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * Execute a method against a new pending request instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (!method_exists($this, $method)) {
            if (static::hasMacro($method)) {
                return $this->macroCall($method, $parameters);
            }

            return tap($this->instance(), function ($request) {
            })->{$method}(...$parameters);
        }

        parent::__call($method, $parameters);
    }
}