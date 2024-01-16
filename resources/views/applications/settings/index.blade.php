@extends('user_type.auth', ['parentFolder' => 'appSettings', 'childFolder' => 'none'])

@section('content')
    @if(isset($message))
        <script>
            alert('{{$message}}');
        </script>

    @endif

    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">Profile Information</h6>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="{{route('app-settings')}}" method="POST" role="form text-left">
                    @csrf
                    <div class="row">

                        @foreach($generalSettings['devices_name']['value'] as $key => $value)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-name" class="form-control-label">Name for {{ $key }}</label>
                                    <div class="">
                                        <input class="form-control" value="{{$value}}" type="text" placeholder="Name"
                                               id="devices_name_{{$key}}" name="devices_name[{{$key}}]"
                                               onfocus="focused(this)" onfocusout="defocused(this)">
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </div>
                    <div hidden class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Phone</label>
                                <div class="">
                                    <input class="form-control" type="tel" placeholder="40770888444" id="number"
                                           name="phone" value="" onfocus="focused(this)" onfocusout="defocused(this)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.location" class="form-control-label">Location</label>
                                <div class="">
                                    <input class="form-control" type="text" placeholder="Location" id="name"
                                           name="location" value="" onfocus="focused(this)"
                                           onfocusout="defocused(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div hidden class="form-group">
                        <label for="about">About Me</label>
                        <div class="">
                            <textarea class="form-control" id="about" rows="3"
                                      placeholder="Say something about yourself" name="about_me"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
