<?php

namespace App\Console\Commands;

class CheckPerformance extends \Illuminate\Console\Command
{
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
	protected $signature = 'check:perf';
	/**
     * The console command description.
     *
     * @var string
     */
	protected $description = 'Check Package Performance';

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
	public function handle()
	{
		$packages = \App\Package::where(['status' => 'ACTIVE'])->orderBy('service_id')->get();
        \Log::error("status:check");

		if (!$packages->isEmpty()) {
			foreach ($packages as $package) {
				$cstatus = 'COMPLETED';
				$ndata = 'NOT ENOUGH DATA';
				$sql = 'SELECT COALESCE(CAST(AVG(G.times) AS DECIMAL(11,2)),\'' . $ndata . '\') AS times' . "\n" . '                   FROM (' . "\n" . '                       SELECT' . "\n" . '                           @rownum := @rownum + 1 AS rank,' . "\n" . '                           Z.name,' . "\n" . '                           Z.times' . "\n" . '                       FROM' . "\n" . '                           (' . "\n" . '                           SELECT' . "\n" . '                               P.name,' . "\n" . '                               (' . "\n" . '                                   CASE WHEN(' . "\n" . '                                       (' . "\n" . '                                           TIME_TO_SEC(' . "\n" . '                                               TIMEDIFF(O.updated_at, O.created_at)' . "\n" . '                                           ) / O.quantity' . "\n" . '                                       ) * 1000' . "\n" . '                                   ) > 1440 THEN(' . "\n" . '                                       (' . "\n" . '                                           TIME_TO_SEC(' . "\n" . '                                               TIMEDIFF(O.updated_at, O.created_at)' . "\n" . '                                           ) / O.quantity' . "\n" . '                                       ) * 1000' . "\n" . '                                   ) ELSE(' . "\n" . '                                       (' . "\n" . '                                           TIME_TO_SEC(' . "\n" . '                                               TIMEDIFF(O.updated_at, O.created_at)' . "\n" . '                                           ) / O.quantity' . "\n" . '                                       ) * 1000' . "\n" . '                                   )' . "\n" . '                               END' . "\n" . '                       ) / 3600 AS times' . "\n" . '                   FROM' . "\n" . '                       packages P,' . "\n" . '                       orders O' . "\n" . '                   WHERE' . "\n" . '                       O.package_id = P.id AND O.status = \'' . $cstatus . '\' AND P.id = \'' . $package->id . '\'' . "\n" . '                   ORDER BY' . "\n" . '                       O.updated_at' . "\n" . '                   DESC' . "\n" . '                   LIMIT 1' . "\n" . '                   ) Z,' . "\n" . '                   (' . "\n" . '                   SELECT' . "\n" . '                       @rownum := 0' . "\n" . '                   ) r' . "\n" . '                   ORDER BY' . "\n" . '                       Z.times' . "\n" . '                   ) G' . "\n" . '                   WHERE' . "\n" . '                   G.rank IN (FLOOR((@rownum+1)  / 2) , CEIL((@rownum+1)  / 2))';
                {
					$avgtime = \DB::select($sql);
				}

				if (!is_null($avgtime)) {
					if ($avgtime[0]->times != $ndata) {
					    $minutes = ($avgtime[0]->times)*60;
					    $hours = floor($minutes / 60);
                        $min = $minutes - ($hours * 60);
                        $min =  number_format((float)$min, 2, '.', '');
                     	if ($minutes >= 120) {
						$package->performance = $hours . ' hrs' . ' '. $min . ' mins' ;
						$package->save();
                     	}
                     	else if ($minutes >= 60) {
						$package->performance = $hours . ' hr' . ' '. $min . ' mins' ;
						$package->save();
                     	}
                     	else if ($minutes < 60) {
						$package->performance =  $min . ' mins' ;
						$package->save();
                     	}
					}
				}
			}
		}
	}
}

?>