@extends('layout.layout')
@section('content')
<div class="h-100 row align-items-center">
        <form method="POST" action="{{url('validateApiKey')}}">
            @csrf 
            <div class="form-group">
              <label for="apiKey">Api Key</label>
              <input type="password" 
                     class="form-control" 
                     id="apiKey" 
                     name="api_key" 
                     aria-describedby="apiKeyHelp" 
                     placeholder="Enter API Key" required>
              <small id="apiKeyHelp" 
                     class="form-text text-muted">Enter your API Key from MailerLite.</small>
              <small id="apiKeyHelp" 
                     class="form-text text-muted">Demo key in the database.</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            
            @if(Session::has('fail'))
                <div class="alert alert-danger">
                    {{Session::get('fail')}}
                </div>
            @endif
        </form>
</div>
@endsection