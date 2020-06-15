<?php

namespace ttungbmt\base;

use Illuminate\Support\Collection;
use kartik\widgets\DatePicker;
use ttungbmt\support\facades\Setting;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\validators\Validator;

class Bootstrap implements BootstrapInterface
{

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        $this->registerModules($app);
        $this->registerComponents($app);
        $this->registerValidators($app);
        $this->registerExtraCollection($app);

        $this->setContainers();
    }

    public function setContainers()
    {

        Yii::$container->setDefinitions([
            DatePicker::class => [
                'type' => DatePicker::TYPE_INPUT,
                'options' => [
                    'placeholder' => 'DD/MM/YYYY'
                ],
                'options2' => [
                    'placeholder' => 'DD/MM/YYYDY'
                ],
                'defaultPluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                    'todayBtn' => 'linked',
                    'language' => 'vi',
                    'calendarWeeks' => true,
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'clearBtn' => true,
                ]
            ],
        ]);
    }

    protected function registerModules($app)
    {
        $app->setModules([
            'settings' => [
                'class' => \yii2mod\settings\Module::class,
            ]
        ]);
    }

    protected function registerComponents($app)
    {
        $i18n = data_get($app, 'components.i18n');


        $app->setComponents([
            'http' => [
                'class' => \ttungbmt\components\Http::class
            ],
            'settings' => [
                'class' => \ttungbmt\components\Settings::class,
            ],
            'i18n' => array_merge_recursive($i18n, [
                'translations' => [
                    'yii2mod.settings' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@yii2mod/settings/messages',
                    ],
                ],
            ]),
        ]);
    }

    protected function registerValidators($app)
    {
        Validator::$builtInValidators = array_merge(Validator::$builtInValidators, [
            'geom' => \ttungbmt\validators\GeomValidator::class,
            'atLeast' => \codeonyii\yii2validators\AtLeastValidator::class,
            'dateCompare' => \nepstor\validators\DateTimeCompareValidator::class,
        ]);
    }

    protected function registerExtraCollection(){
        if(!Collection::hasMacro('firstWhereGet')){
            Collection::macro('firstWhereGet', function ($key, $value = null, $path = null, $default = null) {
                return data_get($this->firstWhere($key, $value), $path, $default);
            });
        }
    }
}