<?php

namespace App\Component\record\src\controllers\console;

use App\Constants\ResponseCode;
use Exception;
use vhallComponent\decouple\controllers\BaseController;

/**
 * RecordController extends BaseController
 *
 * @uses     yangjin
 * @date     2020-08-12
 * @author   jin.yangjin@vhall.com
 * @license  PHP Version 7.3.x {@link http://www.php.net/license/3_0.txt}
 */
class RecordController extends BaseController
{


    /**
     * @return mixed
     * @author  jin.yang@vhall.com
     * @date    2020-08-12
     */

    /**``
     * 点播-删除记录
     *
     *
     * @throws Exception
     * @author ensong.liu@vhall.com
     * @date   2019-05-21 18:48:27
     */
    public function deleteAction()
    {
        //参数列表
        $params = $this->getParam();
        vss_validator($params, [
            'record_id' => 'required',
        ]);

        $params['app_id'] = vss_service()->getTokenService()->getAppId();
        $roomInfo = vss_model()->getRoomsModel()->getInfoByIlId($params['il_id']);
        if($roomInfo['record_id'] == $params['record_id']){
            $this->fail(ResponseCode::COMP_RECORD_DELETE_FAILED);
        }
        //返回数据
        $this->success(vss_service()->getRecordService()->del($params));
    }

    /**
     * 详情
     *
     */
    public function infoAction()
    {
        $params = $this->getParam();
        vss_validator($params, [
            'record_id' => 'required',
        ]);
        $params['vod_id'] = $params['record_id'];
        $data = vss_service()->getRecordService()->info($params);
        $this->success($data);
    }

    /**
     * 点播-获取列表
     *
     *
     * @author ensong.liu@vhall.com
     * @date   2019-05-21 18:48:27
     */
    public function listAction()
    {
        //参数列表
        $params = $this->getParam();
        vss_validator($params, [
            'il_id' => 'required',
            'page'  => '',
            'pagesize' => '',
        ]);

        $data = vss_service()->getRecordService()->getRecordListByConsole($params);

        $this->success($data);
    }

    /**
     * 房间-默认回放
     *
     * @return void
     *
     * @author  ensong.liu@vhall.com
     * @date    2019-02-21 16:11:26
     * @method GET
     * @request int     il_id       房间id
     * @request string  record_id   回放id
     */
    public function setDefaultRecordAction()
    {
        $params = $this->getParam();
        vss_service()->getRecordService()->setDefaultRecord($params);
        $this->success();
    }
}
