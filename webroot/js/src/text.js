/**
 *
 * @param {number} width Original image width
 * @param {number} height Original image height
 * @param {object} options SVG and text options
 * @returns {Text}
 * @constructor
 */
const Text = function Text(width, height, options) {

    /**
     * Renders the text SVG
     *
     * @param {string} text
     * @returns {string}
     */
    this.render = function (text) {
        return `
            <svg viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
                <style>
                    .text {
                        fill: ${options.color || '#000'};
                        font: ${options.size || '12'}px sans-serif;
                    }
                </style>
                <text x="${options.x + options.w / 2}" y="${options.y + options.h / 2}" dominant-baseline="middle" text-anchor="middle" class="text">${text}</text>
            </svg>
        `;
    };

    return this;
};

export default Text;
