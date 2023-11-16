<?php
/**
 * The main template
 *
 * @package uhleloX\var\templates
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

/**
 * Include header
 */
require_once TEMPLATE_PATH . '/' . $this->template->value . '/header.php';

/**
 * Output body
 */
echo $this->x_item->content;

/**
 * Include footer
 */
require_once TEMPLATE_PATH . '/' . $this->template->value . '/footer.php';?>
