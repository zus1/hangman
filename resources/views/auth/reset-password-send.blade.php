@extends('base')
@section('body')
<body>
    @include('components.alert')
    <div class="container">
        <div class="auth-box">
            <form method="POST" action="{{route(\App\Constant\RouteName::AUTH_RESET_PASSWORD_SEND)}}">
                @include('components.errors')
                @if(session('message'))
                    <div class="row row-cols-1 justify-content-center mb-5">
                        <div class="col-8">
                            {{session('message')}}
                        </div>
                    </div>
                    <hr>
                    <div class="row row-cols-1 justify-content-center">
                        <div class="col-8">
                            <div class="mx-5">
                                Did not receive email? &nbsp; <span class="cursor-pointer link-dark" id="resend">Resend</span>
                            </div>
                        </div>
                    </div>
                    <input id="old-email" type="hidden" value="{{old('email')}}">
                @else
                    {{csrf_field()}}
                    <div class="row row-cols-1 justify-content-center mb-5">
                        <div class="col-8">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                    </div>
                    <div class="row row-cols-1 mb-2 justify-content-center">
                        <div class="col-4">
                            <input type="submit" class="btn btn-primary mx-5" value="Send">
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
    <script>
        const resendType = 'reset_password';
        const identifier = document.getElementById('old-email').value;
    </script>
    @vite('resources/js/resend.js')
</body>
@endsection
