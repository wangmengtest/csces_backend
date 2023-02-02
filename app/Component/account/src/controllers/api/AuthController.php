<?php

namespace App\Component\account\src\controllers\api;

use App\Component\room\src\constants\CachePrefixConstant;
use vhallComponent\decouple\controllers\BaseController;
use Illuminate\Support\Arr;

/**
 * AccountControllerTrait
 *
 * @uses     yangjin
 * @date     2020-07-30
 * @author   jin.yangjin@vhall.com
 * @license  PHP Version 7.3.x {@link http://www.php.net/license/3_0.txt}
 */
class AuthController extends BaseController
{
    public function testCreateAction()
    {
        $params = $this->getParam();
        $rule = [
            'username' => 'required',
            'phone'    => 'required',
            'org'      => 'string',
            'dept'     => 'string',
            'role_id'  => 'int'
        ];
        $arr = vss_validator($params, $rule);
        $data = vss_service()->getAccountsService()->createAccount($arr);
        $this->success($data);
    }

    public function testRedisAction()
    {
        $params = $this->getParam();
        $rule = [
            'set' => 'required',
            'key' => 'required',
            'val' => 'required',
            'get' => 'required',
        ];
        $arr = vss_validator($params, $rule);
        print_r($arr);

        $set = $arr['set'];
        vss_redis()->$set($arr['key'], $arr['val'], 3600);
        $get = $arr['get'];
        var_dump(vss_redis()->$get($arr['key']));
    }

    public function testRedisHashAction()
    {
        $params = $this->getParam();
        $rule = [
            'key' => 'required',
            'val' => 'required',
            'field' => 'required',
        ];
        $arr = vss_validator($params, $rule);
        $res = vss_redis()->hset($arr['key'],$arr['field'],$arr['val']);
        var_dump($res);
        $res = vss_redis()->expire($arr['key'], 3600);
        var_dump($res);
        echo 'room cache start' . PHP_EOL;
        $res = vss_service()->getCacheRoomService()->hGetAll($arr['key']);
        print_r($res);
        echo 'room cache end' . PHP_EOL;
        $rows   = vss_redis()->hgetall($arr['key']);
        var_dump($rows);
        $result = [];
        foreach ($rows as $row) {
            $result[] = json_decode($row, true);
        }
        print_r($result);
        return $result;
    }

    public function testRedisDelAction()
    {
        $params = $this->getParam();
        $rule = [
            'key' => 'required',
        ];
        $arr = vss_validator($params, $rule);
        var_dump(vss_redis()->del($arr['key']));
    }

    public function getEnvAction()
    {
        print_r($_ENV);
        echo '----------------------------' . PHP_EOL;
        $path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        $files = file_get_contents($path . '/.env');
        echo "<pre>";
        echo $files;
        echo "</pre>";
    }

    /**
     * 登录
     *
     * @return void
     * @author ensong.liu@vhall.com
     * @date   2019-05-08 16:02:14
     */
    public function autoLoginAction()
    {
        $params = $this->getParam();
        $rule = [
            'username' => 'required',
            'sign'     => 'required',
        ];
        $arr = vss_validator($params, $rule);
        $data = vss_service()->getAccountsService()->autoLogin($arr['username']);
        $this->success($data);
    }

    /**
     * 登录
     *
     * @return void
     * @author ensong.liu@vhall.com
     * @date   2019-05-08 16:02:14
     */
    public function loginAction()
    {
        $params = $this->getParam();
        $rule = [
            'phone'    => 'required',
            'code'     => 'required',
            'nickname' => 'required',
            'type'     => '',
        ];
        $arr = vss_validator($params, $rule);

        $data = vss_service()->getAccountsService()->login($arr);
        $this->success($data);
    }

    /**
     * 观众登录
     *
     * @return void
     * @author ensong.liu@vhall.com
     * @date   2019-05-08 16:02:14
     */
    public function loginWatchAction()
    {
        $params = $this->getParam();
        $rule = [
            'phone'    => 'required',
            'code'     => 'required',
            'nickname' => 'required',
            'type'     => '',
            'il_id'    => '',
        ];
        $arr = vss_validator($params, $rule);

        $checkCode = (env('APP_ENV') == 'production') ? 1 : 0;
        $data = vss_service()->getAccountsService()->login($arr, $checkCode);
        $this->success($data);
    }

    /**
     * 第三方网站调用登陆
     *
     */
    public function thirdLoginAction()
    {
        $data = vss_service()->getAccountsService()->thirdLogin($this->getParam());
        $this->success($data);
    }

    /**
     * 游客
     * @throws Exception
     */
    public function visitorAction()
    {
        $params = $this->getParam();
        vss_validator($params, [
            'il_id' => 'required',
        ]);
        $this->success(vss_service()->getAccountsService()->visitor($this->getParam('il_id')));
    }

    /**
     * 退出
     *
     * @return void
     * @author ensong.liu@vhall.com
     * @date   2019-05-09 00:38:19
     */
    public function logoutAction()
    {
        if ($this->accountInfo) {
            vss_service()->getAccountsService()->logout($this->accountInfo);
        }
        $this->success();
    }
}
