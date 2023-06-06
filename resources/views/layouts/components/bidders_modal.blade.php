<div class="col-sm-12">
  <div class="modal" tabindex="-1" role="dialog" id="bidders_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="bidders_modal_title">Project Bidders</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
          <form class="col-sm-12" method="POST" id="submit_bidders" action="/submit_bidders">
            @csrf
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <h5 for="">Plan ID</h5>
                <input type="text" id="bidders_plan_id" name="bidders_plan_id" class="form-control form-control-sm" value="{{old('bidders_plan_id')}}" readonly>
                <label class="error-msg text-red" >@error('bidders_plan_id'){{$message}}@enderror</label>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                <h5 for="">Project Title</h5>
                <input type="text" id="bidders_project_title" name="bidders_project_title" class="form-control form-control-sm" readonly value="{{old('bidders_project_title')}}" >
                <label class="error-msg text-red" >@error('bidders_project_title'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                <h5 for="bidders_remarks">Remarks</h5>
                <textarea type="text" id="bidders_remarks" name="bidders_remarks" class="form-control form-control-sm " value="{{old('bidders_remarks')}}" ></textarea>
                <label class="error-msg text-red" >@error('bidders_remarks'){{$message}}@enderror</label>
              </div>
            </div>
            <div class="d-flex justify-content-center col-sm-12">
              <button  class="btn btn-primary text-center">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
