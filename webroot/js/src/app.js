import 'shards-ui/dist/js/shards.min';
import 'bootstrap';
import './ajaxForm';
import 'garlicjs';
import './configurator';

// make jQuery available outside of modules
window.$ = $;

$(document).ready(function () {
    $('form.garlic-persist').garlic({
        conflictManager: {
            enabled: true,
            garlicPriority: true
        }
    });
});
