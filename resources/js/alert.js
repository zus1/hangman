
export function alert(message, type)
{
    document.getElementById('alert').className = "visible";
    document.getElementById('alert-wrapper').className = "alert " + type;
    document.getElementById('alert-message').innerText = message;

    let dismiss = document.getElementById('alert-dismiss');
    dismiss.addEventListener('click', dismissAlert);
}

function dismissAlert()
{
    document.getElementById('alert').className = "invisible";
}
