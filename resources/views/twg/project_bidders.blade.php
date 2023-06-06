@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="modal" tabindex="-1" role="dialog" id="bidder_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="bidder_modal_title">Disqualify </h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="bidders_form" action="/disqualify_bidder">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="bidder_id">Bidder ID</label>
                  <input type="text" id="bidder_id" name="bidder_id" class="form-control form-control-sm" readonly value="{{old('bidder_id')}}" >
                  <label class="error-msg text-red" >@error('bidder_id'){{$message}}@enderror</label>
                </div>

                <!-- Business Name -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="business_name">Business Name</label>
                  <input type="text" id="business_name" name="business_name" class="form-control form-control-sm bg-white" readonly value="{{old('business_name')}}" >
                  <label class="error-msg text-red" >@error('business_name'){{$message}}@enderror</label>
                </div>

                <select type="" id="process_type" name="process_type" class="form-control d-none" >
                  <option value="Ineligible"  {{ old('process_type') == 'Ineligible' ? 'selected' : ''}} >Ineligible</option>
                  <option value="Disqualify"  {{ old('process_type') == 'Disqualify' ? 'selected' : ''}} >Disqualify</option>
                  <option value="Reactivate"  {{ old('process_type') == 'Reactivate' ? 'selected' : ''}} >Reactivate</option>
                </select>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                  <label for="ineligibility">Ineligibility</label>
                  <input type="text" id="ineligibility" name="ineligibility" class="form-control form-control-sm bg-white" readonly value="Disqualify" >
                  <label class="error-msg text-red" >@error('ineligibility'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="owner">Owner</label>
                  <input type="text" id="owner" name="owner" class="form-control form-control-sm bg-white" readonly value="{{old('owner')}}" >
                  <label class="error-msg text-red" >@error('owner'){{$message}}@enderror</label>
                </div>



                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="remarks">Remarks<span class="text-red">*</span></label>
                  <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm" value="{{old('remarks')}}" ></textarea>
                  <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
                </div>

              </div>
              <div class="d-flex justify-content-center col-sm-12">
                <button class="btn btn-primary text-center">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>



    <div class="modal" tabindex="-1" role="dialog" id="bid_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="bid_modal_title">Edit</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="proposed_bid_form" action="/edit_proposed_bid">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="proposed_bid_bidder_id">Bidder ID</label>
                  <input type="text" id="proposed_bid_bidder_id" name="proposed_bid_bidder_id" class="form-control form-control-sm" readonly value="{{old('proposed_bid_bidder_id')}}" >
                  <label class="error-msg text-red" >@error('proposed_bid_bidder_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                  <label for="proposed_bid_type">Type</label>
                  <select type="" id="proposed_bid_type" name="proposed_bid_type" class="form-control d-none" >
                    <option value="clustered"  {{ old('proposed_bid_type') == 'bid' ? 'selected' : ''}} >Bid</option>
                    <option value="detailed_bid"  {{ old('proposed_bid_type') == 'detailed_bid' ? 'selected' : ''}} >Detailed Bid</option>
                  </select>
                </div>

                <!-- Business Name -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="proposed_bid_business_name">Business Name</label>
                  <input type="text" id="proposed_bid_business_name" name="proposed_bid_business_name" class="form-control form-control-sm bg-white" readonly value="{{old('proposed_bid_business_name')}}" >
                  <label class="error-msg text-red" >@error('proposed_bid_business_name'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="proposed_bid_owner">Owner</label>
                  <input type="text" id="proposed_bid_owner" name="proposed_bid_owner" class="form-control form-control-sm bg-white" readonly value="{{old('proposed_bid_owner')}}" >
                  <label class="error-msg text-red" >@error('proposed_bid_owner'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="project_cost">Project Cost<span class="text-red">*</span></label>
                  <input type="text" id="project_cost" name="project_cost" class="form-control form-control-sm money2" value="{{number_format($data->project_cost,2,'.',',')}}" readonly>
                  <label class="error-msg text-red" >@error('project_cost'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="proposed_bid">Proposed Bid/Bid as Read<span class="text-red">*</span></label>
                  <input type="text" id="proposed_bid" name="proposed_bid" class="form-control form-control-sm money2" value="{{old('proposed_bid')}}" >
                  <label class="error-msg text-red" >@error('proposed_bid'){{$message}}@enderror</label>
                </div>



                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="bid_in_words">Bid in Words<span class="text-red">*</span></label>
                  <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input" id="is_same_bid_in_words" {{ old('bid_in_words') && old('bid_in_words') == old('proposed_bid') ? 'checked' : ''}}>
                    <label class="custom-control-label" for="is_same_bid_in_words">Same as Bid as Read</label>
                  </div>
                  <input type="text" id="bid_in_words" name="bid_in_words" class="form-control form-control-sm money2" value="{{old('bid_in_words')}}" >
                  <label class="error-msg text-red" >@error('bid_in_words'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="initial_bid_as_evaluated">Initial Bid as Evaluated<span class="text-red">*</span></label>
                  <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input" id="is_same_initial_bid_eval"  {{ old('initial_bid_as_evaluated') == old('proposed_bid') && old('initial_bid_as_evaluated') != null ? 'checked' : ''}}>
                    <label class="custom-control-label" for="is_same_initial_bid_eval">Same as Bid as Read</label>
                  </div>
                  <input type="text" id="initial_bid_as_evaluated" name="initial_bid_as_evaluated" class="form-control form-control-sm money2" value="{{old('initial_bid_as_evaluated')}}" >
                  <label class="error-msg text-red" >@error('initial_bid_as_evaluated'){{$message}}@enderror</label>
                </div>



                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="discount_type">Discount Type<span class="text-red">*</span></label>
                  <select type="text" id="discount_type" name="discount_type" class="form-control form-control-sm">
                    <option value="" {{ old('discount_type') == '' ? 'selected' : ''}} ></option>
                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : ''}} >Percentage</option>
                    <option value="amount"  {{ old('discount_type') == 'amount' ? 'selected' : ''}} >Amount</option>
                  </select>
                  <label class="error-msg text-red" >@error('discount_type'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 {{ old('discount_type') === 'percentage' && old('discount_type') != null ? '' : 'd-none'}}">
                  <label for="discount_source">Discount Source<span class="text-red">*</span></label>
                  <select type="text" id="discount_source" name="discount_source" class="form-control form-control-sm">
                    <option value="" {{ old('discount_source') == '' ? 'selected' : ''}} ></option>
                    <option value="bid_as_evaluated" {{ old('discount_source') == 'bid_as_evaluated' ? 'selected' : ''}} >Bid As Evaluated</option>
                    <option value="bid_in_words" {{ old('discount_source') == 'bid_in_words' ? 'selected' : ''}} >Bid In Words</option>
                    <option value="bid_as_read" {{ old('discount_source') == 'bid_as_read' ? 'selected' : ''}} >Bid As Read</option>
                    <option value="abc"  {{ old('discount_source') == 'abc' ? 'selected' : ''}} >ABC</option>
                  </select>
                  <label class="error-msg text-red" >@error('discount_source'){{$message}}@enderror</label>
                </div>


                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 {{ old('discount_type') == 'percentage' && old('discount_type') != null ? '' : 'd-none'}}" >
                  <label for="discount"> Discount (%)<span class="text-red">*</span></label>
                  <input type="text" id="discount" name="discount" class="form-control form-control-sm" value="{{old('discount')}}" >
                  <label class="error-msg text-red" >@error('discount'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 {{ old('discount_type') != '' && old('discount_type') != null ? '' : 'd-none'}}" >
                  <label for="amount_of_discount">Amount of Discount<span class="text-red">*</span></label>
                  <input type="text" id="amount_of_discount" name="amount_of_discount" class="form-control form-control-sm money2" value="{{old('amount_of_discount')}}" >
                  <label class="error-msg text-red" >@error('amount_of_discount'){{$message}}@enderror</label>
                </div>



                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="bid_as_evaluated">Bid as Evaluated<span class="text-red">*</span></label>
                  <input type="text" id="bid_as_evaluated" name="bid_as_evaluated" class="form-control form-control-sm money2" value="{{old('bid_as_evaluated')}}" >
                  <label class="error-msg text-red" >@error('bid_as_evaluated'){{$message}}@enderror</label>
                </div>


              </div>
              <div class="d-flex justify-content-center col-sm-12">
                <button class="btn btn-primary text-center">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">  @if($data->detailed_bids!=null) Clustered @endif @if($data->mode==1) Project Bidders @else Price Quotations @endif</h2>
          <label class="text-sm">Project Number:  <span class="">{{$data->project_number}}</span></label>
          <br />
          <label class="text-sm">Project Title: <span class="">{{$data->title}}</span></label>
          <br />
          <label class="text-sm">Date Bid Opened: <span class="">{{$data->open_bid}}</span></label>
          <br />
          <label class="text-sm">Project Cost: <span class="text-red">Php {{number_format($data->project_cost,2,'.',',')}}</span></label>
        </div>
        <div class="card-body">

          <div class="col-sm-12">
            <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
          </div>

          <div class="table-responsive">
            <table class="table table-bordered wrap" id="bidders_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Rank</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Proposed Bid/Bid as Read</th>
                  <th class="text-center">Bid in Words</th>
                  <th class="text-center">Initial Bid as Evaluated</th>
                  <th class="text-center">Discount</th>
                  <th class="text-center">Discount in Amount</th>
                  <th class="text-center">Bid as Evaluated</th>
                  <th class="text-center">Discount Type</th>
                  <th class="text-center">Discount Source</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @php
                $row_number=1;
                @endphp
                @foreach($data->project_bidders as $bidder)
                <tr>
                  <td>
                    <div style='white-space: nowrap'>
                      @if(in_array("update",$user_privilege))
                      @if($bidder->bid_status==='active')
                      <button class="btn btn-sm btn-danger disqualify-btn">Disqualify</button>
                      <button class="btn btn-sm btn-warning ineligible-btn">Ineligibile</button>
                      <button class="btn btn-sm btn-success edit-btn">Edit </button>
                      @elseif($bidder->bid_status==='responsive'||$bidder->bid_status==='non-responsive')

                      @else
                      <button class="btn btn-sm btn-primary reactivate-btn">Reactivate</button>
                      @endif
                      @endif
                    </div>
                  </td>
                  <td>{{$bidder->project_bid}}  </td>
                  @php
                  if($bidder->proposed_bid!=null && $bidder->proposed_bid>0 && $bidder->bid_status!='disqualified'){
                    echo "<td>".$row_number."</td>";
                    $row_number=$row_number+1;
                  }
                  else{
                    echo "<td></td>";
                  }
                  @endphp
                  <td>{{$bidder->business_name}}
                    @if($bidder->project_bid===$data->project_bidders[0]->project_bid && $bidder->bid_status=='active' && $bidder->date_received<=$data->open_bid && $bidder->proposed_bid>0)
                    @endif
                    @if($bidder->date_received>$data->open_bid)
                    <span class="badge badge-danger">Late</span>
                    @endif
                  </td>
                  <td>{{$bidder->owner}}</td>
                  <td>{{number_format($bidder->proposed_bid,2,'.',',')}}</td>
                  <td>{{number_format($bidder->bid_in_words,2,'.',',')}}</td>
                  <td>{{number_format($bidder->initial_bid_as_evaluated,2,'.',',')}}</td>
                  <td>{{number_format($bidder->discount,3,'.',',')}}%</td>
                  <td>{{number_format($bidder->amount_of_discount,2,'.',',')}}</td>
                  <td>{{number_format($bidder->bid_as_evaluated,2,'.',',')}}</td>
                  <td>{{$bidder->discount_type}}</td>
                  <td>{{$bidder->discount_source}}</td>
                  <td>{{$bidder->bid_status}}</td>
                </tr>
                @endforeach
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>

    @if($data->detailed_bids!=null)
    <div class="card shadow mt-4">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Detailed Bids</h2>
        </div>
        <div class="card-body">

          <div class="col-sm-12">
            <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
          </div>

          <div class="table-responsive">
            <table class="table table-bordered wrap" id="detailed_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Bid in Words</th>
                  <th class="text-center">Proposed Bid/Bid as Read</th>
                  <th class="text-center">Initial Bid as Evaluated</th>
                  <th class="text-center">Discount</th>
                  <th class="text-center">Discount in Amount</th>
                  <th class="text-center">Bid as Evaluated</th>
                  <th class="text-center">Discount Type</th>
                  <th class="text-center">Discount Source</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data->detailed_bids as $bidder)
                <tr>
                  <td>
                    <div style='white-space: nowrap'>
                      @if(in_array("update",$user_privilege))
                      @if($bidder->bid_status==='active')
                      <button class="btn btn-sm btn-success edit-btn">Edit</button>
                      @endif
                      @endif
                    </div>
                  </td>
                  <td>{{$bidder->project_bid}}  </td>
                  <td>{{$bidder->project_title}}</td>
                  <td>{{number_format($bidder->project_cost,2,'.',',')}}</td>
                  <td>{{$bidder->business_name}}</td>
                  <td>{{$bidder->owner}}</td>
                  <td>{{number_format($bidder->detailed_bid_in_words,2,'.',',')}}</td>
                  <td>{{number_format($bidder->detailed_bid_as_read,2,'.',',')}}</td>
                  <td>{{number_format($bidder->detailed_initial_bid_as_evaluated,2,'.',',')}}</td>
                  <td>{{number_format($bidder->detailed_discount,2,'.',',')}}%</td>
                  <td>{{number_format($bidder->detailed_amount_of_discount,2,'.',',')}}</td>
                  <td>{{number_format($bidder->detailed_bid_as_evaluated,2,'.',',')}}</td>
                  <td>{{$bidder->detailed_discount_type}}</td>
                  <td>{{$bidder->detailed_discount_source}}</td>
                  <td>{{$bidder->bid_status}}</td>
                </tr>
                @endforeach
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>

    @endif


  </div>
</div>

@endsection

@push('custom-scripts')
<script>
if("{{old('process_type')}}"=="Reactivate"){
  $("#bidders_form").prop("action","{{route('reactivate_bidder')}}");
  $("#process_type").val('Reactivate');
}
else if("{{old('process_type')}}"=="Ineligible"){
  $("#bidders_form").prop("action","{{route('disqualify_bidder')}}");
  $("#process_type").val('Ineligible');
}
else{
  $("#bidders_form").prop("action","{{route('disqualify_bidder')}}");
  $("#process_type").val('Disqualify');
}

// datatables
$('#bidders_table thead tr').clone(true).appendTo( '#bidders_table thead' );
$('#bidders_table thead tr:eq(1)').removeClass('bg-primary');

var table=  $('#bidders_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  orderCellsTop: true,
  select: {
    style: 'multi',
    selector: 'td:not(:first-child)'
  },
  order: [[ 2, "asc" ]],
  columnDefs: [ {
    targets: 0,
    orderable: false
  },
  {
    "targets": [1],
    "visible": false
  }],

});

var detailed_table=  $('#detailed_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  orderCellsTop: true,
  select: {
    style: 'multi',
    selector: 'td:not(:first-child)'
  },
  order: [[ 2, "asc" ]],
  columnDefs: [ {
    targets: 0,
    orderable: false
  },
  {
    "targets": [1],
    "visible": false
  } ],

});



var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>2){
  var isProposedError="@error('proposed_bid') true @enderror";
  var isEvaluated="@error('bid_as_evaluated') true @enderror";
  var isDisqualifyError="@error('remarks') true @enderror";
  var isDisqualifyError="@error('discount') true @enderror";
  if(isProposedError==" true " || isEvaluated==" true " || isDisqualifyError==" true "){
    if("{{old('proposed_bid_type')}}"=="detailed"){
      $("#proposed_bid_form").prop("action","/edit_detailed_bid");
    }
    if("{{old('proposed_bid_type')}}"=="clustered"){
      $("#proposed_bid_form").prop("action","/edit_proposed_bid");
    }
    $("#bid_modal").modal('show');
  }
  else if(isDisqualifyError==" true "){
    $("#bidder_modal").modal('show');
  }
  else{

  }

}



if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully Set Project Bidder to Disqualified/Ineligible',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#bidder_modal").modal('hide');
  }

  else if("{{session('message')}}"=="edit_success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully Saved',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#proposed_bid").modal('hide');
  }

  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
}

