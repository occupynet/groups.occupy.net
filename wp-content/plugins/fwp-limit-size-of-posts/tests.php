<?php
include('fwp-limit-size-of-posts.php');
include('../../../wp-includes/formatting.php');

$tests = array(
	0 => array(
		'params' => array(),
		'input' => '
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec fringilla dictum dui, sit amet rhoncus turpis fringilla eu. Etiam lobortis massa in massa consectetur ultricies. Morbi eu magna augue, tempus eleifend felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum est et purus bibendum a lacinia libero tristique. Nulla eu vulputate magna. Pellentesque lectus metus, elementum ut gravida ac, malesuada ut nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer mattis consectetur risus nec euismod. In vulputate tincidunt arcu at congue. Nulla ante metus, condimentum eu suscipit in, tempor eget metus. Morbi faucibus ligula et tortor ultrices a lacinia nibh tempus. Sed sapien sem, blandit ut egestas vel, commodo ut ligula. Ut ut lorem libero, non eleifend turpis. Integer rutrum dui ut massa accumsan accumsan. Vestibulum porttitor interdum leo, a ultrices ante ullamcorper non. Duis venenatis, quam eget imperdiet venenatis, ligula velit gravida dui, et lobortis purus ante nec diam. Sed vestibulum condimentum justo sed fermentum. Vestibulum dictum felis ut metus facilisis et hendrerit risus ultrices. Fusce sed arcu odio.

Nunc adipiscing quam non eros pharetra cursus. Ut volutpat suscipit dignissim. Sed consequat magna quis sapien consequat imperdiet. Donec metus diam, vestibulum sed accumsan at, aliquet vitae lacus. In consectetur ante ac diam mattis tincidunt. Mauris est erat, egestas a viverra ac, ornare fermentum ipsum. Maecenas a massa id magna iaculis facilisis. Donec in augue mauris. Vivamus a velit eget purus scelerisque molestie. In hac habitasse platea dictumst. Nullam nulla sem, pharetra id feugiat ac, facilisis a nibh. Pellentesque ultrices tristique dictum. In tempor bibendum leo, non dictum arcu cursus eget. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.

Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam sit amet bibendum odio. Cras posuere, nunc a accumsan porta, risus massa tempus massa, eu aliquam libero dolor quis felis. Curabitur rutrum risus vitae metus commodo non blandit risus feugiat. Donec sapien nulla, vehicula sit amet tempus eu, laoreet a quam. Phasellus laoreet interdum massa, vel volutpat sapien aliquam a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce iaculis rutrum elementum. Morbi sed nisl sit amet massa imperdiet scelerisque. Mauris varius augue sapien, a fermentum eros. Etiam tristique fermentum mauris ac convallis. Vivamus a lacinia lorem. Vivamus ut tincidunt augue. Duis sem dolor, molestie sed mattis tempus, fringilla a quam. Proin id dolor nisl.
',
		'expect' => '
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec fringilla dictum dui, sit amet rhoncus turpis fringilla eu. Etiam lobortis massa in massa consectetur ultricies. Morbi eu magna augue, tempus eleifend felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum est et purus bibendum a lacinia libero tristique. Nulla eu vulputate magna. Pellentesque lectus metus, elementum ut gravida ac, malesuada ut nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer mattis consectetur risus nec euismod. In vulputate tincidunt arcu at congue. Nulla ante metus, condimentum eu suscipit in, tempor eget metus. Morbi faucibus ligula et tortor ultrices a lacinia nibh tempus. Sed sapien sem, blandit ut egestas vel, commodo ut ligula. Ut ut lorem libero, non eleifend turpis. Integer rutrum dui ut massa accumsan accumsan. Vestibulum porttitor interdum leo, a ultrices ante ullamcorper non. Duis venenatis, quam eget imperdiet venenatis, ligula velit gravida dui, et lobortis purus ante nec diam. Sed vestibulum condimentum justo sed fermentum. Vestibulum dictum felis ut metus facilisis et hendrerit risus ultrices. Fusce sed arcu odio.

Nunc adipiscing quam non eros pharetra cursus. Ut volutpat suscipit dignissim. Sed consequat magna quis sapien consequat imperdiet. Donec metus diam, vestibulum sed accumsan at, aliquet vitae lacus. In consectetur ante ac diam mattis tincidunt. Mauris est erat, egestas a viverra ac, ornare fermentum ipsum. Maecenas a massa id magna iaculis facilisis. Donec in augue mauris. Vivamus a velit eget purus scelerisque molestie. In hac habitasse platea dictumst. Nullam nulla sem, pharetra id feugiat ac, facilisis a nibh. Pellentesque ultrices tristique dictum. In tempor bibendum leo, non dictum arcu cursus eget. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.

Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam sit amet bibendum odio. Cras posuere, nunc a accumsan porta, risus massa tempus massa, eu aliquam libero dolor quis felis. Curabitur rutrum risus vitae metus commodo non blandit risus feugiat. Donec sapien nulla, vehicula sit amet tempus eu, laoreet a quam. Phasellus laoreet interdum massa, vel volutpat sapien aliquam a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce iaculis rutrum elementum. Morbi sed nisl sit amet massa imperdiet scelerisque. Mauris varius augue sapien, a fermentum eros. Etiam tristique fermentum mauris ac convallis. Vivamus a lacinia lorem. Vivamus ut tincidunt augue. Duis sem dolor, molestie sed mattis tempus, fringilla a quam. Proin id dolor nisl.
',
	),
	'no-limits-tags-in' => array(
		'params' => array(),
		'input' => '
<p>Lorem ipsum dolor sit <strong>amet</strong>, consectetur adipiscing elit. Donec fringilla dictum dui, sit amet rhoncus turpis fringilla eu. Etiam lobortis massa in massa consectetur ultricies. Morbi eu magna augue, tempus eleifend felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum est et purus bibendum a lacinia libero tristique. Nulla eu vulputate magna. Pellentesque lectus metus, elementum ut gravida ac, malesuada ut nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer mattis consectetur risus nec euismod. In vulputate tincidunt arcu at congue. Nulla ante metus, condimentum eu suscipit in, tempor eget metus. Morbi faucibus ligula et tortor ultrices a lacinia nibh tempus. Sed sapien sem, blandit ut egestas vel, commodo ut ligula. Ut ut lorem libero, non eleifend turpis. Integer rutrum dui ut massa accumsan accumsan. Vestibulum porttitor interdum leo, a ultrices ante ullamcorper non. Duis venenatis, quam eget imperdiet venenatis, ligula velit gravida dui, et lobortis purus ante nec diam. Sed vestibulum condimentum justo sed fermentum. Vestibulum dictum felis ut metus facilisis et hendrerit risus ultrices. Fusce sed arcu odio.</p>

<p>Nunc adipiscing quam non eros pharetra cursus. Ut volutpat suscipit dignissim. Sed consequat magna quis sapien consequat imperdiet. Donec metus diam, vestibulum sed accumsan at, aliquet vitae lacus. In consectetur ante ac diam mattis tincidunt. Mauris est erat, egestas a viverra ac, ornare fermentum ipsum. Maecenas a massa id magna iaculis facilisis. Donec in augue mauris. Vivamus a velit eget purus scelerisque molestie. In hac habitasse platea dictumst. Nullam nulla sem, pharetra id feugiat ac, facilisis a nibh. Pellentesque ultrices tristique dictum. In tempor bibendum leo, non dictum arcu cursus eget. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>

<p>Class <em>aptent <strong>taciti</strong></em> sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam sit amet bibendum odio. Cras posuere, nunc a accumsan porta, risus massa tempus massa, eu aliquam libero dolor quis felis. Curabitur rutrum risus vitae metus commodo non blandit risus feugiat. Donec sapien nulla, vehicula sit amet tempus eu, laoreet a quam. Phasellus laoreet interdum massa, vel volutpat sapien aliquam a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce iaculis rutrum elementum. Morbi sed nisl sit amet massa imperdiet scelerisque. Mauris varius augue sapien, a fermentum eros. Etiam tristique fermentum mauris ac convallis. Vivamus a lacinia lorem. Vivamus ut tincidunt augue. Duis sem dolor, molestie sed mattis tempus, fringilla a quam. Proin id dolor nisl.</p>
',
		'expect' => '
<p>Lorem ipsum dolor sit <strong>amet</strong>, consectetur adipiscing elit. Donec fringilla dictum dui, sit amet rhoncus turpis fringilla eu. Etiam lobortis massa in massa consectetur ultricies. Morbi eu magna augue, tempus eleifend felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum est et purus bibendum a lacinia libero tristique. Nulla eu vulputate magna. Pellentesque lectus metus, elementum ut gravida ac, malesuada ut nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer mattis consectetur risus nec euismod. In vulputate tincidunt arcu at congue. Nulla ante metus, condimentum eu suscipit in, tempor eget metus. Morbi faucibus ligula et tortor ultrices a lacinia nibh tempus. Sed sapien sem, blandit ut egestas vel, commodo ut ligula. Ut ut lorem libero, non eleifend turpis. Integer rutrum dui ut massa accumsan accumsan. Vestibulum porttitor interdum leo, a ultrices ante ullamcorper non. Duis venenatis, quam eget imperdiet venenatis, ligula velit gravida dui, et lobortis purus ante nec diam. Sed vestibulum condimentum justo sed fermentum. Vestibulum dictum felis ut metus facilisis et hendrerit risus ultrices. Fusce sed arcu odio.</p>

<p>Nunc adipiscing quam non eros pharetra cursus. Ut volutpat suscipit dignissim. Sed consequat magna quis sapien consequat imperdiet. Donec metus diam, vestibulum sed accumsan at, aliquet vitae lacus. In consectetur ante ac diam mattis tincidunt. Mauris est erat, egestas a viverra ac, ornare fermentum ipsum. Maecenas a massa id magna iaculis facilisis. Donec in augue mauris. Vivamus a velit eget purus scelerisque molestie. In hac habitasse platea dictumst. Nullam nulla sem, pharetra id feugiat ac, facilisis a nibh. Pellentesque ultrices tristique dictum. In tempor bibendum leo, non dictum arcu cursus eget. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>

<p>Class <em>aptent <strong>taciti</strong></em> sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam sit amet bibendum odio. Cras posuere, nunc a accumsan porta, risus massa tempus massa, eu aliquam libero dolor quis felis. Curabitur rutrum risus vitae metus commodo non blandit risus feugiat. Donec sapien nulla, vehicula sit amet tempus eu, laoreet a quam. Phasellus laoreet interdum massa, vel volutpat sapien aliquam a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce iaculis rutrum elementum. Morbi sed nisl sit amet massa imperdiet scelerisque. Mauris varius augue sapien, a fermentum eros. Etiam tristique fermentum mauris ac convallis. Vivamus a lacinia lorem. Vivamus ut tincidunt augue. Duis sem dolor, molestie sed mattis tempus, fringilla a quam. Proin id dolor nisl.</p>
',
	),
	'under-char-limit-no-word-limit-no-tags' => array(
		'params' => array('characters' => 256),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
	),
	'under-char-limit-no-word-limit-with-tags' => array(
		'params' => array('characters' => 256),
		'input' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. <strong>Aliquam egestas,</strong> <a href="http://www.google.com/">augue <em>id</em> massa</a> nunc.</p>',
		'expect' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. <strong>Aliquam egestas,</strong> <a href="http://www.google.com/">augue <em>id</em> massa</a> nunc.</p>',
	),
	'at-char-limit-no-word-limit-no-tags' => array(
		'params' => array('characters' => 250),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
	),
	'at-char-limit-no-word-limit-with-tags' => array(
		'params' => array('characters' => 250),
		'input' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. <strong>Aliquam egestas,</strong> <a href="http://www.google.com/">augue <em>id</em> massa</a> nunc.</p>',
		'expect' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. <strong>Aliquam egestas,</strong> <a href="http://www.google.com/">augue <em>id</em> massa</a> nunc.</p>',
	),
	'over-char-limit-no-word-limit-no-tags' => array(
		'params' => array('characters' => 128),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis t...',
	),
	'over-char-limit-no-word-limit-with-internal-tags' => array(
		'params' => array('characters' => 128),
		'input' => 'Lorem <strong>ipsum dolor</strong> sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem <strong>ipsum dolor</strong> sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis t...',
	),
	'over-char-limit-no-word-limit-with-overlapping-tags' => array(
		'params' => array('characters' => 128),
		'input' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. <em>Nulla quis</em> <strong>tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. <em>Nulla quis</em> <strong>t...</strong></p>',
	),
	'no-char-limit-under-word-limit-no-tags' => array(
		'params' => array('words' => 100),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla.',
	),
	'no-char-limit-under-word-limit-with-tags' => array(
		'params' => array('words' => 100),
		'input' => '<p>Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla.</p>',
		'expect' => '<p>Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla.</p>',
	),
	'no-char-limit-at-word-limit-no-tags' => array(
		'params' => array('words' => 100),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.',
	),
	'no-char-limit-at-word-limit-with-tags' => array(
		'params' => array('words' => 100),
		'input' => '<p>Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.</p>',
		'expect' => '<p>Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.</p>',
	),
	'no-char-limit-over-word-limit-no-tags' => array(
		'params' => array('words' => 95),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras ...',
	),
	'no-char-limit-over-word-limit-with-internal-tags' => array(
		'params' => array('words' => 98),
		'input' => 'Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.',
		'expect' => 'Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. ...',
	),
	'no-char-limit-over-word-limit-with-overlapping-tags' => array(
		'params' => array('words' => 98),
		'input' => '<p>Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. Proin vitae.</p>',
		'expect' => '<p>Lorem <strong>ipsum</strong> <em>dolor <a href="blah">sit</a> amet,</em> consectetur adipiscing elit. Praesent metus dui, bibendum vitae volutpat auctor, feugiat ut orci. Praesent ultricies lobortis metus, in tristique nisl placerat ut. Phasellus quis libero nec est convallis lacinia id nec enim. Nam sit amet mi est, eget accumsan risus. Morbi feugiat arcu vitae ligula facilisis et vehicula nisl euismod. Curabitur condimentum, ante ac suscipit congue, turpis arcu viverra libero, sed dignissim elit ipsum in mi. Nam sed libero et odio bibendum sollicitudin. Integer aliquam gravida erat et elementum. Nullam magna dui, fringilla a ullamcorper non, lobortis eget arcu. Cras quis mollis nulla. ...</p>',
	),
	'over-char-limit-under-word-limit-no-tags' => array(
		'params' => array('characters' => 128, 'words' => 500),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis t...',
	),
	'over-char-limit-over-word-limit-no-tags-char-limited' => array(
		'params' => array('characters' => 128, 'words' => 35),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis t...',
	),
	'over-char-limit-over-word-limit-no-tags-word-limited' => array(
		'params' => array('characters' => 128, 'words' => 5),
		'input' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent quis sapien ante. Suspendisse eget turpis tortor. Nulla quis tincidunt lorem. Phasellus feugiat libero id magna tempus a scelerisque odio euismod. Aliquam egestas, augue id massa nunc.',
		'expect' => 'Lorem ipsum dolor sit amet, ...',
	),
);

