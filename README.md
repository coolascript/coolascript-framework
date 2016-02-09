# coolascript-framework
Wordpress development framework

# Getting Started
1. Install coolascript-framework plugin to Wordpress.
2. Add next lines to your theme\plugin project:
```
if ( defined( 'CSFRAMEWORK_VERSION' ) ) {
	class myApp extends \csframework\Csframework {}
	$app = myApp::getInstance()
		->setNamespace( 'myapp' )					// Your project PHP namespace for avoding conflicts
		->setApppath( 'myapp', TEST_PLUGIN_DIR )	// Path to your project
		->setTextDomain( 'myapp' )					// WP locale text domain
		->setFieldsVar( 'myapp' )					// Key for all extra fields
		->run();
}
```