=== WP Project Manager ===
Contributors: tareq1988
Donate Link: http://tareq.wedevs.com/donate/
Tags: project, manager, project manager, project management, todo, todo list, task, basecamp, milestone, message, file, comment, client, team, tracking, planning, lists, reporting
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress Project Management plugin. Manage your project simply with the *right* tools and options.

== Description ==

A WordPress Project Management plugin. Manage your project simply with the \*right\* tools and options. It gives you the taste of basecamp, just inside your loved WordPress.

= Features =
 * Projects
   * Create a new project
   * Set title and details of that project
   * Assign users for that project
 * Messages
   * Messages are used for discussing about the project with co-workers of  that project
   * You can add attachments on messages
   * Comments can be made for discussion
 * To-do List
   * Add as many to-do list as you want with title and description
   * Add tasks, assign users, assign due date
   * See progressbar on the list
   * Add comments on individual to-do lists and to-do's
   * Mark to-do as complete/incomplete
 * Milestone
   * Create milestone
   * Assign messages and to-do list on milestone
   * 3 types of milestones are there, a) upcoming, b) completed and c) late milstone
 * Files
   * See all the uploaded files on messages and comments in one place and navigate to invidual attached threads.

= Quick Demo Video =
[youtube http://www.youtube.com/watch?v=tETwpwjSA4Q]

= Detailed Walkthrough =
[youtube http://www.youtube.com/watch?v=lR61ARrGb28]

= Extensions =
* [WP Project Manger Frontend](http://wedevs.com/plugin/wp-project-manager-frontend/) (*premium*) - brings the plugin functionality to your site frontend.

= Contribute =
This may have bugs and lack of many features. If you want to contribute on this project, you are more than welcome. Please fork the repository from [Github](https://github.com/tareq1988/wp-project-manager).

= Author =
Brought to you by [Tareq Hasan](http://tareq.wedevs.com) from [weDevs](http://wedevs.com)

= Donate =
Please [donate](http://tareq.wedevs.com/donate/) for this awesome plugin to continue it's development to bring more awesome features.

= Contribution =
* French translation by Corentin allard
* Dutch translation by eskamedia
* Brazilian Portuguese translation by Anderson
* German translation by Alexander Pfabel
* Spanish translation by Luigi Libet
* Indonesian translation by hirizh
* Polish translation by Jacek Synowiec

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

== Frequently Asked Questions ==

= Q. Why doesn't it shows up in frontend =
A. Currently all the project management options resides in the admin panel. No functionality shows up in frontend right now.

= Q. Who can create projects? =
A. Only Editors and Admin's can projects and edit them.

= Q. Who can create tasklist, todo, messages or milestone? =
A. Admins and every co-workers from a project can create these.

= Q. Can every member see every project? =
A. Only admins (editor/administrator) can see all of them. Other user roles can only see their assigned projects.

= Q. Can the plugin be extended? =
A. Sure, lots of actions and filters are added by default and will add more of them.

= Q. Found a bug =
A. Found any bugs? Please create an [issue](https://github.com/tareq1988/wp-project-manager/issues) on github.

== Screenshots ==

1. Project dashboard. You can see all your projects from here and can create new one.
2. Single project page, default tab shows all the activities from current project.
3. Create a new project with title and description. Set milestone, attach files and notify users.
4. Single message thread. Discuss with other members. Add comments, attachments and notify users.
5. All messages from this project listed here.
6. View all To-do List and to-do's. See progress on each to-do list. Task assigned to user, due date, comment count and completed to-do's.
7. Create a new To-do list. Set title, description and milestone.
8. Create a new to-do in a to-do list with to-do details, due date and assigned user.
9. Completed, late and up-coming milestone with assigned to-do list and messages.
10. All attached files from message, comment, to-do can be found here.

== Changelog ==

= 0.4.3 =

* new: Spanish translation
* new: German translation
* new: Indonesian translation
* fix: milestone datepicker issue
* fix: some typo fixes
* improved: comment count next to tasklists

= 0.4.2 =

* bug fix: project activity/comments on frontend widget
* bug fix: project activity/comments on comment rss
* bug fix: number of milestones
* improved: plugin textdomain loading
* new: project task progressbar on project listing
* new: tasklist sorting
* new: task sorting
* new: Dutch translation language added
* new: Brazilian Portuguese language added

= 0.4.1 =

* bug fix: attachment association problem on comment update
* bug fix: error on message update

= 0.4 =

* improved: default email format changed to 'text/plain' from 'text/html'
* improved: toggle added on user notification selection
* improved: only date was showing on single message details, time added
* improved: some filters added on URLs
* bug fix: actual file url hidden on files tab for privacy
* bug fix: any user could edit any users message
* bug fix: any user could delete any users message
* new: admin settings page added
* new: email template added
* new: French translation added
* new: file upload size settings added

= 0.3.1 =

* comment update bug fix
* project activity is now grouped by date
* "load more" button added on project activity
* some function documentation added.

= 0.3 =

* Translation capability added
* Attachment security added. All files are now served via a proxy script
  for security with permission checking.

= 0.2.1 =

* Comments display error fix

= 0.2 =

* Remove comments from listing publicly
* Post types are hidden from search

= 0.1 =
Initial version released


== Upgrade Notice ==

Nothing here
