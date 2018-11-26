<?php

namespace app\model;

use think\Model;

class Articles extends Model
{
    public function tags(){
        return $this->belongsToMany('Tags','articles_tags','tags_id','articles_id');
    }
}
