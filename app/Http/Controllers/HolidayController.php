<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Holiday;

class HolidayController extends Controller
{
	public function getHolidays(Request $request)
	{
		$links=getUserLinks();
		$user_privilege=getUserPrivilege();
		if(isset($request->year)){
			$year=$request->year;
			$holidays=Holiday::where('holiday_date','like',$year.'%')->get();
			return back()->withInput()->with('holidays',$holidays);

		}
		else{
			$year=date('Y');
			$holidays=Holiday::where('holiday_date','like',$year.'%')->get();
			return view('admin.holiday',['links'=>$links,'user_privilege'=>$user_privilege,"year"=>$year,"holidays"=>$holidays]);
		}

	}

	public function calculateDueDate(Request $request)
	{
		$date_start=strtotime($request->date);
		$days=$request->days;
		$date_type=$request->date_type;
		$due_date=null;
		$counter=0;
		if($date_type === "Working Days" ){
			$due_date=$date_start;
			while($counter<$days){
				$due_date=strtotime("+1 day", $due_date);
				if(Date('l',$due_date)!="Saturday" && Date('l',$due_date)!="Sunday"){
					$holiday=Holiday::where('holiday_date',Date('Y-m-d',$due_date))->count();
					if($holiday===0){
						$counter=$counter+1;
					}
				}
			}
		}
		if($date_type === "Calendar Days" ){
			$due_date=strtotime("+".$days." day", $date_start);
		}

		return Date('m/d/Y',$due_date);
	}


	public function deleteHoliday($id)
	{
		$holiday=Holiday::find($id);
		$holiday->delete();
		return back()->with("message","delete_success");
	}


	public function submitHoliday(Request $request){
		$data=$request->validate([
			"holiday_date"=>"required",
			"holiday_name"=>"required",
		]);

		$message="success";
		$holiday_date=date('Y-m-d',strtotime($request->holiday_date));
		$holiday_name=$request->holiday_name;

		if($request->id===null){
			$duplicate=Holiday::where([['holiday_date',$holiday_date],['holiday_date',$holiday_date]])->count();
			if($duplicate>0){
				$message="duplicate";
			}
			else{
				$create=Holiday::create([
					"holiday_date"=>$holiday_date,
					"holiday_name"=>$holiday_name
				]);
			}
		}
		else{
			$duplicate=Holiday::where([['holiday_date',$holiday_date],['holiday_date',$holiday_name],['id','<>',$request->id]])->count();
			if($duplicate>0){
				$message="duplicate";
			}
			else{
				$update=Holiday::find($request->id);
				$update->holiday_date=$holiday_date;
				$update->holiday_name=$holiday_name;
				$update->save();
			}
		}

		return back()->withInput()->with('message',$message);
	}
}
