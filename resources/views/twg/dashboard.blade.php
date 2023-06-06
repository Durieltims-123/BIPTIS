@extends('layouts.app')

@section('content')
@include('layouts.headers.cards')

<div class="container-fluid mt--7">
  <div class="row">
    <div class="col-xl-12 mb-5 mb-xl-0">
      <div class="card shadow">
        <div class="card-header ">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="text-uppercase text-red ls-1 mb-1">Upcoming Events</h6>
              <h2 class="text-black mb-0">Procurement Process</h2>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="data_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">Start Date</th>
                  <th class="text-center">End Date</th>
                  <th class="text-center">Process</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                </tr>
              </thead>
              <tbody class="bg-white">

                @foreach($events as $event)
                <tr>
                  <td>{{$event->start_date}}</td>
                  <td>{{$event->end_date}}</td>
                  <td>{{$event->process}}</td>
                  <td>{{$event->project_no}}</td>
                  <td>{{$event->project_title}}</td>
                </tr>
                @endforeach
              </tbody>

            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
  @include('layouts.footers.auth')
</div>
@endsection

@push('js')
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endpush

@push('custom-scripts')
<script>
$.ajax({
  type: 'GET',
  url: '/get_ending_post_qual',
  success: function (data) {
    var ending_post_qual=data.ending_post_qual;
    var extension_post_qual=data.extension_post_qual;

    extension_post_qual.forEach((item, i) => {
      if(extension_post_qual[0].date_diff>40){
        toastr.warning("<a href='/twg_post_qualification'> Post Qualification for project with Number: "+extension_post_qual[i].project_no+" is due on "+extension_post_qual[i].post_qualification_end+". </a>");
      }
      else{
        toastr.warning("<a href='/twg_post_qualification'> Post Qualification for project with Number: "+extension_post_qual[i].project_no+" is due on "+extension_post_qual[i].post_qualification_end+". Request for extension? </a>");
      }
    });

    ending_post_qual.forEach((item, i) => {
      toastr.error("<a href='/twg_post_qualification'> Post Qualification for project with Number: "+ending_post_qual[i].project_no+" is due on "+ending_post_qual[i].post_qualification_end+" </a>");
    });

  },
  error: function() {
  }
});

$('#data_table thead tr').clone(true).appendTo( '#data_table thead' );
$('#data_table thead tr:eq(1)').removeClass('bg-primary');
$('#data_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
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


var table=  $('#data_table').DataTable({
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
  responsive:true,
  columnDefs: [ {
    targets: 0,
    orderable: false
  } ],
  order: [[ 1, "asc" ]],
});

</script>

@endpush