// function
function getDiscount() {
  var bid_in_words=$("#bid_in_words").val();
  var bid_as_read=$("#proposed_bid").val();
  var project_cost=$("#project_cost").val();
  var discount_type=$("#discount_type").val();
  var initial_bid_as_evaluated=$("#initial_bid_as_evaluated").val();
  var discount_source=$("#discount_source").val();

  var discount=$("#discount").val();
  var amount_of_discount=0.00;
  var bid_as_evaluated=0.00;
  project_cost=parseFloat(project_cost.replaceAll(",",""));
  bid_as_read=parseFloat(bid_as_read.replaceAll(',',''));
  bid_in_words=parseFloat(bid_in_words.replaceAll(',',''));
  initial_bid_as_evaluated=parseFloat(initial_bid_as_evaluated.replaceAll(',',''));

  if(discount_type=="percentage"){
    if(discount>0){
      if(discount_source=="abc"){
        amount_of_discount=project_cost* (discount/100);
        bid_as_evaluated=bid_as_read-amount_of_discount;
      }
      else if(discount_source=="bid_as_read"){
        amount_of_discount=bid_as_read* (discount/100);
        bid_as_evaluated=bid_as_read-amount_of_discount;
      }
      else if(discount_source=="bid_in_words"){
        console.log(bid_in_words);
        amount_of_discount=bid_in_words* (discount/100);
        bid_as_evaluated=bid_as_read-amount_of_discount;
      }
      else if(discount_source=="bid_as_evaluated"){
        amount_of_discount=initial_bid_as_evaluated* (discount/100);
        bid_as_evaluated=bid_as_read-amount_of_discount;
      }
      else{
        amount_of_discount=0;
        bid_as_evaluated=0;
        // swal.fire({
        //   title: `Error`,
        //   text: 'Please fill Initial Bid as Evaluated',
        //   buttonsStyling: false,
        //   confirmButtonClass: 'btn btn-sm btn-warning',
        //   icon: 'warning'
        // });
      }

      if(bid_as_evaluated>0){
        $("#amount_of_discount").val(amount_of_discount.toFixed(2));
        $("#bid_as_evaluated").val(bid_as_evaluated.toFixed(2));
      }
    }
    else{
      $("#bid_as_evaluated").val($("#initial_bid_as_evaluated").val());
    }
  }
  else if(discount_type=="amount"){
    var amount_of_discount=$("#amount_of_discount").val();
    amount_of_discount=parseFloat(amount_of_discount.replaceAll(',',''));
    if(amount_of_discount>0){
      bid_as_evaluated=initial_bid_as_evaluated-amount_of_discount;
      if(bid_as_evaluated>0){
        $("#bid_as_evaluated").val(bid_as_evaluated.toFixed(2));
      }
    }
    else{
      $("#bid_as_evaluated").val($("#initial_bid_as_evaluated").val());
    }
  }
  else{
    $("#bid_as_evaluated").val($("#initial_bid_as_evaluated").val());
  }
  $('.money2').unmask();
  $('.money2').mask("#,##0.00", {reverse: true});
}


