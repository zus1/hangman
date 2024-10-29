@extends('base')
@section('body')
<body>
    <div class="container">
        <div class="auth-box">
            <h2 class="text-center mb-5">Register</h2>
            @include('components.errors')
            <form action="{{route(\App\Constant\RouteName::AUTH_REGISTER)}}" method="POST">
                {{csrf_field()}}
                <div class="row row-cols-1 mb-4 mt-1 justify-content-center">
                    <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" id="email" class="form-control" value="{{old('email')}}">
                    </div>
                </div>
                <div class="row row-cols-1 mb-4 justify-content-center">
                    <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input name="password" type="password" id="password" class="form-control">
                            <button id="password-button" name="password" type="button" class="btn btn-outline-info">
                                <i class="bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 mb-4 justify-content-center">
                    <div class="col-12">
                        <label for="confirm-password" class="form-label">Confirm password</label>
                        <div class="input-group">
                            <input name="confirm_password" type="password" id="confirm-password" class="form-control">
                            <button id="confirm-password-button" name="confirm-password" type="button" class="btn btn-outline-info">
                                <i class="bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 mb-5 justify-content-center">
                    <div class="col-12">
                        <label for="nickname" class="form-label">Nickname</label>
                        <input type="text" name="nickname" id="nickname" class="form-control" value="{{old('nickname')}}">
                    </div>
                </div>
                <div class="row row-cols-1 mb-2 justify-content-center">
                    <div class="col-4">
                        <input type="submit" class="btn btn-primary form-control" value="Submit">
                    </div>
                </div>
            </form>
        </div>
    </div>
    @vite('resources/js/password.js')
</body>
@endsection
