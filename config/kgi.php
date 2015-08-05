<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 15/8/1
 * Time: 上午11:12
 */

#defined("KGI_ACCOUNT") or define("KGI_ACCOUNT", "GOLDMF");
#define("KGI_ACCOUNT", "GOLDMF");
#define("KGI_MSGTYPE", "D");
#define("KGI_SELL_MYGTYPE", "");

return [
    //server config
    "kgi_mid_server_ip" => "106.75.194.35",
    "kgi_mid_server_port" => "51003",
    "kgi_redis_server_ip" => "106.75.194.35",
    "kgi_redis_server_port" => "6379",
    "kgi_redis_key_prefix" => "FIX_EXE_",
    //header
    "kgi_begin_string" => "FIX.4.2", //8
    "kgi_sender_comp_id" => "GOLDMF", //49
    "kgi_target_comp_id" => "KGITEST", //56
    "kgi_creat_order_msgtype" => "D", //35
    "kgi_cancel_order_msgtype" => "F",
    "kgi_modify_order_msgtype" => "G",
    //body
    "kgi_account" => "GOLDMF", //1
    "kgi_buy_order_side" => 1, //54 - buy
    "kgi_sell_order_side" => 2, //54 - sell
    "kgi_limit_or_better_ordtype" => 7, //40
    "kgi_hand_inst" => 1, //21
    "kgi_exchange"  => "HK" //100
];