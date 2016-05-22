<?php
function module_enabled($name) {
	return function_exists('apache_get_modules') && in_array($name, apache_get_modules());
}

if (module_enabled('mod_rewrite')) {
	echo "Le module de réécriture est présent.";
} else {
	echo "L'environnement actuel ne dispose pas du module de réécriture ou ne permet pas de le détecter";
}
?>