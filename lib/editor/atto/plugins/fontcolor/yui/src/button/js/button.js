// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    atto_fontcolor
 * @copyright  2014 Rossiani Wijaya  <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_align-button
 */

/**
 * Atto text editor fontcolor plugin.
 *
 * @namespace M.atto_fontcolor
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

/*** PAPLUS: provide accessible text colors on white background. */
var colors = [
        {
            name: 'red',
            color: '#DB383E'
        }, {
            name: 'yellow',
            color: '#826C27'
        }, {
            name: 'green',
            color: '#008900'
        }, {
            name: 'blue',
            color: '#4545CA'
        }, {
            name: 'black',
            color: '#222222'
        }
    ];

Y.namespace('M.atto_fontcolor').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var items = [];
        Y.Array.each(colors, function(color) {
            var colorLabel = M.util.get_string('color_' + color.name, 'atto_fontcolor');
            items.push({
                text: '<div class="coloroption" style="background-color: '
                        + color.color + '" aria-label="' + colorLabel + '" title="' + colorLabel + '"></div>',
                callbackArgs: color.color,
                callback: this._changeStyle
            });
        });

        this.addToolbarMenu({
            icon: 'e/text_color',
            overlayWidth: '4',
            menuColor: '#333333',
            globalItemConfig: {
                inlineFormat: true,
                callback: this._changeStyle
            },
            items: items
        });
    },

    /**
     * Change the font color to the specified color.
     *
     * @method _changeStyle
     * @param {EventFacade} e
     * @param {string} color The new font color
     * @private
     */
    _changeStyle: function(e, color) {
        this.get('host').formatSelectionInlineStyle({
            color: color
        });
    }
});
