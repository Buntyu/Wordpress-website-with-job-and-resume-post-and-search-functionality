<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Field_Types
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Field_Types extends WP_Job_Manager_Field_Editor_Fields {

	private static $instance;
	private        $available_types = array();

	function __construct() {

		add_action( 'job_manager_input_radio', array( $this, 'admin_input_radio' ), 10, 2 );
		add_action( 'job_manager_input_date', array( $this, 'admin_input_date' ), 10, 2 );
		add_action( 'job_manager_input_phone', array( $this, 'admin_input_phone' ), 10, 2 );
		add_action( 'job_manager_input_select', array( $this, 'admin_input_select' ), 10, 2 );
		add_action( 'job_manager_input_multiselect', array( $this, 'admin_input_multiselect' ), 10, 2 );
		add_action( 'job_manager_input_header', array( $this, 'admin_input_header' ), 10, 2 );
		add_action( 'job_manager_input_html', array( $this, 'admin_input_html' ), 10, 2 );
		add_action( 'job_manager_input_actionhook', array( $this, 'admin_input_actionhook' ), 10, 2 );
		add_action( 'resume_manager_input_radio', array( $this, 'admin_input_radio' ), 10, 2 );
		add_action( 'resume_manager_input_date', array( $this, 'admin_input_date' ), 10, 2 );
		add_action( 'resume_manager_input_phone', array( $this, 'admin_input_phone' ), 10, 2 );
		add_action( 'resume_manager_input_select', array( $this, 'admin_input_select' ), 10, 2 );
		add_action( 'resume_manager_input_multiselect', array( $this, 'admin_input_multiselect' ), 10, 2 );
		add_action( 'resume_manager_input_header', array( $this, 'admin_input_header' ), 10, 2 );
		add_action( 'resume_manager_input_html', array( $this, 'admin_input_html' ), 10, 2 );
		add_action( 'resume_manager_input_actionhook', array( $this, 'admin_input_actionhook' ), 10, 2 );

	}

	/**
	 * input_actionhook function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 *
	 * @since 1.3.0
	 *
	 */
	public function admin_input_actionhook( $key, $field ) {

		global $thepostid;
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php if ( ! empty( $field[ 'label' ] ) ) echo esc_html( $field[ 'label' ] ) . ':'; ?></label>
			<?php get_job_manager_template( 'form-fields/actionhook-field.php', array('key' => $key, 'field' => $field, 'admin' => TRUE) ); ?>
		</p>
		<?php
	}

	/**
	 * input_html function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 * @since 1.3.0
	 *
	 */
	public function admin_input_html( $key, $field ) {

		global $thepostid;
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php if ( ! empty( $field[ 'label' ] ) ) echo esc_html( $field[ 'label' ] ) . ':'; ?></label>
			<?php get_job_manager_template( 'form-fields/html-field.php', array('key' => $key, 'field' => $field, 'admin' => TRUE) ); ?>
		</p>
		<?php
	}

	/**
	 * input_header function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 * @since 1.3.0
	 *
	 */
	public function admin_input_header( $key, $field ) {

		global $thepostid;
		?>
		<p class="form-field">
			<?php get_job_manager_template( 'form-fields/header-field.php', array('key' => $key, 'field' => $field, 'admin' => TRUE) ); ?>
		</p>
		<?php
	}

	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public function admin_input_multiselect( $key, $field ) {

		global $thepostid;

		if ( empty( $field[ 'value' ] ) ) {
			$field[ 'value' ] = get_post_meta( $thepostid, $key, TRUE );
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[ 'label' ] ); ?>:</label>
			<?php get_job_manager_template( 'form-fields/multiselect-field.php', array( 'key' => $key, 'field' => $field, 'admin' => TRUE ) ); ?>
		</p>
	<?php
	}

	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public function admin_input_select( $key, $field ) {

		global $thepostid;

		if ( empty( $field[ 'value' ] ) ) {
			$field[ 'value' ] = get_post_meta( $thepostid, $key, TRUE );
		}
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[ 'label' ] ); ?>:</label>
			<?php get_job_manager_template( 'form-fields/select-field.php', array( 'key' => $key, 'field' => $field, 'admin' => TRUE ) ); ?>
		</p>
	<?php
	}

	/**
	 * Output Phone Field Type for Admin WritePanel
	 *
	 *
	 * @since 1.2.1
	 *
	 * @param $key
	 * @param $field
	 */
	public function admin_input_phone( $key, $field ){

		global $thepostid;

		if ( empty( $field[ 'value' ] ) ) {
			$field[ 'value' ] = get_post_meta( $thepostid, $key, TRUE );
		}
		?>
		<p class="form-field form-field-phone">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[ 'label' ] ); ?>:</label>
			<?php get_job_manager_template( 'form-fields/phone-field.php', array( 'key' => $key, 'field' => $field, 'admin' => TRUE ) ); ?>
		</p>
	<?php
	}

	/**
	 * Output Radio Input on Admin WritePanel
	 *
	 *
	 * @since 1.1.10
	 *
	 * @param $key
	 * @param $field
	 */
	public function admin_input_radio( $key, $field ) {
		global $thepostid;

		$meta_key = esc_attr( $key );
		if ( empty( $field[ 'value' ] ) )
			$field[ 'value' ] = get_post_meta( $thepostid, $key, TRUE );
			// Hack for admin section to prevent errors on save for null fields
			if( empty( $field[ 'value' ] ) ) $field['value'] = 'none';
		?>
		<p class="form-field form-field-radio">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[ 'label' ] ); ?></label>
			<input type="radio" style="margin-left: 5px; margin-right: 5px; width: auto;" data-meta_key="<?php echo $key; ?>" class="jmfe-radio jmfe-radio-<?php echo $key; ?> input-radio" name="<?php echo esc_attr( isset( $field[ 'name' ] ) ? $field[ 'name' ] : $key ); ?>" id="<?php echo $key . '-none'; ?>" value="" <?php if ( isset( $field[ 'value' ] ) || isset( $field[ 'default' ] ) ) checked( isset( $field[ 'value' ] ) ? $field[ 'value' ] : $field[ 'default' ], 'none', TRUE ); ?> />
			<strong><?php _e( 'None', 'wp-job-manager-field-editor' ); ?></strong>
			<?php get_job_manager_template( 'form-fields/radio-field.php', array( 'key' => $key, 'field' => $field, 'admin' => true ) ); ?>
		</p>
	<?php
	}

	/**
	 * Output Date Picker Field Type for Admin WritePanel
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $key
	 * @param $field
	 */
	public function admin_input_date( $key, $field ){

		global $thepostid;

		if ( empty( $field[ 'value' ] ) )
			$field[ 'value' ] = get_post_meta( $thepostid, $key, TRUE );
		?>
		<p class="form-field form-field-date">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[ 'label' ] ); ?>:</label>
			<?php get_job_manager_template( 'form-fields/date-field.php', array( 'key' => $key, 'field' => $field, 'admin' => TRUE ) ); ?>
		</p>
		<?php

	}

	/**
	 * Check if Field Type exists
	 *
	 * @since 1.1.9
	 *
	 * @param $field_type
	 *
	 * @return bool
	 */
	function is_valid_type( $field_type ) {

		if ( array_key_exists( $field_type, $this->get_field_types( true ) ) ) return true;

		return false;
	}

	/**
	 * Get Available Field Types
	 *
	 * Based on available templates, and WPJM version, will
	 * return the possible field types that are available.
	 *
	 * @since 1.1.9
	 *
	 * @param bool $as_array Return field types as array
	 *
	 * @param null $list_field_group
	 *
	 * @return string
	 */
	function get_field_types( $as_array = false, $list_field_group = null ) {

		$field_types = array(
			'text'      => 'Text Box',
			'textarea'  => 'Text Area',
			'wp-editor' => 'WP Editor',
			'select'    => 'Dropdown',
			'file'      => 'File Upload',
			'password'  => 'Password Text Box', 
		    'radio'     => 'Radio Buttons',
		    'date'      => 'Date Picker',
			'phone'     => 'Phone Number',
		);

		$field_types = $this->add_other_field_types( $field_types, $list_field_group );

		$field_types = apply_filters( 'field_editor_field_types', $field_types );

		if ( ! $as_array ) $field_types = $this->options()->convert( $field_types );

		return $field_types;
	}

	/**
	 * Add field types that do not save values
	 *
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	function add_no_value_field_types(){

		$no_value_types = array(
			'section_novalue' => '---' . __( 'No Value Field Types', 'wp-job-manager-field-editor' ),
			'header'          => 'Section Header',
			'html'            => 'HTML',
			'actionhook'     => 'WordPress Action Hook',
		);

		$this->available_types = array_merge( $this->available_types, $no_value_types );

		return $this->available_types;
	}

	/**
	 * Add version specific field types
	 *
	 * @since 1.1.9
	 *
	 * @param $field_types
	 *
	 * @return array
	 */
	function add_other_field_types( $field_types, $list_field_group = null) {

		if( $list_field_group ){

			switch( $list_field_group ){

				case 'job':
					$this->wpjm();
					break;

				case 'company':
					$this->wpjm();
					break;

				case 'resume_fields':
					$this->wpjm();
					$this->wprm();
					break;

			}

		}

		$this->add_no_value_field_types();

		return array_merge( $field_types, $this->available_types );

	}

	/**
	 * WP Job Manager Field Types
	 *
	 * Will return the available field types based on the
	 * currently installed version of WP Job Manager.
	 *
	 * @since 1.1.9
	 *
	 * @return array
	 */
	function wpjm() {

		$wpjm_types = array(
			'1.15.0' => array(
				'checkbox' => __( 'Checkbox', 'wp-job-manager-field-editor' )
			),
			'1.14.0' => array(
				'multiselect' => __( 'Multi-Select', 'wp-job-manager-field-editor' ),
				'taxonomy_field_type' => '---' . __( 'Taxonomy Field Types', 'wp-job-manager-field-editor' ),
				'term-checklist'   => __( 'Taxonomy Checklist', 'wp-job-manager-field-editor' ),
				'term-select'      => __( 'Taxonomy Dropdown', 'wp-job-manager-field-editor' ),
				'term-multiselect' => __( 'Taxonomy Multi-Select Dropdown', 'wp-job-manager-field-editor' )
			)
		);

		foreach ( $wpjm_types as $version => $types ) {

			if ( version_compare( JOB_MANAGER_VERSION, $version, 'ge' ) ) {
				$this->available_types = array_merge( $this->available_types, $types );
			}

		}

		return $this->available_types;

	}

	/**
	 * WP Job Manager Resumes Field Types
	 *
	 * Will return the available field types based on the
	 * currently installed version of WP Job Manager.
	 *
	 * @since 1.1.9
	 *
	 * @return array
	 */
	function wprm() {

		$wprm_types = array(
			'1.7.0' => array(
				'taxonomy_field_type' => '---' . __( 'Taxonomy Field Types', 'wp-job-manager-field-editor' ),
				'term-checklist'   => __( 'Taxonomy Checklist', 'wp-job-manager-field-editor' ),
				'term-select'      => __( 'Taxonomy Dropdown', 'wp-job-manager-field-editor' ),
				'term-multiselect' => __( 'Taxonomy Multi-Select Dropdown', 'wp-job-manager-field-editor' )
			),
		);

		foreach ( $wprm_types as $version => $types ) {

			if ( version_compare( RESUME_MANAGER_VERSION, $version, 'ge' ) && version_compare( JOB_MANAGER_VERSION, '1.14.0', 'ge' ) ) {
				$this->available_types = array_merge( $this->available_types, $types );
			}

		}

		return $this->available_types;

	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return wp_job_manager_field_editor
	 */
	static function get_instance() {

		if ( null == self::$instance ) self::$instance = new self;

		return self::$instance;
	}

}

WP_Job_Manager_Field_Editor_Field_Types::get_instance();