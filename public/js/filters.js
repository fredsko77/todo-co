const handleFilters = (btn, e) => {

    e.preventDefault();

    let itemsPerPage = document.querySelector('#itemsPerPage').value;
    let sort = document.querySelector('#sort').value;
    let status = document.querySelector('#status').checked ? document.querySelector('#status').value : '';

    const data = {
        status,
        itemsPerPage,
        sort
    }

    let url = new URL(window.location.href);
    let searchParams = url.searchParams;

    for (const key in data) {
        if (
            data[key] === undefined ||
            data[key] === null ||
            data[key] === '' ||
            data[key] === 'default'
        ) {
            searchParams.delete(key);
            delete data[key];
        } else {
            searchParams.set(key, data[key]);
        }
    }

    return window.location = url.href;

}

const initiateFilters = () => {
    let url = new URL(window.location.href);
    let searchParams = new URLSearchParams(url.search);
    let queries = {
        status: searchParams.get('status'),
        itemsPerPage: searchParams.get('itemsPerPage'),
        sort: searchParams.get('sort')
    };
    for (const key in queries) {
        let el = document.querySelector(`[value="${queries[key]}"]`);
        if (el) {
            el.classList.contains('form-check-input') ? el.setAttribute('checked', 'checked') : el.setAttribute('selected', 'selected')
        }
    }
}

initiateFilters()