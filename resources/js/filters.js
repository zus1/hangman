
let applyBtn = document.getElementById('apply-filters');
let resetBtn = document.getElementById('reset-filters');
let orderBySelect = document.getElementById('order-by');
let filtersDiv = document.getElementById('filters');

let filters = Array.from(filtersDiv.getElementsByTagName('input'))
    .concat(Array.from(filtersDiv.getElementsByTagName('select')));

window.addEventListener('load', onLoad);
applyBtn.addEventListener('click', applyFilters);
resetBtn.addEventListener('click', resetFilters);
orderBySelect.addEventListener('change', applyOrderBy);

function onLoad()
{
    let queryString = new URLSearchParams(window.location.search);

    for(let key in filters) {
        if(queryString.has(filters[key].name)) {
            filters[key].value = queryString.get(filters[key].name);
        }
    }

    if(queryString.has('order_by')) {
        orderBySelect.value = queryString.get('order_by') + ';' + queryString.get('order_direction');
    }
}

function applyFilters()
{
    let currentQueryString = new URLSearchParams(window.location.search);

    for(let key in filters) {
        if(filters[key].value !== '') {
            currentQueryString.set(filters[key].name, filters[key].value);
        }
    }

    window.location.href = filtersRoute + '?' + currentQueryString.toString();
}

function resetFilters()
{
    let currentQueryString = new URLSearchParams(window.location.search);

    for (let key in filters) {
        filters[key].value = '';

        currentQueryString.delete(filters[key].name);
    }

    window.location.search = currentQueryString.toString();
}

function applyOrderBy(event)
{
    let currentQueryString = new URLSearchParams(window.location.search);
    const [by, direction] = event.target.value.split(';');

    currentQueryString.set('order_by', by);
    currentQueryString.set('order_direction', direction)

    window.location.href = filtersRoute + '?' + currentQueryString.toString();
}
