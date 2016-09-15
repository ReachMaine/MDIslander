<?php 
	if(class_exists('AQ_Block')) {

	    define('AQPB_CUSTOM_DIR', get_template_directory() . '/page-builder/');
	    define('AQPB_CUSTOM_URI', get_template_directory_uri() . '/page-builder/');

	    //include the block files
	    require_once(AQPB_CUSTOM_DIR . 'blocks/eafeature-block.php');
	    require_once(AQPB_CUSTOM_DIR . 'blocks/eacat_landing.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/ea_tag_columns.php');
	    //register the blocks
	    aq_register_block('eafeature_Block');
	    aq_register_block('eacat_landing');
	    aq_register_block('ea_tag_cols');

	}
?>