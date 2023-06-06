<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\templateProcessor;
use ZipArchive;
use App\{App, ProjectPlans, RequestForExtension, RequestForExtensionBids, Order};

class OrderController extends Controller
{
	function getOrders(Request $request)
	{

		$title = "Orders";
		if ($request->year != null) {
			$year = $request->year;
			$requests = RequestForExtension::select("order_request.*", "request_for_extension.*")->where("request_date_generated", "like", $year . "%")->orderBy("request_for_extension.request_id", "desc")->leftJoin("order_request", "order_request.request_id", "request_for_extension.request_id")->get();
			return back()->withInput()->with("requests", $requests);
		} else {
			$year = date("Y");
			$requests = RequestForExtension::select("order_request.*", "request_for_extension.*")->where("request_date_generated", "like", $year . "%")->orderBy("request_for_extension.request_id", "desc")->leftJoin("order_request", "order_request.request_id", "request_for_extension.request_id")->get();
			$links = getUserLinks();
			$user_privilege = getUserPrivilege();


			return view("admin.order", ["links" => $links, 'user_privilege' => $user_privilege, "title" => $title, "year" => $year, "requests" => $requests]);
		}
	}


	function submitOrder(Request $request)
	{
		$data = $request->validate([
			"order_date_generated" => "required|after:request_date_generated",
			"order_number" => "required",
			"order_date_generated" => "required|after:date_generated|before_or_equal:requested_date",
			"order_remarks" => "nullable"
		]);
		$APP = new APP;
		$order = Order::where("request_id", $request->request_id)->first();
		$message = "success";
		if ($order === null) {
			$duplicate = Order::where("order_number", $request->order_number)->count();
			if ($duplicate > 0) {
				$message = "duplicate";
			} else {
				$updated_order = Order::create([
					"order_date_generated" => date("Y-m-d", strtotime($request->order_date_generated)),
					"order_number" => $request->order_number,
					"request_id" => $request->request_id,
					"order_remarks" => $request->order_remarks
				]);

				$ids = [];
				$request_for_extension = RequestForExtension::find($request->request_id);
				$bids = RequestForExtensionBids::where("request_id", $request->request_id)->get();
				foreach ($bids as $bid) {
					$value = $APP->getBid($bid->project_bid);
					array_push($ids, $value->plan_id);
				}

				$ids = implode(",", $ids);
				$process = "post_qualification";
				$date = $request->requested_date;
				$remarks = "ORDER NUMBER:" . $updated_order->order_number . ", " . $request_for_extension->request_reason;
				$APP = new APP();
				$message = $APP->extendSpecificProcess($ids, $process, $date, $remarks);
			}
		} else {
			$duplicate = Order::where([["order_number", $request->order_number], ["order_id", "<>", $order->order_id]])->count();
			if ($duplicate > 0) {
				$message = "duplicate";
			} else {
				$updated_order = Order::find($order->order_id);
				$updated_order->order_date_generated = date("Y-m-d", strtotime($request->order_date_generated));
				$updated_order->order_number = $request->order_number;
				$updated_order->request_id = $request->request_id;
				$updated_order->order_remarks = $request->order_remarks;
				$updated_order->save();

				// $ids=[];
				// $request_for_extension=RequestForExtension::find($updated_order->request_id);
				// $bids=RequestForExtensionBids::where("request_id",$updated_order->request_id)->get();
				// foreach($bids as $bid){
				// 	$value=$APP->getBid($bid->project_bid);
				// 	array_push($ids,$value->plan_id);
				// }
				//
				// $ids=implode(",",$ids);
				// $process="post_qualification";
				// $date=$request->requested_date;
				// $remarks="ORDER NUMBER:".$updated_order->order_number.", ".$request_for_extension->request_reason;
				// $APP= new APP();
				// $message=$APP->extendSpecificProcess($ids,$process,$date,$remarks);


			}
		}


		return back()->with("message", $message);
	}


