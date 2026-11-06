<?php
// Local Docker only: stop the HTTPS Redirection plugin from forcing https://.
add_filter('option_active_plugins', function ($p) {
	return array_values(array_diff((array) $p, ['https-redirection/https-redirection.php']));
});
