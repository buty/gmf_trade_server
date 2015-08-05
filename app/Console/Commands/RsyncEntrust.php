<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Libs\RsyncOrder\DispatchOrder;

class RsyncEntrust extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'rsync:stock';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Rsync stock trade orders.';

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
		$rsync = new DispatchOrder($target);
		$res = $rsync->handle();

		$this->info('success');
	}



	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['target', InputArgument::REQUIRED, '指定同步券商标识.'],
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
