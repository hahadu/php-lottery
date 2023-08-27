<?php

namespace Hahadu\Lottery;

class Lottery
{

    /** @var array  */
    protected $read_list = [];
    /**
     * 概率算法获取中奖id
     * @param $proArr
     * @return int|string
     */
    protected function get_prize($proArr) {

        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {

            $randNum = mt_rand(1, $proSum);

            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }

        }
        unset ($proArr);
        return $result;
    }

    /**
     * 执行抽奖
     * @param array $prize_arr
     * @return array
     */
    public static function exec(array $prize_arr)
    {
        return (new self)->run($prize_arr);

    }

    /**
     *
     * @param array $prize_arr
     * @return array
     */
    public function build_pend_list(array $prize_arr): array
    {
        $pendingArr = [];
        foreach ($prize_arr as $k => $v) {

            $pendingArr[$v['id']] = $v['prorate'];

        }
        return $pendingArr;

    }
    /**
     * 执行抽奖
     * @param $prize_arr
     * @return array
     */
    public function run($prize_arr){
        $prize = $this->startPrize($prize_arr);
        $res['prize'] = $prize; //抽中的奖项
        $res['list'] = $this->shuffleUnsetPrize($prize['id']);
        return $res;
    }

    public function startPrize($prize_arr)
    {
        $this->read_list = $prize_arr;
          //总概率（prorate总和）等于100%
        $pendingArr = $this->build_pend_list($prize_arr);
        $prize_id = $this->get_prize($pendingArr); //根据概率获取奖项id
        return $this->search($prize_id);
    }

    /**
     * 未奖的奖项列表
     * @param $prize_id
     * @return array
     */
    protected function shuffleUnsetPrize($prize_id)
    {
        //将中奖项从数组中剔除，剩下未中奖项，如果是数据库验证，这里可以省掉
        $unset_prize = $this->search_filter($prize_id);

        shuffle($unset_prize); //打乱数组顺序
        return $unset_prize;
    }
    /**
     * 搜索匹配结果相同的数组
     * @param $id
     * @return array
     */
    private function search($id): array
    {
        return array_values(array_filter($this->read_list,function ($item)use($id){
            if($item['id']==$id){
                return $item;
            }
            return false;
        }))[0];
    }

    /**
     * 返回除搜索结果以为的数组
     * @param $id
     * @return array
     */
    private function search_filter($id): array
    {
        return array_values(array_filter($this->read_list,function ($item)use($id){
            if($item['id']!=$id){
                return $item;
            }
            return false;
        }));

    }

}
