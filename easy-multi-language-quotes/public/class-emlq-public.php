<?php

class EMLQ_Public {

	private $plugin_name;
	private $version;
	private $option_name = 'emlq_settings';
	private static $quotes_cache = null;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Noto+Sans+SC:wght@400;700&family=Noto+Serif+JP:wght@400;700&display=swap', array(), null );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/emlq-public.css', array(), $this->version, 'all' );
        
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/emlq-public.js', array(), $this->version, true );
        wp_localize_script( $this->plugin_name, 'emlqData', array(
            'apiUrl' => rest_url( 'emlq/v1/quote' ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ));
	}

    public function register_routes() {
        register_rest_route( 'emlq/v1', '/quote', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_item' ),
            'permission_callback' => '__return_true'
        ));
    }

    public function get_item( $request ) {
        // Use cached quotes if available
        if ( self::$quotes_cache === null ) {
            self::$quotes_cache = get_option( 'emlq_all_quotes' );
        }
        $all_quotes = self::$quotes_cache;
        
        $options = get_option( $this->option_name );

		if ( empty( $all_quotes ) || ! is_array( $all_quotes ) ) {
			return new WP_REST_Response( array( 'html' => '' ), 200 );
		}

		$frequency = isset( $options['frequency'] ) ? $options['frequency'] : 'always';
		$selected_quote = $this->get_quote_based_on_frequency( $all_quotes, $frequency );

        if ( ! $selected_quote ) {
            return new WP_REST_Response( array( 'html' => '' ), 200 );
        }

        $html = $this->generate_html( $selected_quote );
        return new WP_REST_Response( array( 
            'html' => $html,
            'index' => isset( $selected_quote['_index'] ) ? $selected_quote['_index'] : null
        ), 200 );
    }

	public function render_shortcode( $atts ) {
        // Return placeholder for JS to populate
        return '<div class="emlq-root"></div>';
	}

	private function get_quote_based_on_frequency( $quotes, $frequency ) {
		if ( 'always' === $frequency ) {
			// Avoid repeating the same quote immediately
			$last_index = isset( $_COOKIE['emlq_last_index'] ) ? intval( $_COOKIE['emlq_last_index'] ) : -1;
			$available_indices = array_keys( $quotes );
			
			// If we have more than 1 quote, remove the last one from available options
			if ( count( $available_indices ) > 1 && $last_index >= 0 ) {
				$available_indices = array_diff( $available_indices, array( $last_index ) );
			}
			
			$random_index = $available_indices[ array_rand( $available_indices ) ];
			
			// Embed index in quote data so we can set cookie via JS (avoid headers_sent issues)
			$selected_quote = $quotes[ $random_index ];
			$selected_quote['_index'] = $random_index;
			
			return $selected_quote;
		}

		$transient_name = 'emlq_current_quote';
		$cached_quote = get_transient( $transient_name );

		if ( false !== $cached_quote ) {
			return $cached_quote;
		}

		$new_quote = $quotes[ array_rand( $quotes ) ];
		
		$expiration = ( 'hourly' === $frequency ) ? HOUR_IN_SECONDS : DAY_IN_SECONDS;

		set_transient( $transient_name, $new_quote, $expiration );

		return $new_quote;
	}

	private function generate_html( $quote ) {
		// Prepare data safely
		$zh = isset( $quote['quote']['zh'] ) ? $quote['quote']['zh'] : '';
		$en = isset( $quote['quote']['en'] ) ? $quote['quote']['en'] : '';
		$ja = isset( $quote['quote']['ja'] ) ? $quote['quote']['ja'] : '';
		$author = isset( $quote['author'] ) ? $quote['author'] : '';

		ob_start();
		?>
		<div class="easy-quotes-container">
			<div class="easy-quotes-quote">
				<?php if ( $zh ) : ?>
					<div class="emlq-text emlq-zh"><?php echo esc_html( $zh ); ?></div>
				<?php endif; ?>
				
				<?php if ( $en ) : ?>
					<div class="emlq-text emlq-en"><?php echo esc_html( $en ); ?></div>
				<?php endif; ?>

				<?php if ( $ja ) : ?>
					<div class="emlq-text emlq-ja"><?php echo esc_html( $ja ); ?></div>
				<?php endif; ?>

				<?php if ( $author ) : ?>
					<div class="emlq-author">- <?php echo esc_html( $author ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
