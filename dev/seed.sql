-- Testdaten für dev/check.sh: ein öffentlicher Kalender und derselbe Kalender an Stellen, die ein
-- anonymer Besucher nicht sehen darf. Kalender 10 (2020, alle Türchen offen) enthält den Code
-- GEHEIM-1, Kalender 11 (2099, noch alles zu) den Code ZUKUNFT-1.

INSERT INTO tl_member_group (id, tstamp, name) VALUES (1, 1, 'Kunden');

INSERT INTO tl_theme (id, tstamp, name) VALUES (1, 1, 'Test');
INSERT INTO tl_layout (id, pid, tstamp, name, modules) VALUES (1, 1, 1, 'Standard', 'a:1:{i:0;a:3:{s:3:"mod";s:1:"0";s:3:"col";s:4:"main";s:6:"enable";s:1:"1";}}');

INSERT INTO tl_page (id, pid, sorting, tstamp, title, alias, type, language, fallback, published, includeLayout, layout, protected, `groups`, stop, useSSL) VALUES
    (1, 0, 128, 1, 'Start', 'start', 'root', 'de', 1, 1, 1, 1, 0, NULL, '', 0),
    (2, 1, 128, 1, 'Kalender', 'kalender', 'regular', '', 0, 1, 0, 0, 0, NULL, '', 0),
    (3, 1, 256, 1, 'Mitglieder', 'mitglieder', 'regular', '', 0, 1, 0, 0, 1, 'a:1:{i:0;s:1:"1";}', '', 0),
    (4, 1, 384, 1, 'Entwurf', 'entwurf', 'regular', '', 0, 0, 0, 0, 0, NULL, '', 0),
    (5, 4, 128, 1, 'Unter Entwurf', 'unter-entwurf', 'regular', '', 0, 1, 0, 0, 0, NULL, '', 0),
    (6, 1, 512, 1, 'Abgelaufen', 'abgelaufen', 'regular', '', 0, 1, 0, 0, 0, NULL, '1000000000', 0),
    (7, 3, 128, 1, 'Unter Mitglieder', 'unter-mitglieder', 'regular', '', 0, 1, 0, 0, 0, NULL, '', 0);

INSERT INTO tl_article (id, pid, sorting, tstamp, title, alias, inColumn, published, protected, `groups`) VALUES
    (2, 2, 128, 1, 'Kalender', 'kalender-artikel', 'main', 1, 0, NULL),
    (3, 3, 128, 1, 'Mitglieder', 'mitglieder-artikel', 'main', 1, 0, NULL),
    (4, 4, 128, 1, 'Entwurf', 'entwurf-artikel', 'main', 1, 0, NULL),
    (5, 5, 128, 1, 'Unter Entwurf', 'unter-entwurf-artikel', 'main', 1, 0, NULL),
    (6, 6, 128, 1, 'Abgelaufen', 'abgelaufen-artikel', 'main', 1, 0, NULL),
    (7, 2, 256, 1, 'Unveröffentlicht', 'unveroeffentlicht', 'main', 0, 0, NULL),
    (8, 2, 384, 1, 'Nur Mitglieder', 'nur-mitglieder', 'main', 1, 1, 'a:1:{i:0;s:1:"1";}'),
    (9, 7, 128, 1, 'Unter Mitglieder', 'unter-mitglieder-artikel', 'main', 1, 0, NULL);

INSERT INTO tl_advent_calendar (id, tstamp, title, year, keepOpen, shuffle, demoMode, backgroundType, snow) VALUES
    (10, 1, 'Offen (2020)', 2020, '1', '', '', 'none', ''),
    (11, 1, 'Zukunft (2099)', 2099, '1', '', '', 'none', '');

INSERT INTO tl_advent_calendar_door (pid, tstamp, day, headline, couponCode, published) VALUES
    (10, 1, 1, 'Türchen eins', 'GEHEIM-1', '1'),
    (11, 1, 1, 'Türchen eins', 'ZUKUNFT-1', '1');

INSERT INTO tl_content (id, pid, ptable, sorting, tstamp, type, adventCalendar, invisible, protected, `groups`) VALUES
    (100, 2, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (101, 3, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (102, 4, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (103, 5, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (104, 6, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (105, 7, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (106, 8, 'tl_article', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (107, 2, 'tl_article', 256, 1, 'advent_calendar', 10, 1, 0, NULL),
    (108, 2, 'tl_article', 384, 1, 'advent_calendar', 10, 0, 1, 'a:1:{i:0;s:1:"1";}'),
    (109, 2, 'tl_article', 512, 1, 'advent_calendar', 11, 0, 0, NULL),
    (110, 1, 'tl_news', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (111, 9, 'tl_article', 128, 1, 'element_group', 0, 0, 0, NULL),
    (112, 111, 'tl_content', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (113, 2, 'tl_article', 640, 1, 'element_group', 0, 1, 0, NULL),
    (114, 113, 'tl_content', 128, 1, 'advent_calendar', 10, 0, 0, NULL),
    (115, 2, 'tl_article', 768, 1, 'element_group', 0, 0, 0, NULL),
    (116, 115, 'tl_content', 128, 1, 'advent_calendar', 10, 0, 0, NULL);
