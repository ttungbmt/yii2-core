<?php
namespace ttungbmt\support\facades;

use sergeymakinen\facades\Facade;

/**
 * @method static \ttungbmt\components\Settings get(string $section, string $key = null, string $default = null)
 *
 * @see \ttungbmt\components\Settings
 */

class Setting extends Facade
{
    /**
     * @inheritDoc
     */
    public static function getFacadeComponentId()
    {
        return 'settings';
    }
}