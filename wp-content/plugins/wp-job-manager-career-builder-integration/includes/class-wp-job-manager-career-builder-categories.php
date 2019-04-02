<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_Career_Builder_Categories
 */
class WP_Job_Manager_Career_Builder_Categories {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'job_listing_category_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'job_listing_category_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10 );
	}

	/**
	 * Category thumbnail fields.
	 */
	public function add_category_fields() {
		$categories = WP_Job_Manager_Career_Builder_API::get_categories();
		?>
		<div class="form-field">
			<label for="display_type"><?php _e( 'Map to Career Builder Category', 'wp-job-manager-career-builder-integration' ); ?></label>
			<select id="career_builder_category" name="career_builder_category" class="postform">
				<option value=""><?php _e( 'None', 'wp-job-manager-career-builder-integration' ); ?></option>
				<?php foreach ( $categories as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public function edit_category_fields( $term ) {
		$career_builder_categories = get_option( 'career_builder_categories', array() );
		$categories                = WP_Job_Manager_Career_Builder_API::get_categories();
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Map to Career Builder Category', 'wp-job-manager-career-builder-integration' ); ?></label></th>
			<td>
				<select id="career_builder_category" name="career_builder_category" class="postform">
					<option value=""><?php _e( 'None', 'wp-job-manager-career-builder-integration' ); ?></option>
					<?php foreach ( $categories as $key => $value ) : ?>
						<option <?php selected( isset( $career_builder_categories[ $term->term_id ] ) && $career_builder_categories[ $term->term_id ] === $key ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * save_category_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 */
	public function save_category_fields( $term_id ) {
		if ( isset( $_POST['career_builder_category'] ) ) {
			$career_builder_categories             = get_option( 'career_builder_categories', array() );
			$career_builder_categories[ $term_id ] = sanitize_text_field( $_POST['career_builder_category'] );
			update_option( 'career_builder_categories', $career_builder_categories );
		}
	}
}

new WP_Job_Manager_Career_Builder_Categories();
