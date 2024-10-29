import {getCookie} from './cookie.js'

let newGameBtn = document.getElementById('start');
let grid = document.getElementById('grid');
let gridElements = grid.getElementsByTagName('div');
let message = document.getElementById('message');

newGameBtn.addEventListener('click', start);

function gridListeners(action)
{
    let cells = Array.from(gridElements).filter((element) => element.role === "gridcell");

    for (let element in cells) {
        if(action === 'load') {
            cells[element].addEventListener('click', play);
        }
        if(action === 'remove') {
            cells[element].removeEventListener('click', play);
        }
    }
}

function start()
{
    const fieldsNum = document.getElementById('fields-num-select').value;
    const params = fieldsNum !== '' ? {"fields_num": fieldsNum} : {};

    const headers = [];
    const api_key = getCookie('hangman_api_key');
    if(api_key !== null) {
        headers['Authorization'] = api_key;
    }

    call('POST', 'tik_create', params, headers).then(data => handleStartPromise(data));
}

function play(event=null)
{
    const player = localStorage.getItem('player');

    const query = {"tik": localStorage.getItem('tik_id')};

    let body = {"player": player};
    if((event !== null ? event.target.id : null) !== null) {
        body['position'] = event.target.id ;
    }

    call('PUT', 'tik_play', query, [], body).then(data => handlePlayPromise(data, player));
}

async function call(method, targetRoute, query=[], headers=[], body=[])
{
    let promise = new Promise(function (resolve) {
        const req = new XMLHttpRequest();
        req.open(method, route(targetRoute, query));

        req.setRequestHeader('Accept', 'application/json');
        if(body !== []) {
            req.setRequestHeader('Content-Type', 'application/json');
        }
        for (const [header, value] in headers) {
            req.setRequestHeader(header, value);
        }

        req.onload = function () {
            resolve({"response": req.response, "status": req.status})
        }

        if(body !== []) {
            req.send(JSON.stringify(body));
        } else {
            req.send();
        }
    })

    return await promise;
}

function handleStartPromise(data)
{
    const status = data.status;

    if(status !== 200) {
        console.log(data.response)

        return;
    }

    const response = JSON.parse(data.response);
    localStorage.setItem('tik_id', response.id);
    createGrid(response);
    gridListeners('load');
    removeResult(); //removes previous result
    handlePlayer(response);
}

function handlePlayer(response)
{
    localStorage.setItem('player', response.starts === 1 ? 'player': 'opponent');

    setTurn(response.starts === 1 ? 'Your turn' : 'Opponents turn');

    if(response.starts === 0) {
        play();
    }
}

function createGrid(response)
{
    grid.innerHTML = '';

    const totalRows = response.fields_num/response.streak_length
    for(let i = 0; i < totalRows; i++) {
        let row = createRow();

        appendColumns(row, response, i);

        grid.appendChild(row);
    }
}

function createRow()
{
    let row = document.createElement('div');
    row.setAttribute('class', 'row py-1 justify-content-center');

    return row;
}

function appendColumns(row, response, offset)
{
    for(let i = response.streak_length * offset; i < response.streak_length * offset + response.streak_length; i++) {

        let column = document.createElement('div');
        column.setAttribute('class', 'col-1 mx-1');

        appendTikBox(column, i+ 1);
        row.appendChild(column);
    }
}

function appendTikBox(column, id)
{
    let tikBox = document.createElement('div');
    tikBox.setAttribute('class', 'tik-box');
    tikBox.setAttribute('role', 'gridcell');
    tikBox.setAttribute('id', id);

    column.appendChild(tikBox);
}



function handlePlayPromise(data, player, cellId=null)
{
    if(data.status !== 200) {
        console.log(data.response);

        return;
    }

    const response = JSON.parse(data.response);

    reconstructGrid(response, player, cellId);

    if(finishIfPossible(response, player) === true) {
        return;
    }

    if(player === 'player') {
        localStorage.setItem('player', 'opponent');

        setTurn('Opponents turn');

        gridListeners('remove');

        setTimeout(play, 3000);
    }

    if(player === 'opponent') {
        localStorage.setItem('player', 'player');

        gridListeners('load');

        setTurn('Your turn');
    }
}

function finishIfPossible(response)
{
    if(response.is_finished === false) {
        return false;
    }

    setResult(response.result);

    gridListeners('remove');
    setTurn('Waiting for new game');
    localStorage.setItem('player', 'none');

    return true;
}

function setResult(result)
{
    message.innerText = result;
    if(result === 'victory') {
        message.setAttribute('class', 'alert alert-success');
    }
    if(result === 'defeat') {
        message.setAttribute('class', 'alert alert-danger');
    }
    if(result === 'tie') {
        message.setAttribute('class', 'alert alert-info');
    }
}

function removeResult()
{
    message.innerText = '';
    message.removeAttribute('class');
}

function reconstructGrid(response, player, cellId)
{
    if(cellId !== null) {
        document.getElementById(cellId).innerHTML = response.starts === '1' ? 'X' : 'O';

        return;
    }

    for(let element in response.grid) {
        if(response.grid[element] === null) {
            continue;
        }

        document.getElementById(element).innerHTML = response.grid[element] === 1 ? 'X' : 'O';
    }
}

function setTurn(message)
{
    document.getElementById('turn').innerText = message;
}
