// register webpack stylesheet and js with theme
wp_enqueue_style( 'site_main_css', get_template_directory_uri() . '/css/build/main.min.css' );
wp_enqueue_script( 'site_main_js', get_template_directory_uri() . '/js/build/app.min.css' , null , null , true );
// include the css file
$cssFilePath = glob( get_template_directory() . '/css/build/main.min.*' );
$cssFileURI = get_template_directory_uri() . '/css/build/' . basename($cssFilePath[0]);
wp_enqueue_style( 'site_main_css', $cssFileURI );
// include the javascript file
$jsFilePath = glob( get_template_directory() . '/js/build/app.min.*.js' );
$jsFileURI = get_template_directory_uri() . '/js/build/' . basename($jsFilePath[0]);
wp_enqueue_script( 'site_main_js', $jsFileURI , null , null , true );
