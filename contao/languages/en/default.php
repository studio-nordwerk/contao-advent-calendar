<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['MOD']['advent_calendar'] = ['Advent calendar', 'Manage 24 doors with offers, vouchers or stories'];
$GLOBALS['TL_LANG']['CTE']['advent_calendar'] = ['Advent calendar', 'Shows an advent calendar with 24 doors.'];

$GLOBALS['TL_LANG']['tl_advent_calendar']['title'] = ['Title', 'Backend only, e.g. "Advent calendar 2026".'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['year'] = ['Year', 'Leave empty to reuse the calendar every year.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['keepOpen'] = ['Past doors stay open', 'Otherwise only today\'s door can be opened.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['shuffle'] = ['Shuffle doors', 'Like a real calendar. The order always stays the same.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['demoMode'] = ['Demo mode: all doors open until 30 November', 'For showing and checking. Ends on 1 December by itself. With real voucher codes, switch it off anyway, otherwise everyone can see the codes until then.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['backgroundType'] = ['Background', 'The poinsettia image ships with the bundle. Choose "Own image" to use your own.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['backgroundTypes'] = ['poinsettia' => 'Poinsettia (included)', 'image' => 'Own image', 'none' => 'No image'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['singleSRC'] = ['Background image', 'Landscape, at least 1600 pixels wide.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['snow'] = ['Snowfall', 'Light snowfall. Hidden for visitors who prefer reduced motion.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['everyYear'] = 'every year';
$GLOBALS['TL_LANG']['tl_advent_calendar']['title_legend'] = 'Calendar';
$GLOBALS['TL_LANG']['tl_advent_calendar']['rules_legend'] = 'Doors';
$GLOBALS['TL_LANG']['tl_advent_calendar']['look_legend'] = 'Appearance';
$GLOBALS['TL_LANG']['tl_advent_calendar']['edit'] = 'Edit calendar';
$GLOBALS['TL_LANG']['tl_advent_calendar']['children'] = 'Edit doors';

$GLOBALS['TL_LANG']['tl_advent_calendar_door']['day'] = ['Day', 'Which day in December does the door open?'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['kicker'] = ['Kicker', 'Small line above the headline.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['headline'] = ['Headline', ''];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['singleSRC'] = ['Image', 'Optional. Best taken from a non-public folder, otherwise the image can be opened early via its address. Set the image size in the content element.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['text'] = ['Text', ''];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['price'] = ['Price', 'Optional.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['oldPrice'] = ['Old price', 'Optional, shown struck through.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['couponCode'] = ['Voucher code', 'Optional. Visitors can copy it with one click.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['url'] = ['Link', 'Optional.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['target'] = ['Open in new window', ''];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['linkTitle'] = ['Button text', 'Default: "Zum Angebot".'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['published'] = ['Show door', ''];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['door_legend'] = 'Door';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['content_legend'] = 'Content';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['offer_legend'] = 'Offer';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['link_legend'] = 'Button';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['publish_legend'] = 'Publish';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['hiddenLabel'] = 'hidden';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['dayTaken'] = 'There already is a door for day %s in this calendar.';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['publicFolder'] = 'Doors %s: the images are in a public folder (%s). Anyone who knows or guesses the address can see them before the day. Safer: put the images in a folder that is not public in the file manager.';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['toggle'] = 'Show/hide door';

$GLOBALS['TL_LANG']['tl_content']['adventCalendar'] = ['Calendar', 'Which advent calendar should be shown?'];
$GLOBALS['TL_LANG']['tl_content']['advent_calendar_legend'] = 'Advent calendar';

// Calendar texts (templates content_element/advent_calendar and advent_calendar/door)
$GLOBALS['TL_LANG']['MSC']['adventCalendarEarlyKicker'] = 'Just a little longer';
$GLOBALS['TL_LANG']['MSC']['adventCalendarEarly'] = 'This door opens on %s December.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarEarlyText'] = 'Until then, have a look behind today’s door.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarPastKicker'] = 'Already over';
$GLOBALS['TL_LANG']['MSC']['adventCalendarPast'] = 'This door has closed again.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarPastText'] = 'Today’s door is waiting for you.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarEmpty'] = 'Nothing behind this door today, sorry.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarError'] = 'Something went wrong.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarErrorText'] = 'Please try again in a moment.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarDemo'] = 'Preview: until 30 November all doors are open to try out.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarDoor'] = 'Door %s';
$GLOBALS['TL_LANG']['MSC']['adventCalendarDialog'] = 'Door';
$GLOBALS['TL_LANG']['MSC']['adventCalendarClose'] = 'Close';
$GLOBALS['TL_LANG']['MSC']['adventCalendarCode'] = 'Your code:';
$GLOBALS['TL_LANG']['MSC']['adventCalendarCopy'] = 'Copy code';
$GLOBALS['TL_LANG']['MSC']['adventCalendarButton'] = 'Shop now';
