import {isJson} from "./helpers.js";

let languageSelect = document.getElementById('language-select');
languageSelect.addEventListener('change', loadTranslations);

async function loadTranslations(event)
{
    const lang = event.target.value;

    let promise = new Promise(function (resolve) {
        let req = new XMLHttpRequest();
        req.open('GET', route('language') + "?language=" + lang);
        req.setRequestHeader('Accept', 'application/json');
        req.onload = function () {
            console.log(req.response);
            resolve(req.response)
        }
        req.send();
    })

    let response = await promise;

    onPromise(response);
}

function onPromise(response)
{
    if(isJson(response) === true) {
        const responseObj = JSON.parse(response)

        document.getElementById('title').innerText = responseObj.title;
        document.getElementById('guess-letter').placeholder = responseObj.letterPlaceholder;
        document.getElementById('guess-word').placeholder = responseObj.wordPlaceholder;
        document.getElementById('letter-check-btn').innerText = responseObj.checkButton;
        document.getElementById('word-check-btn').innerText = responseObj.guessButton;
        document.getElementById('new-game-btn').innerText = responseObj.newGameButton;

        return;
    }

    console.log(response);
}
