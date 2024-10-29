@extends('base')
@section('body')
    <body>
        <div class="container container-main">
            <div class="row row-cols-1 justify-content-center mb-5">
                <div class="col-8">
                    <h2 class="text-center">History</h2>
                </div>
            </div>
            <div class="row row-cols-2 justify-content-center mb-3">
                <div class="col-4">
                    <button class="btn btn-primary" data-bs-toggle="collapse" href="#filters" aria-expanded="false" aria-controls="filters">Filters</button>
                </div>
                <div class="col-4">
                    <select id="order-by" class="form-select">
                        <option value="created_at;desc" selected>Newest first</option>
                        <option value="created_at;asc">Oldest first</option>
                        <option value="created_at;desc">Newest first</option>
                        <option value="mistakes;desc">Most mistakes</option>
                        <option value="mistakes;asc">Leas mistakes</option>
                    </select>
                </div>
            </div>
            <div class="row row-cols-1 justify-content-center mb-3">
                <div class="col-12">
                    <div class="collapse" id="filters">
                        <div class="card card-body bg-info-subtle">
                            <div class="row row-cols-4 justify-content-center">
                                <div class="col-3">
                                    <div class="card-title mb-2 text-center">Mistakes</div>
                                    <div class="row row-cols-2">
                                        <div class="col-6">
                                            <input type="number" name="from:mistakes" min="0" id="mistakes-filter-from" class="form-control" placeholder="from">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="to:mistakes" min="0" id="mistakes-filter-to" class="form-control" placeholder="to">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 mx-3">
                                    <div class="card-title text-center">Started at</div>
                                    <div class="row row-cols-2">
                                        <div class="col-6">
                                            <input type="datetime-local" name="from:created_at" id="date-filter-from" class="form-control" placeholder="from">
                                        </div>
                                        <div class="col-6">
                                            <input type="datetime-local" name="to:created_at" id="date-filter-to" class="form-control" placeholder="to">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 mx-3">
                                    <div class="card-title text-center">Result</div>
                                    <select id="result-filter" class="form-select" name="result">
                                        <option value="" selected></option>
                                        @foreach($filterResult as $filter)
                                            <option value="{{$filter}}">{{$filter}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 mx-3" style="padding-top: 30px">
                                    <button class="btn btn-success" id="apply-filters">Apply</button>
                                    <button class="btn btn-danger" id="reset-filters">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 justify-content-center mb-4">
                <div class="col-12">
                    <table class="table table-striped table-hover table-info">
                        <thead>
                            <tr>
                                <td>Id</td>
                                <td>Word</td>
                                <td>Started At</td>
                                <td>Mistakes</td>
                                <td>Finished</td>
                                <td>Language</td>
                                <td>Result</td>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($games as $game)
                                <tr>
                                    <td>{{$game->id}}</td>
                                    <td>{{$game->word}}</td>
                                    <td>{{$game->created_at}}</td>
                                    <td>{{$game->mistakes}}</td>
                                    <td>{{$game->is_finished}}</td>
                                    <td>{{$game->language}}</td>
                                    <td>{{$game->result}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row row-cols-1 justify-content-center">
                <div class="col-8">
                    {{$games->links()}}
                </div>
            </div>
        </div>
        <script>
            const filtersRoute = route('me_games');
        </script>
        @vite('resources/js/filters.js')
    </body>
@endsection
