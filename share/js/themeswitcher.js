/**
 * http://www.kelvinluck.com/assets/jquery/styleswitch/toggle.html
 * require jquery/plugins/stylesheetToggle.j
 * <link rel="stylesheet" type="text/css" href="styles1.css" title="styles1" media="screen" />
	<link rel="alternate stylesheet" type="text/css" href="styles2.css" title="styles2" media="screen" />
	<link rel="alternate stylesheet" type="text/css" href="styles3.css" title="styles3" media="screen" />
	<p><a href="serversideSwitch.html" id="toggler">Toggle</a> between stylesheets.</p>
	<ul>
		<li><a href="serversideSwitch.html?style=style1" rel="styles1" class="styleswitch">styles1</a></li>
		<li><a href="serversideSwitch.html?style=style2" rel="styles2" class="styleswitch">styles2</a></li>
		<li><a href="serversideSwitch.html?style=style3" rel="styles3" class="styleswitch">styles3</a></li>
	</ul>
 */
$(function()
	{
		// Call stylesheet init so that all stylesheet changing functions 
		// will work.
		$.stylesheetInit();
		
		// This code loops through the stylesheets when you click the link with 
		// an ID of "toggler" below.
		$('#toggler').bind(
			'click',
			function(e)
			{
				$.stylesheetToggle();
				return false;
			}
		);
		
		// When one of the styleswitch links is clicked then switch the stylesheet to
		// the one matching the value of that links rel attribute.
		$('.styleswitch').bind(
			'click',
			function(e)
			{
				$.stylesheetSwitch(this.getAttribute('rel'));
				return false;
			}
		);
	}
);
