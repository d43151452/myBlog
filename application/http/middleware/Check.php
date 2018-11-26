<?php

namespace app\http\middleware;

use think\Controller;

class Check extends Controller
{
    public function handle($request, \Closure $next)
    {
        if(!session('login')){
            return $this->error('您尚未登录作者账号');
        }
        return $next($request);
    }
}
