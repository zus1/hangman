@extends('base')
@section('body')
<body>
    <div class="container">
        <div class="auth-box">
            <form method="POST" action="{{route(\App\Constant\RouteName::AUTH_RESET_PASSWORD)}}">
                @include('components.errors')
                {{csrf_field()}}
                <input type="hidden" name="token" id="token">
                <div class="row row-cols-1 justify-content-center mb-3">
                    <div class="col-8">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                </div>
                <div class="row row-cols-1 justify-content-center mb-4">
                    <div class="col-8">
                        <label class="form-label" for="confirm-password">Confirm password</label>
                        <input type="password" id="confirm-password" name="confirm_password" class="form-control">
                    </div>
                </div>
                <div class="row row-cols-1 justify-content-center mb-4">
                    <div class="col-4">
                        <input type="submit" class="btn btn-primary mx-5" value="Reset">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        window.onload = function () {
            const queryString = new URLSearchParams(window.location.search);
            document.getElementById('token').value = queryString.get('token');
        }
    </script>
</body>
@endsection
