<?php
/**
 * WP List Table admin page view
 * Bill
 * @license   GPL-2.0+
 */

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form id="badbots-filter" method="get">
		<input type="hidden" name="page" value="<?php echo strip_tags($_REQUEST['page']) ?>" />

        <?php $reportattacks_list_table->search_box('Search IP', 'search-table'); ?>
       
		<?php $reportattacks_list_table->display() ?>
	</form>
</div>

