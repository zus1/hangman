@extends('base')
@section('body')
<body>
    <div class="container">
        <div class="auth-box">
            <h2 class="text-center mb-5">Login</h2>
            @include('components.errors')
            <form action="{{route(\App\Constant\RouteName::AUTH_LOGIN)}}" method="POST">
                {{csrf_field()}}
                <div class="row row-cols-1 mb-4 mt-1 justify-content-center">
                    <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" id="email" class="form-control" value="{{old('email')}}">
                    </div>
                </div>
                <div class="row row-cols-1 mb-3 justify-content-center">
                    <div class="col-12">
                        <label for="Password" class="form-label">Password</label>
                        <div class="input-group">
                            <input name="password" type="password" id="password" class="form-control">
                            <button id="password-button" name="password" type="button" class="btn btn-outline-info">
                                <i class="bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="row row-cols-2 mb-4 justify-content-center">
                    <div class="col-4">
                        <div class="form-check">
                            <label for="remember" class="form-check-label">Remember me</label>
                            <input name="remember_me" type="checkbox" id="remember" class="form-check-input">
                        </div>
                    </div>
                    <div class="col-4">
                        <a class="link-dark" target="_blank" href="{{route(\App\Constant\RouteName::AUTH_RESET_PASSWORD_SEND_FORM)}}">Forgot password?</a>
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
