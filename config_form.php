<style>
	.helper{font-size:.85em;}
	.input-block{width:100%;}
</style>

<p>Use the shortcode <code>[calendarfeed]</code> to add the feed to a page.<br><strong>Please Note: </strong>RSS feeds must be properly formatted for time-based notifications.</p>


<h2><?php echo __('Feed Settings'); ?></h2>

<fieldset id="feed">

	<div class="field">
	    <div class="two columns alpha">
	        <label for="cf_rssfeed"><?php echo __('RSS URL'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <p class="explanation"><?php echo __("Enter the URL of the feed."); ?></p>

	        <div class="input-block">
	            <input type="text" class="textinput" name="cf_rssfeed" value="<?php echo get_option('cf_rssfeed'); ?>">
	            <p class="helper"></p>
	        </div>
	    </div>
	</div>

</fieldset>



<h2><?php echo __('Display Settings'); ?></h2>

<fieldset id="display">


	<!-- notifications -->
	<div class="field">
	    <div class="two columns alpha">
	        <label for="cf_notify"><?php echo __('Notifications'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <?php echo get_view()->formCheckbox('cf_notify', true,
	array('checked'=>(boolean)get_option('cf_notify'))); ?>

	        <p class="explanation"><?php echo __('Display calendar notifications. Dismissing the notification will hide it for 24 hours or until the user clears their browser cookies.'); ?></p>
	    </div>
	</div>
	
	<!-- mobile -->
	<div class="field">
	    <div class="two columns alpha">
	        <label for="cf_displayOnMobile"><?php echo __('Mobile'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <?php echo get_view()->formCheckbox('cf_displayOnMobile', true,
	array('checked'=>(boolean)get_option('cf_displayOnMobile'))); ?>

	        <p class="explanation"><?php echo __('Display calendar notifications on mobile devices (defined as browser viewports smaller than or equal to 768px).'); ?></p>
	    </div>
	</div>	
	
</fieldset>