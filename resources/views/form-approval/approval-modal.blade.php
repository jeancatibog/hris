<!-- Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
  <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="approvalModalLabel">{{ucfirst($form)}} Form</h4>
      </div>
      <form class="form-horizontal approval-form" role="form" method="POST" action="{{ route('form-approval.update', ['id' => $id, 'action_id']) }}">
        <input type="hidden" name="ftype" value="{{$form}}">
        <div class="modal-body">
          <div class="container">
            <div class="row">
                <div class="panel panel-default">
                </div>
                <div class="form-group">
                  <label for="reason" class="col-md-4 control-label">Approvers Remarks</label>
                  <div class="col-md-6">
                      <textarea class="form-control" rows="5" id="approvers_remarks" name="approvers_remarks" required></textarea>
                      @if ($errors->has('approvers_remarks'))
                          <span class="help-block">
                              <strong>{{ $errors->first('approvers_remarks') }}</strong>
                          </span>
                      @endif
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="_method" value="PATCH">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="col-md-10">
            <button type="button" class="btn btn-success col-sm-3 col-xs-5 btn-margin btn-approval" id="approved" title="Approve"><i class="fa fa-thumbs-up"></i><span>Approve</span>
            </button>
            <button type="button" class="btn btn-primary col-sm-3 col-xs-5 btn-margin btn-approval" id="disapproved" title="Disapprove"><i class="fa fa-thumbs-down"></i><span>Disapprove</span>
            </button>
            <button type="button" class="btn btn-danger col-sm-3 col-xs-5 btn-margin btn-approval" id="cancelled" title="Cancel"><i class="fa fa fa-close"></i><span>Cancel</span>
            </button>
          </div>
        </div>
      </form>  
  </div>
</div>    
<div id="content">
</div>