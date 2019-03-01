/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
const moment = require('moment');

import 'fullcalendar';

$('.homepage #calendar').fullCalendar({
    themeSystem: 'bootstrap4',
    defaultView: 'listWeek',
    visibleRange: {
        start: '2019-02-24',
        end: '2019-03-25'
    },
    eventRender: function(event, $el) {
        if (event.parsed_text) {
            $el.find(".fc-list-item-title a").html(event.parsed_text);
        }
    },
    noEventsMessage: 'No notifications scheduled',
    eventSources: [
        {url: 'feed'}
    ]
});