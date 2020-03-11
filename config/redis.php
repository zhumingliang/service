<?php
/**
 * Created by singwa
 * User: singwa
 * motto: 现在的努力是为了小时候吹过的牛逼！
 * Time: 00:40
 */
return [
    "code_pre" => "mall_code_pre_",
    "code_expire" => 60,
    "token_pre" => "mall_token_pre",
    "cart_pre" => "mall_cart_",
    // 延迟队列 - 订单是否需要取消状态检查
    "order_status_key" => "order_status",
    "order_expire" => 20*60,
];