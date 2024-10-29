
let passwordBtn = document.getElementById('password-button');
let confirmPasswordBtn = document.getElementById('confirm-password-button');

if(passwordBtn !== null) {
    passwordBtn.addEventListener('click', showHide);
}
if(confirmPasswordBtn !== null) {
    confirmPasswordBtn.addEventListener('click', showHide);

}

function showHide(event)
{
    let target = event.target.nodeName === 'BUTTON' ? event.target : event.target.parentElement;
    let passwordField = document.getElementById(target.name);

    if(passwordField.type === 'password') {
        passwordField.type = 'text';

        showHideBtn(target, 'bi-eye-slash');
    } else {
        passwordField.type = 'password';

        showHideBtn(target, 'bi-eye');
    }

}

function showHideBtn(target, className)
{
    let i = document.createElement('i');
    i.className = className;

    target.innerHTML = '';
    target.appendChild(i);

}
