<div class="col-sm-12">
  <div class="modal" tabindex="-1" role="dialog" id="extend_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="extend_modal_title">Extend Project/s</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
          <form class="col-sm-12" method="POST" id="submit_extend" action="/extend_process">
            @csrf
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <h5 for="">Plan ID</h5>
                <input type="text" id="extend_plan_id" name="extend_plan_id" class="form-control form-control-sm" value="{{old('extend_plan_id')}}" readonly>
                <label class="error-msg text-red" >@error('extend_plan_id'){{$message}}@enderror</label>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                <h5 for="">Project Numbers</h5>
                <input type="text" id="extend_project_number" name="extend_project_number" class="form-control form-control-sm" readonly value="{{old('extend_project_number')}}" >
                <label class="error-msg text-red" >@error('extend_project_number'){{$message}}@enderror</label>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0 d-none">
                <h5 for="process">Process</h5>
                <select type="text" id="process" name="process" class="form-control form-control-sm " value="{{old('process')}}"  >
                  <option value="advertisement_posting">Advertisement Posting</option>
                  <option value="pre_bid">Prebid</option>
                  <option value="submission_opening">Bid Submission</option>
                  <option value="bid_evaluation">Bid Evaluation</option>
                  <option value="post_qualification">Post Qualification</option>
                  <option value="notice_of_award">Notice of Awards</option>
                  <option value="contract_preparation_signing">Contract Preparation and Signing</option>
                  <option value="approval_by_higher_authority">Approval By Higher Authority</option>
                  <option value="notice_to_proceed">Notice to Proceed</option>
                </select>
                <label class="error-msg text-red" >@error('process'){{$message}}@enderror</label>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                <h5 for="extend_date">Date</h5>
                <input type="text" id="extend_date" name="extend_date" class="form-control form-control-sm datepicker2" value="{{old('extend_date')}}" ></input>
                <label class="error-msg text-red" >@error('extend_date'){{$message}}@enderror</label>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                <h5 for="extend_remarks">Remarks</h5>
                <textarea type="text" id="extend_remarks" name="extend_remarks" class="form-control form-control-sm " value="{{old('extend_remarks')}}" ></textarea>
                <label class="error-msg text-red" >@error('extend_remarks'){{$message}}@enderror</label>
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
