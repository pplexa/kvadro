﻿/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.filebrowserBrowseUrl = 'http://afo.loc/admin/templates/js/kcfinder/browse.php?type=files';
    config.filebrowserImageBrowseUrl = 'http://afo.loc/admin/templates/js/kcfinder/browse.php?type=images';
    config.filebrowserFlashBrowseUrl = 'http://afo.loc/admin/templates/js/kcfinder/browse.php?type=flash';
    config.filebrowserUploadUrl = 'http://afo.loc/admin/templates/js/kcfinder/upload.php?type=files';
    config.filebrowserImageUploadUrl = 'http://afo.loc/admin/templates/js/kcfinder/upload.php?type=images';
    config.filebrowserFlashUploadUrl = 'http://afo.loc/admin/templates/js/kcfinder/upload.php?type=flash';    
};