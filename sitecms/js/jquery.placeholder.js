/************** jQuery Placeholder
Set placeholder text on any input or textarea using an abstract
method that is compatible across all browsers and is not
HTML5 dependent.

This software is written by William Saleme with the incorporation
of F., Wong's jQuery Caret plugin (All rights reserved). This
software is licensed and released under the MIT License.

Copyright ©2013 William Saleme

License Terms:
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************/

(function ($,len,createRange,duplicate) {

	// Moves the caret inside the input or textarea to a specified selection
	$.fn.caret=function(options,opt2){
		var start,end,t=this[0],browser=$.browser.msie;
		if(typeof options==="object" && typeof options.start==="number" && typeof options.end==="number") {
			start=options.start;
			end=options.end;
		} else if(typeof options==="number" && typeof opt2==="number"){
			start=options;
			end=opt2;
		} else if(typeof options==="string"){
			if((start=t.value.indexOf(options))>-1) end=start+options[len];
			else start=null;
		} else if(Object.prototype.toString.call(options)==="[object RegExp]"){
			var re=options.exec(t.value);
			if(re != null) {
				start=re.index;
				end=start+re[0][len];
			}
		}
		if(typeof start!="undefined"){
			if(browser){
				var selRange = this[0].createTextRange();
				selRange.collapse(true);
				selRange.moveStart('character', start);
				selRange.moveEnd('character', end-start);
				selRange.select();
			} else {
				this[0].selectionStart=start;
				this[0].selectionEnd=end;
			}
			this[0].focus();
			return this
		} else {
			// Modification as suggested by Андрей Юткин
           if(browser){
				var selection=document.selection;
                if (this[0].tagName.toLowerCase() != "textarea") {
                    var val = this.val(),
                    range = selection[createRange]()[duplicate]();
                    range.moveEnd("character", val[len]);
                    var s = (range.text == "" ? val[len]:val.lastIndexOf(range.text));
                    range = selection[createRange]()[duplicate]();
                    range.moveStart("character", -val[len]);
                    var e = range.text[len];
                } else {
                    var range = selection[createRange](),
                    stored_range = range[duplicate]();
                    stored_range.moveToElementText(this[0]);
                    stored_range.setEndPoint('EndToEnd', range);
                    var s = stored_range.text[len] - range.text[len],
                    e = s + range.text[len]
                }
			// End of Modification
            } else {
				var s=t.selectionStart,
					e=t.selectionEnd;
			}
			var te=t.value.substring(s,e);
			return {start:s,end:e,text:te,replace:function(st){
				return t.value.substring(0,s)+st+t.value.substring(e,t.value[len])
			}}
		}
	}

	// Makes the text unselectable within the input/textarea
	$.fn.disableSelection = function() {
		return this.each(function() {
			$(this).css({
				'MozUserSelect':'none',
				'webkitUserSelect':'none'
			}).attr('unselectable','on').bind('selectstart', function() {
				return false;
			});
		});
	};

	// Makes the text selectable within the input/textarea
	$.fn.enableSelection = function() {
		return this.each(function() {
			$(this).css({
				'MozUserSelect':'',
				'webkitUserSelect':''
			}).attr('unselectable','off').unbind('selectstart');
		});
	};

	// Check availability for native placeholder
	$.supportPlaceholder = function() {
	    var val = false;
	    var input = document.createElement('input');
	    if('placeholder' in input) {
	        val = true;
	    }
	    delete input;
	    return val;
	};

	// The Initializing method
	$.fn.placeholder = function() {
		return this.each(function() {
			// Work on the ones that have the placeholder Attribute and only if the native placeholder is not supported
			if($(this).attr('placeholder') != undefined && !$.supportPlaceholder()) {

				// Store this input/textarea in a variable for later use
				var placeholder = this;

				// Store the original text and colors in the data object
				$(placeholder).data('placeholder',{
					'text': $(placeholder).attr('placeholder'),
					'color': {
						'off': ($(placeholder).attr('placeholdercolor') == undefined ? '#cccccc' : $(placeholder).attr('placeholdercolor')),
						'on': $(placeholder).css('color')
					},
					'active': false
				});

				// Attach Object Methods
				$(placeholder).on('updateColor', function() {
					if(!$(placeholder).data('placeholder').active) {
						$(placeholder).css('color',$(placeholder).data('placeholder').color.off);
					} else {
						$(placeholder).css('color',$(placeholder).data('placeholder').color.on);
					}
				});

				// Attach the Event Handlers
				$(placeholder).on('focus', function() {
					if(!$(placeholder).data('placeholder').active) {
						$(placeholder).caret({start: 0, end: 0});
						$(placeholder).disableSelection();
					}
				});

				$(placeholder).on('keydown', function() {
					if(!$(placeholder).data('placeholder').active) {
						$(placeholder).enableSelection();
						$(placeholder).val('');
						$(placeholder).data('placeholder').active = true;
						$(placeholder).trigger('updateColor');
					}
				});

				$(placeholder).on('blur', function() {
					if($(placeholder).val() == '') {
						$(placeholder).val($(placeholder).data('placeholder').text);
						$(placeholder).data('placeholder').active = false;
						$(placeholder).trigger('updateColor');
					}
				});

				$(placeholder).closest('form').on('submit', function() {
					if(!$(placeholder).data('placeholder').active) {
						$(placeholder).val('');
					}
				});

				// Initialize the Element
				if($(placeholder).val() == '') {
					$(placeholder).val($(placeholder).data('placeholder').text);
					$(placeholder).trigger('updateColor');
				}
			}
		});
	};
})(jQuery,"length","createRange","duplicate");