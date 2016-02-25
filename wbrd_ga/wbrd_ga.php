<?php 
	/*
	Plugin Name: WEBroad GA
	Plugin URI: http://webroad.pl/cms/5954-2-tworzenie-wtyczki-wordpress
	Description: Prosta wtyczka umieszczająca kod śledzenia Google Analitics
	Version: 1.0
	Author: Michal Kortas
	Author URI: http://webroad.pl
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/
?>
<?php 

class wbrd_ga
{

    private $options;

    public function __construct() {
        add_action('wp_footer', array( $this, 'show_ga_code' ));
        add_action('admin_menu', array( $this, 'add_page' ));
        add_action('admin_init', array( $this, 'page_init' ));
    }

	public function show_ga_code() {
		$this->options = get_option('ga');
		?>
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			  ga('create', '<?php echo $this->options['ga_id'] ?>', 'auto');
			  ga('send', 'pageview');
			</script>
		<?php
		
	}
    
	public function add_page() {
		add_options_page(
			'Settings Admin', 
			'WEBroad GA', 
			'manage_options', 
			'ga_settings_page', 
			array( $this, 'create_page' )
		);
	}

	public function create_page() {
		$this->options = get_option( 'ga' );
		?>
		<div class="wrap">
			<h2>Ustawienia WEBroad GA</h2>           
			<form method="post" action="options.php">
			<?php
				settings_fields( 'ga_options' );   
				do_settings_sections( 'ga_settings_page' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}

	public function page_init() {        
		register_setting(
			'ga_options',
			'ga',
			array($this, 'sanitize')
		);

		add_settings_section(
			'ga_section', 
			'Zarządzanie kodem śledzenia',
			array( $this, 'section_callback' ),
			'ga_settings_page'
		);  

		add_settings_field(
			'ga_id',
			'Kod śledzenia',
			array( $this, 'id_number_callback' ),
			'ga_settings_page',
			'ga_section'      
		);      

	}

	public function sanitize( $input ) {
		$new_input = array();
		if( isset( $input['ga_id'] ) )
			$new_input['ga_id'] = sanitize_text_field( $input['ga_id'] );
		
		return $new_input;
	}

	public function section_callback() {
		echo 'Wprowadź swoje ustawienia poniżej:';
	}

	public function id_number_callback() {
		if(isset( $this->options['ga_id'] )) $ga_id = esc_attr( $this->options['ga_id']);
		echo '<input type="text" id="ga_id" name="ga[ga_id]" value="'.$ga_id.'">';
	}
}


$ga_settings_page = new wbrd_ga();

?>