	function generateOrderRequest(Request $request)
	{
		$APP = new APP;
		$order = Order::where("order_number", $request->id)->first();
		$order = Order::find($order->order_id);
		$RequestForExtension = RequestForExtension::find($order->request_id);
		$RequestForExtensionBids = RequestForExtensionBids::where("request_id", $RequestForExtension->request_id)->get();
		$bids_array = [];
		$formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
		$processed_bids = [];
		foreach ($RequestForExtensionBids as $key => $value) {
			if (in_array($value->project_bid, $bids_array) === false) {
				$bids_format = (object)[];
				$clusters = $APP->getClusterBids($value->project_bid);
				$bid = $APP->getBid($value->project_bid);
				$bidders_rank = $APP->getBiddersData($bid->procact_id, "active,responsive,non-responsive");
				$rank = 1;
				if (count($bidders_rank) == 1) {
					if ($bid->procact_mode_id == 1) {
						$rank = "Lone Bidder";
					} else {
						$rank = "Lone Quotation";
					}
				} else {
					foreach ($bidders_rank as $key_rank => $bidder_rank) {
						if ($bidder_rank->project_bid === $bid->project_bid) {
							if ($bid->procact_mode_id == 1) {
								$rank = date("jS", strtotime($key_rank + 1)) . " LCB";
							} else {
								$rank = date("jS", strtotime($key_rank + 1)) . " LCPQ";
							}
							break;
						}
					}
				}
				$title = '';
				$total = 0;
				$project_cost = '';
				if (count($clusters) > 1) {
					$letter = "A";
					foreach ($clusters as $cluster) {
						array_push($bids_array, $cluster->project_bid);
						$temp = $letter . ". " . $cluster->project_title;
						$title = $title . "   " . $temp;
						$total = $total + $cluster->project_cost;
						$project_cost = $project_cost . " PHP " . number_format((float)$cluster->project_cost, 2, ".", ",");
						$letter = ++$letter;
						if ($cluster->special_case_1 == 1) {
							$title = $cluster->project_title;
						}
					}
					$project_cost = $project_cost . "= PHP " . number_format((float)$total, 2, ".", ",");
				} else {
					$title = $clusters[0]->project_title;
					$project_cost = "PHP " . number_format((float)$bid->project_cost, 2, ".", ",");
				}
				$location = strtoupper(strtolower($clusters[0]->municipality_name)) . ",Benguet";
				$bids_format->title = $title;
				$bids_format->title = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
				$bids_format->rank = $rank;
				$bids_format->business_name = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($bid->business_name))));
				$bids_format->date_opened = $clusters[0]->open_bid;
				$bids_format->date_formatted = date("F d, Y", strtotime($clusters[0]->open_bid));
				$bids_format->opening_number = getOpeningNumber($clusters[0]->procact_id);
				$bids_format->procact_mode_id = $clusters[0]->procact_mode_id;
				$bids_format->location = $location;
				$bids_format->project_cost = $project_cost;
				array_push($processed_bids, $bids_format);
			}
		}
		$request_bids = count($processed_bids);

		if ($request_bids === 1) {
			$requestTemplateProcessor = new templateProcessor(public_path() . "\\" . "word_templates/BAC Request for Extension one.docx");
			$orderTemplateProcessor = new templateProcessor(public_path() . "\\" . "word_templates/Order one.docx");
			$requestTemplateProcessor->setValue("bidder", str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($processed_bids[0]->business_name)))));
			$requestTemplateProcessor->setValue("rank", $processed_bids[0]->rank);
			$requestTemplateProcessor->setValue("date_opened", $processed_bids[0]->date_formatted);
			$requestTemplateProcessor->setValue("project_title", $processed_bids[0]->title);
			if ($processed_bids[0]->procact_mode_id === 1) {
				$requestTemplateProcessor->setValue("bid_or_quotation", "bid");
				$orderTemplateProcessor->setValue("bid_and_quotation", "bid out");
			} else {
				$requestTemplateProcessor->setValue("bid_or_quotation", "quotation");
				$orderTemplateProcessor->setValue("bid_and_quotation", "quotation");
			}

			$orderTemplateProcessor->setValue("date_opened", $processed_bids[0]->date_formatted);
			$orderTemplateProcessor->setValue("no", "1");
			$orderTemplateProcessor->setValue("title", strtoupper(strtolower($processed_bids[0]->title)));
			$orderTemplateProcessor->setValue("location", $processed_bids[0]->location);
			$orderTemplateProcessor->setValue("abc", $processed_bids[0]->project_cost);
		} else if ($request_bids > 1) {
			$processed_bids = $APP->sortObject($processed_bids, array("date_opened" => "asc", "procact_mode_id" => "asc", "opening_number" => "asc"));
			$dates = array_unique(array_column($processed_bids, "date_formatted"));
			$modes = array_unique(array_column($processed_bids, "procact_mode_id"));
			if (count($dates) == 1) {
				$requestTemplateProcessor = new templateProcessor(public_path() . "\\" . "word_templates/BAC Request for Extension multiple 1.docx");
				$orderTemplateProcessor = new templateProcessor(public_path() . "\\" . "word_templates/Order multiple 1.docx");
				$requestTemplateProcessor->setValue("project_numbers", $formatter->format((int)count($processed_bids)) . " (" . count($processed_bids) . ")");
				$requestTemplateProcessor->setValue("date_opened", $processed_bids[0]->date_formatted);
				$orderTemplateProcessor->setValue("date_opened", $processed_bids[0]->date_formatted);
				$orderTemplateProcessor->cloneRow("no", $request_bids);
				$index = 1;
				foreach ($processed_bids as $processed_bid) {
					$orderTemplateProcessor->setValue("no#" . $index, $index);
					$orderTemplateProcessor->setValue("title#" . $index, $processed_bid->title);
					$orderTemplateProcessor->setValue("location#" . $index, $processed_bid->location);
					$orderTemplateProcessor->setValue("abc#" . $index, $processed_bid->project_cost);
					$index++;
				}
			} else {

				$dates_formatted = '';
				$requestTemplateProcessor = new templateProcessor(public_path() . "\\" . "word_templates/BAC Request for Extension multiple 2.docx");
				$orderTemplateProcessor = new templateProcessor(public_path() . "\\" . "word_templates/Order multiple 2.docx");
				$requestTemplateProcessor->setValue("project_numbers", $formatter->format((int)count($processed_bids)) . " (" . count($processed_bids) . ")");
				$orderTemplateProcessor->cloneBlock("table_block", count($dates), true, true);
				$date_index = 0;
				foreach ($dates as $date) {
					if ($date_index === 0) {
						$dates_formatted = $dates_formatted . $date;
					} else if ($date_index === count($dates) - 1) {
						$dates_formatted = $dates_formatted . " and " . $date;
					} else {
						$dates_formatted = $dates_formatted . "," . $date;
					}
					$orderTemplateProcessor->setValue("opening#" . ($date_index + 1), $date);
					$projects = array_filter($processed_bids, function ($value) use ($date) {
						return $value->date_formatted == $date;
					});
					$orderTemplateProcessor->cloneRow("no#" . ($date_index + 1), count($projects));

					$project_index = 1;
					foreach ($projects as $project) {
						$orderTemplateProcessor->setValue("no#" . ($date_index + 1) . "#" . $project_index, $project_index);
						$orderTemplateProcessor->setValue("title#" . ($date_index + 1) . "#" . $project_index, $project->title);
						$orderTemplateProcessor->setValue("location#" . ($date_index + 1) . "#" . $project_index, $project->location);
						$orderTemplateProcessor->setValue("abc#" . ($date_index + 1) . "#" . $project_index, $project->project_cost);
						$project_index++;
					}

					$date_index = $date_index + 1;
				}
				$requestTemplateProcessor->setValue("date_opened", $dates_formatted);
				$orderTemplateProcessor->setValue("date_opened", $dates_formatted);
			}

			if (count($modes) === 1) {
				if ($modes[0] === 1) {
					$requestTemplateProcessor->setValue("bid_and_quotation", "bids");
					$orderTemplateProcessor->setValue("bid_and_quotation", "bid out");
				} else if ($modes[0] === 2) {
					$requestTemplateProcessor->setValue("bid_and_quotation", "quotations");
					$orderTemplateProcessor->setValue("bid_and_quotation", "quotations");
				} else {
					$requestTemplateProcessor->setValue("bid_and_quotation", "quotations");
					$orderTemplateProcessor->setValue("bid_and_quotation", "quotations");
				}
			} else {
				$requestTemplateProcessor->setValue("bid_and_quotation", "bids and quotations");
				$orderTemplateProcessor->setValue("bid_and_quotation", "bid out and quotations");
			}

			$modes_string = [];

			foreach ($modes as $mode_key => $mode) {
				$temp = '';
				if ($modes[0] === 1) {
					$temp = "Public Bidding";
				} else if ($modes[0] === 2) {
					$temp = "Small Value Procurement";
				} else {
					$temp = "Negotiated Procurement Under Two Failed Bidding";
				}

				if ($mode_key === 0) {
					$modes_string = $temp;
				} else if ($mode_key === (count($modes) - 1)) {
					$modes_string = $modes_string . " and " . $temp;
				} else {
					$modes_string = $modes_string . ", " . $temp;
				}
			}
			$requestTemplateProcessor->setValue("bidding_or_svp", $modes_string);
		} else {
			return abort(403, "Missing Requested Projects!");
		}

		$governor = DB::table("governors")->select(DB::raw("UPPER(name) as uc_name"))->orderBy("governor_id", "desc")->first();
		$governor_name = $governor->uc_name;

		$bac = DB::table("bids_and_awards_committee")
			->select(
				"bids_and_awards_committee.*",
				DB::raw("UPPER(CONCAT(if(bac_ch.member_prefix is null ,'',bac_ch.member_prefix),' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname)) AS bac_chairman_name"),
				DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
				DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
				DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
				DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
				DB::raw("UPPER(CONCAT(if(bac_twg_ch.member_prefix is null ,'',bac_twg_ch.member_prefix),' ',bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname)) AS bac_twg_chairman_name"),
				DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
			)
			->join("member as bac_ch", "bac_ch.member_id", "=", "bids_and_awards_committee.bac_chairman")
			->join("member as bac_vice_ch", "bac_vice_ch.member_id", "=", "bids_and_awards_committee.bac_vice_chairman")
			->join("member as bac_alternate_vice_ch", "bac_alternate_vice_ch.member_id", "=", "bids_and_awards_committee.bac_alternate_vice_chairman")
			->join("member as bac_sec_ch", "bac_sec_ch.member_id", "=", "bids_and_awards_committee.bac_sec_chairman")
			->join("member as bac_sec_vice_ch", "bac_sec_vice_ch.member_id", "=", "bids_and_awards_committee.bac_sec_vice_chairman")
			->join("member as bac_twg_ch", "bac_twg_ch.member_id", "=", "bids_and_awards_committee.bac_twg_chairman")
			->join("member as bac_twg_vice_ch", "bac_twg_vice_ch.member_id", "=", "bids_and_awards_committee.bac_twg_vice_chairman")
			->orderBy("bac_id", "desc")
			->first();

		$requestTemplateProcessor->setValue("bac_request_date", date("F d, Y", strtotime($order->order_date_generated)));
		$requestTemplateProcessor->setValue("governor", $governor_name);
		$requestTemplateProcessor->setValue("bac_chairperson", $bac->bac_chairman_name);

		// order_remarks
		$orderTemplateProcessor->setValue("order_number", $order->order_number);
		$orderTemplateProcessor->setValue("request_date", date("F d, Y", strtotime($RequestForExtension->request_date)));
		$orderTemplateProcessor->setValue("project_number", $formatter->format((int)count($processed_bids)) . " (" . count($processed_bids) . ")");
		$orderTemplateProcessor->setValue("governor", $governor_name);
		$orderTemplateProcessor->setValue("day", date("jS", strtotime($order->order_date_generated)));
		$orderTemplateProcessor->setValue("month_year", date("F Y", strtotime($order->order_date_generated)));

		$path1 = public_path("/word_results/BAC Request For Extension.docx");
		$path2 = public_path("/word_results/Order.docx");
		$requestTemplateProcessor->saveAs($path1);
		$orderTemplateProcessor->saveAs($path2);
		$headers = array(
			"Content-Type" => "application/octet-stream",
		);
		$zip_file = public_path("/zips/Order.zip");
		$zip = new ZipArchive;
		$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$zip->addFile($path1, "BAC Request For Extension.docx");
		$zip->addFile($path2, "Order.docx");
		$zip->close();

		return  response()->file($zip_file)->deleteFileAfterSend(true);
	}
}
