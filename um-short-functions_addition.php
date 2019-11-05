Replace lines 209-215 of um-short-functions.php with the following code to 
correctly display array user meta fields using shortcode {usermeta:meta:key}.
    // Support for all usermeta keys
	if ( ! empty( $matches[1] ) && is_array( $matches[1] ) ) {
		foreach ( $matches[1] as $match ) {
			$strip_key = str_replace( 'usermeta:', '', $match );
			
			$value = um_user( $strip_key );
			if(is_array($value)) {
				$value = implode(', ', $value);
			}

			$content = str_replace( '{' . $match . '}', $value, $content );
		}
	}