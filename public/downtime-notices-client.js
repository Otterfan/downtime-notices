/*
Options:
    * function callback: a callback function to call instead of displaying standard notice
    * bool styles: true if you want to apply the built-in styles (default false)
    * string class: the class name to assign the notice div (default 'downtime-notification')
    * string url: the URL of the notifications API (default is production)
*/
const BCLibDowntimeNotices = function (options) {
    const apiUrl = 'https://arc.bc.edu/notices/active';
    const cssText = ' z-index: 101;\n' +
        'top: 0;\n' +
        'left: 0;\n' +
        'right: 0;\n' +
        'background: #fde073;\n' +
        'text-align: center;\n' +
        'overflow: hidden;\n' +
        'padding: 1em 2em;\n' +
        'box-shadow: 0 0 5px black;';
    const request = buildRequest();

    options = setOptions(options);
    request.send();

    function setOptions(options) {
        const opts = options || {};
        opts.url = options.hasOwnProperty('url') ? options.url : apiUrl;
        opts.callback = options.hasOwnProperty('callback') ? options.callback : display;
        opts.styles = options.hasOwnProperty('styles') ? options.styles : false;
        opts.class = options.hasOwnProperty('class') ? options.class : 'downtime-notification';
        return opts;
    }

    function buildRequest() {
        const request = new XMLHttpRequest();

        request.open('GET', options.url, true);
        request.onload = processRequest;

        // Ignore errors for now.
        request.onerror = function () {
        };

        return request;
    }

    function processRequest() {
        if (request.status >= 200 && request.status < 400) {
            let data = JSON.parse(request.responseText);
            options.callback(data);
        }
    }

    function display(data) {
        const text = data.notes.sort(compareNotes)[0].text;
        const note = document.createElement('div');

        note.setAttribute('class', options.class);
        note.style.cssText = options.styles ? cssText : '';
        note.innerHTML = text;

        document.body.insertBefore(note, document.body.childNodes[1]);
    }

    // Sort notes by highest priority, then most recent.
    function compareNotes(a, b) {
        if (a.priority < b.priority)
            return -1;
        if (a.priority > b.priority)
            return 1;
        if (a.start > b.start)
            return -1;
        if (a.start < b.start)
            return 1;
        return 0;
    }
};