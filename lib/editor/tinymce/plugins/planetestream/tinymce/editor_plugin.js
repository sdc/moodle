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
/**
 * tinymce text editor integration version file.
 *
 * @package    tinymce_planetestream
 * @copyright  Planet Enterprises Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function () {
    tinymce.create('tinymce.plugins.PlaneteStreamPlugin', {
        init: function (ed, url) {
            if (ed.getParam('disabled', {}) === true) {
                return;
            }
            ed.addButton('planetestream', {
                title: 'Add Planet eStream item',
                cmd: 'mceplanetestream',
                image: ed.getParam('base_path', {})
+ '/lib/editor/tinymce/plugins/planetestream/pix/icon.png'
            });
            ed.addCommand('mceplanetestream', function () {
                if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') != -1) {
                    return;
                }
                ed.windowManager.open({
                    file: url + '/iframe.html?estream_url=' + ed.getParam('estream_url', {})
 + '&estream_path=' + encodeURIComponent(ed.getParam('estream_path', {})
 + '&estream_height=' + ed.getParam('estream_height', {})
 + '&estream_width=' + ed.getParam('estream_width', {})),
                    width: 1000 + parseInt(ed.getLang('advimage.delta_width', 0)),
                    height: 700 + parseInt(ed.getLang('advimage.delta_height', 0)),
                    inline: 1
                }, {
                    plugin_url: url
                });
            });
        }
    });
    // Register plugin.
    tinymce.PluginManager.add('planetestream', tinymce.plugins.PlaneteStreamPlugin);
})();
