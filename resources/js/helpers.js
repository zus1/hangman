
export function isJson(toCheck)
{
    try {
        JSON.parse(toCheck)
    } catch(e) {
        return false;
    }

    return true;
}
