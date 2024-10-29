import * as helpers from './helpers.js'

let letterCheckBtn = document.getElementById('letter-check-btn');
let wordCheckBtn = document.getElementById('word-check-btn');
let messageSpan = document.getElementById('message');
let mistakesSpan = document.getElementById('mistakes');
let img = document.getElementById('hangman-image');
let letter = document.getElementById('guess-letter');
let word = document.getElementById('guess-word');

letterCheckBtn.addEventListener('click', checkLetter);
wordCheckBtn.addEventListener('click', checkWord);
letter.addEventListener('keyup', validateLetter);
word.addEventListener('keyup', validateWord)

function checkLetter() {
    check(letter.value).then((data, statusCode) => handlePromise(data, statusCode))
}

function checkWord() {
    check(null, word.value).then((data, statusCode) => handlePromise(data, statusCode))
}

function handlePromise(promise)
{
    const data = promise.response;
    const status = promise.status

    if(helpers.isJson(data) === false || status !== 200) {
        console.log(data);

        return;
    }

    let response = JSON.parse(data);

    img.src = response.image;
    handleMessage(response.mistakes, response.message);
    mistakesSpan.innerHTML = response.mistakes;

    for(let guess in response.guesses) {
        if(response.guesses[guess] === ' ') {
            continue;
        }
        document.getElementById(guess).value = response.guesses[guess];
    }
}

function handleMessage(mistakes, message)
{
    if(Number(mistakesSpan.innerHTML) === mistakes) {
        messageSpan.className = 'alert alert-success';
    } else {
        messageSpan.className = 'alert alert-danger';
    }

    messageSpan.innerHTML = message
}

async function check(letter = null, word = null)
{
    let promise = new Promise(function (resolve) {
        let req = new XMLHttpRequest();
        req.open('PUT', route('hangman_play', {hangman: localStorage.getItem('id')}));
        req.setRequestHeader('Content-Type', 'application/json');
        req.setRequestHeader('Accept', 'application/json');
        req.onload = function () {
            console.log(req.response);
            resolve({"response": req.response, "status": req.status});
        }
        req.send(JSON.stringify({
            'letter': letter,
            'word': word,
        }));
    });

    return await promise;
}

function validateLetter(event)
{
    let validateLetterDiv = document.getElementById('validate-letter');

    if(event.target.value.length !== 1) {
        event.target.className = 'form-control is-invalid';
        validateLetterDiv.innerText = 'Only one letter allowed';
        letterCheckBtn.disabled = true;
    } else {
        event.target.className = 'form-control';
        validateLetterDiv.innerText = '';
        letterCheckBtn.disabled = false;
    }
}

function validateWord(event)
{
    let validateDiv = document.getElementById('validate-word');

    if(event.target.value.length === 0) {
        event.target.className = 'form-control is-invalid';
        validateDiv.innerText = 'Required';
        wordCheckBtn.disabled = true;
    } else {
        event.target.className = 'form-control';
        validateDiv.innerText = '';
        wordCheckBtn.disabled = false;
    }
}
