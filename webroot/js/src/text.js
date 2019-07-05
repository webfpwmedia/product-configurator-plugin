/**
 *
 * @param {object} selections User key:value selections
 * @param {object} map Token/value map for svg options
 * @returns {Text}
 * @constructor
 */
const Text = function Text(selections, map) {
    let options = {};

    for (let token in selections) {
        const value = selections[token];
        if (!map.hasOwnProperty(`{{${token}}}`)) {
            continue;
        }

        const mapping = map[`{{${token}}}`];
        if (!mapping.hasOwnProperty(value)) {
            continue;
        }

        options = Object.assign(options, mapping[value]);
    }

    /**
     * Renders the text SVG
     *
     * @param {string} text
     * @returns {string}
     */
    this.render = function (text) {
        return `
            <svg viewBox="0 0 ${options.w || 0} ${options.h || 0}" xmlns="http://www.w3.org/2000/svg">
                <style>
                    .text {
                        fill: ${options.color || '#000'};
                        font: regular ${options.size || '12'}px sans-serif;
                    }
                </style>
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="text">${text}</text>
            </svg>
        `;
    };

    return this;
};

export default Text;
