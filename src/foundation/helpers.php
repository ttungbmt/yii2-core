<?php

use ttungbmt\foundation\Mix;

if (!function_exists('app')) {
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return Yii::$app;
        }

        return data_get(Yii::$app, $abstract);
    }
}

if (!function_exists('toFilterValue')) {
    function toFilterValue($options )
    {
        return function ($model) use($options){
            $filter = collect($options['filter']);
            $value = $options['value'];
            return $filter->get($model->{$value}, '');
        };
    }
}

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param array|string $key
     * @param null $default
     * @return ttungbmt\web\Request|string|array
     */
    function request($key = null, $default = null)
    {

        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->input($key);

        return is_null($value) ? value($default) : $value;
    }
}


if (!function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function mix($path, $manifestDirectory = '')
    {
        return with(new Mix)(...func_get_args());
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param string $path
     * @return string
     */
    function public_path($path = '')
    {
        return Yii::getAlias('@webroot') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}


if (!function_exists('delete_all_files')) {
    /**
     * Get the path
     * @param string $path
     * @return void
     */
    function delete_all_files($path)
    {
        array_map('unlink', array_filter((array)glob($path)));
    }
}
if (!function_exists('toInpOptions')) {
    /**
     * Get the path
     * @param array $raw
     * @return array
     */
    function toInpOptions($raw)
    {
        return collect($raw)->map(function ($v, $k) {
            return ['value' => (string)$k, 'label' => $v];
        })->values()->all();
    }
}


