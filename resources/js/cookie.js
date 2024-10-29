
export function getCookie(name)
{
    let cookies = decodeURIComponent(document.cookie);
    let cookiesArr = cookies.split(';');

    for(const index in cookiesArr) {
        let cookieArr = cookiesArr[index].trim().split('=');

        if(cookieArr[0] === name) {
            return cookieArr[1];
        }
    }

    return null;
}