// events

$('#bidders_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input class="px-0 mx-0" type="text" placeholder="Search '+title+'" />' );
    $(this).addClass('sorting_disabled');
    $( 'input', this ).on( 'keyup change', function () {
      if ( table.column(i).search() !== this.value ) {
        table
        .column(i)
        .search( this.value )
        .draw();
      }
    } );
  }
});

@if(in_array("update",$user_privilege))
// Disqualify
$('#bidders_table tbody').on('click', '.disqualify-btn', function (e) {
  Swal.fire({
    title:'Disqualify @if($data->mode==1) Project Bidder @else  Price Quotation @endif',
    text: 'Are you sure to Disqualify this @if($data->mode==1) Project Bidder @else  Price Quotation @endif?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#bidder_modal_title").html("Disqualify");
      $("#bidders_form").prop("action","{{route('disqualify_bidder')}}");
      var data = table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  business_name=data[3];
      $("#process_type").val('Disqualify');
      $("#ineligibility").val('Disqualify');
      business_name=business_name.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[4]);
      $("#bidder_modal").modal('show');
    }
  });

});

// ineligible
$('#bidders_table tbody').on('click', '.ineligible-btn', function (e) {
  Swal.fire({
    title:'Ineligible @if($data->mode==1) Project Bidder @else  Price Quotation @endif',
    text: 'Are you sure to set  this @if($data->mode==1) Project Bidder @else  Price Quotation @endif Ineligible?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#bidder_modal_title").html("Ineligible");
      $("#bidders_form").prop("action","{{route('disqualify_bidder')}}");
      var data = table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  business_name=data[3];
      $("#process_type").val('Ineligibility');
      $("#ineligibility").val('Ineligible');
      business_name=business_name.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[4]);
      $("#bidder_modal").modal('show');
    }
  });

});

