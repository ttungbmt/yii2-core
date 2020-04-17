<?php
namespace ttungbmt\db;

use Carbon\Carbon;
use Illuminate\Support\Arr;

trait QueryTrait
{
    public function pluck($value, $key = null){
        return Arr::pluck($this->all(), $value, $key);
    }

    public function andFilterSearch(array $condition)
    {
        $condition = $this->filterCondition($condition);
        $condition = array_map('trim', $condition);


        if ($condition !== []) {
            $this->andWhere($condition);
        }

        return $this;
    }

    public function andFilterDate(array $condition)
    {
        if(isAssoc($condition)){

            foreach ($condition as $field => $item){

                list($date_from, $date_to) = $item;
                $parseDate = function ($date){ return ($date && validateDate($date, 'd/m/Y')) ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d') : $date;};
                $date_from = $parseDate($date_from);
                $date_to = $parseDate($date_to);

                if($date_from && !$date_to){
                    $this->andFilterWhere(['>=', $field, $date_from]);
                } elseif (!$date_from && $date_to){
                    $this->andFilterWhere(['<=', $field, $date_to]);
                } elseif ($date_from && $date_to){
                    if($date_from == $date_to){
                        $this->andFilterWhere(['=', $field, $date_from]);
                    } else {
                        $this->andFilterWhere(['between', $field, $date_from, $date_to]);
                    }
                }
            }
        } else {
            $parseDate = function ($date){ return ($date && validateDate($date, 'd/m/Y')) ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d') : $date;};
            $this->andFilterWhere([$condition[0], $condition[1], $parseDate($condition[2])]);
        }

        return $this;
    }
}