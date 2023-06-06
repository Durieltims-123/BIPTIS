@extends('layouts.app')
@section('content')
@include('layouts.headers.cards2')
@section('css')
<link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-4">
    <div class="fade-in">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header bg-secodary  text-light rounded-0">
                        <h2 class="text-dark mb-0">Document Tracking Form</h2>
                    </div>
                    <div class="card-body  border-top-0 pt-0">
                        <form id="document_form" name="document_form">
                            @csrf
                            <div class="row mt-0">
                                <div class="col-lg-6">
                                    <label for="recipient" class="col-form-label pb-0">Recipient<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Select Recipient" id="recipient" name="recipient" class="form-control form-control-sm recipient" value="{{ old('recipient') }}" onkeyup="this.value = this.value.toUpperCase();">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-danger text-white " id="recipient_clear_btn" type="button" onclick="$.clearvalue(recipient_clear_btn)"><i class="fa fa-times text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 d-none">
                                <label class="col-form-label pb-0">Procact ID </label>
                                <div class="input-group">
                                    <input type="text" placeholder="" id="procact_id" name="procact_id" class="form-control form-control-sm rounded-0 procact_id " value="{{ old('procact_id') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="col-form-label pb-0">Project Title<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Select Project Title" id="project_name" name="project_name" class="form-control form-control-sm rounded-0 project_name " value="{{ old('project_name') }}" onkeyup="this.value = this.value.toUpperCase();">
                                        <input type="hidden" id="mode_of_procurement" value="">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-danger text-white " id="project_clear_btn" type="button" onclick="$.clearvalue(project_clear_btn)"><i class="fa fa-times text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <div class="row jumbotron pt-2 pb-3 pl-1 pr-1 m-0 rounded-0  border-top-0 bg-secondary">s
                                        <div class="col-md-12 col-lg-3">
                                            <label class="col-form-label pb-0 pt-0">Project Number</label>
                                            <input type="text" placeholder="Project Number" id="project_number" class="form-control form-control-sm rounded-0  bg-secondary" readonly>
                                        </div>
                                        <div class="col-md-12 col-lg-3">
                                            <label class="col-form-label pb-0 pt-0">ABC</label>
                                            <input type="text" placeholder="Approved Budget Cost" id="project_abc" class="form-control form-control-sm rounded-0  bg-secondary" readonly>
                                        </div>
                                        <div class="col-md-12 col-lg-3">
                                            <label class="col-form-label pb-0 pt-0">Project Type</label>
                                            <input type="text" placeholder="Project Type" id="project_type" class="form-control form-control-sm rounded-0  bg-secondary" readonly>
                                        </div>
                                        <div class="col-md-12 col-lg-3">
                                            <label class="col-form-label pb-0 pt-0">Project Location</label>
                                            <input type="text" placeholder="Project Location" id="project_location" class="form-control form-control-sm rounded-0  bg-secondary" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6 d-none">
                                    <label class="col-form-label pb-0">Contractors ID</label>
                                    <div class="input-group">
                                        <input type="text" placeholder="" id="contractors_id" name="contractors_id" class="form-control form-control-sm rounded-0 contractors_id " value="{{ old('contractors_id') }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <label class="col-form-label pb-0">Select Contractors</label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Select Contractor" id="contractor" name="contractor" class="form-control form-control-sm rounded-0 contractor " value="{{ old('contractor') }}" onkeyup="this.value = this.value.toUpperCase();">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-danger text-white " id="contractor_clear_btn" type="button" onclick="$.clearvalue(contractor_clear_btn)"><i class="fa fa-times text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <div class="row jumbotron pt-2 pb-3 pl-1 pr-1 m-0 rounded-0  border-top-0 bg-secondary" id="selected_contractors">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label class="col-form-label pb-0">Batch Remarks</label>
                                    {{-- <div class="input-group">
                                        <input type="text" placeholder="Enter Batch Remarks" name="batch_remarks"
                                        class="form-control form-control-sm rounded-0 batch_remarks "
                                        value="{{ old('batch_remarks') }}"
                                    onkeyup="this.value = this.value.toUpperCase();">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-danger text-white " type="button"><i class="fa fa-times text-white"></i></button>
                                    </div>
                                </div> --}}
                                <textarea type="text" placeholder="Enter Batch Remarks" name="batch_remarks" class="form-control form-control-sm rounded-0 batch_remarks " value="{{ old('batch_remarks') }}"></textarea>
                            </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-lg-9">
                                    <label><b>Documents</b></label>
                                </div>
                                <div class="col-lg-3">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-secondary  rounded-0 border-bottom-0 border-right-0" type="button" id="add_item"><i class="fa fa-file-medical"></i><small> ADD DOCUMENT
                                                ROW</small></button>
                                        <button class="btn btn-sm btn-secondary  rounded-0 border-bottom-0" type="button" id="add_pow" onclick="$.addpowmarkup()"><i class="fa fa-folder-plus" id="pow_btn_icon"></i><small id="pow_btn_text"> ADD POW</small></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 table-responsive" id="items_table">
                            <table class="table table-striped w-100" id="documents_table">
                                <thead>
                                    <tr class="bg-secondary">
                                        <td class="text-center  text-dark w-10">Actions</td>
                                        <td class="text-center  text-dark ">Document Type</td>
                                        <td class="text-center  text-dark ">Remarks</td>
                                        <!-- <td class="text-center  text-dark ">Electronic Copy (Optional)</td> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center w-10 p-0">
                                            <button class="btn-secondary  w-100  rounded-0 buttons border-top-0 border-right-0" type="button" id="add_item" disabled="disabled"><i class="icon-sign-blank"></i></button>
                                        </td>
                                        <td class="text-center p-0">
                                            <input type="text" name="document[0][type]" class="form-control form-control-sm rounded-0 document_type  border-top-0 border-right-0" placeholder="Document Type" value="{{ old('document[0][type]') }}" required>
                                        </td>
                                        <td class="text-center p-0">
                                            <input type="text" name="document[0][remarks]" class="form-control form-control-sm rounded-0 remarks  border-top-0 border-right-0" placeholder="Remarks" value="{{ old('document[0][remarks]') }}">
                                        </td>
                                        <!-- <td class="text-center p-0 w-25 file-upload">
                                                            <input type="file" name="document[0][soft_copy]" class="form-control form-control-sm rounded-0 soft_copy  border-top-0 w-100" placeholder="Upload Soft Copy" accept=".pdf" value="{{ old('document[0][softcopy]') }}">
                                                        </td> -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 offset-3 mt-4 mb-0">
                            <div class="row">
                                {{ $data ?? '' }}
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <button type="button" id="submitdocuments" onclick="$.submitform()" class="btn btn-primary btn-block">Submit</button>
                                </div>
                                <div class="col-lg-6">
                                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary btn-block">Return</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/additional-methods.min.js') }}"></script>
{{-- <script src="{{ asset('js/jquery.form.js') }}"></script> --}}
{{-- <script src="{{ asset('assets/select2/dist/js/select2.full.min.js') }}"></script> --}}
{{-- <script>
            $('.brand_name').select2({
                placeholder: 'Select an option',
                theme: 'bootsrap'
            });
            $('.generic_name').select2({
                placeholder: 'Select an option',
                theme: 'bootstrap'
            });
        </script> --}}