$('#bidders_table tbody').on('click', '.edit-btn', function (e) {
  $('.error-msg').html("");
  var data = table.row( $(this).parents('tr') ).data();
  $("#proposed_bid_bidder_id").val(data[1]);
  var  business_name=data[3];
  var proposed_amount=data[5];
  var discount=data[8].replaceAll(",","");
  var amount_of_discount=data[9].replaceAll(",","");
  business_name=business_name.replace('                                                            <span class="badge badge-danger">Late</span>','');
  $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>',''));
  $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>',''));
  $("#project_cost").val("{{number_format($data->project_cost,2,'.',',')}}");
  $("#proposed_bid_owner").val(data[4]);
  $("#bid_in_words").val(data[6].replaceAll(",",""));
  $("#initial_bid_as_evaluated").val(data[7].replaceAll(",",""));
  $("#proposed_bid_bidder_modal").modal('show');
  $("#proposed_bid").val(proposed_amount.replaceAll(",",""));
  $("#proposed_bid_form").prop("action","/edit_proposed_bid");
  $("#proposed_bid_type").val("clustered");
  $("#amount_of_discount").val(amount_of_discount);
  $("#discount_type").val(data[11]);
  $("#discount_source").val(data[12]);
  $("#discount").val(discount.replaceAll('%',''));
  if($("#proposed_bid").val()==$("#initial_bid_as_evaluated").val()){
    $("#is_same_initial_bid_eval").prop("checked",true);
  }
  if($("#proposed_bid").val()==$("#bid_in_words").val()){
    $("#is_same_bid_in_words").prop("checked",true);
  }
  if(data[11]=="percentage"){
    $("#discount_source").parent().removeClass("d-none");
    $("#discount").parent().removeClass("d-none");
    $("#amount_of_discount").parent().removeClass("d-none");
  }
  if(data[11]=="amount"){
    $("#amount_of_discount").parent().removeClass("d-none");
  }
  $("#bid_modal").modal('show');
  getDiscount();
  $("#bid_as_evaluated").val(data[10].replaceAll(",",""));
  $('.money2').unmask();
  $('.money2').mask("#,##0.00", {reverse: true});

});

