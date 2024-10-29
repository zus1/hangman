import * as helpers from './helpers.js'
import {getCookie} from "./cookie.js";

let messageSpan = document.getElementById('message');
let mistakesSpan = document.getElementById('mistakes');
let maxMistakesSpan = document.getElementById('max-mistakes');
let img = document.getElementById('hangman-image');
let lettersDiv = document.getElementById('letters');
let newGameBtn = document.getElementById('new-game-btn');
let letter = document.getElementById('guess-letter');
let word = document.getElementById('guess-word');
let languageSelect = document.getElementById('language-select');

newGameBtn.addEventListener('click', startNewGame);

async function startNewGame()
{
    let promise = new Promise(function (resolve) {
        let req = new XMLHttpRequest();
        req.open('POST', import.meta.env.VITE_APP_URL + '/api/hangmans?language=' + languageSelect.value);

        const cookie = getCookie('hangman_api_key');
        if(cookie !== null) {
            req.setRequestHeader('Authorization', cookie)
        }

        req.onload = function () {
            if (req.status === 200) {
                resolve(req.response)
            } else {
                resolve(req.status)
            }
        }

        req.send();
    })

    let data = await promise;

    if(helpers.isJson(data)) {
        onPromise(JSON.parse(data))
    } else {
        onPromise(data)
    }
}

function onPromise(data)
{
    if(typeof data === 'string') {
        console.log(data);

        return;
    }

    messageSpan.innerHTML = data.message;
    messageSpan.className = 'alert alert-info';
    mistakesSpan.innerHTML = 0;
    maxMistakesSpan.innerHTML = data.max_mistakes
    word.value = '';
    letter.value = '';

    img.src = data.image;
    localStorage.setItem('id', data.id);

    addLetterFields(data);
}

function addLetterFields(data)
{
    lettersDiv.innerHTML = '';
    for (let i = 0; i < data.word_length; i++) {
        if(Array.from(data.word_spaces).includes(i) !== false) {
            handleSpace();

            continue;
        }

        lettersDiv.innerHTML += "<span class='col-1 col-sm-1 col-lg-1 col-md-1 gy-2'><input id='"+ i
            +"' type='text' class='form-control' readonly></span>"
    }
}

function handleSpace()
{
    const spans = lettersDiv.getElementsByTagName('span');
    let lastItem = spans.item(spans.length -1)

    lettersDiv.innerHTML = lettersDiv.innerHTML.substring(0, lettersDiv.innerHTML.length - lastItem.outerHTML.length)

    lastItem.style = 'margin-right: 50px';

    lettersDiv.innerHTML += lastItem.outerHTML;
}

