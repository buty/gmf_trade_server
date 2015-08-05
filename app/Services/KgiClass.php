<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 15/8/1
 * Time: 上午10:38
 */

namespace App\Services;

use Illuminate\Support\Facades\Config;
use PhpSpec\Exception\Exception;
use Psy\Exception\ErrorException;
use App\Models\KgiOrder;

class KgiClass {

    static public $TagFieldMapping = array(
        1=>"account",
        11=>"cl_order_id",
        8=>"begin_string",
        35=>"msg_type",
        49=>"sender_comp_id",
        56=>"target_comp_id",
        6=>"avg_px",
        14=>"cum_qty",
        17=>"exec_id",
        20=>"exec_trans_type",
        37=>"order_id",
        38=>"order_qty",
        39=>"order_status",
        40=>"order_type",
        41=>"orig_cl_order_id",
        44=>"price",
        54=>"side",
        55=>"symbol",
        60=>"transact_time",
        58=>"text",
        150=>"exec_type",
        151=>"leaves_qty",
        100=>"exchange",
        207=>"security_exchange",
        10=>"trailer"
    );
    static public $TagFieldUpdateMapping = array(
        11=>"cl_order_id",
        6=>"avg_px",
        14=>"cum_qty",
        17=>"exec_id",
        20=>"exec_trans_type",
        37=>"order_id",
        39=>"order_status",
        41=>"orig_cl_order_id",
        58=>"text",
        150=>"exec_type",
        151=>"leaves_qty",
        100=>"exchange",
        207=>"security_exchange",
        10=>"trailer"
    );


    public static function create_order($cl_order_id, $symbol, $side, $price, $qty) {
        #$kgiConfig = Config::get('kgi');
        $header = array(
            8   => Config::get("kgi.kgi_begin_string"), //"FIX.4.2",
            49  => Config::get("kgi.kgi_sender_comp_id"), //"GOLDMF",
            56  => Config::get("kgi.kgi_target_comp_id"), //"KGITEST",
            35 => Config::get("kgi.kgi_creat_order_msgtype"),//"D"
        );

        $time_str = date("Y-m-d H:i:s");
        $body = array(
            1   => Config::get("kgi.kgi_account"),
            11  => $cl_order_id,
            21  => Config::get("kgi.kgi_hand_inst"),
            38  => $qty,
            40  => Config::get("kgi.kgi_limit_or_better_ordtype"),
            44  => $price,
            54  => $side, //1 – Buy 2 – Sell
            55  => $symbol,
            60  => $time_str,
            100 => Config::get("kgi.kgi_exchange")
        );

        $socket_write_json = self::_pack_tcp_request_json($header, $body);

        self::_tcp_send_request($socket_write_json);

        //$return = array_merge($header, $body);
        return array("cl_order_id" => $cl_order_id);
    }

    public static function cancel_order($cl_order_id, $symbol, $side, $cancel_order_id) {
        //echo "cancel order...\n";

        $header = array(
            8   => Config::get("kgi.kgi_begin_string"), //"FIX.4.2",
            49  => Config::get("kgi.kgi_sender_comp_id"), //"GOLDMF",
            56  => Config::get("kgi.kgi_target_comp_id"), //"KGITEST",
            35 => Config::get("kgi.kgi_cancel_order_msgtype"), //"F"
        );

        $time_str = date("Y-m-d H:i:s");
        $body = array(
            11  => $cl_order_id,
            41  => $cancel_order_id,
            54  => $side,
            55  => $symbol,
            60  => $time_str
        );
        $socket_write_json = self::_pack_tcp_request_json($header, $body);

        self::_tcp_send_request($socket_write_json);

        $return = array_merge($header, $body);
        return $return;
    }

    public static function modify_order($cl_order_id, $symbol, $side, $modif_order_id, $price, $qty) {
        //echo "modify order...\n";

        $header = array(
            8   => Config::get("kgi.kgi_begin_string"), //"FIX.4.2",
            49  => Config::get("kgi.kgi_sender_comp_id"), //"GOLDMF",
            56  => Config::get("kgi.kgi_target_comp_id"), //"KGITEST",
            35  => Config::get("kgi.kgi_modify_order_msgtype"), //"G"
        );

        $time_str = date("Y-m-d H:i:s");
        $body = array(
            11  => $cl_order_id,
            38  => $qty,
            40  => Config::get("kgi.kgi_limit_or_better_ordtype"),
            41  => $modif_order_id,
            44  => $price,
            54  => $side,
            55  => $symbol,
            60  => $time_str
        );
        $socket_write_json = self::_pack_tcp_request_json($header, $body);

        self::_tcp_send_request($socket_write_json);

        $return = array_merge($header, $body);
        return $return;
    }

