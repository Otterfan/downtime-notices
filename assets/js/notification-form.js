// assets/js/greet.js


let start_picker = document.getElementById('notification_start'),
    start_date_picker = document.getElementById('notification_start_date'),
    start_time_picker = document.getElementById('notification_start_time'),
    today = start_picker.getAttribute('data-min-date'),
    right_now = start_picker.getAttribute('data-min-time'),
    end_date_picker = document.getElementById('notification_finish_date'),
    end_time_picker = document.getElementById('notification_finish_time'),
    form = document.getElementsByName('notification')[0];

form.addEventListener("submit", validate);

start_date_picker.setAttribute('min', today);

setMinValues();

start_date_picker.addEventListener('input', setMinValues);
end_date_picker.addEventListener('input', setMinValues);


function setMinValues() {
    let earliest_end_date = max(today, start_date_picker.value);
    end_date_picker.setAttribute('min', earliest_end_date);

    if (today === start_date_picker.value) {
        start_time_picker.setAttribute('min', right_now);
    }

    if (start_date_picker.value === end_date_picker.value) {
        end_time_picker.setAttribute('min', start_time_picker.value);
    }
}

function max(a, b) {
    return a > b ? a : b;
}

function validate(event) {
    if (!startTimeIsValid() || !endTimeIsValid()) {
        event.preventDefault();
    }
}

function startTimeIsValid() {
    if (start_date_picker.value > today) {
        return true;
    }

    if (start_date_picker.value < today) {
        return false;
    }

    return start_time_picker.value >= right_now;
}

function endTimeIsValid() {
    if (end_date_picker.value === '') {
        return true;
    }

    if (end_date_picker.value > start_date_picker.value) {
        return true;
    }

    if (end_date_picker.value < start_date_picker.value) {
        return false;
    }

    return end_time_picker.value >= start_time_picker.value;
}
