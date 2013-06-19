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
	'Location' => '<p><span class="event-location name">' . $EM_Event->output('#_LOCATIONLINK') . '</span></p><p><span class="event-location address">' . $EM_Event->output('#_LOCATIONADDRESS') . '</span> <span class="event-location city">' . $EM_Event->output('#_LOCATIONTOWN') . '</span> <span class="event-location state">' . $EM_Event->output('#_LOCATIONSTATE') . '</span></p><p><span class="event-location region">' . $EM_Event->output('#_LOCATIONREGION') . '</span></p>',
	'Address' => $EM_Event->output('#_LOCATIONADDRESS'),
	'Map' => $EM_Event->output('#_LOCATIONMAP'),
	'Categories' => $EM_Event->output('#_EVENTCATEGORIES'),
	'Tags' => $EM_Event->output('#_EVENTTAGS'),
	'Body' => $EM_Event->output('#_EVENTNOTES'),
	'Image' => $EM_Event->output('#_EVENTIMAGE'),
	'Edit' => $EM_Event->output('#_EDITEVENTLINK'),
	'Attendees' => $EM_Event->output('#_BOOKINGATTENDEES'),
	'Related' => $EM_Event->output('#_CATEGORYNEXTEVENTS'),
	'Contact' => $EM_Event->output('#_CONTACTUSERNAME'),
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

			<div class="event-location address"><?php echo $args['Location']; ?></div>
			<?php } ?>

			<div class="meta">
				<div class="event-categories categories"><?php echo $args['Categories']; ?></div>
				<div class="event-tags tags"><?php echo $args['Tags']; ?></div>
				<div class="event-contact">Added by: <?php the_author_posts_link(); ?></div>
			</div>

		</div>
		<div class="event-body nine columns">
			<div class="event-image post-thumbnail"><?php echo $args['Image']; ?></div>

			<div class="event-description clearfix"><?php echo $args['Body']; ?></div>

			<?php if($args['Location']) { ?>
			<div class="event-location map"><?php echo $args['Map']; ?></div>
			<?php } ?>
		</div>
	</section>
	<footer class="clearfix twelve columns">

		<h4 class="event-heading">Related Events</h4>
		<div class="related-events-list"><?php echo $args['Related']; ?></div>

		<div class="event-edit-link meta"><?php echo $args['Edit']; ?></div>

	</footer>

</div>

<?php// } ?>