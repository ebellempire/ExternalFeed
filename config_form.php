<style>
	.helper{font-size:.85em;}
	.input-block{width:100%;}
</style>


<h2><?php echo __('Usage');?></h2>
<p>Use the shortcode <code>[externalfeed]</code> to add the feed to a page or simply enable notifications to display a notice at the top of each page. Users may dismiss the notification for 24 hours (by default, or as set below).*</p>

<h2><?php echo __('Feed Settings'); ?></h2>

<fieldset id="feed">

	<div class="field">
	    <div class="two columns alpha">
	        <label for="ef_rssfeed"><?php echo __('RSS URL'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <p class="explanation"><?php echo __("Enter the URL of the feed."); ?></p>

	        <div class="input-block">
	            <input type="text" class="textinput" name="ef_rssfeed" value="<?php echo get_option('ef_rssfeed'); ?>">
	            <p class="helper"></p>
	        </div>
	    </div>
	</div>

	<!-- calendar settings -->
	<div class="field">
	    <div class="two columns alpha">
	        <label for="ef_isCalendar"><?php echo __('Calendar Format'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <?php echo get_view()->formCheckbox('ef_isCalendar', true,
	array('checked'=>(boolean)get_option('ef_isCalendar'))); ?>

	        <p class="explanation"><?php echo __('Check this option if your feed contains future-dated events. Modifies icons and various text labels.'); ?></p>
	    </div>
	</div>

</fieldset>



<h2><?php echo __('Display Settings'); ?></h2>

<fieldset id="display">


	<!-- notifications -->
	<div class="field">
	    <div class="two columns alpha">
	        <label for="ef_notify"><?php echo __('Notifications'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <?php echo get_view()->formCheckbox('ef_notify', true,
	array('checked'=>(boolean)get_option('ef_notify'))); ?>

	        <p class="explanation"><?php echo __('Display calendar notifications. Dismissing the notification will hide it for 24 hours or until the user clears their browser cookies.'); ?></p>
	    </div>
	</div>


	<!-- cookies -->
	<div class="field">
	    <div class="two columns alpha">
	        <label for="ef_cookieExpiration"><?php echo __('Notification dismissal'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <p class="explanation"><?php echo __('If a user dismisses a notification, how many days should pass before they see a notification again?*'); ?></p>
	        <div class="input-block">
	            <input type="text" class="textinput" name="ef_cookieExpiration" value="<?php echo (int)get_option('ef_cookieExpiration'); ?>">
	        </div>
	        
	    </div>
	</div>	

	
	<!-- mobile -->
	<div class="field">
	    <div class="two columns alpha">
	        <label for="ef_displayOnMobile"><?php echo __('Mobile'); ?></label>
	    </div>

	    <div class="inputs five columns omega">
	        <?php echo get_view()->formCheckbox('ef_displayOnMobile', true,
	array('checked'=>(boolean)get_option('ef_displayOnMobile'))); ?>

	        <p class="explanation"><?php echo __('Display calendar notifications on mobile devices (defined as browser viewports smaller than or equal to 768px).'); ?></p>
	    </div>
	</div>	
	
</fieldset><hr>
<p><small><strong>*This plugin uses a single text-based cookie to detect if a user has dismissed a feed notification within the duration of time set n the "Notification dismissal" option (in which case the user will not receive any further notifications). Consult relevant laws and reguations to determine if your jurisdiction (or that of your users) requires consent for the use of cookies. For information regarding the UK's Privacy and Electronic Communications Regulations, visit <a href="https://ico.org.uk/for-organisations/guide-to-pecr/cookies-and-similar-technologies/" target="_blank">ico.org.uk</a>. This plugin does not currently support a consent process. You may disable notifications and continue to use the plugin shortcode widget (which does not use cookies) if you feel that the notification cookies put you or your users at risk.</small></p>