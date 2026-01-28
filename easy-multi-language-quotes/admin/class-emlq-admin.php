<?php
class EMLQ_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		// Enqueue admin-specific styles if needed.
	}

	public function add_plugin_admin_menu() {
		add_options_page(
			'Easy Quotes Settings',
			'Easy Quotes',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' )
		);
	}

	public function register_settings() {
		register_setting( $this->plugin_name, 'emlq_settings', array( $this, 'validate_settings' ) );

		add_settings_section(
			'emlq_general_section',
			'General Settings',
			array( $this, 'section_callback' ),
			$this->plugin_name
		);

		add_settings_field(
			'emlq_source_type',
			'Data Source',
			array( $this, 'render_source_type_field' ),
			$this->plugin_name,
			'emlq_general_section'
		);

		add_settings_field(
			'emlq_manual_json',
			'Manual JSON Input',
			array( $this, 'render_manual_json_field' ),
			$this->plugin_name,
			'emlq_general_section'
		);

		add_settings_field(
			'emlq_file_upload',
			'Upload File (JSON/CSV)',
			array( $this, 'render_file_upload_field' ),
			$this->plugin_name,
			'emlq_general_section'
		);

		add_settings_field(
			'emlq_refresh_frequency',
			'Refresh Frequency',
			array( $this, 'render_frequency_field' ),
			$this->plugin_name,
			'emlq_general_section'
		);
	}

	public function validate_settings( $input ) {
		$valid = array();
		$valid['source_type'] = isset( $input['source_type'] ) && in_array( $input['source_type'], array( 'manual', 'file' ) ) ? $input['source_type'] : 'manual';
		$valid['frequency']   = isset( $input['frequency'] ) && in_array( $input['frequency'], array( 'always', 'hourly', 'daily' ) ) ? $input['frequency'] : 'always';
		$valid['manual_json'] = isset( $input['manual_json'] ) ? wp_kses_post( $input['manual_json'] ) : '';

		// Handle file upload
		if ( ! empty( $_FILES['emlq_upload_file']['name'] ) ) {
			$file = $_FILES['emlq_upload_file'];
			$ext  = pathinfo( $file['name'], PATHINFO_EXTENSION );

			if ( in_array( strtolower( $ext ), array( 'json', 'csv' ) ) ) {
				$content = file_get_contents( $file['tmp_name'] );
				$valid['manual_json'] = $content; // Overwrite manual input with file content
			} else {
				add_settings_error( 'emlq_settings', 'invalid_file_type', 'Invalid file type. Please upload JSON or CSV.' );
			}
		}

		// Parse and validation the JSON/CSV content
		$quotes = $this->parse_content( $valid['manual_json'] );
		
		if ( false === $quotes ) {
			add_settings_error( 'emlq_settings', 'invalid_content', 'Invalid JSON or CSV content.' );
		} else {
			// Save parsed quotes to a separate option for easier access
			update_option( 'emlq_all_quotes', $quotes );
		}

		return $valid;
	}

	private function parse_content( $content ) {
		// Try JSON first
		$json = json_decode( $content, true );
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $json ) ) {
			return $json;
		}
		
		// If not JSON, try generic CSV parsing (simplified for now)
		// Assuming headers: author, zh, en, ja
		// This is a naive implementation, specific CSV structure might be needed
		$lines = explode( "\n", $content );
		if ( count( $lines ) > 1 ) {
			// Very basic CSV check - checking for delimiters
			if ( strpos( $lines[0], ',' ) !== false ) {
				$csv_data = array_map( 'str_getcsv', $lines );
				$header = array_shift( $csv_data );
				$quotes = array();
				foreach ( $csv_data as $row ) {
					if ( count( $row ) === count( $header ) ) {
						$item = array_combine( $header, $row );
						
						// Normalize structure to match JSON: { author, quote: { zh, en, ja } }
						// Logic depends on CSV headers; assuming specific naming or auto-mapping
						$normalized = array(
							'author' => isset($item['author']) ? $item['author'] : 'Unknown',
							'quote' => array(
								'zh' => isset($item['zh']) ? $item['zh'] : '',
								'en' => isset($item['en']) ? $item['en'] : '',
								'ja' => isset($item['ja']) ? $item['ja'] : '',
							)
						);
						$quotes[] = $normalized;
					}
				}
				return $quotes;
			}
		}

		return false;
	}

	public function section_callback() {
		echo 'Configure how quotes are loaded and displayed.';
	}

	public function render_source_type_field() {
		$options = get_option( 'emlq_settings' );
		$val = isset( $options['source_type'] ) ? $options['source_type'] : 'manual';
		?>
		<select name="emlq_settings[source_type]">
			<option value="manual" <?php selected( $val, 'manual' ); ?>>Manual Input</option>
			<option value="file" <?php selected( $val, 'file' ); ?>>File Upload (Overwrite Manual)</option>
		</select>
		<?php
	}

	public function render_manual_json_field() {
		$options = get_option( 'emlq_settings' );
		$val = isset( $options['manual_json'] ) ? $options['manual_json'] : '';
		?>
		<textarea name="emlq_settings[manual_json]" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( $val ); ?></textarea>
		<p class="description">Enter JSON directly here or see parsed result after upload.</p>
		<?php
	}

	public function render_file_upload_field() {
		?>
		<input type="file" name="emlq_upload_file" />
		<p class="description">Upload a JSON or CSV file to populate the quotes. This will overwrite the manual input field.</p>
		<?php
	}

	public function render_frequency_field() {
		$options = get_option( 'emlq_settings' );
		$val = isset( $options['frequency'] ) ? $options['frequency'] : 'always';
		?>
		<select name="emlq_settings[frequency]">
			<option value="always" <?php selected( $val, 'always' ); ?>>Every Page Refresh</option>
			<option value="hourly" <?php selected( $val, 'hourly' ); ?>>Hourly</option>
			<option value="daily" <?php selected( $val, 'daily' ); ?>>Daily</option>
		</select>
		<?php
	}

	public function display_plugin_setup_page() {
		?>
		<div class="wrap">
			<h1>Easy Multi-Language Quotes</h1>
			<form action="options.php" method="post" enctype="multipart/form-data">
				<?php
				settings_fields( $this->plugin_name );
				do_settings_sections( $this->plugin_name );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
