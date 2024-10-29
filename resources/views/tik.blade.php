@extends('base')
@section('body')
    <div class="container container-main text-center">
        <div class="row row-cols-2 mb-5 justify-content-center">
            <div class="col-3">
                <div id="turn">Waiting for new game</div>
            </div>
            <div class="col-3">
                <div id="message"></div>
            </div>
        </div>
        <div id="grid">
            @for($i = 1; $i <= 9; $i = $i + 3)
                <div class="row py-1 justify-content-center">
                    <div class="col-1 mx-1">
                        <div id="{{$i}}"  class="tik-box" role="gridcell"></div>
                    </div>
                    <div class="col-1 mx-1">
                        <div id="{{$i + 1}}"  class="tik-box" role="gridcell"></div>
                    </div>
                    <div class="col-1 mx-1">
                        <div id="{{$i + 2}}"  class="tik-box" role="gridcell"></div>
                    </div>
                </div>
            @endfor
        </div>
        <div class="row row-cols-1 mt-5 justify-content-center">
            <div class="col-3">
                <div class="input-group">
                    <button id="start" class="btn btn-lg btn-primary">New game</button>
                    <select id="fields-num-select" class="form-select">
                        <option value="" disabled selected>Fields</option>
                        @foreach($possibleGrids as $possibleGrid)
                            <option value="{{$possibleGrid->fields_num}}">{{$possibleGrid->fields_num}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/tik.js')
@endsection