<script type="text/javascript">
    var curr_row = 0;
    var curr_col = 1;
    $(document).ready(function() {
        $('#items_table').formNavigation();
        $(".pocurement_process").on('keyup', function() {
            if ($('.procurement_process').val() === null) {
                $('.pow').remove();
            }
        });
    });
    (function($) {
        $.fn.formNavigation = function() {
            $(this).each(function() {
                $(this).find('input, select').on('keydown', function(e) {
                    var row_eq = $(this).parents("tr:first");
                    var row = check_row($(this));
                    var col = check_col($(this));
                    current_pointer();
                    switch (e.which) {
                        case 39: // navigate right
                            $(this).closest('td').nextUntil().find('input, select').first().focus();
                            break;
                        case 37: // navigate left
                            $(this).closest('td').prevUntil().find('input, select').last().focus();
                            break;
                        case 40: // navigate down
                            $(this).closest('tr').nextAll("tr:has(input, select)").children().eq($(this).closest('td').index()).find('input, select').focus();
                            break;
                        case 38: // navigate up
                            $(this).closest('tr').prevAll("tr:has(input, select)").children().eq($(this).closest('td').index()).find('input, select').focus();
                            break;
                        case 46: // (delete) delete row
                            if (row !== 0) {
                                var temp_row = curr_row;
                                var temp_col = curr_col;
                                var last_row = $('#items_table tr:nth-last-child(2)').closest('tr').index() + 1;
                                if (temp_row == last_row && temp_row != 0) temp_row = last_row;
                                if (row == last_row) temp_row = last_row - 1;
                                $('#items_table tr:nth-child(' + (temp_row + 1) + ') td:nth-child(' + (temp_col + 1) + ')').find('input, select').focus();
                                $(this).closest("tr").remove();
                            }
                            break;
                    }

                });
            });
        };
    })(jQuery);

    // !- Document Types Autocomplete Initialization
    var document_type_autocomplete_init = {
        minLength: 0
        , autocomplete: true
        , select: function(event, ui) {
            if (ui.item.id != '') {
                let duplicate = false;
                $(".document_type").each(function(i) {
                    if (ui.item.value == $(this).val()) {
                        duplicate = true;
                    }
                });
                if (duplicate == false) {
                    $(this).val(ui.item.value);
                } else {
                    $(this).val('');
                    swal.fire({
                        title: 'Duplicate Document'
                        , text: 'Please verify the document!.'
                        , buttonsStyling: false
                        , confirmButtonClass: 'btn btn-sm btn-danger'
                        , icon: 'warning'
                    });


                }
            } else {
                $(this).val('');
            }
            return false;
        }
        , source: function(request, response) {

            $.ajax({
                'url': '/getdocumenttypes'
                , 'data': {
                    "_token": "{{ csrf_token() }}"
                    , "term": request.term
                    , "procact_id": $("#procact_id").val()
                }
                , 'method': "get"
                , 'dataType': "json"
                , 'success': function(data) {
                    response(data);
                }
            });
        }
        , change: function(event, ui) {
            if (ui.item === null) {
                $(this).val('');
            }
        }
    }
    $(".document_type").autocomplete(document_type_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });
    // -!

    // !- Project Name Autocomplete Initialization
    var project_name_autocomplete_init = {
        minLength: 0
        , autocomplete: true
        , select: function(event, ui) {
            if (ui.item.id != '') {
                $(this).val(ui.item.value);
                $('#procact_id').val(ui.item.procact_id);
                $('#project_number').val(ui.item.project_number);
                $('#project_abc').val(ui.item.abc);
                $('#project_type').val(ui.item.project_type);
                $('#mode_of_procurement').val(ui.item.mode);
                $('#project_location').val(ui.item.project_location);
                $("#selected_contractors").html("");
                $('#contractors_id').val('');
                $('#contractor').val('');

            } else {
                $(this).val('');
                $('#project_number').val('');
                $('#project_abc').val('');
                $('#project_type').val('');
                $('#mode_of_procurement').val('');
                $('#project_location').val('');
                $('#procact_id').val('');
                $("#selected_contractors").html("");
                $('#contractors_id').val('');
                $('#contractor').val('');
            }
            return false;
        }
        , source: function(request, response) {
            $.ajax({
                'url': '/getprojectnames'
                , 'data': {
                    "_token": "{{ csrf_token() }}"
                    , "term": request.term
                }
                , 'method': "get"
                , 'dataType': "json"
                , 'success': function(data) {
                    response(data);
                }
            });
        }
        , change: function(event, ui) {
            if (ui.item === null) {
                $(this).val('');
                $('#project_number').val('');
                $('#project_abc').val('');
                $('#project_type').val('');
                $('#mode_of_procurement').val('');
                $('#project_location').val('');
                $('#procact_id').val('');
                //$(".procurement_process").attr('disabled', false);
            } else {
                if (ui.item != null) {
                    $('#project_number').val(ui.item.project_number);
                    $('#project_abc').val(ui.item.abc);
                    $('#project_type').val(ui.item.project_type);
                    $('#mode_of_procurement').val(ui.item.mode);
                    $('#project_location').val(ui.item.project_location);
                    $('#procact_id').val(ui.item.procact_id);
                }
            }
        }
    }
    $(".project_name").autocomplete(project_name_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });
    // -!

    // !- Recipient Autocomplete Initialization
    var recipient_autocomplete_init = {
        minLength: 0
        , autocomplete: true
        , select: function(event, ui) {
            if (ui.item.id != '') {
                $(this).val(ui.item.value);
            } else {
                $(this).val('');
            }
            return false;
        }
        , source: function(request, response) {
            $.ajax({
                'url': '/getOffice'
                , 'data': {
                    "_token": "{{ csrf_token() }}"
                    , "term": request.term
                }
                , 'method': "get"
                , 'dataType': "json"
                , 'success': function(data) {
                    response(data);
                }
            });
        }
        , change: function(event, ui) {
            if (ui.item === null) {
                $(this).val('');
            }
        }
    }
    $(".recipient").autocomplete(recipient_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });

    // !- Contractor Autocomplete Initialization
    var contractor_autocomplete_init = {
        minLength: 0
        , autocomplete: true
        , select: function(event, ui) {
            if (ui.item.id != '') {
                $(this).val(ui.item.value);
                let contractors_id = [];
                if ($("#contractors_id").val() != "") {
                    contractors_id = $("#contractors_id").val().split(",");
                }
                if (contractors_id.includes(ui.item.id + "") == false) {
                    contractors_id.push(ui.item.id);
                    $("#contractors_id").val(contractors_id.toString(""));
                    let html = '<div class="form-group d-flex  mb-0  col-sm-6">' +
                        '<div class="input-group ">' +
                        '<input type="text" placeholder="" id="' + ui.item.id + '" value="' + ui.item.value + '" class="form-control form-control-sm bg-white" readonly>' +
                        '<div class="input-group-append">' +
                        '<button class="btn btn-sm btn-secondary remove_contractor"   value="' + ui.item.id + '" type="button"><i class="fa fa-times text-red"></i></button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $("#selected_contractors").html($("#selected_contractors").html() + html);


                    $(".remove_contractor").click(function() {
                        let contractors_id = [];
                        if ($("#contractors_id").val() != "") {
                            contractors_id = $("#contractors_id").val().split(",");
                        }
                        let id = $(this).val();
                        let new_array = contractors_id.filter(data => data != id);
                        $("#contractors_id").val(new_array.toString(""));
                        $(this).parents('.d-flex').remove();
                    });
                }
            } else {
                $(this).val('');
            }
            return false;
        }
        , source: function(request, response) {
            $.ajax({
                'url': '/getcontractors'
                , 'data': {
                    "_token": "{{ csrf_token() }}"
                    , "term": request.term
                    , "procact_id": $("#procact_id").val()
                }
                , 'method': "get"
                , 'dataType': "json"
                , 'success': function(data) {
                    response(data);
                }
            });
        }
        , change: function(event, ui) {
            if (ui.item === null) {
                $(this).val('');
            }
        }
    }
    $(".contractor").autocomplete(contractor_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });

    // !- Add Item (Either on Clicking Add Button or Pressing 'Insert')
    $("body, #add_item").on('click keydown', function(e) { //
        if (($(this).is('#add_item') && e.type == 'click') || (e.type == 'keydown' && e.which == 45)) { // (insert) add new row
            var html = markup();
            $('#documents_table tbody>tr:last').after(html);
            // point cusor to last added tr, look for generic names input and call autocomplete options
            // !- Reinitialize autocomplete for new Element
            $('#documents_table tbody>tr:last>td:eq(1)>input:text.document_type').autocomplete(document_type_autocomplete_init).focus(function() {
                $(this).autocomplete('search', $(this).val());
            });
            // $('#items_table tbody>tr:last>td:eq(2)>input:text.brand_name').autocomplete(brand_autocomplete_init).focus(function() {
            //     $(this).autocomplete('search', $(this).val())
            // });
            // $('#items_table tbody>tr:last>td:eq(3)>input:text.item_description').autocomplete(description_autocomplete_init).focus(function() {
            //     $(this).autocomplete('search', $(this).val())
            // });
            // -!
            check_row($('#items_table'));
            if ($('#documents_table tr').length < 3) {
                $('#mytable tbody tr:eq(0) td:eq(0)').find('input, select').first().focus();
            }
            current_pointer();
            var last_row = $('#items_table tr:nth-last-child(2)').closest('tr').index();
            $('#documents_table').formNavigation();
        }
    });
    // !- Delete Currently Focused Item (Either Clicking Delete Button or Pressing 'Delete')
    $("#items_table").on("dblclick", ".delete_row", function() {
        $(this).closest("tr").remove();
    });
    // !- Move Currently Focused Item Up or Down (Either on Pressing Button or Pressing 'Page Up' or 'Page Down')
    $("#items_table").on("click", ".up,.down", function() {
        var row = $(this).parents("tr:first");
        var row_eq = check_row($(this));
        if (row_eq == 1) {
            if ($(this).is(".down")) {
                row.insertAfter(row.next());
            }
        } else {
            if ($(this).is(".up")) {
                row.insertBefore(row.prev());
            } else {
                row.insertAfter(row.next());
            }
        }
    });
    // !- Get Index of Currently Focused Row
    function check_row(current_element) {
        var col = current_element.index();
        var tr = current_element.closest('tr');
        var row = tr.index();
        return row;
    }
    // !- Get Index of Currently Focused Column
    function check_col(current_element) {
        var col = current_element.index();
        return col;
    }
    // !- Check Currently Focused Input Field
    function current_pointer() {
        $("input").each(function() {
            var input = $(this).is(":focus");
            if (input) {
                curr_row = $(this).parents("tr:first").index();
                curr_col = $(this).parents("td:first").index();
            } else {
                curr_row = curr_row;
                curr_col = curr_col;
            }
        });
    }
    // !- HTML Markup for adding row
    var i = 1;

    function markup() {
        var html = '<tr>';
        html += '<td class="w-10 p-0"">';
        html += '<div class="btn-group w-100">';
        html += '<button type="button" class="btn btn-sm btn-secondary up  add_item rounded-0 buttons"><i class="fa fa-caret-up text-info"></i></button>';
        html += '<button type="button" class="btn btn-sm btn-secondary down  add_item rounded-0 buttons"><i class="fa fa-caret-down text-primary"></i></button>';
        html += '<button type="button" class="btn btn-sm btn-secondary delete_row border border-danger add_item rounded-0 buttons"><i class="fa fa-trash text-danger"></i></button>';
        html += '</div>';
        html += '</td>';
        html += '<td class="text-center p-0">';
        html += '<input type="text" name="document[' + i + '][type]" class="form-control form-control-sm rounded-0 document_type  border-right-0 border-top-0" placeholder="Document Type" required>';
        html += '</td>';
        html += '<td class="text-center p-0">';
        html += '<input type="text" name="document[' + i + '][remarks]" class="form-control form-control-sm rounded-0 remarks  border-right-0 border-top-0" placeholder="Remarks">';
        html += '</td>';
        html += '</tr>';
        i++;
        return html;
    }
    $.addpowmarkup = function() {
        if ($("#procact_id").val() == "") {
            swal.fire({
                title: 'Unknown Project'
                , text: 'Please select a project before adding documents'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });

            return '';
        }

        if ($("#recipient").val() == "") {

            swal.fire({
                title: 'Unknown Recipient'
                , text: 'Please select a Recipient before adding documents'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });

            return '';
        }


        if ($(".pow")[0]) {
            return '';
        }
        if ($("#mode_of_procurement").val() == 'bidding') {
            var pow = ['Program of Work', 'Detailed Estimates', 'Quantity Take-off', 'Time Spot Schedule', 'Work Program Schedule', 'Approved Plans and Specification'];
        } else {
            var pow = ['Program of Work', 'Detailed Estimates', 'Quantity Take-off', 'Time Spot Schedules', 'Quality Control Program', 'Work Program Schedule', 'Approved Plans and Specification'];
        }

        $.each(pow, function(key, value) {
            if (key !== 0) {
                var html = '';
                html += '<tr class="pow">';
                html += '<td class="w-10 p-0"">';
                html += '<div class="btn-group w-100">';
                html += '<button type="button" class="btn btn-sm btn-secondary up  add_item rounded-0 buttons"><i class="fa fa-caret-up text-info"></i></button>';
                html += '<button type="button" class="btn btn-sm btn-secondary down  add_item rounded-0 buttons"><i class="fa fa-caret-down text-primary"></i></button>';
                html += '<button type="button" class="btn btn-sm btn-secondary delete_row border border-danger add_item rounded-0 buttons"><i class="fa fa-trash text-danger"></i></button>';
                html += '</div>';
                html += '</td>';
                html += '<td class="text-center p-0">';
                html += '<input type="text" name="document[' + i + '][type]" class="form-control form-control-sm rounded-0 document_type  border-right-0 border-top-0" placeholder="Document Type" value="' + value + '" required>';
                html += '</td>';
                html += '<td class="text-center p-0">';
                html += '<input type="text" name="document[' + i + '][remarks]" class="form-control form-control-sm rounded-0 remarks  border-right-0 border-top-0" placeholder="Remarks">';
                html += '</td>';
                // html += '<td class="text-center p-0 w-25 file-upload">';
                // html += '<input type="file" name="document['+i+'][soft_copy]" class="form-control form-control-sm rounded-0 soft_copy w-100  border-top-0" placeholder="Upload Soft Copy" accept=".pdf" >';
                // html += '</td>';
                html += '</tr>';
                $('#documents_table tbody>tr:last').after(html);
                $('#documents_table tbody>tr:last>td:eq(1)>input:text.document_type').autocomplete(document_type_autocomplete_init).focus(function() {
                    $(this).autocomplete('search', $(this).val());
                });
            } else {
                if ($('input[name="document[0][type]"]').val() === '') {
                    $('input[name="document[0][type]"]').val(value);
                } else {
                    var html = '';
                    html += '<tr class="pow">';
                    html += '<td class="w-10 p-0"">';
                    html += '<div class="btn-group w-100">';
                    html += '<button type="button" class="btn btn-sm btn-secondary up  add_item rounded-0 buttons"><i class="fa fa-caret-up text-info"></i></button>';
                    html += '<button type="button" class="btn btn-sm btn-secondary down  add_item rounded-0 buttons"><i class="fa fa-caret-down text-primary"></i></button>';
                    html += '<button type="button" class="btn btn-sm btn-secondary delete_row border border-danger add_item rounded-0 buttons"><i class="fa fa-trash text-danger"></i></button>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td class="text-center p-0">';
                    html += '<input type="text" name="document[' + i + '][type]" class="form-control form-control-sm rounded-0 document_type  border-right-0 border-top-0" placeholder="Document Type" value="' + value + '" required>';
                    html += '</td>';
                    html += '<td class="text-center p-0">';
                    html += '<input type="text" name="document[' + i + '][remarks]" class="form-control form-control-sm rounded-0 remarks  border-right-0 border-top-0" placeholder="Remarks">';
                    html += '</td>';
                    // html += '<td class="text-center p-0 w-25 file-upload">';
                    // html += '<input type="file" name="document['+i+'][soft_copy]" class="form-control form-control-sm rounded-0 soft_copy w-100  border-top-0" placeholder="Upload Soft Copy" accept=".pdf" >';
                    // html += '</td>';
                    html += '</tr>';
                    $('#documents_table tbody>tr:last').after(html);
                    $('#documents_table tbody>tr:last>td:eq(1)>input:text.document_type').autocomplete(document_type_autocomplete_init).focus(function() {
                        $(this).autocomplete('search', $(this).val());
                    });
                }
            }
            i++;
        });
        $('#documents_table').formNavigation();;
        $('#pow_btn_icon').removeClass('fa fa-folder-plus');
        $('#pow_btn_icon').addClass('fa fa-folder-minus');
        $('#pow_btn_text').text('REM POW');
        $('#add_pow').off();
        $('#add_pow').on('click', function() {
            $.rempowmarkup();
        });
    }
    $.rempowmarkup = function() {
        if ($('input[name="document[0][type]"]').val() === 'Program of Work') {
            $('input[name="document[0][type]"]').val('');
        }
        $(".pow").remove();
        $('#pow_btn_icon').removeClass('fa fa-folder-minus');
        $('#').addClass('fa fa-folder-plus');
        $('#powpow_btn_icon_btn_text').text('ADD POW');
    }
    $.submitform = function() {
        var formData = new FormData($("#document_form")[0]);
        console.log(formData);
        $.ajax({
            url: '/storedocuments'
            , dataType: 'json'
            , method: 'post'
            , processData: false
            , contentType: false,
            //data: $("#document_form").serializeArray(),
            data: formData
            , success: function(data) {
                console.log(data);
                $.confirm({
                    theme: 'modern'
                    , icon: data.icon
                    , title: data.title
                    , content: data.message
                    , autoClose: 'Ok|10000'
                    , buttons: {
                        Ok: {
                            text: 'Ok'
                            , btnClass: data.confirm_button
                            , action: function() {
                                if (data.status == 'success') {
                                    $(".pow").remove();
                                    $("#document_form").trigger('reset');
                                }

                            }
                        }
                    }
                });
            }
        });
    }
    $.clearvalue = function(id) {
        if (id.id == 'recipient_clear_btn') {
            $('#recipient').val('');
        }
        if (id.id == 'project_clear_btn') {
            $('#project_name').val('');
            $('#project_number').val('');
            $('#project_abc').val('');
            $('#project_type').val('');
            $('#project_location').val('');
            $('#contractor').val('');
            $('#procurement_process').val('');
            $('#procact_id').val('');
        }
        if (id.id == 'contractor_clear_btn') {
            $('#contractor').val('');
        }
        if (id.id == 'process_clear_btn') {
            $('#procurement_process').val('');
            $(".pow").remove();
            if($(".procurement_process").val() === '') {
            } else {

            }
            // $(".procurement_process").attr('disabled', false);
            $(".procurement_process").off();
            $(".procurement_process").autocomplete(procurement_process_autocomplete_init).focus(function() {
                $(this).autocomplete('search', $(this).val())
            });
        }
    }

</script>
@endsection
