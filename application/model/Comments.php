<?php

namespace app\model;

use think\Model;

class Comments extends Model
{
    public function getContentAttr($value)
    {
        $reg = '/\[:EM(\d+)\]/';
        return preg_replace($reg, '<img src="/static/qq/$1.gif" alt="")">', $value);
    }

    public function getReplyAttr($value)
    {
        $reg = '/\[:EM(\d+)\]/';
        return preg_replace($reg, '<img src="/static/qq/$1.gif" alt="")">', $value);
    }
}
