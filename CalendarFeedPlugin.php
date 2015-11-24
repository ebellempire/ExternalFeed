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
	** Public display
	*/

	public function hookPublicFooter()
	{

		echo cf_calendar_scripts();

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

function cf_calendar_scripts($option=null)
{	
	$feed=get_option('cf_rssfeed');
	$show_mobile=get_option('cf_displayOnMobile');
	$breakpoint='768';
	?>
	
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">google.load("feeds", "1");</script>            
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
        	
	jQuery(jQuery(window)).load(function(){
	    var feed = new google.feeds.Feed('<?php echo $feed;?>');
	    feed.load(function (data) {
	        
	        if(getCookie('notify-closed')!=='true'){
				var entry = data.feed.entries[0];
				var date = new Date(entry.publishedDate);
				var formattedDate = date.toLocaleString(navigator.language, {month:'long', day: 'numeric', year:'numeric', hour: '2-digit', minute:'2-digit'});
				var parser = document.createElement('a');
				parser.href = entry.link;
		        
		        // styles and container
		        jQuery('body').prepend('<style type="text/css">#notify-container{display:none;background:#fff;box-shadow:0 0 .15em #333;position:relative;top:0;z-index:999;line-height:1.25em;padding:7px 0;}#notify-icon-close{cursor:pointer;float:right;height:1em;width:auto;position:relative;right:7px;top:7px;}#notify-inner{margin:0 auto;max-width:50em;}#notify-content{padding-left:75px;}#notify-icon-cal{height:60px;width:60px; padding-left:5px; float:left;}#notify-host{font-size:.8em;font-style:italic;color:#777;}#notify-content a{color:inherit;border:none;text-decoration:none;}#notify-date{font-size:.9em;}</style><div id="notify-container"></div>');
		        
		        // content
		        jQuery('#notify-container').html('<div id="notify-inner"><img id="notify-icon-cal" src="/plugins/CalendarFeed/assets/cal.png"><div id="notify-content"><a href="'+entry.link+'" target="_blank"><strong>'+entry.title+'</strong></a><br><span id="notify-date">'+formattedDate+'</span><br><span id="notify-host">Learn more at <a style="" href="'+entry.link+'" target="_blank">'+parser.hostname+'</a></span></div></div>');
		        
		        // doing some tricks to get the height to work well with the animation
		        var computedHeight=jQuery('#notify-container').height();
		        jQuery('#notify-container').css("height",computedHeight).prepend('<img alt="close event notification" id="notify-icon-close" src="/plugins/CalendarFeed/assets/close.png">').slideDown('slow','swing');
		        
		        // close button
				jQuery('#notify-icon-close').click(function(e){
					var container=document.getElementById(this.parentElement.id);
					jQuery(container).slideUp('fast','swing');
					setCookie('notify-closed','true',1);
		
				});
	        }else{
		        console.log('Notifications dismissed for one day.');
	        }
	        
	    });
	});
	</script>
	<?php

	if(!$show_mobile){ ?>
	
	<style type="text/css">
	@media all and (max-width:<?php echo $breakpoint;?>px){
		#notify#calendar{display:none !important;}
	}
	</style>
	<?php }

}