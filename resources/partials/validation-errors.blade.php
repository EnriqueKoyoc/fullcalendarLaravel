@if ($errors->any())
@foreach ($errors->all() as $error)
<div class="alert alert-danger"><strong>* {{$error}}</strong></div>

@endforeach
@endif