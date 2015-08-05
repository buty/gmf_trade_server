<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\UserTradeList;

class updateCloseAmountPrice extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'stock:updatecolse';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update user stock close price and amount.';

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
		if(!$this->_checkTime()) {
			$this->info('update time is limited');
			return false;
		}
		$user_trade_list = new UserTradeList;

		$i = 0; $step = 20;
		while ($data = $user_trade_list->orderBy('id', 'DESC')->offset($i)->limit(20)->get()->toArray()) {
			if(!$data) break;

			foreach ($data as $k => $v) {
				$user_trade_list->where(
					'id', $v['id']
				)->update(
					[
					'last_amount' => $v['stock_amount'],
					'last_price'  => $v['cost_price']
					]
				);
			}
			$i += $step;
		}

		$this->info('update close amount price task finish...');
	}

	private function _checkTime() {
		return true;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			//['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
