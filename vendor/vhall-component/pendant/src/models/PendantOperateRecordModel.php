<?php

namespace vhallComponent\pendant\models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\ModelTrait;
use vhallComponent\decouple\models\WebBaseModel;

/**
 * Class PendantOperateRecordModel
 *
 * @package  vhallComponent\pendant\models
 * @property integer $id
 * @property integer $pendant_id               挂件id
 * @property integer $account_id               操作人id
 * @property integer $il_id                    房间id
 * @property integer $date                     日期
 * @property integer type                      操作类型，1=点击
 * @property string  $created_at               创建时间
 * @property string  $updated_at               更新时间
 * @property string  $deleted_at
 *
 * @date     2021/2/22
 * @author   jun.ou@vhall.com
 * @license  PHP Version 7.3.x {@link http://www.php.net/license/3_0.txt}
 */
class PendantOperateRecordModel extends WebBaseModel
{
    

    protected $table = 'pendant_operate_record';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * @param Builder $model
     * @param array   $condition
     *
     * @return Builder
     *
     * @date     2021/3/18
     * @author   jun.ou@vhall.com
     * @license  PHP Version 7.3.x {@link http://www.php.net/license/3_0.txt}
     */
    public function buildCondition(Builder $model, array $condition): Builder
    {
        $model = parent::buildCondition($model, $condition); // TODO: Change the autogenerated stub

        $model->when(!empty($condition['start_time']), function ($query) use ($condition) {
            $query->where('date', '>=', $condition['start_time']);
        });

        $model->when(!empty($condition['end_time']), function ($query) use ($condition) {
            $query->where('date', '<=', $condition['end_time']);
        });

        return $model;
    }

    /**
     * @param $ilId
     * @param $pendantId
     * @param $data
     *
     * @return array|false|PendantStatsModel
     *
     * @date     2021/3/18
     * @author   jun.ou@vhall.com
     * @license  PHP Version 7.3.x {@link http://www.php.net/license/3_0.txt}
     */
    public function create($data)
    {
        $datetime         = date('Y-m-d H:i:s');
        $this->il_id      = $data['il_id'];
        $this->pendant_id = $data['pendant_id'];
        $this->account_id = $data['account_id'];
        $this->type       = $data['type'];
        $this->date       = $data['date'];
        $this->updated_at = $datetime;
        $this->created_at = $datetime;
        if (!$this->save()) {
            return false;
        }
        return $this->toArray();
    }

    /**
     * @param $ilId
     * @param $pendantId
     * @param $date
     *
     * @return int
     *
     * @date     2021/3/24
     * @author   jun.ou@vhall.com
     * @license  PHP Version 7.3.x {@link http://www.php.net/license/3_0.txt}
     */
    public function getUv($ilId, $pendantId, $date)
    {
        return self::query()
            ->where(['il_id' => $ilId])
            ->where(['pendant_id' => $pendantId])
            ->where(['date' => $date])
            ->distinct('account_id')
            ->count('account_id');
    }
}