$('#detailed_table tbody').on('click', '.edit-btn', function (e) {
  $('.error-msg').html("");
  var data = detailed_table.row( $(this).parents('tr') ).data();
  $("#proposed_bid_bidder_id").val(data[1]);
  var  business_name=data[4];
  var proposed_amount=data[7];
  var discount=data[9].replaceAll(",","");
  var amount_of_discount=data[10].replaceAll(",","");
  business_name=business_name.replace('                                                            <span class="badge badge-danger">Late</span>','');
  $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>',''));
  // $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>',''));
  $("#project_cost").val(data[3].replaceAll(",",""));
  $("#proposed_bid_owner").val(data[5]);
  $("#bid_in_words").val(data[6].replaceAll(",",""));
  $("#initial_bid_as_evaluated").val(data[8].replaceAll(",",""));
  $("#proposed_bid_bidder_modal").modal('show');
  $("#proposed_bid").val(proposed_amount.replaceAll(",",""));
  $("#proposed_bid_form").prop("action","/edit_detailed_bid");
  $("#proposed_bid_type").val("detailed");
  $("#amount_of_discount").val(amount_of_discount);
  $("#discount_type").val(data[12]);
  $("#discount_source").val(data[13]);
  $("#discount").val(discount.replaceAll('%',''));
  if($("#proposed_bid").val()==$("#initial_bid_as_evaluated").val()){
    $("#is_same_initial_bid_eval").prop("checked",true);
  }
  if($("#proposed_bid").val()==$("#bid_in_words").val()){
    $("#is_same_bid_in_words").prop("checked",true);
  }
  if(data[13]=="percentage"){
    $("#discount_source").parent().removeClass("d-none");
    $("#discount").parent().removeClass("d-none");
    $("#amount_of_discount").parent().removeClass("d-none");
  }
  if(data[13]=="amount"){
    $("#amount_of_discount").parent().removeClass("d-none");
  }
  $("#bid_modal").modal('show');
  getDiscount();
  $("#bid_as_evaluated").val(data[11].replaceAll(",",""));
  $('.money2').unmask();
  $('.money2').mask("#,##0.00", {reverse: true});


});

