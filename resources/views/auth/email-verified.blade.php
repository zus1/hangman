@extends('base')
@section('body')
<body>
    @include('components.alert')
    <div class="container">
        <div class="auth-box">
            <div class="row row-cols-1 justify-content-center">
                <div class="col-12 justify-content-center">
                    @if(isset($success))
                        {{$success}}
                    @endif
                    @if(isset($error))
                        {{$error}}&nbsp;<span id="resend" class="link-danger cursor-pointer">Resend</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        const resendType = 'verify';

        const queryString = new URLSearchParams(window.location.search);
        const identifier = queryString.get('token');
    </script>
    @vite('resources/js/resend.js')
</body>
@endsection
