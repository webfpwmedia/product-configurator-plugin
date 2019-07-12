/**
 * Creates a north-size resizer that resizes $element
 *
 * @param {jQuery} $element Element to resize
 * @param {string} resizerElementSelector Selector for "resizer" element
 * @param {function} onResize Optional callback to call on resize
 * @constructor
 */
const NSResizer = function ($element, resizerElementSelector, onResize) {
    const $resizeElement = $element.find(resizerElementSelector);
    const originalHeight = $element.height();
    let currentHeight = 0;
    let currentMouseY = 0;

    $resizeElement.css({
        cursor: 'ns-resize'
    });

    $resizeElement.on('mousedown', function (event) {
        currentHeight = $element.height();
        currentMouseY = event.pageY;

        $(document).on('mousemove', resize);
        $(document).on('mouseup', function (event) {
            $(document).off('mousemove', resize);
        });
    });

    function resize(event) {
        const delta = event.pageY - currentMouseY;
        const newHeight = currentHeight + delta;
        if (newHeight > originalHeight) {
            $element.height(newHeight);
        }

        if (typeof onResize === 'function') {
            onResize();
        }
    }
};

export default NSResizer;
