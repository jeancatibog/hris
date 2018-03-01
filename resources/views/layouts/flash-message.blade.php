@if ($message = Session::get('message'))
<div class="alert alert-{{ Session::get('status') }} alert-block">
        {{ $message }}
</div>
@endif