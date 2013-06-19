<?php
/* 
 * Remember that this file is only used if you have chosen to override event pages with formats in your event settings!
 * You can also override the single event page completely in any case (e.g. at a level where you can control sidebars etc.), as described here - http://codex.wordpress.org/Post_Types#Template_Files
 * Your file would be named single-event.php
 */
/*
 * This page displays a single event, called during the the_content filter if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */
global $EM_Event;
/* @var $EM_Event EM_Event */

//function single_event_details () {
$args = array(
	'Title' => $EM_Event->output('#_EVENTLINK'),
	'Date' => $EM_Event->output('#D. #M #j, #Y #@_{ \u\n\t\i\l M j Y}'),
	'Time' => $EM_Event->output('#_12HSTARTTIME - #_12HENDTIME'),
	'Location' => '<span class="event-location name">' . $EM_Event->output('#_LOCATIONLINK') . '</span> <span class="event-location address">' . $EM_Event->output('#_LOCATIONADDRESS') . '</span> <span class="event-location city">' . $EM_Event->output('#_LOCATIONTOWN') . '</span> <span class="event-location state">' . $EM_Event->output('#_LOCATIONSTATE') . '</span> <span class="event-location region">' . $EM_Event->output('#_LOCATIONREGION') . '</span>',
	'Address' => $EM_Event->output('#_LOCATIONADDRESS'),
	'Map' => $EM_Event->output('#_LOCATIONMAP'),
	'Categories' => $EM_Event->output('#_EVENTCATEGORIES'),
	'Tags' => $EM_Event->output('#_EVENTTAGS'),
	'Body' => $EM_Event->output('#_EVENTNOTES'),
	'Image' => $EM_Event->output('#_EVENTIMAGE'),
	'Edit' => $EM_Event->output('#_EDITEVENTLINK'),
	'Attendees' => $EM_Event->output('#_BOOKINGATTENDEES'),
	'Related' => $EM_Event->output('#_CATEGORYNEXTEVENTS'),
	);
?>

<div class="event-wrapper">

	<section class="clearfix">
		<div class="sidebar three columns event-details">
			<h4 class="event-heading">Date/Time</h4>
			<div class="event-dates-times">
			<span class="event-date"><?php echo $args['Date']; ?></span>
			<span class="event-time"><?php echo $args['Time']; ?></span>
			</div>

			<?php if($args['Location']) { ?>
			<h4 class="event-heading">Location</h4>
			
			<div class="event-location map"><?php echo $args['Map']; ?></div>
			<?php } ?>

			<div class="event-categories categories"><?php echo $args['Categories']; ?></div>

			<div class="event-tags tags"><?php echo $args['Tags']; ?></div>

		</div>
		<div class="event-body nine columns">
			<div class="event-image"><?php echo $args['Image']; ?></div>
			<?php echo $args['Body']; ?>
		</div>
	</section>
	<footer class="clearfix twelve columns">
		{logged_in}<div class="event-edit-link"><?php echo $args['Edit']; ?></div>{/logged_in}

		{has_bookings}
		<h4 class="event-heading">RSVPs</h4>
		<div class="rsvp-list"><?php echo $args['Attendees']; ?></div>
		{/has_bookings}

		<h4 class="event-heading">Related Events</h4>
		<div class="related-events-list"><?php echo $args['Related']; ?></div>
	</footer>

</div>

<?php// } ?>