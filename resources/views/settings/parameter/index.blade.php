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
                <h6 class="mb-0">
                    <Information></Information>
                </h6>
            </div>
            <div class="card-body pt-4 p-3">
                <form method="POST" role="form text-left">
                    @csrf
                    <div class="row">
                        @foreach($parameter_profile as $key => $value)
                            @php
                                $name = 'parameter_profile['.$key.']';
                            @endphp
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{$name}}" class="form-control-label">{{$key}}</label>

                                    <div class="">
                                        <textarea class="form-control" rows="4" id="{{$name}}" name="{{$name}}"
                                                  onfocus="focused(this)"
                                                  onfocusout="defocused(this)">@json($value)</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach

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
