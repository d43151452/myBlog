<?php

namespace app\model;

use think\Model;

class Logs extends Model
{
    public function getDateAttr($value){
        return str_replace('-','/',$value);
    }
}
