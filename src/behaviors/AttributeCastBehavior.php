<?php
namespace ttungbmt\behaviors;

use Illuminate\Support\Str;
use ttungbmt\support\facades\Formatter;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Date;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\StringHelper;

class AttributeCastBehavior extends \yii\behaviors\AttributeTypecastBehavior
{
    public $typecastAfterFind = true;
    public $typecastBeforeSave = true;

    public $dateFormat = 'Y-m-d H:i:s';

    const TYPE_INT = 'int';
    const TYPE_REAL = 'real';
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = 'double';
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY = 'array';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_BOOL = 'bool';
    const TYPE_JSON = 'json';
    const TYPE_COLLECTION = 'collection';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_CUSTOM_DATETIME = 'custom_datetime';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_DATESTR = 'datestr';

    protected static $primitiveCastTypes = [
        'array',
        'bool',
        'boolean',
        'collection',
        'custom_datetime',
        'date',
        'datetime',
        'decimal',
        'double',
        'float',
        'int',
        'integer',
        'json',
        'object',
        'real',
        'string',
        'timestamp',
    ];

    protected function typecastValue($value, $type)
    {
        if (is_scalar($type)) {
            if (is_object($value) && method_exists($value, '__toString')) {
                $value = $value->__toString();
            }

            switch ($type) {
                case self::TYPE_INT:
                    return (int) $value;
                case self::TYPE_REAL:
                case self::TYPE_FLOAT:
                case self::TYPE_DOUBLE:
                    return $this->fromFloat($value);
//                case self::TYPE_DECIMAL:
//                    return $this->asDecimal($value, explode(':', $this->getCasts()[$type], 2)[1]);
                case self::TYPE_COLLECTION:
                    return new BaseCollection($this->fromJson($value));
                case self::TYPE_DATE:
                    return $this->asDate($value);
                case self::TYPE_DATESTR:
                    $format = Str::of(Yii::$app->formatter->dateFormat)->replace('php:', '');
                    return Carbon::parse($value)->format($format);
                case self::TYPE_DATETIME:
                case self::TYPE_CUSTOM_DATETIME:
                    return $this->asDateTime($value);
                case self::TYPE_TIMESTAMP:
                    return $this->asTimestamp($value);
                case self::TYPE_OBJECT:
                    return $this->fromJson($value, true);
                case self::TYPE_ARRAY:
                case self::TYPE_JSON:
                    return $this->fromJson($value);
                default:
            }
        }

        return parent::typecastValue($value, $type);
    }



    protected function reverseTypecastAttributes($attributeNames = null){
        $attributeTypes = [];

        if ($attributeNames === null) {
            $attributeTypes = $this->attributeTypes;
        } else {
            foreach ($attributeNames as $attribute) {
                if (!isset($this->attributeTypes[$attribute])) {
                    throw new InvalidArgumentException("There is no type mapping for '{$attribute}'.");
                }
                $attributeTypes[$attribute] = $this->attributeTypes[$attribute];
            }
        }

        foreach ($attributeTypes as $attribute => $type) {
            $value = $this->owner->{$attribute};
            if ($this->skipOnNull && $value === null) {
                continue;
            }
            $this->owner->{$attribute} = $this->reverseTypecastValue($value, $type);
        }
    }

    protected function reverseTypecastValue($value, $type)
    {
        switch ($type) {
//            case self::TYPE_INT:
//                return (int) $value;
//            case self::TYPE_REAL:
//            case self::TYPE_FLOAT:
//            case self::TYPE_DOUBLE:
//                return $this->fromFloat($value);
////            case self::TYPE_DECIMAL:
////                return $this->asDecimal($value, explode(':', $this->getCasts()[$type], 2)[1]);
//            case self::TYPE_DATE:
//                return $this->asDate($value);
//            case self::TYPE_DATETIME:
//            case self::TYPE_CUSTOM_DATETIME:
//                return $this->asDateTime($value);
//            case self::TYPE_TIMESTAMP:
//                return $this->asTimestamp($value);
            case self::TYPE_DATESTR:
                try {
                    $format = Str::of(Yii::$app->formatter->dateFormat)->replace('php:', '');
                    return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return $value;
                }
            case self::TYPE_COLLECTION:
            case self::TYPE_OBJECT:
            case self::TYPE_ARRAY:
            case self::TYPE_JSON:
                return collect($value)->toJson();
            default:
        }


//        if (is_scalar($type)) {
//            if (is_object($value) && method_exists($value, '__toString')) {
//                $value = $value->__toString();
//            }
////
//
//        }

    }

    public function beforeSave($event)
    {
        $this->reverseTypecastAttributes();
    }

    /**
     * Decode the given float.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function fromFloat($value)
    {
        switch ((string) $value) {
            case 'Infinity':
                return INF;
            case '-Infinity':
                return -INF;
            case 'NaN':
                return NAN;
            default:
                return (float) $value;
        }
    }

    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, ! $asObject);
    }

    /**
     * Return a decimal as string.
     *
     * @param  float  $value
     * @param  int  $decimals
     * @return string
     */
    protected function asDecimal($value, $decimals)
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * Return a timestamp as DateTime object with time set to 00:00:00.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDate($value)
    {
        return $this->asDateTime($value)->startOfDay();
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDateTime($value)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof CarbonInterface) {
            return Date::instance($value);
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return Date::parse(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if ($this->isStandardDateFormat($value)) {
            return Date::instance(Carbon::createFromFormat('Y-m-d', $value)->startOfDay());
        }

        $format = $this->getDateFormat();

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        if (Date::hasFormat($value, $format)) {
            return Date::createFromFormat($format, $value);
        }

        return Date::parse($value);
    }

    /**
     * Determine if the given value is a standard date format.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Return a timestamp as unix timestamp.
     *
     * @param  mixed  $value
     * @return int
     */
    protected function asTimestamp($value)
    {
        return $this->asDateTime($value)->getTimestamp();
    }

}