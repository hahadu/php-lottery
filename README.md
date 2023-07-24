# php-lottery

基本抽奖算法

```php
    $prize_arr = [

        ['id'=>1,'prize'=>'平板电脑','prorate'=>1],
        ['id'=>2,'prize'=>'数码相机','prorate'=>5],
        ['id'=>3,'prize'=>'音箱设备','prorate'=>10],

        ['id'=>4,'prize'=>'4G优盘','prorate'=>12],

        ['id'=>5,'prize'=>'10Q币','prorate'=>22],

        ['id'=>6,'prize'=>'下次没准就能中哦','prorate'=>50],

    ];

    $lottery = (new Lottery());
    dump($lottery->run($prize_arr));

```
