import 'shards-ui/dist/js/shards.min';
import '../../shards/js/shards-dashboards.1.1.0';
import 'bootstrap';
import './ajaxForm';
import './configurator';
import JSONEditor from 'jsoneditor';
import NSResizer from "./nsresizer";

// make jQuery available outside of modules
window.$ = $;

$(document).ready(function () {
    $('.json-editor').each(function () {
        const $this = $(this);
        const $submit = $this.closest('form').find(':submit');
        const editorId = $this.prop('id') + '-editor';
        const $editorContainer = $('<div></div>')
            .prop('id', editorId)
            .css({
                width: '100%',
                height: $this.height(),
            });

        $this.parent().append($editorContainer);
        $this.hide();

        const editor = new JSONEditor($editorContainer[0], {
            mainMenuBar: false,
            mode: 'code',
            onChange: function () {
                try {
                    $this.val(JSON.stringify(editor.get()));
                    $submit.prop('disabled', false);
                } catch (Error) {
                    $submit.prop('disabled', true);
                }
            }
        }, JSON.parse($this.val()));

        new NSResizer($editorContainer, '.jsoneditor-statusbar', function () {
            // hack to trigger editor refresh, editor.refresh() doesn't work
            window.dispatchEvent(new Event('resize'));
        });
    });
});
