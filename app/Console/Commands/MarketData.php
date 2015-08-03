<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Libs\ParseHq\DispatchData;

class MarketData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'market:data';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'market hq data parse.';

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
		$target = $this->argument('target');

		$dispatch = new DispatchData($target);
		$res = $dispatch->handle();
		
		$this->info($res);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['target', InputArgument::REQUIRED, 'need parse hq target. [Sh, Sz]'],
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
