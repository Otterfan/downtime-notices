var $collectionHolder;

// setup an "add a tag" link
const $addTermButton = $('<button type="button" class="add_term_link">Add a term</button>');
const $newLinkLi = $('<div></div>').append($addTermButton);


jQuery(document).ready(function () {
    // Get the ul that holds the collection of terms
    $collectionHolder = $('#best_bet_terms');

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('li').each(function () {
        addTermFormDeleteLink($(this));
    });

    // add the "add a term" anchor and li to the terms ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTermButton.on('click', function (e) {
        // add a new tag form (see next code block)
        addTermForm($collectionHolder, $newLinkLi);
    });
});

function addTermForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    const prototype = $collectionHolder.data('prototype');

    // get the new index
    const index = $collectionHolder.data('index');

    let newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    let $newFormLi = $('<div></div>').append(newForm);
    $newLinkLi.before($newFormLi);

    // add a delete link to the new form
    addTermFormDeleteLink($newFormLi);
}

function addTermFormDeleteLink(termFormLi) {
    const $removeFormButton = $('<button type="button">Delete this term</button>');
    termFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
        // remove the li for the tag form
        termFormLi.remove();
    });
}
