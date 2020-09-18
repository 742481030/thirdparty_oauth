<?php
/**
 * Created by PhpStorm
 * User: pl
 * Date: 2020/9/18
 * Time: 10:37
 */

namespace Pl1998\ThirdpartyOauth\Handle;


use GuzzleHttp\Client;

class WeiXinOauth
{
    protected $client;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * 执行重定向扫码.
     */
    public function authorization()
    {
        $url = 'https://open.weixin.qq.com/connect/qrconnect';
        $query = array_filter([
            'app_id' => $this->config['client_id'],
            'callback' => $this->config['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'snsapi_login',
            'state' => 'STATE'
        ]);

        $url = $url.'?'.http_build_query($query).'#wechat_redirect';

        header('Location:'.$url);
        exit();
    }

    public function getAccessToken()
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';

        $query = array_filter([
            'appid' => $this->config['client_id'],
            'code' => $_GET['code'],
            'grant_type' => 'authorization_code',
            'secret' => $this->config['client_secret'],
        ]);

        return $this->client->request('POST', $url, [
            'query' => $query,
        ])->getBody()->getContents();
    }

    public function getUserInfo($aouth)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$aouth['access_token'].'&openid='.$aouth['openid'];

        return $this->client->get($url)->getBody()->getContents();
    }
}