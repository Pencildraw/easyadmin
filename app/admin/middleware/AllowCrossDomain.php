<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/12/23
 * Time: 15:10
 * manongw.com 承接小程序，网页，app，h5等开发
 */
namespace app\api\middleware;
class AllowCrossDomain{
    /**
     * 设置跨域
     * @param $request
     * @param \Closure $next
     * @return mixed|void
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $origin = $request->header('Origin', '');
        //OPTIONS请求返回204请求
        if ($request->method(true) === 'OPTIONS') {
            $response->code(204);
        }
        $response->header([
            'Access-Control-Allow-Origin'      => $origin,
            'Access-Control-Allow-Methods'     => 'GET,POST,PUT',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers'     => '*',
        ]);
        return $response;
    }
}