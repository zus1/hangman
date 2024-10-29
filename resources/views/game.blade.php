@php use Illuminate\Support\Facades\URL;@endphp
@extends('base')
@section('body')
<body>
    <div class="container text-center justify-content-center container-main">
        <div class="row row-cols-1 justify-content-end">
            <div class="col-2">
                <select id="language-select" class="form-select">
                    @foreach($languages as $language)
                        <option value="{{$language->short}}">{{$language->short}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row row-cols-2 justify-content-center mt-3">
            <div class="col-1 alert alert-danger">
                <span id="mistakes" class="mistakes">0</span>/<span id="max-mistakes">0</span>
            </div>
            <div class="col-5 align-content-center" style="height: 55px">
                <div id="message" role="alert"></div>
            </div>
        </div>
        <div class="row justify-content-center align-items-center mt-3">
            <div class="col-6">
                <img id="hangman-image" src="{{URL::to('/images/hangman/0.jpg')}}" alt="Hangman stages">
            </div>
        </div>
        <div class="row mt-5">
            <div id="letters" class="row justify-content-center">
                @for($i = 0; $i < 10; $i++)
                    <span class="col-1 col-sm-1 col-lg-1 col-md-1 gy-2">
                            <input type="text" class="form-control" readonly>
                        </span>
                @endfor
            </div>
        </div>

        <div class="row row-cols-1 justify-content-center align-items-end mt-5">
            <div class="col-3">
                <div id="validate-letter" style="height: 10px; color: red"></div>
            </div>
        </div>
        <div class="row row-cols-1 justify-content-center mt-3">
            <div class="col-2">
                <div class="input-group">
                    <input id="guess-letter" type="text" class="form-control" placeholder="Guess letter">
                    <button id="letter-check-btn" class="btn btn-sm btn-success" disabled>Check</button>
                </div>
            </div>
        </div>
        <div class="row row-cols-1 justify-content-center align-items-end mt-3">
            <div class="col-3">
                <div id="validate-word" style="height: 10px; color: red"></div>
            </div>
        </div>
        <div class="row justify-content-center mt-3">
            <div class="col-4">
                <div class="input-group">
                    <input id="guess-word" type="text" class="form-control" placeholder="Guess word">
                    <button id="word-check-btn" class="btn btn-sm btn-success" disabled>Guess</button>
                </div>
            </div>
        </div>
        <div class="row row-cols-1 justify-content-center mt-5">
            <div class="col-2">
                <button id="new-game-btn" class="btn btn-lg btn-primary">New Game</button>
            </div>
        </div>
    </div>
    @vite('resources/js/start.js')
    @vite('resources/js/check.js')
</body>
@endsection
