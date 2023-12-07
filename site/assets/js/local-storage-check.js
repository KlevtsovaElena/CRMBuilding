console.log("Файл local-storage-check.js подключен");

let obj = {
    '1': window.location.href,
    '2': window.location.hostname,
    '3': window.location.origin,
    '4': window.location.pathname,
    '5': window.location.search
}

if(localStorage.getItem(window.location.pathname)) {
    alert('естғ клөч ' +  window.location.pathname);
    let p = JSON.parse(localStorage.getItem(window.location.pathname));
    if(window.location.search !== p.getParams) {
        alert('getParams не совпадают')
        alert(p.getParams + '      ' + window.location.search)
        // alert(window.location.origin + window.location.pathname + p.getParams)
        window.location.replace(window.location.origin + window.location.pathname + p.getParams)
    }
} else {
    alert('нет клөча '+  window.location.pathname) 
}

