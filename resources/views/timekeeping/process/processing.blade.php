@extends('layouts.base', ['module' => 'Timekeeping Processing'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <!-- <div class="panel-heading">Add new period cover</div> -->
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('timekeeping.processing') }}">
                        {{ csrf_field() }}
                        <br>
                        <br>
                        <div class="form-group{{ $errors->has('period_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Process Coverage</label>
                            <div class="col-md-6">
                                <select class="form-control" name="period_id" required>
                                  <option value="">Please select period coverage</option>
                                    @foreach ($periods as $period)
                                        <option value="{{$period->id}}">{{ date("M d, Y", strtotime($period->start_date)) }} - {{ date("M d, Y", strtotime($period->end_date)) }}</option>
                                    @endforeach
                                </select>

                              @if ($errors->has('period_id'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('period_id') }}</strong>
                                  </span>
                              @endif
                            </div>
                        </div>
                        <div class="form-group">
                          <div class="col-md-8 col-md-offset-6">
                            <button type="submit" class="btn btn-primary process-btn">
                                Process
                            </button>
                          </div>
                          <div class="col-md-6 col-md-offset-4">
                            <div class="progress" style="display: none;">
                              <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
                              aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
                                % Complete
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
@endsection