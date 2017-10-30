<?php


include_once('Otwartezamkniete_LifeCycle.php');

class Otwartezamkniete_Plugin extends Otwartezamkniete_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            '1' => array(__('Poniedziałek', 'my-awesome-plugin')),
            '2' => array(__('Wtorek', 'my-awesome-plugin')),
            '3' => array(__('Środa', 'my-awesome-plugin')),
            '4' => array(__('Czwartek', 'my-awesome-plugin')),
            '5' => array(__('Piątek', 'my-awesome-plugin')),
            '6' => array(__('Sobota', 'my-awesome-plugin')),
            '0' => array(__('Niedziela', 'my-awesome-plugin')),
           /* 'AmAwesome' => array(__('I like this awesome plugin', 'my-awesome-plugin'), 'false', 'true'),
            'CanDoSomething' => array(__('Which user role can do something', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')*/
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'OtwarteZamkniete';
    }

    protected function getMainPluginFileName() {
        return 'otwartezamkniete.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

	
	
	
	
	public function setPluginData(){
			
		?>
		<script type="text/javascript">

			var otwartezamkniete_data = <?php echo $this->getPluginData(); ?>;

		</script>

		<?php
	}
		
	public function getPluginData(){
		date_default_timezone_set( 'Europe/Warsaw' );
		$r = array( 'test' => 'jest jeszcze bardziej ok',  );
			$max = 7;
			$opt = array();
			for( $i = 0; $i < $max; $i++){
				$opt[ $i ] = explode(' do ', get_option( 'Otwartezamkniete_Plugin_' . $i ));				
			}
    
    
    $currentDate = date("Y-m-d H:i:s");
    $currentDayNo = date("w");
    $currentDayWorkingHours = $opt[ $currentDayNo ];
    
    
    $start =  date("Y-m-d") . " " . $currentDayWorkingHours[0] . ":00";
    $stop =  date("Y-m-d") . " " . $currentDayWorkingHours[1] . ":00";
  
    
   // echo '<pre>'; echo var_dump( $currentDate, $start, $stop); echo '</pre>';
		return json_encode( array( 'open' => $currentDate >= $start && $currentDate <= $stop,  ) );
	}
	
    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
        add_action('wp_head', array(&$this, 'setPluginData'));

		
		
		
        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        
		wp_enqueue_script( 'vue', "https://unpkg.com/vue" );
		wp_enqueue_script( 'otwartezamkniete-js', plugins_url('/js/otwartezamkniete.js', __FILE__ ), array( 'vue' ), '1.0', true );
		



        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }

		

	
}