    private static function _tcp_send_request( $socket_write_json=null ){
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if( false === $sock ){
            echo "sock create error!\n";
        }
        $address = Config::get("kgi.kgi_mid_server_ip");
        $port = Config::get("kgi.kgi_mid_server_port");
        try {
            $result = socket_connect($sock, $address, $port);
            if ($result === false) {
                echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($sock)) . "\n";
                exit();
                die;
            }
            socket_write( $sock, $socket_write_json, strlen($socket_write_json) );
            while( $out_str = socket_read($sock, 2048) ){
//                echo "revice result\n";
//                echo $out_str . "\n";
                $json_data = json_decode( $out_str );
                break;
            }
            socket_close( $sock );
        }catch (\ErrorException $e) {
            //echo $e->getMessage() . "\n";
        }
    }

    private static function _pack_tcp_request_json( $head, $body = null ){

        $time_str = date("Y-m-d H:i:s");

        //create order list
        self::_create_order_list($head, $body);

        $head_set = $head;
        if($body && is_array($body)) {
            $body_set = $body;
        }else{
            $body_set = array();
        }

        $json_dat = array(
            'head_keys' => array_keys( $head_set ),
            'head_vals' => array_values( $head_set ),
            'body_keys' => array_keys( $body_set ),
            'body_vals' => array_values( $body_set ),
        );

        $reqest_json = json_encode( $json_dat );
        return $reqest_json;
    }

    //生成订单id号，暂时放在这里
    public static function _get_cl_order_id() {
        $cl_order_id = 'gmf_' . time();
        return $cl_order_id;
    }

    public static function _create_order_list($header, $body) {
        $order_list_data = array();
        $tag_field_arr = self::$TagFieldMapping;

        foreach($header as $k => $v) {
            $order_list_data[$tag_field_arr[$k]] = $v;
        }
        foreach($body as $k => $v) {
            if(isset($tag_field_arr[$k])) {
                $order_list_data[$tag_field_arr[$k]] = $v;
            }
        }
        $KgiOrderModel = new KgiOrder();
        foreach($order_list_data as $k=>$v) {
            $KgiOrderModel->{$k} = $v;
        }
        $KgiOrderModel->created_at = date("Y-m-d H:i:s");
        $KgiOrderModel->updated_at = date("Y-m-d H:i:s");
        $res = $KgiOrderModel->save();
        return $res?true:false;
    }

    public static function _update_order_list($data) {
        $update_data = array();
        $tag_field_arr = self::$TagFieldUpdateMapping;
        foreach($data as $k=>$v) {
            if(isset($tag_field_arr[$k])) {
                $update_data[$tag_field_arr[$k]] = $v;
            }
        }
        $KgiOrderModel = new KgiOrder();
        $cl_order_id = $update_data['cl_order_id'];
        $KgiOrderObj = $KgiOrderModel->where('cl_order_id', $cl_order_id)->first();
        if(empty($KgiOrderObj) && !is_object($KgiOrderObj)) {
            return false;
        }

        foreach($update_data as $k=>$v) {
            $KgiOrderObj->{$k} = $v;
        }
        $KgiOrderObj->updated_at = date("Y-m-d H:i:s");
        $res = $KgiOrderObj->save();
        return $res?true:false;
    }

    //获取需要更新的订单，脚本更新。
    static function get_order_list($msg_type=array("D"), $status=array(0,1,2,3,4,5,6,7,8,"E","N"), $start_end_time=array()) {
        $KgiOrderModel = new KgiOrder();
        $order_list = $KgiOrderModel->whereIn('order_status', $status)->whereIn('msg_type', $msg_type)->orderBy('created_at', 'desc')->get();
        $order_list_arr = $order_list->toArray();
        foreach($order_list_arr as $v) {
            self::get_order_info_by_cl_order_id($v['cl_order_id']);
        }
        return $order_list->toArray();
    }

    static function get_order_info_by_cl_order_id($cl_order_id) {
        $retrive = self::_retrive_order_report($cl_order_id);
        $result = $retrive['header'] + $retrive['body'] + $retrive['trailer'];
        //更新本地db
        self::_update_order_list($result);
        $KgiOrderModel = new KgiOrder();
        $cl_order_id = $result['11'];
        $order_info = $KgiOrderModel->where('cl_order_id', $cl_order_id)->first();
        $return = array();
        if($order_info) {
            $return = $order_info->toArray();
        }
        return $return;
    }

    private static function _retrive_order_report( $cl_order_id ){
        $redis_svr = new \Redis();
        $redis_svr->connect( Config::get("kgi.kgi_redis_server_ip"), Config::get("kgi.kgi_redis_server_port") );
        $redis_key = Config::get("kgi.kgi_redis_key_prefix") . $cl_order_id;

        $xml_report = $redis_svr->get( $redis_key );
        //echo "retriving key for :{$redis_key}\n";

        // var_dump( $xml_report );
        $report_arr = self::_parse_report_field_map_from_xml( $xml_report );
        return $report_arr;

    }

    private static function _parse_report_field_map_from_xml( $xml_string ){
        $xml_obj = simplexml_load_string( $xml_string );

        $header_map = $body_map = $trailer_map = array();

        if( !empty( $xml_obj->header) ){
            $header_map = self::_xml2arr( $xml_obj->header );
        }

        if( !empty( $xml_obj->body ) ){
            $body_map = self::_xml2arr( $xml_obj->body );
        }

        if( !empty( $xml_obj->trailer ) ){
            $trailer_map = self::_xml2arr( $xml_obj->trailer );
        }


        $map_pack = array(
            'header' => $header_map,
            'body' => $body_map,
            'trailer' => $trailer_map,
        );
        return $map_pack;
    }


    private static function _xml2arr( $xml_obj ){
        if( empty( $xml_obj ) ){
            return array();
        }
        $map = array();
        foreach ($xml_obj->field as $value) {
            $f = intval( $value['number'] );
            $val = strval( $value );
            $map[$f] = $val;
        }
        return $map;
    }
}