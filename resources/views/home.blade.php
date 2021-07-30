@extends('layout.layout')

@section('content')
<div class="row" style="margin-top:50px;">
    <div class="col-lg-12 col-md-12">
        <div class="row">
            <div class="col-md-6">
                <h3>Subscribers</h3>
            </div>
            <div class="col-md-6">
                <ul class="nav justify-content-center">
                    <li class="nav-item">
                      <a class="nav-link active btn btn-sm btn-primary " href="/subscribe">Add Subscribers</a>
                    </li>
                  </ul>
            </div>
        </div>
        <div class="row">
            <table class="table" id="subscribers">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Email</th>
                        <th scope="col">Name</th>
                        <th scope="col">Country</th>
                        <th scope="col">Subscribe Date</th>
                        <th scope="col">Subscribe Time</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot class="thead-light">
                    <tr>
                        <th scope="col">Email</th>
                        <th scope="col">Name</th>
                        <th scope="col">Country</th>
                        <th scope="col">Subscribe Date</th>
                        <th scope="col">Subscribe Time</th>
                        <th scope="col">Delete</th>
                    </tr>
                 </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" 
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer"></script>
<script src="{{asset('js/home.js')}}"></script>
@endsection