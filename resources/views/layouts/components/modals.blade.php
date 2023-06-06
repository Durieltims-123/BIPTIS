<div class="col-sm-12">
  <div class="modal" tabindex="-1" role="dialog" id="rebid_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="rebid_modal_title">Rebid Project</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
          <form class="col-sm-12" method="POST" id="submit_rebid" action="/submit_rebid">
            @csrf
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <h5 for="">Plan ID</h5>
                <input type="text" id="rebid_plan_id" name="rebid_plan_id" class="form-control form-control-sm" value="{{old('rebid_plan_id')}}" readonly>
                <label class="error-msg text-red" >@error('rebid_plan_id'){{$message}}@enderror</label>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                <h5 for="">Project Title</h5>
                <input type="text" id="rebid_project_title" name="rebid_project_title" class="form-control form-control-sm" readonly value="{{old('rebid_project_title')}}" >
                <label class="error-msg text-red" >@error('rebid_project_title'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                <h5 for="rebid_remarks">Remarks</h5>
                <textarea type="text" id="rebid_remarks" name="rebid_remarks" class="form-control form-control-sm " value="{{old('rebid_remarks')}}" ></textarea>
                <label class="error-msg text-red" >@error('rebid_remarks'){{$message}}@enderror</label>
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

<div class="col-sm-12">
  <div class="modal" tabindex="-1" role="dialog" id="review_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="review_modal_title">Set Project for Review</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
          <form class="col-sm-12" method="POST" id="submit_review" action="/submit_review">
            @csrf
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <h5 for="">Plan ID</h5>
                <input type="text" id="review_plan_id" name="review_plan_id" class="form-control form-control-sm" value="{{old('review_plan_id')}}" readonly>
                <label class="error-msg text-red" >@error('review_plan_id'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                <h5 for="">Project Title</h5>
                <input type="text" id="review_project_title" name="review_project_title" class="form-control form-control-sm" readonly value="{{old('review_project_title')}}" >
                <label class="error-msg text-red" >@error('review_project_title'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                <h5 for="remarks">Remarks</h5>
                <textarea type="text" id="review_remarks" name="review_remarks" class="form-control form-control-sm " value="{{old('review_remarks')}}" ></textarea>
                <label class="error-msg text-red" >@error('review_remarks'){{$message}}@enderror</label>
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

<div class="col-sm-12">
  <div class="modal" tabindex="-1" role="dialog" id="rebideval_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="rebideval_modal_title">Set Project for Bid Evaluation</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
          <form class="col-sm-12" method="POST" id="submit_rebideval" action="/submit_rebideval">
            @csrf
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <h5 for="">Plan ID</h5>
                <input type="text" id="rebideval_plan_id" name="rebideval_plan_id" class="form-control form-control-sm" value="{{old('rebideval_plan_id')}}" readonly>
                <label class="error-msg text-red" >@error('rebideval_plan_id'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                <h5 for="">Project Title</h5>
                <input type="text" id="rebideval_project_title" name="rebideval_project_title" class="form-control form-control-sm" readonly value="{{old('rebideval_project_title')}}" >
                <label class="error-msg text-red" >@error('rebideval_project_title'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                <h5 for="remarks">Remarks</h5>
                <textarea type="text" id="rebideval_remarks" name="rebideval_remarks" class="form-control form-control-sm " value="{{old('rebideval_remarks')}}" ></textarea>
                <label class="error-msg text-red" >@error('rebideval_remarks'){{$message}}@enderror</label>
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
