<?php
class ExternalFeedPlugin extends Omeka_Plugin_AbstractPlugin
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
		'ef_displayOnMobile'=>0,
		'ef_notify'=>1,
		'ef_rssfeed'=>null,
		'ef_isCalendar'=>0,
		'ef_cookieExpiration'=>1,
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
		set_option('ef_displayOnMobile', (bool)(int)$_POST['ef_displayOnMobile']);
		set_option('ef_notify', (bool)(int)$_POST['ef_notify']);
		set_option('ef_isCalendar', (bool)(int)$_POST['ef_isCalendar']);
		set_option('ef_cookieExpiration', (int)$_POST['ef_cookieExpiration']);
		set_option('ef_rssfeed', $_POST['ef_rssfeed']);
	}

	/*
	** Public default scripts and styles
	*/

	public function hookPublicFooter()
	{

		echo ef_footerScripts();

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
        add_shortcode('externalfeed', array($this, 'ef_embedExternalFeed'));
    }
    
    public function ef_embedExternalFeed($args, $view)
    {
        // converts shortcode into hidden empty HTML container, which is populated by the footer scripts
        return '<div id="ef-widget-container" style="display:none;"><div id="ef-widget-header"><img src="'.ef_feedIcon().'"><h3>'.ef_feedLabel().'</h3><div id="ef-widget-feed-link"><span></span></div></div><div id="ef-widget-inner"><ul></ul></div></div>';
    }	
    

	
}

function ef_feedIcon()
{
   return get_option('ef_isCalendar') ? '/plugins/CalendarFeed/assets/cal-trans.png' : '/plugins/CalendarFeed/assets/feed-trans.png';
}

function ef_feedLabel()
{
   return get_option('ef_isCalendar') ? 'Upcoming Events' : 'Recent News';
}

function ef_datePrefix()
{
   return get_option('ef_isCalendar') ? 'Save the date: ' : 'Posted: ';
}

function ef_footerScripts()
{	
	$feed=get_option('ef_rssfeed');
	$show_mobile=get_option('ef_displayOnMobile');
	$show_notifications=get_option('ef_notify');
	$breakpoint='768';
	?>
	
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">google.load("feeds", "1");</script>            

	<?php

	if(!$show_mobile){ ?>
	
	<style type="text/css">
	@media all and (max-width:<?php echo $breakpoint;?>px){
		#ef-notify-container{display:none !important;}
	}
	</style>
	<?php }
	
	ef_feedActions($feed,$show_notifications,ef_feedIcon(),ef_feedLabel(),ef_datePrefix());	

}

function ef_feedActions($feed,$show_notifications,$icon,$label,$datePrefix)
{ 
	if($feed): ?>
	<script>
	var show_notifications ='<?php echo $show_notifications; ?>';
	var icon ='<?php echo $icon;?>';	
	var label ='<?php echo $label;?>';	
	var is_cal ='<?php echo get_option('ef_isCalendar');?>';	
	var exdays='<?php echo get_option('ef_cookieExpiration');?>';
	

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
		var date_prefix ='<?php echo $datePrefix;?>';
		var date = new Date(date);
		format=[];
		var options={month:'long', day: 'numeric', year:'numeric'};
		if(is_cal){
			options.hour= '2-digit';
			options.minute= '2-digit';
		}
		format=options;
		var formattedDate = date.toLocaleString(navigator.language, format);	
		return date_prefix+formattedDate;	
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
	        jQuery('body').prepend('<style type="text/css">#ef-notify-container{display:none;background:#fff;box-shadow:0 0 .15em #333;position:relative;top:0;z-index:999;line-height:1.25em;padding:7px 0;}#ef-notify-icon-close{cursor:pointer;float:right;height:1em;width:auto;position:relative;right:7px;top:0px;}#ef-notify-inner{margin:0 auto;max-width:50em;}#ef-notify-content{padding-left:75px;}#ef-notify-icon-cal{height:60px;width:60px; padding-left:5px; float:left;}#ef-notify-host{font-size:.8em;font-style:italic;color:#777;}#ef-notify-content a{color:inherit;border:none;text-decoration:none;}#ef-notify-date{font-size:.9em;}#ef-widget-container{background: #eaeaea;padding: 0.5em;border-radius: .25em;}#ef-widget-container h3{}#ef-widget-inner{}#ef-widget-feed-link{color: #777;font-size: .9em;font-style: italic;}#ef-widget-feed-link a{color:inherit;}#ef-widget-container a{border:none;text-decoration:none;}#ef-widget-container h3{margin:0;}#ef-widget-header{}#ef-widget-header h3,#ef-widget-feed-link{padding-left:60px;}#ef-widget-header img{width:50px;height:auto;float:left;}  </style><div id="ef-notify-container"></div>');
		    
		    // feed embed
		    jQuery.each(data.feed.entries,function(e,event){
			   jQuery('#ef-widget-inner ul').append('<li><a href="'+event.link+'">'+event.title+'</a><br><span>'+formatDate(event.publishedDate)+'</span></li>');
		    });
		    jQuery('#ef-widget-feed-link span').html('via: <a href="'+data.feed.link+'">'+hostname(data.feed.link))+'</a>';
		    jQuery('#ef-widget-container').fadeIn();
		    
		    
		    // feed notification 	        
	        if(show_notifications && getCookie('ef-notify-closed')!=='true'){
		        
				var entry = data.feed.entries[0];
		        
		        // content
		        jQuery('#ef-notify-container').html('<div id="ef-notify-inner"><img id="ef-notify-icon-cal" src="'+icon+'"><div id="ef-notify-content"><a href="'+entry.link+'" target="_blank"><strong>'+entry.title+'</strong></a><br><span id="ef-notify-date">'+formatDate(entry.publishedDate)+'</span><br><span id="ef-notify-host">Learn more at <a style="" href="'+entry.link+'" target="_blank">'+hostname(entry.link)+'</a></span></div></div>');
		        
		        // doing some tricks to get the height to work well with the animation
		        var computedHeight=jQuery('#ef-notify-container').height();
		        jQuery('#ef-notify-container').css("height",computedHeight).prepend('<img alt="close event notification" id="ef-notify-icon-close" src="/plugins/CalendarFeed/assets/close.png">').slideDown('slow','swing');
		        
		        // close button
				jQuery('#ef-notify-icon-close').click(function(e){
					var container=document.getElementById(this.parentElement.id);
					jQuery(container).slideUp('fast','swing');
					setCookie('ef-notify-closed','true', exdays);
		
				});
	        }else{
		        console.log('Notifications dismissed until cookie expires or is deleted by user.');
	        }
	        
	    });
	});
	</script>	
	<?php endif; 
}		