@endif

@if(in_array("delete",$user_privilege))
$('#bidders_table tbody').on('click', '.reactivate-btn', function (e) {
  Swal.fire({
    title:'Reactivate @if($data->mode==1) Project Bidder @else  Price Quotation @endif',
    text: 'Are you sure to Reactivate this @if($data->mode==1) Project Bidders @else Price Quotations @endif?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#bidder_modal_title").html("Reactivate");
      var data = table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  business_name=data[3];
      $("#bidders_form").prop("action","{{route('reactivate_bidder')}}");
      $("#process_type").val('Reactivate');
      business_name=business_name.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[4]);
      $("#bidder_modal").modal('show');
    }
  });
});
@endif

$("input").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function functionName() {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});


$(".money2").click(function () {
  $('.money2').mask("#,##0.00", {reverse: true});
});

$(".money2").keyup(function () {
  $('.money2').mask("#,##0.00", {reverse: true});
});


$("#proposed_bid").change(function functionName() {
  getDiscount();
});

$("#bid_in_words").change(function functionName() {
  getDiscount();
});

$("#initial_bid_as_evaluated").change(function functionName() {
  getDiscount();
});

$("#discount").change(function functionName() {
  if($("#discount").val()>0){
    if($("#discount_source").val()==""){
      $(this).val("");
      swal.fire({
        title: `Error`,
        text: 'Please Select Discount Source',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{
      getDiscount();
    }
  }
});

$("#discount_source").change(function functionName() {
  getDiscount();
});

$("#amount_of_discount").change(function functionName() {
  getDiscount();
});

$("#discount_type").change(function functionName() {
  if($(this).val()=="percentage"){
    $("#amount_of_discount").parent().removeClass("d-none");
    $("#discount").parent().removeClass("d-none");
    $("#discount_source").parent().removeClass("d-none");
  }
  else if($(this).val()=="amount"){
    $("#discount").parent().removeClass("d-none");
    $("#discount").parent().addClass("d-none");
    $("#discount_source").parent().removeClass("d-none");
    $("#discount_source").parent().addClass("d-none");
    $("#amount_of_discount").parent().removeClass("d-none");
  }
  else{
    $("#discount_source").parent().removeClass("d-none");
    $("#discount_source").parent().addClass("d-none");
    $("#discount").parent().removeClass("d-none");
    $("#amount_of_discount").parent().removeClass("d-none");
    $("#discount").parent().addClass("d-none");
    $("#amount_of_discount").parent().addClass("d-none");

  }
  $("#bid_as_evaluated").val($("#initial_bid_as_evaluated").val());
  $("#discount").val(0);
  $("#discount_source").val("");
  $("#amount_of_discount").val(0);
});


$("#is_same_bid_in_words").click(function functionName() {

  if($(this).prop("checked")==true){
    var value=$("#proposed_bid").val();
    $("#bid_in_words").val(value);
    $("#bid_in_words").click();
  }
  else{
    $("#bid_in_words").val("");
  }
});

$("#is_same_initial_bid_eval").click(function functionName() {
  if($(this).prop("checked")==true){
    var value=$("#proposed_bid").val();
    $("#initial_bid_as_evaluated").val(value);
    $("#initial_bid_as_evaluated").change();
  }
  else{
    $("#initial_bid_as_evaluated").val("");
    $("#initial_bid_as_evaluated").change();
  }
});

</script>
@endpush
