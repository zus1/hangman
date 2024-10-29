import {alert} from './alert.js'

let resend = document.getElementById('resend');

if(resend !== null) {
    resend.addEventListener('click', resendEmail);
}

async function resendEmail()
{
    let promise = new Promise(function (resolve) {
        let req = new XMLHttpRequest();

        req.open('POST', route('auth_email_resend', {"identifier": identifier, "type": resendType}));
        req.setRequestHeader('Accept', 'application/json');

        req.onload = () => resolve({"response": req.response, "status": req.status});

        req.send();
    });

    let response = await promise;

    handlePromise(response);
}

function handlePromise(data)
{
    const response = JSON.parse(data["response"]);

    if(data['status'] !== 200) {
        console.log(response);

        return;
    }

    alert('Email sent', 'alert-info');
}

