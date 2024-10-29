<nav class="navbar navbar-expand-lg bg-primary-subtle">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{route(\App\Constant\RouteName::HANGMAN_START)}}">Hangman</a>
        <div class="collapse navbar-collapse" id="collapsable-navbar">
            <div class="navbar-nav">
                <a class="nav-link active" aria-current="page" href="{{route(\App\Constant\RouteName::HANGMAN_START)}}">Home</a>
                <a class="nav-link" href="{{route(\App\Constant\RouteName::TIK_START)}}">Tik Tak Toe</a>
                @if(\Illuminate\Support\Facades\Auth::user())
                    <a class="nav-link" href="{{route(\App\Constant\RouteName::ME_GAMES)}}">History</a>
                    <a class="nav-link" href="{{route(\App\Constant\RouteName::AUTH_LOGOUT)}}">Logout</a>
                @else
                    <a class="nav-link" href="{{route(\App\Constant\RouteName::AUTH_LOGIN_FORM)}}">Login</a>
                    <a class="nav-link" href="{{route(\App\Constant\RouteName::AUTH_REGISTER_FORM)}}">Register</a>
                @endif
            </div>
        </div>
    </div>
</nav>
