<?php
class CalendarFeedPlugin extends Omeka_Plugin_AbstractPlugin
{

	protected $_hooks = array(
		'install',
		'uninstall',
		'config_form',
		'config',
		'public_footer',
		'initialize',
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

	/**
	 * Add Shortcode.
	 */

    public function hookInitialize()
    {
        add_shortcode('calendarfeed', array($this, 'cf_embedCalendarFeed'));
    }

    public function cf_embedCalendarFeed($args, $view)
    {
        return '<div id="cf-upcoming-container" style="display:none;"><div id="cf-upcoming-header"><img src="/plugins/CalendarFeed/assets/cal-trans.png"><h3>Upcoming Events</h3><div id="cf-upcoming-feed-link"><span></span></div></div><div id="cf-upcoming-inner"><ul></ul></div></div>';
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

	        // styles and notification container
	        jQuery('body').prepend('<style type="text/css">#cf-notify-container{display:none;background:#fff;box-shadow:0 0 .15em #333;position:relative;top:0;z-index:999;line-height:1.25em;padding:7px 0;}#cf-notify-icon-close{cursor:pointer;float:right;height:1em;width:auto;position:relative;right:7px;top:0px;}#cf-notify-inner{margin:0 auto;max-width:50em;}#cf-notify-content{padding-left:75px;}#cf-notify-icon-cal{height:60px;width:60px; padding-left:5px; float:left;}#cf-notify-host{font-size:.8em;font-style:italic;color:#777;}#cf-notify-content a{color:inherit;border:none;text-decoration:none;}#cf-notify-date{font-size:.9em;}#cf-upcoming-container{background: #eaeaea;padding: 0.5em;border-radius: .25em;}#cf-upcoming-container h3{}#cf-upcoming-inner{}#cf-upcoming-feed-link{color: #777;font-size: .9em;font-style: italic;}#cf-upcoming-feed-link a{color:inherit;}#cf-upcoming-container a{border:none;text-decoration:none;}#cf-upcoming-container h3{margin:0;}#cf-upcoming-header{}#cf-upcoming-header h3,#cf-upcoming-feed-link{padding-left:60px;}#cf-upcoming-header img{width:50px;height:auto;float:left;}  </style><div id="cf-notify-container"></div>');
		    
		    // calendar embed
		    jQuery.each(data.feed.entries,function(e,event){
			   console.log(event); 
			   jQuery('#cf-upcoming-inner ul').append('<li><a href="'+event.link+'">'+event.title+'</a><br><span>'+formatDate(event.publishedDate)+'</span></li>');
		    });
		    jQuery('#cf-upcoming-feed-link span').html('via: <a href="'+data.feed.link+'">'+hostname(data.feed.link))+'</a>';
		    jQuery('#cf-upcoming-container').fadeIn();
		    
		    
		    
		    // event notification 	        
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