function debug_out ($value, $format = 'text/plain') {
	ob_start();
	var_dump($value);
	$out = ob_get_contents();
	ob_end_clean();
	
	switch ($format) :
	case 'text/html' :
		print '<pre>'.htmlspecialchars($out).'</pre>';
		break;
	case 'text/plain' :
	default :
		print $out;
	endswitch;
} /* function debug_out () */

function get_bloginfo ($param) {
	switch ($param) :
	case 'charset' :
		$ret = 'utf-8';
		break;
	default :
		$ret = NULL;
	endswitch;
	return $ret;
} /* function get_bloginfo() */

function add_filter ($hook, $callback, $priority, $arguments) {
	// NOOP
}

?>
<html>
<body>
<style type="text/css">
tr { vertical-align: top; }
th.test, td.test { width: 20%; }
th.results, td.results { width: 80%; }
th.test.ok { background-color: green; color: white; }
th.test.fail { background-color: yellow; }
th, td { border-bottom: 1px dotted black; }

</style>
<table>
<tr><th class="test">Test</th> <th class="results">Results</th></tr>

<?php
foreach ($tests as $name => $test) :
	$output = $limiter->filter($test['input'], $test['params']);
	if ($output == $test['expect']) :
?>
		<tr><th scope="row" class="test ok"><?php print $name; ?></th>
		<td class="results">OK!</td></tr>
<?php
	else :
?>
		<tr><th scope="row" class="test fail"><?php print $name; ?></th>
		<td class="results fail">FAIL. <?php print debug_out($output, 'text/html'); ?></td>
		</tr>
<?php
	endif;
endforeach;
?>
</table>
</body>
</html>

