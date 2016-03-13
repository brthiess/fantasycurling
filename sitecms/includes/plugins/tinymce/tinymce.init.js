var basePath = "/*Archive*/PACMS/V3.0";
var cmsRoot = "/sitecms/";
tinymceInitDefault("textarea.tinymceEditor");

//Default
function tinymceInitDefault(tinymceSelector){
	
	tinymce.init({
		selector: tinymceSelector, 
		theme: "modern", 
		height: 400,
		content_css: basePath+'/css/typography.css',
		
		textcolor_map: [
			"111111", "Very dark gray",
			"333333", "Dark gray",
			"666666", "Gray",
			"999999", "Light Gray",
			"CCCCCC", "Very light Gray",
			"FFFFFF", "White",
			"C70000", "Red"
		],
	
		style_formats:[
		{
			title: "Headers",
			items: [
				/*{title: "Header 1",format: "h1"},*/
				{title: "Header 2", format: "h2"},
				{title: "Header 3", format: "h3"},
				{title: "Header 4", format: "h4"},
				{title: "Header 5", format: "h5"},
				{title: "Header 6", format: "h6"}
			]
		},
		{
			title: "Inline",
			items: [ 
				{title: "Bold", icon: "bold", format: "bold"}, 
				{title: "Italic", icon: "italic", format: "italic"}, 
				{title: "_Underline", icon: "underline", format: "underline"}, 
				{title: "Strikethrough", icon: "strikethrough", format: "strikethrough"}, 
				{title: "Superscript", icon: "superscript", format: "superscript"}, 
				{title: "Subscript", icon: "subscript", format: "subscript"}
			]
		}, 
		{
			title: "_Blocks",
			items: [
				{title: "Paragraph", format: "p"}, 
				{title: "Blockquote", format: "blockquote"}, 
				{title: "Div", format: "div"}, 
				{title: "Pre", format: "pre"}
			]
		}, 
		{
			title: "_Alignment",
			items: [
				{title: "Left", icon: "alignleft", format: "alignleft"}, 
				{title: "Center", icon: "aligncenter", format: "aligncenter"}, 
				{title: "Right", icon: "alignright", format: "alignright"}, 			
				{title: "Justify", icon: "alignjustify", format: "alignjustify"},
				{title: 'Image Left', selector: 'img', styles: {'float': 'left', 'margin': '0 15px 10px 0'} },
				{title: 'Image Right', selector: 'img', styles: {'float': 'right', 'margin': '0 0 10px 15px'} },
				{title: 'Table Cell Align Top', selector: 'td', styles: {'vertical-align': 'top'} },
				{title: 'Table Cell Align Middle', selector: 'td', styles: {'vertical-align': 'middle'} },
				{title: 'Table Cell Align Bottom', selector: 'td', styles: {'vertical-align': 'bottom'} }
			]
		}, 
		{
			title: "Font Size", items: [
				{title: '14pt', inline:'small' },
				{title: '16pt', inline:'span', styles: { fontSize: '12px', 'font-size': '16px' } },
				{title: '18pt', inline:'span', styles: { fontSize: '12px', 'font-size': '18px' } },
				{title: '22pt', inline:'span', styles: { fontSize: '12px', 'font-size': '22px' } },
				{title: '26pt', inline:'span', styles: { fontSize: '12px', 'font-size': '26px' } },
				{title: '28pt', inline:'span', styles: { fontSize: '12px', 'font-size': '28px' } },
				{title: '34pt', inline:'span', styles: { fontSize: '12px', 'font-size': '34px' } }
			]
		}
		],
	
		plugins: "paste textcolor image media link hr autolink code lists preview anchor searchreplace wordcount visualblocks insertdatetime table fullscreen responsivefilemanager",
		
		toolbar1: "styleselect | forecolor | undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
		toolbar2: "responsivefilemanager | hr | image | media | youtube | link unlink anchor | preview code | pastetext",
		
		image_advtab: true,
		menubar: true,
		relative_urls: false,
		remove_script_host: true,
		document_base_url: "/",
	
		external_filemanager_path: basePath + "/filemanager/",
		filemanager_title:"Responsive Filemanager",
		nanospell_server: "php",
		external_plugins: { "filemanager" : basePath + "/filemanager/plugin.min.js", "nanospell" : basePath + cmsRoot + "includes/plugins/tinymce/plugins/nanospell/plugin.js"}
	});

}

//Basic
tinymce.init({
    selector: "textarea.tinymceBasic", 
    theme: "modern", 
    height: 400,
	content_css: basePath+'/css/typography.css',
	
	textcolor_map: [
		"111111", "Very dark gray",
		"333333", "Dark gray",
		"666666", "Gray",
		"999999", "Light Gray",
		"CCCCCC", "Very light Gray",
		"FFFFFF", "White",
		"C70000", "Red"
	],
    
	style_formats:[
	{
		title: "Inline",
		items: [ 
			{title: "Bold", icon: "bold", format: "bold"}, 
			{title: "Italic", icon: "italic", format: "italic"}, 
			{title: "_Underline", icon: "underline", format: "underline"}, 
			{title: "Strikethrough", icon: "strikethrough", format: "strikethrough"}, 
			{title: "Superscript", icon: "superscript", format: "superscript"}, 
			{title: "Subscript", icon: "subscript", format: "subscript"}
		]
	}, 
	{
		title: "_Alignment",
		items: [
			{title: "Left", icon: "alignleft", format: "alignleft"}, 
			{title: "Center", icon: "aligncenter", format: "aligncenter"}, 
			{title: "Right", icon: "alignright", format: "alignright"}, 			
			{title: "Justify", icon: "alignjustify", format: "alignjustify"}
		]
	}, 
    {
		title: "Font Size", items: [
			{title: '10pt', inline:'span', styles: { fontSize: '12px', 'font-size': '10px' } },
			{title: '11pt', inline:'span', styles: { fontSize: '12px', 'font-size': '11px' } },
			{title: '12pt', inline:'span', styles: { fontSize: '12px', 'font-size': '12px' } },
			{title: '14pt', inline:'span', styles: { fontSize: '12px', 'font-size': '14px' } },
			{title: '16pt', inline:'span', styles: { fontSize: '12px', 'font-size': '16px' } },
			{title: '18pt', inline:'span', styles: { fontSize: '12px', 'font-size': '18px' } }
		]
	}
	],

    plugins: "paste textcolor link autolink code lists preview anchor searchreplace wordcount visualblocks insertdatetime table fullscreen responsivefilemanager",
    
    toolbar1: "styleselect | forecolor | undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
   	toolbar2: "responsivefilemanager | link unlink anchor | preview code | pastetext",
    
    image_advtab: true,
    menubar: true,
	relative_urls: false,
	remove_script_host: true,
	document_base_url: "/",

    external_filemanager_path: basePath + "/filemanager/",
   	filemanager_title:"Responsive Filemanager",
	nanospell_server: "php",
   	external_plugins: { "filemanager" : basePath + "/filemanager/plugin.min.js","nanospell": basePath + cmsRoot + "includes/plugins/tinymce/plugins/nanospell/plugin.js"}
});
