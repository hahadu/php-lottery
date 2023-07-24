<?php

namespace Hahadu\Lottery;

use Illuminate\Support\Arr;
use Hahadu\Helper\ArrayHelper;

class Lottery
{

    protected $prize_data = [];
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
     * @param $prize_arr
     * @return array
     */
    public static function exec($prize_arr)
    {
        return (new self)->run($prize_arr);

    }

    /**
     *
     * @param $prize_arr
     * @return array
     */
    public function build_pend_list($prize_arr): array
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
        $this->prize_data = $prize_arr;
        //如果中奖数据是放在数据库里，这里就需要进行判断中奖数量

        //在中1、2、3等奖的，如果达到最大数量的则unset相应的奖项，避免重复中大奖
        //总概率（prorate总和）等于100%

        $pendingArr = $this->build_pend_list($prize_arr);

        $prize_id = $this->get_prize($pendingArr); //根据概率获取奖项id

        $res['prize'] = $this->search($prize_id)[0]; //中奖项

        //将中奖项从数组中剔除，剩下未中奖项，如果是数据库验证，这里可以省掉
        $unset_prize = $this->search_filter($prize_id);

        shuffle($unset_prize); //打乱数组顺序

        $res['list'] = $unset_prize;
        return $res;
    }

    /**
     * 搜索匹配结果相同的数组
     * @param $id
     * @return array
     */
    private function search($id): array
    {
        return array_values(array_filter($this->prize_data,function ($item)use($id){
            if($item['id']==$id){
                return $item;
            }
            return false;
        }));

//        return array_map(function ($item){
//            unset($item['prorate']);
//            return $item;
//        },$array);
    }

    /**
     * 返回除搜索结果以为的数组
     * @param $id
     * @return array
     */
    private function search_filter($id): array
    {
        return array_values(array_filter($this->prize_data,function ($item)use($id){
            if($item['id']!=$id){
                return $item;
            }
            return false;
        }));
//        return array_map(function ($item){
//            unset($item['prorate']);
//            return $item;
//        },$array);

    }

}
