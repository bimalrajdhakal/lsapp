
{{-- alert error  message --}}
@if(count($errors)>0)
  @foreach ($errors->all() as $error)
      <div class="alert alert-danger">
          {{$error}}
      </div>
  @endforeach
@endif
{{-- session success alert message --}}
@if(session('success'))
      <div class="alert alert-success">
          {{session('success')}}
      </div>
@endif
{{-- session error alert message --}}
@if(session('error'))
      <div class="alert alert-danger">
          {{session('error')}}
      </div>
@endif