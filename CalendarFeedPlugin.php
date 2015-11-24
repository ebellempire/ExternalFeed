<?php
class CalendarFeedPlugin extends Omeka_Plugin_AbstractPlugin
{

	protected $_hooks = array(
		'install',
		'uninstall',
		'config_form',
		'config',
		'public_footer',
	);


	protected $_options = array(
		'cf_displayOnMobile'=>0,
		'cf_notify'=>1,
		'cf_rssfeed'=>null,
	);


	/*
    ** Plugin options
    */

	public function hookConfigForm()
	{
		require dirname(__FILE__) . '/config_form.php';
	}

	public function hookConfig()
	{
		set_option('cf_displayOnMobile', (bool)(int)$_POST['cf_displayOnMobile']);
		set_option('cf_notify', (bool)(int)$_POST['cf_notify']);
		set_option('cf_rssfeed', $_POST['cf_rssfeed']);
	}

	/*
	** Public default scripts and styles
	*/

	public function hookPublicFooter()
	{

		echo cf_footerScripts();

	}

	/**
	 * Install the plugin.
	 */
	public function hookInstall()
	{
		$this->_installOptions();

	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall()
	{
		$this->_uninstallOptions();

	}
}

function cf_footerScripts()
{	
	$feed=get_option('cf_rssfeed');
	$show_mobile=get_option('cf_displayOnMobile');
	$show_notifications=get_option('cf_notify');
	$breakpoint='768';
	?>
	
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">google.load("feeds", "1");</script>            

	<?php

	if(!$show_mobile){ ?>
	
	<style type="text/css">
	@media all and (max-width:<?php echo $breakpoint;?>px){
		#cf-notify-container{display:none !important;}
	}
	</style>
	<?php }
	
	if($show_notifications){	
		cf_calendarNotifications($feed);	
	}
}

function cf_calendarNotifications($feed)
{ 
	if($feed): ?>
	<script>
	function stripHTML(dirtyString) {
	    var container = document.createElement('div');
	    container.innerHTML = dirtyString;
	    return container.textContent || container.innerText;
	}

	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	}
	
	function formatDate(date){
		var date = new Date(date);
		var formattedDate = date.toLocaleString(navigator.language, {month:'long', day: 'numeric', year:'numeric', hour: '2-digit', minute:'2-digit'});	
		return formattedDate;	
	}
	
	function hostname(link){
		var parser = document.createElement('a');
		parser.href = link;	
		return parser.hostname;	
	}
        	
	jQuery(jQuery(window)).load(function(){
	    var feed = new google.feeds.Feed('<?php echo $feed;?>');
	    feed.load(function (data) {
	        
	        if(getCookie('cf-notify-closed')!=='true'){
		        
				var entry = data.feed.entries[0];
		        
		        // content
		        jQuery('#cf-notify-container').html('<div id="cf-notify-inner"><img id="cf-notify-icon-cal" src="/plugins/CalendarFeed/assets/cal-trans.png"><div id="cf-notify-content"><a href="'+entry.link+'" target="_blank"><strong>'+entry.title+'</strong></a><br><span id="cf-notify-date">'+formatDate(entry.publishedDate)+'</span><br><span id="cf-notify-host">Learn more at <a style="" href="'+entry.link+'" target="_blank">'+hostname(entry.link)+'</a></span></div></div>');
		        
		        // doing some tricks to get the height to work well with the animation
		        var computedHeight=jQuery('#cf-notify-container').height();
		        jQuery('#cf-notify-container').css("height",computedHeight).prepend('<img alt="close event notification" id="cf-notify-icon-close" src="/plugins/CalendarFeed/assets/close.png">').slideDown('slow','swing');
		        
		        // close button
				jQuery('#cf-notify-icon-close').click(function(e){
					var container=document.getElementById(this.parentElement.id);
					jQuery(container).slideUp('fast','swing');
					setCookie('cf-notify-closed','true',1);
		
				});
	        }else{
		        console.log('Notifications dismissed for one day.');
	        }
	        
	    });
	});
	</script>	
	<?php endif; 
}		