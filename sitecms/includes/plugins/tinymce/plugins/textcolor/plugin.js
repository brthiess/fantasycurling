/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('textcolor', function(editor) {
	function mapColors() {
		var i, colors = [], colorMap;

		colorMap = editor.settings.textcolor_map || [
			"111111", "Very dark gray",
			"333333", "Dark gray",
			"666666", "Gray",
			"999999", "Light Gray",
			"CCCCCC", "Very light Gray",
			"FFFFFF", "White",
			"F79022", "Orange",
			"973434", "Brick red"
		];

		for (i = 0; i < colorMap.length; i += 2) {
			colors.push({
				text: colorMap[i + 1],
				color: colorMap[i]
			});
		}

		return colors;
	}

	function renderColorPicker() {
		var ctrl = this, colors, color, html, last, rows, cols, x, y, i;

		colors = mapColors();

		html = '<table class="mce-grid mce-grid-border mce-colorbutton-grid" role="presentation" cellspacing="0"><tbody>';
		last = colors.length - 1;
		rows = editor.settings.textcolor_rows || 5;
		cols = editor.settings.textcolor_cols || 8;

		for (y = 0; y < rows; y++) {
			html += '<tr>';

			for (x = 0; x < cols; x++) {
				i = y * cols + x;

				if (i > last) {
					html += '<td></td>';
				} else {
					color = colors[i];
					html += (
						'<td>' +
							'<div id="' + ctrl._id + '-' + i + '"' +
								' data-mce-color="' + color.color + '"' +
								' role="option"' +
								' tabIndex="-1"' +
								' style="' + (color ? 'background-color: #' + color.color : '') + '"' +
								' title="' + color.text + '">' +
							'</div>' +
						'</td>'
					);
				}
			}

			html += '</tr>';
		}

		html += '</tbody></table>';

		return html;
	}

	function onPanelClick(e) {
		var buttonCtrl = this.parent(), value;

		if ((value = e.target.getAttribute('data-mce-color'))) {
			buttonCtrl.hidePanel();
			value = '#' + value;
			buttonCtrl.color(value);
			editor.execCommand(buttonCtrl.settings.selectcmd, false, value);
		}
	}

	function onButtonClick() {
		var self = this;

		if (self._color) {
			editor.execCommand(self.settings.selectcmd, false, self._color);
		}
	}

	editor.addButton('forecolor', {
		type: 'colorbutton',
		tooltip: 'Text color',
		selectcmd: 'ForeColor',
		panel: {
			html: renderColorPicker,
			onclick: onPanelClick
		},
		onclick: onButtonClick
	});

	editor.addButton('backcolor', {
		type: 'colorbutton',
		tooltip: 'Background color',
		selectcmd: 'HiliteColor',
		panel: {
			html: renderColorPicker,
			onclick: onPanelClick
		},
		onclick: onButtonClick
	});
});
