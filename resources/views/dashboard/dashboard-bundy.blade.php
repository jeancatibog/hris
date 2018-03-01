<div class="container">
    <div class="row">
    	<div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-horizontal" id="time-logs" role="form" method="POST"  action="{{ route('timekeeping.log') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

				    	<input type="hidden" name="employee_id" value="{{Auth::user()->employee_id}}">
				    	<input type="hidden" class="ctype" name="ctype" value="">
				    	<input type="hidden" class="checkdate" name="date" value="">
				    	<input type="hidden" class="checktime" name="checktime" value="">
                    	<div class="form-group">
                    		<div class="col-md-2">
						        <button type="submit" class="btn btn-primary btn-in btn-log"><span>TIME IN</span></button>
						    </div>
							<div class="col-md-8 col-md-offset-0">
						    	<div id="clock" class="light">
								    <div class="display">
								        <div class="weekdays"></div>
								        <div class="ampm"></div>
								        <div class="alarm"></div>
								        <div class="digits"></div>
								    </div>
								</div>
							</div>
						    <div class="col-md-2">
						        <button type="submit" class="btn btn-primary btn-out btn-log"><span>TIME OUT</span></button>
						    </div>
						</div>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>    
