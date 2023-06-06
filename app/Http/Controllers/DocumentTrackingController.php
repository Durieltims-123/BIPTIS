<?php

namespace App\Http\Controllers;

use Redis;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Library\NCLNameCaseRu;
use App\Notifications\DocumentsRouted;
use App\DocumentType;
use App\ProjectDocument;
use App\ProjectPlans;
use App\User;
use App\APP;
use App\Procact;
use App\Contractors;
use App\ProcurementProcesses;
use App\ProcessDocuments;
use App\Municipality;
use App\Barangay;
use App\ProcurementMode;
use App\Fund;
use App\Roles;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class DocumentTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $links = getUserLinks();
        $data = [
            'title' => 'Document Tracking',
            'links' => $links
        ];

        return view('documenttracking.index')->with($data)->with("role", $this->getAuth());
    }


    public function getAuth()
    {
        $bacsec = DB::table('user_roles')
            ->where([['users.email', auth()->user()->email]])
            ->whereIn('user_roles.role_id', [1, 4, 5])
            ->join('users', 'users.id', 'user_roles.user_id')->count();


        if ($bacsec > 0) {
            $role = 1;
        } else {
            $twg = DB::table('user_roles')->where([['users.email', auth()->user()->email]])
                ->whereIn('user_roles.role_id', [2, 3])
                ->join('users', 'users.id', 'user_roles.user_id')->count();

            $role = 2;
        }

        return $role;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $links = getUserLinks();
        $data = [
            'title' => 'Document Tracking',
            'links' => $links
        ];
        return view('documenttracking.create')->with($data)->with("role", $this->getAuth());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    public function storedocuments(Request $request)
    {

        // $this->validate($request);, [
        //     'recipient' => 'required|string',
        //     'contractor' => 'required|string',
        //     'project_name' => 'required|string'
        // ]);
        DB::beginTransaction();
        $send_ids = array();
        try {
            $projectplan_id = ProjectPlans::select('procacts')->where('procact_id', '=', $request->input('procact_id'))->first()->plan_id;
            $mode_id = $projectplan_id->procact_mode_id;

            if ($mode_id == 1) {
                $mode_of_procurement = 'bidding';
            } elseif ($mode_id == 2) {
                $mode_of_procurement = 'svp';
            } else {
                $mode_of_procurement = 'negotiated';
            }
            if ($request->input('contractor') !== null) {
                $contractor_id = Contractors::select('contractor_id')->where('business_name', '=', $request->input('contractor'))->first()->contractor_id;
            } else {
                $contractor_id = 0;
            }
            $sender_id = Auth::user()->id;
            $receiver_id = User::select('id')->where('name', '=', $request->input('recipient'))->first()->id;
            $process_id = ProcurementProcesses::select('id')->where('process_name', '=', $request->input('procurement_process'))->where('mode_of_procurement', '=', $mode_of_procurement)->first()->id;
            $lock = true;
            foreach ($request->input('document') as $key => $document) {
                $documenttype_id = DocumentType::select('id')->where('document_type', '=', $document['type'])->where('project_type', '=', $mode_of_procurement)->first()->id;
                $project_document = ProjectDocument::select('*')->where('document_type_id', '=', $documenttype_id)->where('plan_id', '=', $projectplan_id)->where('contractor_id', '=', $contractor_id)->get();

                if (count($project_document) == 0) {
                    $i = (int) $key;
                    $projectdocument = new ProjectDocument;
                    $projectdocument->plan_id = $projectplan_id;

                    $projectdocument->contractor_id = $contractor_id;
                    $projectdocument->sender = $sender_id;
                    $projectdocument->receiver = $receiver_id;
                    $projectdocument->status = 'sent';
                    $projectdocument->active_status = 'true';
                    $projectdocument->document_type_id = $documenttype_id;
                    $projectdocument->procurement_processes_id = $process_id;
                    $projectdocument->remarks = $document['remarks'];
                    $projectdocument->batch_remarks = $request->input('batch_remarks');
                    $projectdocument->file_status = 'no attachment';
                    $projectdocument->file_directory = '';
                    //dd($projectdocument);
                    if ($request->file('document') !== null) {
                        $lock_key = -1;
                        foreach ($request->file('document') as $key => $file) {
                            $k = (int) $key;
                            if ($i == $k) {
                                if ($request->hasFile("document." . $k . ".soft_copy")) {
                                    $lock_key = $k;
                                }
                            }
                        }
                        foreach ($request->file('document') as $key => $file) {
                            $k = (int) $key;
                            if ($lock_key == $k) {
                                $projectdocument->file_status = 'has attachment';
                                $projectdocument->file_directory = Storage::disk('local')->put('files', $file['soft_copy']);
                                $lock_key = -1;
                            }
                        }
                    }
                    $projectdocument->save();
                    event(new \App\Events\SendNotification($projectdocument->id));
                    $send_ids[] = ['project_document_id' => $projectdocument->id];
                } else {
                    $lock = false;
                }
            }
            if ($lock) {
                $user = User::find($receiver_id);
                $user->notify(new DocumentsRouted($send_ids));
                DB::commit();
                $data = [
                    'status' => 'success',
                    'title' => 'Success!',
                    'icon' => 'fa fa-check text-success',
                    'confirm_button' => 'btn-green',
                    'message' => 'The document/s has been added to the list.' . error_get_last()
                ];
                return response()->json($data);
            } else {
                DB::rollback();
                $data = [
                    'status' => 'error',
                    'title' => 'Duplicate!',
                    'icon' => 'fa fa-time text-danger',
                    'confirm_button' => 'btn-red',
                    'message' => 'The data you were trying to save was duplicate. Please Check existing records and try again.' . error_get_last()
                ];
                return response()->json($data);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            $data = [
                'status' => 'error',
                'title' => 'Error!',
                'icon' => 'fa fa-time text-danger',
                'confirm_button' => 'btn-red',
                'message' => 'There was an error adding Document/s to the list' . error_get_last()
            ];
            return response()->json($data);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $projectdocument = ProjectDocument::select('project_documents.id', 'document_types.document_type', 'project_plans.project_title', 'contractors.business_name', 'sender.name as sender_name', 'receiver.name as receiver_name', 'file_status', 'file_directory', 'sender.id as sender_id', 'receiver.id as receiver_id', 'project_plans.plan_id', 'contractors.contractor_id', 'project_documents.created_at', 'project_documents.document_type_id')
            ->join('project_plans', function ($join) {
                $join->on('project_documents.plan_id', '=', 'project_plans.plan_id');
            })
            ->leftJoin('contractors', function ($join) {
                $join->on('project_documents.contractor_id', '=', 'contractors.contractor_id');
            })
            ->join('document_types', function ($join) {
                $join->on('project_documents.document_type_id', '=', 'document_types.id');
            })
            ->join('users as sender', function ($join) {
                $join->on('project_documents.sender', '=', 'sender.id');
            })
            ->join('users as receiver', function ($join) {
                $join->on('project_documents.receiver', '=', 'receiver.id');
            });
        $projectdocument = $projectdocument->where('project_documents.id', '=', $id)
            ->first();
        $data = [
            'title' => 'Document Tracking',
            'project_document' => $projectdocument
        ];
        return view('documenttracking.show')->with($data)->with("role", $this->getAuth());
    }

    public function checklist()
    {
        $links = getUserLinks();
        $data = [
            'title' => 'Document Tracking',
            'links' => $links
        ];
        return view('documenttracking.checklist')->with($data)->with("role", $this->getAuth());
    }

    public function getprojectdocuments($id)
    {
        $projectdocument = ProjectDocument::select('project_documents.id', 'document_types.document_type', 'project_plans.project_title', 'contractors.business_name', 'sender.name as sender_name', 'receiver.name as receiver_name', 'file_status', 'file_directory', 'sender.id as sender_id', 'receiver.id as receiver_id', 'project_plans.plan_id', 'contractors.contractor_id', 'project_documents.created_at', 'project_documents.document_type_id')
            ->join('project_plans', function ($join) {
                $join->on('project_documents.plan_id', '=', 'project_plans.plan_id');
            })
            ->leftJoin('contractors', function ($join) {
                $join->on('project_documents.contractor_id', '=', 'contractors.contractor_id');
            })
            ->join('document_types', function ($join) {
                $join->on('project_documents.document_type_id', '=', 'document_types.id');
            })
            ->join('users as sender', function ($join) {
                $join->on('project_documents.sender', '=', 'sender.id');
            })
            ->join('users as receiver', function ($join) {
                $join->on('project_documents.receiver', '=', 'receiver.id');
            });
        $projectdocument = $projectdocument->where('project_plans.plan_id', '=', $id)
            ->first();
        $data = [
            'title' => 'Document Tracking',
            'project_document' => $projectdocument
        ];
        return view('documenttracking.showprojectdocuments')->with($data)->with("role", $this->getAuth());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function trackingsettings()
    {
        $links = getUserLinks();
        $data = [
            'title' => 'Document Tracking',
            'links' => $links
        ];
        return view('documenttracking.trackingsettings')->with($data)->with("role", $this->getAuth());
    }

    public function savesettings(Request $request)
    {
        DB::beginTransaction();
        $check = DocumentType::select('*')->where('document_type', '=', $request->document_type)->where('project_type', '=', $request->project_type)->first();
        if ($check) {
            DB::rollback();
            $data = [
                'status' => 'error',
                'title' => 'Error!',
                'icon' => 'fa fa-times text-danger',
                'confirm_button' => 'btn-red',
                'message' => 'Duplicate detected, Please check the information you provided.' . error_get_last()
            ];
            return response()->json($data);
        }
        $lastdocument = DocumentType::latest()->orderBy('id', 'desc')->first();
        $last_id = $lastdocument->document_number;
        $newtype = new DocumentType;
        $newtype->document_number = $last_id + 1;
        $newtype->project_type = $request->project_type;
        $newtype->document_type = $request->document_type;
        $newtype->document_status = 'active';
        $newtype->save();
        if (!$newtype) {
            DB::rollback();
            $data = [
                'status' => 'error',
                'title' => 'Error!',
                'icon' => 'fa fa-time text-danger',
                'confirm_button' => 'btn-red',
                'message' => 'There was an error adding new Document Type: ' . $request->document_type . '.' . error_get_last()
            ];
            return response()->json($data);
        }
        DB::commit();
        $data = [
            'status' => 'success',
            'title' => 'Success!',
            'icon' => 'fa fa-check text-success',
            'confirm_button' => 'btn-green',
            'message' => 'Successfully Added new Document Type: ' . $request->document_type . '.' . error_get_last()
        ];
        return response()->json($data);
    }
    public function saveprocessdoccuments(Request $request)
    {
        DB::begintransaction();
        $processes = ProcurementProcesses::select('*', 'procurement_processes.id as procurement_process_id', 'process_documents.id as process_documents_id', 'document_types.id as document_types_id')
            ->leftJoin('process_documents', function ($join) {
                $join->on('process_documents.procurement_processes_id', '=', 'procurement_processes.id');
            })
            ->leftJoin('document_types', function ($join) {
                $join->on('process_documents.document_types_id', '=', 'document_types.id');
            })
            ->where('procurement_processes.process_name', '=', $request->process_name)
            ->where('document_types.document_type', '=', $request->document_type)
            ->where('document_types.project_type', '=', $request->project_type)
            ->where('procurement_processes.mode_of_procurement', '=', $request->project_type)
            //->where('')
            ->get();
        if (count($processes) > 0) {
            DB::rollback();
            $data = [
                'status' => 'error',
                'title' => 'Error!',
                'icon' => 'fa fa-times text-danger',
                'confirm_button' => 'btn-red',
                'message' => 'Duplicate detected, Please check the information you provided.' . error_get_last()
            ];
            return response()->json($data);
        }
        $process = ProcurementProcesses::select('*')->where('process_name', '=', $request->process_name)->where('mode_of_procurement', '=', $request->project_type)->first()->id;
        $document = DocumentType::select('id')->where('document_type', '=', $request->document_type)->where('project_type', '=', $request->project_type)->first()->id;
        $newdocument = new ProcessDocuments;
        $newdocument->procurement_processes_id = $process;
        $newdocument->document_types_id = $document;
        $newdocument->process_document_status = 'active';
        $newdocument->save();
        if (!$newdocument) {
            DB::rollback();
            $data = [
                'status' => 'error',
                'title' => 'Error!',
                'icon' => 'fa fa-time text-danger',
                'confirm_button' => 'btn-red',
                'message' => 'There was an error adding new Document Type: ' . $request->document_type . '.' . error_get_last()
            ];
            return response()->json($data);
        }
        DB::commit();
        $data = [
            'status' => 'success',
            'title' => 'Success!',
            'icon' => 'fa fa-check text-success',
            'confirm_button' => 'btn-green',
            'message' => 'Successfully Added new Document(' . $request->document_type . ') for the process: ' . $request->process_name . '.' . error_get_last()
        ];
        return response()->json($data);
    }
    public function managedocumenttypes(Request $request)
    {
        DB::beginTransaction();
        $id = explode('|', $request->id);
        foreach ($id as $i) {
            $managetype = DocumentType::find($i);
            if ($request->src == 'disabled') {
                $managetype->document_status = 'inactive';
            }
            if ($request->src == 'enabled') {
                $managetype->document_status = 'active';
            }
            $managetype->save();
            DB::commit();
        }
    }
    public function getdocumenttypes(Request $request)
    {
        $term = $request->term;
        $processes = [];
        if (isset($request->procact_id)) {
            $procact = Procact::find($request->procact_id);
            $mode_of_procurement = strtolower(ProcurementMode::find($procact->procact_mode_id)->mode);
            if ($term !== '' && $term !== null) {
                $processes = DocumentType::select('*')
                    ->where('document_status', '=', 'active')
                    ->where('document_type', 'LIKE', '%' . $term . '%')
                    ->where('project_type', '=', $mode_of_procurement)
                    ->take(10)->get();
            } else {
                $processes = DocumentType::select('id', 'document_type', 'project_type')
                    ->where('document_status', '=', 'active')
                    ->where('document_type', 'LIKE', '%' . $term . '%')
                    ->where('project_type', '=', $mode_of_procurement)
                    ->take(10)
                    ->get();
            }
        }

        if (count($processes) > 0) {
            foreach ($processes as $process) {
                $results[] = [
                    'id' => $process->document_types_id,
                    'value' => $process->document_type
                ];
            }
        } else {
            $results[] = [
                'id' => '',
                'value' => 'No Match Found'
            ];
        }
        return response()->json($results);
    }

    public function getprojectnames(Request $request)
    {
        $term = $request->term;
        $results = array();
        $plans = ProjectPlans::select('*')
            ->where([['status', 'onprocess'], ['project_title', 'LIKE', '%' . $term . '%'], ['is_old', '<>', true], ['procacts.bid_evaluation', '<>', null]])
            ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
            ->take(10)->get();
        if (sizeOf($plans) != 0) {
            foreach ($plans as $plan) {
                $mode = strtolower(ProcurementMode::find($plan->mode_id)->mode);
                $location = $plan->load('Municipality', 'Barangay');
                $barangay = Barangay::find($plan->barangay_id) ? Barangay::find($plan->barangay_id)->barangay_name . ', ' : '';
                $municipality = Municipality::find($plan->municipality_id)->municipality_name ? Municipality::find($plan->municipality_id)->municipality_name : '';
                $results[] = [
                    'id' => $plan->plan_id,
                    'value' => $plan->project_title,
                    'project_number' => $plan->project_no,
                    'abc' => $plan->abc,
                    'project_type' => $plan->project_type,
                    'mode' => $mode,
                    'procact_id' =>  $plan->procact_id,
                    'project_location' => $barangay . $municipality
                ];
            }
        } else {
            $results[] = [
                'id' => '',
                'value' => 'No Match Found'
            ];
        }
        return response()->json($results);
    }

    public function getOffice(Request $request)
    {

        $term = $request->term;
        $role_id = DB::table('user_roles')->select('role_id')->where('user_id', Auth::user()->id)->first()->role_id;
        $roles = Roles::where([['name', 'LIKE', '%' . $term . '%'], ['id', '<>', $role_id]])->take(10)->get();

        if (sizeOf($roles) != 0) {
            foreach ($roles as $role) {
                $results[] = [
                    'id' => $role->id,
                    'value' => $role->name,
                    'display_name' => $role->name
                ];
            }
        } else {
            $results[] = [
                'id' => '',
                'value' => 'No Match Found'
            ];
        }
        return response()->json($results);
    }

    public function getcontractors(Request $request)
    {
        $term = $request->term;
        $results = array();
        $APP = new APP;
        // $contractors = Contractors::select('contractor_id', 'business_name')
        // ->where('status', '=', 'active')
        // ->where('business_name', 'LIKE', '%'.$term.'%')
        // ->take(10)->get();

        $contractors = $APP->getBiddersData($request->procact_id, 'active,non-responsive,responsive,disapproved');

        if (sizeOf($contractors) != 0) {
            foreach ($contractors as $contractor) {
                $results[] = [
                    'id' => $contractor->project_bid,
                    'value' => $contractor->business_name
                ];
            }
        } else {
            $results[] = [
                'id' => '',
                'value' => 'No Match Found'
            ];
        }
        return response()->json($results);
    }

    public function getallcontractors(Request $request)
    {
        $plan_id = ProjectPlans::where('project_title', '=', $request->project_title)->first()->plan_id;
        $app = new APP;
        $contractors = $app->getAllCurrentBidders($plan_id);
        $contractors = $contractors['project_bidders'];
        $results = array();
        if (sizeOf($contractors) != 0) {
            foreach ($contractors as $contractor) {
                $results[] = [
                    'id' => $contractor->contractor_id,
                    'value' => $contractor->business_name
                ];
            }
        } else {
            $results[] = [
                'id' => '',
                'value' => 'No Match Found'
            ];
        }
        return response()->json($results);
    }

    public function getprocurementprocesses(Request $request)
    {
        $term = $request->term;
        $project = ProjectPlans::where('project_title', '=', $request->project_name)->first()->mode_id;
        if ($project == 1) {
            $mode_of_procurement = 'bidding';
        } elseif ($project == 2) {
            $mode_of_procurement = 'svp';
        } else {
            $mode_of_procurement = 'negotiated';
        }
        $results = array();
        $processes = ProcurementProcesses::select('id', 'process_name')
            ->where('status', '=', 'active')
            ->where('mode_of_procurement', '=', $mode_of_procurement)
            ->where('process_name', 'LIKE', '%' . $term . '%')
            ->take(10)->get();
        if (sizeOf($processes) != 0) {
            foreach ($processes as $process) {
                $results[] = [
                    'id' => $process->id,
                    'value' => $process->process_name
                ];
            }
        } else {
            $results[] = [
                'id' => '',
                'value' => 'No Match Found'
            ];
        }
        return response()->json($results);
    }

    public function getdocuments(Request $request)
    {
        $type = $_REQUEST['t'];
        $filter = $_REQUEST['f'];
        //dd($filter);
        // $projectdocuments = ProjectDocument::select('bid_docs.proposed_bid as bid_docs_proposed', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_project_id', 'rfqs.proposed_bid as rfq_proposed_bid', 'rfqs.rfq_id', 'rfq_projects.rfq_project_id', 'procacts.procact_id', 'project_documents.procurement_processes_id', 'procurement_processes.process_name', 'project_documents.id', 'document_types.document_type', 'project_plans.project_title', 'contractors.business_name', 'sender.name as sender_name', 'receiver.name as receiver_name', 'file_status', 'file_directory', 'sender.id as sender_id', 'receiver.id as receiver_id', 'project_plans.plan_id', 'contractors.contractor_id', 'project_documents.document_type_id', 'project_documents.status', 'project_documents.created_at', 'project_documents.status as project_documents_status', 'project_plans.abc as abc');
        $projectdocuments = ProjectDocument::select('project_documents.procurement_processes_id', 'procurement_processes.process_name', 'project_documents.id', 'document_types.document_type', 'project_plans.project_title', 'contractors.business_name', 'sender.name as sender_name', 'receiver.name as receiver_name', 'file_status', 'file_directory', 'sender.id as sender_id', 'receiver.id as receiver_id', 'project_plans.plan_id', 'contractors.contractor_id', 'project_documents.document_type_id', 'project_documents.status', 'project_documents.created_at', 'project_documents.status as project_documents_status', 'project_plans.abc as abc', 'project_plans.mode_id', 'document_types.documentary_classification');
        $projectdocuments = $projectdocuments->distinct();
        $projectdocuments = $projectdocuments->join('project_plans', function ($join) {
            $join->on('project_documents.plan_id', '=', 'project_plans.plan_id');
        });
        $projectdocuments = $projectdocuments->leftJoin('contractors', function ($join) {
            $join->on('project_documents.contractor_id', '=', 'contractors.contractor_id');
        });
        $projectdocuments = $projectdocuments->join('document_types', function ($join) {
            $join->on('project_documents.document_type_id', '=', 'document_types.id');
        });
        $projectdocuments = $projectdocuments->join('users as sender', function ($join) {
            $join->on('project_documents.sender', '=', 'sender.id');
        });
        $projectdocuments = $projectdocuments->join('users as receiver', function ($join) {
            $join->on('project_documents.receiver', '=', 'receiver.id');
        });
        $projectdocuments = $projectdocuments->leftJoin('procurement_processes', function ($join) {
            $join->on('procurement_processes.id', '=', 'project_documents.procurement_processes_id');
        });
        // $projectdocuments = $projectdocuments->leftJoin('procacts', function($join){
        //     $join->on('procacts.plan_id', '=', 'project_plans.plan_id');
        // });
        // $projectdocuments = $projectdocuments->leftJoin('rfq_projects', function($join){
        //     $join->on('rfq_projects.procact_id', '=', 'procacts.procact_id');
        // });
        // $projectdocuments = $projectdocuments->leftJoin('rfqs', function($join){
        //     $join->on('rfqs.rfq_id', '=', 'rfq_projects.rfq_id');
        // });
        // $projectdocuments = $projectdocuments->leftJoin('bid_doc_projects', function($join){
        //     $join->on('bid_doc_projects.procact_id', '=', 'procacts.procact_id');
        // });
        // $projectdocuments = $projectdocuments->leftJoin('bid_docs', function($join){
        //     $join->on('bid_docs.bid_doc_id', '=', 'bid_doc_projects.bid_doc_id');
        // });
        if ($type == 'documentonly') {
            $projectdocuments = $projectdocuments->where('project_documents.document_type_id', '=', $_REQUEST['d']);
            $projectdocuments = $projectdocuments->where('project_plans.plan_id', '=', $_REQUEST['p']);
            $projectdocuments = $projectdocuments->where('contractors.contractor_id', '=', $_REQUEST['c']);
            $projectdocuments = $projectdocuments->orderBy('project_documents.id', 'desc');
        }
        if ($type == 'projectonly') {
            $projectdocuments = $projectdocuments->where('project_plans.plan_id', '=', $_REQUEST['p']);
            $projectdocuments = $projectdocuments->where('contractors.contractor_id', '=', $_REQUEST['c']);
        }
        if ($filter != '') {
            $role_id = DB::table('user_roles')->select('role_id')->where('user_id', '=', Auth::id())->first()->role_id;
            if ($filter == 'forwarded-tab') {
                $projectdocuments = $projectdocuments->where('sender.id', '=', $role_id);
                $projectdocuments = $projectdocuments->where('project_documents.status', '=', 'sent');
                $projectdocuments = $projectdocuments->where('project_documents.active_status', '=', 'true');
            } elseif ($filter == 'forreceiving-tab') {
                $projectdocuments = $projectdocuments->where('receiver.id', '=', $role_id);
                $projectdocuments = $projectdocuments->where('project_documents.status', '=', 'sent');
                $projectdocuments = $projectdocuments->where('project_documents.active_status', '=', 'true');
            } elseif ($filter == 'pending-tab') {
                // $role_id = DB::table('user_roles')->select('role_id')->where('user_id', '=', Auth::user()->id)->first()->role_id;
                $projectdocuments = $projectdocuments->where('receiver.id', '=', $role_id);
                $projectdocuments = $projectdocuments->where('project_documents.status', '=', 'received');
                $projectdocuments = $projectdocuments->where('project_documents.active_status', '=', 'true');
            } elseif ($filter == 'ended-tab') {
                $projectdocuments = $projectdocuments->where('receiver.id', '=', $role_id);
                $projectdocuments = $projectdocuments->where('project_documents.status', '=', 'ended');
                $projectdocuments = $projectdocuments->where('project_documents.active_status', '=', 'true');
            } elseif ($filter == 'unsent-tab') {
                $projectdocuments = $projectdocuments->where('sender.id', '=', $role_id);
                $projectdocuments = $projectdocuments->where('project_documents.status', '=', 'unsent');
                $projectdocuments = $projectdocuments->where('project_documents.active_status', '=', 'true');
            } else {
                $projectdocuments = $projectdocuments->where('project_documents.active_status', '=', 'true');
            }
        }
        $projectdocuments = $projectdocuments->orderBy('project_plans.project_title', 'asc');
        $projectdocuments = $projectdocuments->orderBy('contractors.business_name', 'asc');
        $projectdocuments = $projectdocuments->orderBy('project_documents.created_at', 'desc');
        $projectdocuments = $projectdocuments->get();
        //dd($projectdocuments);
        $data = [
            'data' => $projectdocuments
        ];
        return response()->json($data);
    }

    public function showdocuments($id)
    {
        $projectdocument = ProjectDocument::find($id);
        $projectdocument = $projectdocument->load('DocumentType', 'ProjectPlans');
        $projectplan = ProjectPlans::find($projectdocument->plan_id);
        $documenttype = DocumentType::find($projectdocument->document_type_id);
        $contractor = Contractors::find($projectdocument->contractor_id);
        $data = [
            'project_document' => $projectdocument,
            'project_plan' => $projectplan,
            'document_type' => $documenttype,
            'contractor' => $contractor
        ];
        return view('documenttracking.showdocument')->with($data)->with("role", $this->getAuth());
    }

    public function managedocuments()
    {
        DB::begintransaction();
        $id = $_REQUEST['id'];
        // dump($id);
        // dump($_REQUEST['contractors']);
        //  dd($_REQUEST['forwarddata']);
        // dd($_REQUEST['document_type_forwarddata']);
        // dd($_REQUEST['document_types']);
        $recipient = array_key_exists('recipient', $_REQUEST) ? $_REQUEST['recipient'] : false;
        $recipient = $recipient ? User::where('name', '=', explode('|', $recipient)[0])->first()->id : false;
        $recipient = $recipient ? DB::table('user_roles')->select('role_id')->where('user_id', '=', $recipient)->first()->role_id : false;
        $sender = $recipient ? Auth::user()->id : false;
        $send_ids = array();
        $loopcount = 0;
        if ($_REQUEST['src'] == 'forward') {
            foreach ($_REQUEST['document_type_forwarddata'] as $a => $data) {
                $plan = ProjectPlans::select('plan_id', 'mode_id')->where('project_title', '=', $data[0])->first();
                $plan_id = $plan->plan_id;
                $mode_id = $plan->mode_id;
                $procact_id = DB::table('procacts')->select('procact_id')->where('plan_id', '=', $plan_id)->latest()->first()->procact_id;
                if ($mode_id == 1) $mode = 'bidding';
                else $mode = 'svp';
                $procurement_process_id = DB::table('procurement_processes')->select('id')->where('process_name', '=', $data[3])->where('mode_of_procurement', '=', $mode)->latest()->first()->id;
                foreach ($_REQUEST['document_types'] as $b => $document_type) {
                    if ($document_type[0] !== '' && $document_type[0] !== null) {
                        if ($document_type[1] == $data[2]) {
                            $document_type_id = DocumentType::select('*')->where('document_type', '=', $document_type[0])->where('project_type', '=', $mode)->first()->id;
                            if ($data[1] == '') {
                                foreach ($_REQUEST['contractors'] as $c => $contractor) {
                                    if ($contractor[1] == $data[2]) {
                                        if ($contractor[0] !== null && $contractor[0] !== '') {
                                            $newdocumentstatus = new ProjectDocument;
                                            $newdocumentstatus->status = 'sent';
                                            $newdocumentstatus->plan_id = $plan_id;
                                            $newdocumentstatus->procact_id = $procact_id;
                                            $newdocumentstatus->document_type_id = $document_type_id;
                                            $newdocumentstatus->contractor_id = Contractors::where('business_name', '=', $contractor[0])->first()->contractor_id ? Contractors::where('business_name', '=', $contractor[0])->first()->contractor_id : 0;
                                            $newdocumentstatus->procurement_processes_id = $procurement_process_id;
                                            $newdocumentstatus->receiver = $recipient;
                                            $newdocumentstatus->sender = $sender;
                                            $newdocumentstatus->file_status = 'no attachment';
                                            $newdocumentstatus->active_status = 'true';
                                            $newdocumentstatus->file_directory = '';
                                            $newdocumentstatus->remarks = '';
                                            $newdocumentstatus->batch_remarks = '';
                                            $newdocumentstatus->save();
                                        } else {
                                            $newdocumentstatus = new ProjectDocument;
                                            $newdocumentstatus->status = 'sent';
                                            $newdocumentstatus->plan_id = $plan_id;
                                            $newdocumentstatus->procact_id = $procact_id;
                                            $newdocumentstatus->document_type_id = $document_type_id;
                                            $newdocumentstatus->contractor_id = 0;
                                            $newdocumentstatus->procurement_processes_id = $procurement_process_id;
                                            $newdocumentstatus->receiver = $recipient;
                                            $newdocumentstatus->sender = $sender;
                                            $newdocumentstatus->file_status = 'no attachment';
                                            $newdocumentstatus->active_status = 'true';
                                            $newdocumentstatus->file_directory = '';
                                            $newdocumentstatus->remarks = '';
                                            $newdocumentstatus->batch_remarks = '';
                                            $newdocumentstatus->save();
                                        }
                                        event(new \App\Events\SendNotification($newdocumentstatus->id));
                                        $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                                    }
                                }
                            } else {
                                $newdocumentstatus = new ProjectDocument;
                                $newdocumentstatus->status = 'sent';
                                $newdocumentstatus->plan_id = $plan_id;
                                $newdocumentstatus->procact_id = $procact_id;
                                $newdocumentstatus->document_type_id = $document_type_id;
                                $newdocumentstatus->contractor_id = Contractors::where('business_name', '=', $data[1])->first()->contractor_id ? Contractors::where('business_name', '=', $data[1])->first()->contractor_id : 0;
                                $newdocumentstatus->procurement_processes_id = $procurement_process_id;
                                $newdocumentstatus->receiver = $recipient;
                                $newdocumentstatus->sender = $sender;
                                $newdocumentstatus->file_status = 'no attachment';
                                $newdocumentstatus->active_status = 'true';
                                $newdocumentstatus->file_directory = '';
                                $newdocumentstatus->remarks = '';
                                $newdocumentstatus->batch_remarks = '';
                                $newdocumentstatus->save();
                                event(new \App\Events\SendNotification($newdocumentstatus->id));
                                $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                            }
                        }
                    }
                }
            }
        }
        foreach ($id as $i) {
            if ($_REQUEST['src'] == 'forward') {
                if ($_REQUEST['forwarddata'] !== null) {
                    foreach ($_REQUEST['forwarddata'] as $j => $forwarddata) {
                        if ($forwarddata[0] == $i) {
                            foreach ($_REQUEST['contractors'] as $k => $forwardcontractor) {
                                if ($forwarddata[1] == $forwardcontractor[1]) {
                                    $project_document = (object)[];
                                    if ($forwardcontractor[0] !== null && $forwardcontractor[0] !== '') {
                                        $projectdocument = ProjectDocument::find($i);
                                        $newdocumentstatus = new ProjectDocument;
                                        $newdocumentstatus->status = 'sent';
                                        $newdocumentstatus->plan_id = $projectdocument->plan_id;
                                        $newdocumentstatus->procact_id = $projectdocument->procact_id;
                                        $newdocumentstatus->document_type_id = $projectdocument->document_type_id;
                                        $newdocumentstatus->contractor_id = Contractors::where('business_name', '=', $forwardcontractor[0])->first()->contractor_id ? Contractors::where('business_name', '=', $forwardcontractor[0])->first()->contractor_id : $project_document->contractor_id;
                                        $newdocumentstatus->procurement_processes_id = $projectdocument->procurement_processes_id;
                                        if ($recipient) $newdocumentstatus->receiver = $recipient;
                                        else $newdocumentstatus->receiver = $projectdocument->receiver;
                                        if ($sender) $newdocumentstatus->sender = $sender;
                                        else $newdocumentstatus->sender = $projectdocument->sender;
                                        $newdocumentstatus->file_status = $projectdocument->file_status;
                                        $newdocumentstatus->active_status = 'true';
                                        $newdocumentstatus->file_directory = $projectdocument->file_directory;
                                        $newdocumentstatus->remarks = $projectdocument->remarks;
                                        $newdocumentstatus->batch_remarks = $projectdocument->batch_remarks;
                                        $newdocumentstatus->save();
                                        $projectdocument->active_status = 'false';
                                        $projectdocument->save();
                                        DB::commit();
                                        // Send Notification
                                        event(new \App\Events\SendNotification($newdocumentstatus->id));
                                        $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                                    } else {
                                        $projectdocument = ProjectDocument::find($i);
                                        $newdocumentstatus = new ProjectDocument;
                                        $newdocumentstatus->status = 'sent';
                                        $newdocumentstatus->plan_id = $projectdocument->plan_id;
                                        $newdocumentstatus->procact_id = $projectdocument->procact_id;
                                        $newdocumentstatus->document_type_id = $projectdocument->document_type_id;
                                        $newdocumentstatus->procurement_processes_id = $projectdocument->procurement_processes_id;
                                        $newdocumentstatus->contractor_id = $projectdocument->contractor_id;
                                        if ($recipient) $newdocumentstatus->receiver = $recipient;
                                        else $newdocumentstatus->receiver = $projectdocument->receiver;
                                        if ($sender) $newdocumentstatus->sender = $sender;
                                        else $newdocumentstatus->sender = $projectdocument->sender;
                                        $newdocumentstatus->file_status = $projectdocument->file_status;
                                        $newdocumentstatus->active_status = 'true';
                                        $newdocumentstatus->file_directory = $projectdocument->file_directory;
                                        $newdocumentstatus->remarks = $projectdocument->remarks;
                                        $newdocumentstatus->batch_remarks = $projectdocument->batch_remarks;
                                        $newdocumentstatus->save();
                                        $projectdocument->active_status = 'false';
                                        $projectdocument->save();
                                        DB::commit();
                                        // Send Notification
                                        event(new \App\Events\SendNotification($newdocumentstatus->id));
                                        $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $projectdocument = ProjectDocument::find($i);
                    $newdocumentstatus = new ProjectDocument;
                    $newdocumentstatus->status = 'sent';
                    $newdocumentstatus->plan_id = $projectdocument->plan_id;
                    $newdocumentstatus->procact_id = $projectdocument->procact_id;
                    $newdocumentstatus->document_type_id = $projectdocument->document_type_id;
                    $newdocumentstatus->procurement_processes_id = $projectdocument->procurement_processes_id;
                    $newdocumentstatus->contractor_id = $projectdocument->contractor_id;
                    if ($recipient) $newdocumentstatus->receiver = $recipient;
                    else $newdocumentstatus->receiver = $projectdocument->receiver;
                    if ($sender) $newdocumentstatus->sender = $sender;
                    else $newdocumentstatus->sender = $projectdocument->sender;
                    $newdocumentstatus->file_status = $projectdocument->file_status;
                    $newdocumentstatus->active_status = 'true';
                    $newdocumentstatus->file_directory = $projectdocument->file_directory;
                    $newdocumentstatus->remarks = $projectdocument->remarks;
                    $newdocumentstatus->batch_remarks = $projectdocument->batch_remarks;
                    $newdocumentstatus->save();
                    $projectdocument->active_status = 'false';
                    $projectdocument->save();
                    DB::commit();
                    // Send Notification
                    if ($_REQUEST['src'] == 'forward') {
                        event(new \App\Events\SendNotification($newdocumentstatus->id));
                        $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                    }
                }
            } elseif ($_REQUEST['src'] == 'discard') {
                // unsent document
                $projectdocument = ProjectDocument::find($i);
                $newdocumentstatus = new ProjectDocument;
                $newdocumentstatus->status = 'unsent';
                $newdocumentstatus->plan_id = $projectdocument->plan_id;
                $newdocumentstatus->procact_id = $projectdocument->procact_id;
                $newdocumentstatus->document_type_id = $projectdocument->document_type_id;
                $newdocumentstatus->procurement_processes_id = $projectdocument->procurement_processes_id;
                $newdocumentstatus->contractor_id = $projectdocument->contractor_id;
                if ($recipient) $newdocumentstatus->receiver = $recipient;
                else $newdocumentstatus->receiver = $projectdocument->receiver;
                if ($sender) $newdocumentstatus->sender = $sender;
                else $newdocumentstatus->sender = $projectdocument->sender;
                $newdocumentstatus->file_status = $projectdocument->file_status;
                $newdocumentstatus->active_status = 'true';
                $newdocumentstatus->file_directory = $projectdocument->file_directory;
                $newdocumentstatus->remarks = $projectdocument->remarks;
                $newdocumentstatus->batch_remarks = $projectdocument->batch_remarks;
                $newdocumentstatus->save();
                $discard_id = $newdocumentstatus->id;
                $projectdocument->active_status = 'false';
                $projectdocument->save();
                // return to received, if not available then totally discard
                $oldstatus = ProjectDocument::where('document_type_id', '=', $projectdocument->document_type_id)->where('active_status', '=', 'false')->where('status', '=', 'received')->latest()->first();
                if ($oldstatus) {
                    $newdocumentstatus = new ProjectDocument;
                    $newdocumentstatus->status = 'received';
                    $newdocumentstatus->plan_id = $oldstatus->plan_id;
                    $newdocumentstatus->procact_id = $oldstatus->procact_id;
                    $newdocumentstatus->document_type_id = $oldstatus->document_type_id;
                    $newdocumentstatus->procurement_processes_id = $oldstatus->procurement_processes_id;
                    $newdocumentstatus->contractor_id = $oldstatus->contractor_id;
                    if ($recipient) $newdocumentstatus->receiver = $recipient;
                    else $newdocumentstatus->receiver = $oldstatus->receiver;
                    if ($sender) $newdocumentstatus->sender = $sender;
                    else $newdocumentstatus->sender = $oldstatus->sender;
                    $newdocumentstatus->file_status = $oldstatus->file_status;
                    $newdocumentstatus->active_status = 'true';
                    $newdocumentstatus->file_directory = $oldstatus->file_directory;
                    $newdocumentstatus->remarks = $oldstatus->remarks;
                    $newdocumentstatus->batch_remarks = $oldstatus->batch_remarks;
                    $newdocumentstatus->save();
                    $discard_status = ProjectDocument::find($discard_id);
                    $discard_status->active_status = 'false';
                    $discard_status->save();
                }
                DB::commit();
                // Send Notification
                if ($_REQUEST['src'] == 'forward') {
                    event(new \App\Events\SendNotification($newdocumentstatus->id));
                    $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                }
            } else {
                $projectdocument = ProjectDocument::find($i);
                $newdocumentstatus = new ProjectDocument;
                if ($_REQUEST['src'] == 'receive') $newdocumentstatus->status = 'received';
                if ($_REQUEST['src'] == 'end') $newdocumentstatus->status = 'ended';
                $newdocumentstatus->plan_id = $projectdocument->plan_id;
                $newdocumentstatus->procact_id = $projectdocument->procact_id;
                $newdocumentstatus->document_type_id = $projectdocument->document_type_id;
                $newdocumentstatus->procurement_processes_id = $projectdocument->procurement_processes_id;
                $newdocumentstatus->contractor_id = $projectdocument->contractor_id;
                $newdocumentstatus->receiver = Auth::user()->id;
                if ($sender) $newdocumentstatus->sender = $sender;
                else $newdocumentstatus->sender = $projectdocument->sender;
                $newdocumentstatus->file_status = $projectdocument->file_status;
                $newdocumentstatus->active_status = 'true';
                $newdocumentstatus->file_directory = $projectdocument->file_directory;
                $newdocumentstatus->remarks = $projectdocument->remarks;
                $newdocumentstatus->batch_remarks = $projectdocument->batch_remarks;
                $newdocumentstatus->save();
                $projectdocument->active_status = 'false';
                $projectdocument->save();
                DB::commit();
                // Send Notification
                if ($_REQUEST['src'] == 'forward') {
                    event(new \App\Events\SendNotification($newdocumentstatus->id));
                    $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
                }
            }
        }
        if ($recipient) {
            $user = User::find($recipient);
            $user->notify(new DocumentsRouted($send_ids));
        }
    }

    public function getdocumentslist()
    {
        $type = $_REQUEST['t'];
        $documents = DocumentType::select('*');
        if ($type != 'undefined') {
            $documents = $documents->where('project_type', '=', $type);
            $documents = $documents->where('document_type', '!=', 'deleted');
        } else {
            $documents = $documents->where('project_type', '=', 'svp');
        }
        $documents = $documents->get();
        $data = [
            'data' => $documents
        ];
        return response()->json($data);
    }

    public function getprocesseslist(Request $request)
    {
        $type = $request->project_type;
        // $processes = ProcessDocuments::select('*')
        // ->join('procurement_processes', function($join){
        //     $join->on('procurement_processes.id', '=', 'process_documents.procurement_processes_id');
        // });
        $processes = ProcurementProcesses::select('*', 'document_types.project_type as project_type', 'procurement_processes.mode_of_procurement as mode_of_procurement', 'procurement_processes.id as procurement_process_id', 'process_documents.id as process_documents_id', 'document_types.id as document_types_id')
            ->leftJoin('process_documents', function ($join) {
                $join->on('process_documents.procurement_processes_id', '=', 'procurement_processes.id');
            })
            ->leftJoin('document_types', function ($join) {
                $join->on('process_documents.document_types_id', '=', 'document_types.id');
            })
            ->where('mode_of_procurement', '=', $type)
            ->orderBy('procurement_process_id', 'asc');
        // if($type != 'undefined'){
        //     $processes = $processes->where('mode_of_procurement', '=', $type);
        //     $processes = $processes->where('project_type', '=', $type);
        // }else{
        //     $processes = $processes->where('mode_of_procurement', '=', 'svp');
        //     $processes = $processes->where('project_type', '=', 'svp');
        // }
        $processes = $processes->get();
        $process_arr = array();
        $temp_arr = array();
        $prev_arr = array();
        $last_item = $processes->pop();
        $processes->push($last_item);
        if ($processes[0] != null) {
            $temp = $processes[0]->procurement_process_id;
            foreach ($processes as $process) {
                if ($process->procurement_process_id != $temp) {
                    $temp = ProcurementProcesses::find($temp);
                    $process_arr[] = [
                        'id' => $temp->id,
                        'process_name' => $temp->process_name,
                        'mode_of_procurement' => $temp->mode_of_procurement,
                        'status' => $temp->status,
                        'documents' => $temp_arr
                    ];
                    $temp_arr = array();
                }
                $temp_arr[] = [
                    'document_type' => $process->document_type,
                    'document_status' => $process->document_status
                ];
                if ($processes->last() == $process) {
                    $process_arr[] = [
                        'id' => $process->procurement_process_id,
                        'process_name' => $process->process_name,
                        'mode_of_procurement' => $process->mode_of_procurement,
                        'status' => $process->status,
                        'documents' => $temp_arr
                    ];
                }
                $temp = $process->procurement_process_id;
            }
        }
        $data = [
            'data' => $process_arr
        ];
        return response()->json($data);
    }

    public function getnotifications(Request $request)
    {
        if ($request->src == 'main') {
            $user = Auth::user();
            return response()->json($user->notifications);
        } else {
            if (!isset($request->src)) {
                $row = array();
                $notifications = DB::table('notifications')->select('*')->where('notifiable_id', '=', Auth::user()->id)->where('read_at', '=', null)->orderByRaw('created_at DESC')->take(20)->get();
                foreach ($notifications as $notification) {
                    $decode_documents = json_decode($notification->data, true);

                    foreach ($decode_documents['documents'] as $key => $data) {
                        foreach ($data as $dat) {
                            dump($key . ' = ' . $dat);
                        }
                    }
                }
            } else {
                $project_document = ProjectDocument::find($request->src);
                return response()->json($project_document->load('DocumentType'));
            }
        }
        dd();
    }

    public function removeprocessdocuments(Request $request)
    {
        $document_type = DocumentType::where('document_type', '=', $request->document_type)->where('project_type', '=', $request->mode_of_procurement)->first()->id;
        $procurement_process = ProcurementProcesses::where('process_name', '=', $request->process_name)->where('mode_of_procurement', '=', $request->mode_of_procurement)->first()->id;
        $process_document = ProcessDocuments::where('procurement_processes_id', '=', $procurement_process)->where('document_types_id', '=', $document_type)->delete();
    }

    public function getprojectplans(Request $request)
    {
        $type = $_REQUEST['t'];
        $filter = $_REQUEST['f'];
        $projectdocuments = ProjectPlans::select('*')
            ->distinct();
        // ->join('project_documents', function($join){
        //     $join->on('project_documents.plan_id', '=', 'project_plans.plan_id');
        // })
        // ->join('contractors', function($join){
        //     $join->on('project_documents.contractor_id', '=', 'contractors.contractor_id');
        // })

        // ->join('document_types', function($join){
        //     $join->on('project_documents.document_type_id', '=', 'document_types.id');
        // })
        // ->join('users as sender', function($join){
        //     $join->on('project_documents.sender', '=', 'sender.id');
        // })
        // ->join('users as receiver', function($join){
        //     $join->on('project_documents.receiver', '=', 'receiver.id');
        // })
        // ->leftJoin('procurement_processes', function($join){
        //     $join->on('procurement_processes.id', '=', 'project_documents.procurement_processes_id');
        // });
        $projectdocuments = $projectdocuments->get();
        $data = [
            'data' => $projectdocuments
        ];
        return response()->json($data);
    }

    public function getdocumentchecklist(Request $request)
    {
        try {
            $data = array();
            $projectplan = ProjectPlans::where('project_plans.project_title', '=', $request->project_name)->first();
            $mode = strtolower(ProcurementMode::find($projectplan->mode_id)->mode);
            $ongoing_cnt = 0;
            $done_cnt = 0;
            $unsent_cnt = 0;
            $notrouted_cnt = 0;
            if ($projectplan) {
                $document_types = DocumentType::select('*')->where('project_type', '=', $mode)->get();
                $document_cnt = sizeOf($document_types);
                foreach ($document_types as $key => $document_type) {
                    if ($request->contractor == '') {
                        $project_document = ProjectDocument::select('*')->where('plan_id', '=', $projectplan->plan_id)->where('document_type_id', '=', $document_type->id)->latest()->first();
                    } else {
                        $contractor = Contractors::select('*')->where('business_name', '=', $request->contractor)->first();
                        $project_document = ProjectDocument::select('*')->where('plan_id', '=', $projectplan->plan_id)->where('document_type_id', '=', $document_type->id)->where('contractor_id', '=', $contractor->contractor_id)->latest()->first();
                    }

                    $lock = false;
                    if (!empty($project_document) && $document_type->id == $project_document->document_type_id) {
                        if ($project_document->status == 'sent' || $project_document->status == 'received') {
                            $status = 'fa fa-hourglass-half text-info';
                            $ongoing_cnt++;
                        } elseif ($project_document->status == 'unsent') {
                            $status = 'fa fa-comment-slash text-danger';
                            $unsent_cnt++;
                        } elseif ($project_document->status == 'ended') {
                            $status = 'fa fa-check text-success';
                            $done_cnt++;
                        }
                        $data[] = [
                            'project_document_id' => $project_document->id,
                            'document_type_id' => $document_type->id,
                            'document_number' => $document_type->document_number,
                            'document_type' => $document_type->document_type,
                            'status' => $status
                        ];
                        $lock = true;
                    }
                    if ($lock) {
                        continue;
                    } else {
                        $data[] = [
                            'project_document_id' => 0,
                            'document_type_id' => $document_type->id,
                            'document_number' => $document_type->document_number,
                            'document_type' => $document_type->document_type,
                            'status' => 'fa fa-times text-danger'
                        ];
                        $notrouted_cnt++;
                        continue;
                    }
                }
            }

            if ($request->for == 'table') {
                $data = [
                    'data' => $data
                ];
            } else {
                $data = [
                    "ongoing_cnt" => $ongoing_cnt,
                    "done_cnt" => $done_cnt,
                    "unsent_cnt" => $unsent_cnt,
                    "notrouted_cnt" => $notrouted_cnt,
                    "document_cnt" => $document_cnt
                ];
            }

            return response()->json($data);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json($data);
        }
    }

    public function generatedocumentchecklist(Request $request)
    {
        $projectplan = ProjectPlans::select('*')->where('project_title', '=', $_REQUEST['project_title'])->first();

        $mode = strtolower(ProcurementMode::find($projectplan->mode_id)->mode);
        $data = [
            'project_name' => $request->project_title,
            'for' => 'table'
        ];
        $data = new Request($data);
        $counts = $this->getdocumentchecklist($data);

        if ($mode == 'svp') {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('phpword/templates/COA TRANSMITTAL SPV TEMPLATE.docx');
        } else {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('phpword/templates/COA TRANSMITTAL BIDDING TEMPLATE.docx');
        }
        $documenttypes = DocumentType::select('*')->where('project_type', '=', $mode)->get();

        $templateProcessor->setValue('auditor_name', 'MR. HARVY TORALBA');
        $templateProcessor->setValue('auditor_position', 'Audit Team Leader');
        $templateProcessor->setValue('auditor_team', 'Audit Group F - Audit Team No. 1');
        $templateProcessor->setValue('date', date("F j, Y"));
        $templateProcessor->setValue('project_title', $projectplan->project_title);
        $templateProcessor->setValue('source_of_fund', Fund::find($projectplan->fund_id)->source);
        $templateProcessor->setValue('abc', '&#8369;  ' . number_format($projectplan->abc, 2));
        $templateProcessor->setValue('salutation', 'SIR:');
        $i = 1;
        foreach ($documenttypes as $key => $documenttype) {
            $query = ProjectDocument::select('*')->where('plan_id', '=', $projectplan->plan_id)->where('document_type_id', '=', $documenttype->id)->latest()->first();
            if ($query !== null) {
                if ($query->status == 'ended') {
                    $templateProcessor->setValue('rem' . $i, '✔');
                } else {
                    $templateProcessor->setValue('rem' . $i, '✗');
                }
            } else {
                $templateProcessor->setValue('rem' . $i, '✗');
            }
            $templateProcessor->setValue('p' . $i, '');
            $i++;
        }
        if ($mode == 'svp') {
            $resultFile = $templateProcessor->saveAs('phpword/results/COA TRANSMITTAL SPV.docx');
        } else {
            $resultFile = $templateProcessor->saveAs('phpword/results/COA TRANSMITTAL BIDDING.docx');
        }

        if ($_GET['src'] == 'word') {
            if ($mode == 'svp') {
                $file = 'phpword/results/COA TRANSMITTAL SPV.docx';
            } else {
                $file = 'phpword/results/COA TRANSMITTAL BIDDING.docx';
            }
            $headers = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            return response()->download($file, 'COATSVP_' . date('m_d_Y-h_i_s') . '.docx', $headers);
        } else {
            if ($mode == 'svp') {
                $resultPath = 'phpword/results/COA TRANSMITTAL SPV.docx';
            } else {
                $resultPath = 'phpword/results/COA TRANSMITTAL BIDDING.docx';
            }
            $thisfile = 'COATSVP_' . date('m_d_Y-h_i_s') . '.pdf';
            $resultfilepath = public_path() . '\phpword\results\\' . $thisfile;
            // CONVERT DOCX TO PDF FORMAT
            $word = new \COM("Word.Application") or die("Could not initialise Object.");
            $word->Visible = 0;
            $word->DisplayAlerts = 0;
            $word->Documents->Open(asset($resultPath));
            $word->ActiveDocument->ExportAsFixedFormat($resultfilepath, 17, false, 0, 0, 0, 0, 7, true, false, 2, true, true, false);
            $word->Quit(false);
            unset($word);
            // CONVERT FILE TO BASE64
            $openfayl = file_get_contents($resultfilepath, true);
            $data = base64_encode($openfayl);
            // DELETE USED FILES TO PREVENT BULKING OF STORAGE FILE
            unlink($resultfilepath);
            unlink($resultPath);
            return response()->json($data);
        }
    }

    public function recievebiddingdocuments($plan_id, $procact_id, $contractor_id, $procurement_mode, $documentary_classification)
    {
        $document_types = DocumentType::select('*')->where('project_type', '=', $procurement_mode)->where('documentary_classification', '=', $documentary_classification)->where('document_status', '=', 'active')->get();
        if (count($document_types) == 0) {
            dd('error sumwer');
        }
        foreach ($document_types as $document_type) {
            $newdocumentstatus = new ProjectDocument;
            $newdocumentstatus->status = 'received';
            $newdocumentstatus->plan_id = $plan_id;
            $newdocumentstatus->document_type_id = $document_type->id;
            $newdocumentstatus->contractor_id = $contractor_id;
            $project_activity_status = DB::table('project_activity_status')->where('plan_id', '=', $plan_id)->where('procact_id', '=', $procact_id)->first();
            if ($project_activity_status->pre_proc == 'pending') {
                $process_name = 'Pre-procurement';
            } else {
                if ($project_activity_status->advertisement == 'pending') {
                    $process_name = 'Advertisement';
                } else {
                    if ($project_activity_status->pre_bid == 'pending') {
                        $process_name = 'Pre-bid Conference';
                    } else {
                        if ($project_activity_status->open_bid == 'pending') {
                            $process_name = 'Submission of Bid';
                        } else {
                            if ($project_activity_status->bid_evaluation == 'pending') {
                                $process_name = 'Bid Evaluation';
                            } else {
                                if ($project_activity_status->post_qual == 'pending') {
                                    $process_name = 'Post Qualification';
                                } else {
                                    if ($project_activity_status->award_notice == 'pending') {
                                        $process_name = 'Issuance of Notice of Awards';
                                    } else {
                                        if ($project_activity_status->contract_signing == 'pending') {
                                            $process_name = 'Contract Preparation and Signing';
                                        } else {
                                            if ($project_activity_status->authority_approval == 'pending') {
                                                $process_name = 'Approval by Higher Authority';
                                            } else {
                                                if ($project_activity_status->proceed_notice == 'pending') {
                                                    $process_name = 'Notice to Proceed';
                                                } else {
                                                    $process_name = 'Project Initiated';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $newdocumentstatus->procurement_processes_id = ProcurementProcesses::select('id')->where('mode_of_procurement', '=', $procurement_mode)->where('process_name', '=', $process_name)->first()->id;

            // $roles = DB::table('user_roles')->where('role_id', '=', 1)->get();
            // $receiver = '';
            // if(count($roles) > 0){
            //     $last_key = count($roles)-1;
            //     foreach($roles as $key => $role){
            //         $receiver .= $role->user_id;
            //         if($key != $last_key){
            //             $receiver .= ',';
            //         }
            //     }
            // }
            $receiver = DB::table('roles')->where('name', '=', 'BAC-SEC')->first()->id;
            $newdocumentstatus->receiver = $receiver;
            //$newdocumentstatus->documentary_classification = $documentary_classification;
            $newdocumentstatus->sender = DB::table('users')->where('name', '=', 'PGO')->first()->id;
            $newdocumentstatus->file_status = 'no attachment';
            $newdocumentstatus->procact_id = $procact_id;
            $newdocumentstatus->active_status = 'true';
            $newdocumentstatus->file_directory = '';
            if ($documentary_classification == 'Program of Work') {
                $newdocumentstatus->remarks = 'Added Program of Work';
                $newdocumentstatus->batch_remarks = 'Bidder\'s Documentary Requirements';
            } elseif ($documentary_classification == 'Bidding Documents') {
                $newdocumentstatus->remarks = 'SVP Bidding Documents';
                $newdocumentstatus->batch_remarks = 'Bidder\'s Documentary Requirements';
            } elseif ($documentary_classification == 'Bidder\'s Technical Proposals/Documents') {
                $newdocumentstatus->remarks = 'Bidding Technical Documents';
                $newdocumentstatus->batch_remarks = 'Bidder\'s Documentary Requirements';
            } elseif ($documentary_classification == 'Bidder\'s Financial Proposals/Documents') {
                $newdocumentstatus->remarks = 'Bidding Financial Documents';
                $newdocumentstatus->batch_remarks = 'Bidder\'s Documentary Requirements';
            } else {
                $newdocumentstatus->remarks = '';
            }
            $newdocumentstatus->save();
        }
        // $user = User::find($recipient);
        $send_ids[] = ['project_document_id' => $newdocumentstatus->id];
        Auth::user()->notify(new DocumentsRouted($send_ids));
    }
}
