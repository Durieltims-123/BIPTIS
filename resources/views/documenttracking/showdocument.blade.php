@extends($role==1 ? 'layouts.app' : 'layouts.app2');


@section('css')
    {{-- <link href="{{ asset('assets/select2/dist/css/select2.min.css') }}" rel="stylesheet" /> --}}
    {{-- <link href="{{ asset('css/select2-bootstrap.min.css') }}" rel="stylesheet" /> --}}

    <link href="{{ asset('argon/vendor/jquery/dist/jquery-ui.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('argon/vendor/dropzone/dist/dropzone.css') }}" rel="stylesheet" />
    <style>
        label {
            display: inline-block;
            width: 300px;
        }
        /*
        #pr_date:invalid+span:after {
            content: '✖';
            padding-left: 5px;
            color: red;
            font-size: 1em!important;
            vertical-align: middle;
        }

        #pr_date:valid+span:after {
            content: '✓';
            padding-left: 5px;
            color: green;
            font-size: 1em!important;
        }
        */
        .w-5{
            width: 5%!important;
        }
        .w-10{
            width: 15%!important;
        }
        .w-25{
            width: 25%!important;
        }
        .form-control:focus,.custom-select:focus {
            outline: none !important;
            border: 1px solid rgb(0, 184, 40)!important;
            box-shadow: 0 0 15px #719ECE!important;
        }
        .buttons{
            height: calc(2.75rem + 2px)!important;
            line-height: 1.5!important;
            width: 100%;
        }
        .soft-copy{
            overflow: hidden!important;
        }
        input[type="file"]{
            visibility: visible;
            content: 'Choose File'!important;
            background: #ffffff none repeat scroll 0 0;
            border: 2px dotted #ccc!important;
            box-shadow: none;
            color: rgb(150, 150, 150);
            vertical-align: middle!important;
        }
        input::-webkit-file-upload-button{
            opacity: 0!important;
            font-size: 0px;
        }
        form .error {
            color: #d60909;
        }
        form .error:before {
            content: ' ✖ ';
            color: #d60909;
        }

        form .valid:before{
            display: none;
            /* content: ' ✓'; */
            color: #0eff06;
        }
        #field{
            margin-left:.5em;
            float:left;
        }
        #field,label{
            float:left;font-family:Arial,Helvetica,sans-serif;font-size:small}
        br{
            clear:both
        }
        input{
            overflow: hidden!important;
            border:1px solid #000;
            margin-bottom: 0em;
        }
        input.error, select.error{
            border:1px solid red
        }
        input.valid, select.valid{
            border:1px solid green
        }

        label.error{

        }
        /* #showall_info{
            color: red!important;
            text-align: center!important;
        }
        .pagination{
            margin: 0 2px!important;
        } */
        p{
            font-size: 13px!important;
        }
        a>p{
            text-decoration: none;
            color: rgb(58, 58, 58);
        }
        a>p:hover{
            text-decoration: underline;
            color: rgb(7, 150, 194);
        }
        .page-item .page-link, .page-item span {
            border-radius: 0%!important;
            /* border: 1px rgb(7, 150, 194) solid!important;  */
            /* background-color: rgba(255, 255, 255, 0)!important;  */
            /* color: #12808f!important; */
        }
        .nav>li>a.active,
        .nav>li>a:hover,
        .nav>li>a:focus {
            /* background: repeating-linear-gradient(
                /* -55deg,
                0deg,
                #222,
                #222 5px,
                #333 5px,
                #333 10px
            ); */
            background-color: #222!important;
            color:rgb(255, 255, 255)!important;
        }
    </style>
@endsection
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-4">
    <div id="values">
        <input type="hidden" id="document_type_id" value="{{ $project_document->document_type_id }}">
        <input type="hidden" id="plan_id" value="{{ $project_document->plan_id }}">
        <input type="hidden" id="contractor_id" value="{{ $project_document->contractor_id }}">
    </div>
    <div class="fade-in">
        <div class="row">
            <div class="col-lg-12">
                <div class="btn-group">
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 "><i class="fa fa-caret-left"></i> Return</a>
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 "><i class="fa fa-home"></i> Home</a>
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 "><i class="fa fa-redo"></i> Refresh</a>
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 ">Forward <i class="fa fa-caret-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header text-light rounded-0 pt-2 pl-3 pb-0 ">
                        <h3 class="text-dark">Document Tracking</h3>
                    </div>
                    <div class="card-body pt-2 pl-3 pb-3  border-top-0">
                        <div class="row">
                            <div class="col-md-2">
                                <p class="mt-3 mb-0">Document Type:</p>
                            </div>
                            <div class="col-md-10">
                                <p class="mt-3 mb-0"><b>{{ $document_type->document_type }}</b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <p class="mt-0 mb-0">Project Title:</p>
                            </div>
                            <div class="col-md-10">
                                <a href="#"><p class="mt-0 mb-0"><b>{{ $project_plan->project_title }}</b></p></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <p class="mt-0 mb-3">Contractor:</p>
                            </div>
                            <div class="col-md-10">
                                <a href="#"><p class="mt-0 mb-3"><b>{{ $contractor->business_name }}</b></p></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9 pb-0 pt-0">
                                <h3 class="text-dark pt-0 pb-0 mt-0 mb-0">Uploaded Document</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                @if($project_document->file_status == 'has attachment')
                                    <iframe src="{{ url($project_document->file_directory) }}" width="100%" height="700px" title="description"></iframe>
                                @else
                                    <div class="">
                                        <i class="fa fa-thumbs-down fa-10x"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')

@endsection
