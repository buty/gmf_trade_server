<?php
namespace App\Console\Commands;

use App\Services\KgiClass;
use Illuminate\Console\Command;
use App\Services\KgiClass as Kgi;
use Illuminate\Support\Facades\Config;

class KgiOrder extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'kgiOrder';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        echo "console start...\n";
        //test model
        //Kgi::create_order_list();
//            $update_data = array(
//                    "11" => "1234560_1438653286",
//                    "58"      => "test eric",
//                    "39" => 2
//            );
//        Kgi::_update_order_list($update_data);
        $res = Kgi::get_order_info_by_cl_order_id("1234560_1438740655");
            var_dump($res);
        die;
            $res = Kgi::get_order_list(array("D"));
            var_dump($res);
            die;




        $create_buy_order = array(
                "cl_order_id"   => Kgi::_get_cl_order_id(),
                "symbol"        => "0008",
                "price"         => "4.80",
                "qty"           => 2000,
                "side"          => Config::get("kgi.kgi_buy_order_side")
        );
        $create_order_info = Kgi::create_order($create_buy_order["cl_order_id"], $create_buy_order['symbol'], $create_buy_order['side'], $create_buy_order['price'], $create_buy_order['qty']);
        //sleep(2);
        //$order_report = Kgi::get_order_info_by_cl_order_id($create_order_info['cl_order_id']);
        //var_dump($order_report);
        die;
            /*

        $create_sell_order = array(
                "cl_order_id"   => Kgi::_get_cl_order_id(),
                "symbol"    => "0008",
                "price"     => "4.8",
                "qty"       => 2000,
                "side"      => Config::get("kgi.kgi_sell_order_side")
        );

        $create_order_info = Kgi::create_order($create_buy_order['symbol'], $create_buy_order['side'], $create_buy_order['price'], $create_buy_order['qty']);
        sleep(2);
        $order_report = Kgi::get_order_info_by_cl_order_id($create_order_info['cll_order_id']);
        var_dump($order_report);

        $cancel_order = array(
                "symbol"    => "0008",
                "side"      => Config::get("kgi.kgi_buy_order_side"),
                "cancel_order_id" => "1234560_1438328367"
        );
        //$symbol, $side, $cancel_order_id
        Kgi::cancel_order($cancel_order);

        $modify_order = array(
                "symbol"    => "0008",
                "side"      => Config::get("kgi.kgi_buy_order_side"),
                "modif_order_id" => "1234560_1438328367",
                "price"     => "4.8",
                "qty"       => 1000
        );
        //$symbol, $side, $modif_order_id, $price, $qty
        Kgi::modify_order($modify_order);
            */
	}


}
