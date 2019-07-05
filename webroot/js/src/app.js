import 'shards-ui/dist/js/shards.min';
import 'bootstrap';
import './ajaxForm';
import 'garlicjs';
import './configurator';

// make jQuery available outside of modules
window.$ = $;

$(document).ready(function () {
    // storage value for empty selections
    const NONE = '[empty]';

    $('form.garlic-persist').garlic({
        conflictManager: {
            enabled: true,
            garlicPriority: true
        },
        prePersist: function ($element, value) {
            if (value === '') {
                return NONE;
            }

            return value;
        },
        preRetrieve: function ($element, currentValue, storedValue) {
            if (storedValue === NONE) {
                return '';
            }

            return storedValue;
        }
    });
});
