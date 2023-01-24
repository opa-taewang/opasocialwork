<?php

namespace App\Console\Commands;
use Carbon;

class SellerSync extends \Illuminate\Console\Command
{
	protected $signature = 'seller:sync';
	protected $description = 'Send Seller Sync report to admin';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
	    		\Log::error('sellersync');
		$apis = \App\API::where('package_end_point', '!=', '')->get();

		foreach ($apis as $api) {
			$params = [];
			$apiRequestParams = \App\ApiRequestParam::where(['api_id' => $api->id, 'api_type' => 'package'])->get();

			if (!$apiRequestParams->isEmpty()) {
				foreach ($apiRequestParams as $row) {
					$params[$row->param_key] = $row->param_value;
				}

				$client = new \GuzzleHttp\Client();

				try {
					$param_key = 'form_params';

					if ($api->package_method === 'GET') {
						$param_key = 'query';
					}

					 {
						$res = $client->request($api->package_method, $api->package_end_point, [
							$param_key => $params,
							'headers' => ['Accept' => 'application/json']
						]);
					}

					if ($res->getStatusCode() === 200) {
						$resp = $res->getBody()->getContents();
						$r = array_cast_recursive(json_decode($resp));
						$amaps = \App\ApiMapping::where(['api_id' => $api->id])->get();

						if (!$amaps->isEmpty()) {
							foreach ($amaps as $amap) {
								$pckg = \App\Package::where(['id' => $amap->package_id, 'status' => 'INACTIVE', 'preferred_api_id' => $amap->api_id])->first();

								if (!is_null($pckg)) {
									$flag = 0;

									for ($i = 0; $i < count($r); $i++) {
										if ($r[$i][$api->package_id_key] == $amap->api_package_id) {
											$flag = 1;
											break;
										}

										if ($amap->api_package_id == '') {
											$flag = 0;
											break;
										}
									}

									if ($flag == 1) {
										$reason = 'Package added by seller.';
										//						$lastrec = \DB::table('sync_notifications')->where(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'red', 'action' => 'Package marked INACTIVE'])->orderBy('created_at', 'DESC')->first();
																														//									if(Carbon\Carbon::now()->diffInMinutes($lastrec->created_at) > 5){


										\App\SyncNotification::updateOrCreate( ['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'green', 'action' => 'Package marked ACTIVE']);
																				//	}
										$pckg->status = 'ACTIVE';
										$pckg->save();
									}
								}

								$pckg = \App\Package::where(['id' => $amap->package_id, 'status' => 'ACTIVE', 'preferred_api_id' => $amap->api_id])->first();

								if (!is_null($pckg)) {
									$flag = 0;

									for ($i = 0; $i < count($r); $i++) {
										if ($r[$i][$api->package_id_key] == $amap->api_package_id) {
											$flag = 1;
											break;
										}

										if ($amap->api_package_id == '') {
											$flag = 1;
											break;
										}
									}

									if ($flag == 0) {
										$reason = 'Package removed by seller.';
														//	$lastrec = \DB::table('sync_notifications')->where(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'red', 'action' => 'Package marked INACTIVE'])->orderBy('created_at', 'DESC')->first();
																														//									if(Carbon\Carbon::now()->diffInMinutes($lastrec->created_at) > 5){


									\App\SyncNotification::updateOrCreate( ['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'red', 'action' => 'Package marked INACTIVE']);
																														//									}
										$pckg->status = 'INACTIVE';
										$pckg->save();
									}
								}
							}
						}

						for ($i = 0; $i < count($r); $i++) {
							$amaps = \App\ApiMapping::where(['api_package_id' => $r[$i][$api->package_id_key], 'api_id' => $api->id])->get();
							$r[$i][$api->rate_key] = $r[$i][$api->rate_key] * $api->rate;

							if (!$amaps->isEmpty()) {
								foreach ($amaps as $amap) {
									$flag = 0;
									$reason = '';
									$pckg = \App\Package::where(['id' => $amap->package_id, 'status' => 'ACTIVE', 'preferred_api_id' => $amap->api_id])->first();

									if (!is_null($pckg)) {
										if ($pckg->minimum_quantity < $r[$i][$api->min_key]) {
											$reason = 'Seller minimum(' . $r[$i][$api->min_key] . ') is higher than package minimum(' . $pckg->minimum_quantity . ').';
								//		$lastrec = \DB::table('sync_notifications')->where(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'blue', 'action' => 'Minimum Quantity is Updated to (' . $r[$i][$api->min_key] . ').'])->orderBy('created_at', 'DESC')->first();
																														//if(Carbon\Carbon::now()->diffInMinutes($lastrec->created_at) > 5){


										\App\SyncNotification::updateOrCreate( ['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'blue', 'action' => 'Minimum Quantity is Updated to (' . $r[$i][$api->min_key] . ').']);
																														//}
											$pckg->minimum_quantity = $r[$i][$api->min_key];
											$pckg->save();
										}

										if ($r[$i][$api->max_key] < $pckg->maximum_quantity) {
										    if ($pckg->order_limit == 1) {
										        
										    }
										    else 
											$reason = 'Seller maximum(' . $r[$i][$api->max_key] . ') is lower than package maximum(' . $pckg->maximum_quantity . ').';
									//	$lastrec = \DB::table('sync_notifications')->where(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'blue', 'action' => 'Maximum Quantity is Updated to (' . $r[$i][$api->max_key] . ').'])->orderBy('created_at', 'DESC')->first();
																				//if(Carbon\Carbon::now()->diffInMinutes($lastrec->created_at) > 5){


											\App\SyncNotification::updateOrCreate(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'blue', 'action' => 'Maximum Quantity is Updated to (' . $r[$i][$api->max_key] . ').']);
																				//}
											$pckg->maximum_quantity = $r[$i][$api->max_key];
											$pckg->save();
										}

										if ($pckg->seller_cost < 0) {
											$pckg->seller_cost = $r[$i][$api->rate_key];
											$pckg->cost_per_item = ($r[$i][$api->rate_key])/1000;
											$pckg->save();
										}
										else {
											$sel = (double) $pckg->seller_cost;
											$selnew = (double) $r[$i][$api->rate_key];
										   

										if ($sel < $selnew) {
												$reason = 'Seller Cost is increased from ' . $sel . ' to ' . $selnew . '.';
												$vpct = $r[$i][$api->rate_key] / $pckg->seller_cost;
												$pckg->seller_cost = $r[$i][$api->rate_key];
									//	$lastrec = \DB::table('sync_notifications')->where(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'violet', 'action' => 'Package price increased to ' . ($pckg->price_per_item * $vpct)])->orderBy('created_at', 'DESC')->first();
									//	if(Carbon\Carbon::now()->diffInMinutes($lastrec->created_at) > 5){

											\App\SyncNotification::updateOrCreate( ['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'violet', 'action' => 'Package price increased to ' . ($pckg->price_per_item * $vpct)]);
									//	}
												$pckg->price_per_item = $pckg->price_per_item * $vpct;
									            $pckg->cost_per_item = $pckg->cost_per_item * $vpct;
												$pckg->save();
											}
											else if ($sel > $selnew) {
												$reason = 'Seller Cost is decreased from ' . $sel . ' to ' . $selnew . '.';
												$vpct = $r[$i][$api->rate_key] / $pckg->seller_cost;
												$pckg->seller_cost = $r[$i][$api->rate_key];
										//$lastrec = \DB::table('sync_notifications')->where(['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'violet', 'action' => 'Package price decreased to ' . ($pckg->price_per_item * $vpct)])->orderBy('created_at', 'DESC')->first();
									//	if(Carbon\Carbon::now()->diffInMinutes($lastrec->created_at) > 5){

											\App\SyncNotification::updateOrCreate( ['api_id' => $api->id, 'api_name' => $api->name, 'package_id' => $pckg->id, 'package_name' => $pckg->name, 'reason' => $reason, 'color' => 'violet', 'action' => 'Package price decreased to ' . ($pckg->price_per_item * $vpct)]);
									//	}
												$pckg->price_per_item = $pckg->price_per_item * $vpct;
						                        $pckg->cost_per_item = $pckg->cost_per_item * $vpct;
												$pckg->save();
											}
										}
									}
								}
							}
						}

					}
				}
				catch (\Exception $e) {
				}
			}
		}
	}
}

?>