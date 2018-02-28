<div class="container">
    <div class="row">
    	<div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                        {{ csrf_field() }}
                    <div class="form-group">
					    <div class="col-md-6 col-md-offset-4">
					    	<input type="hidden" name="employee_id" value="{{Auth::user()->employee_id}}">
					    	<input type="hidden" class="ctype" name="ctype">
					    	<input type="hidden" class="checkdate" name="date">
					    	<input type="hidden" class="checktime" name="checktime">
					        <button type="submit" class="btn btn-primary btn-in btn-log">TIME IN</button>
					        <button type="submit" class="btn btn-primary btn-out btn-log">TIME OUT</button>
					    </div>
					</div>
                </div>
            </div>
        </div>            
    </div>
</div>    
