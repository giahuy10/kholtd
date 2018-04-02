SELECT data FROM josde_session WHERE session_id = 'e04tdc9etn4ruvrlamn12b4f00';
SHOW FULL COLUMNS FROM josde_users;
SELECT * FROM josde_users WHERE id = 428;
SELECT g.id,g.title FROM josde_usergroups AS g INNER JOIN josde_user_usergroup_map AS m ON m.group_id = g.id WHERE m.user_id = 428;
SELECT extension_id AS id,element AS option,params,enabled FROM josde_extensions WHERE type = 'component';
SELECT id, rules FROM josde_viewlevels;
SELECT * FROM josde_users WHERE id = 428;
SELECT g.id,g.title FROM josde_usergroups AS g INNER JOIN josde_user_usergroup_map AS m ON m.group_id = g.id WHERE m.user_id = 428;
SELECT b.id FROM josde_user_usergroup_map AS map LEFT JOIN josde_usergroups AS a ON a.id = map.group_id LEFT JOIN josde_usergroups AS b ON b.lft <= a.lft AND b.rgt >= a.rgt WHERE map.user_id = 428;
SELECT folder AS type,element AS name,params FROM josde_extensions WHERE enabled = 1 AND type = 'plugin' AND state IN (0,1) AND access IN (1,1,2,3,6) ORDER BY ordering;
SELECT id,name,rules,parent_id FROM josde_assets WHERE name IN ('root.1','com_admin','com_ajax','com_akeeba','com_associations','com_banners','com_cache','com_categories','com_checkin','com_config','com_contact','com_content','com_contenthistory','com_cpanel','com_fields','com_finder','com_gift','com_installer','com_inventory','com_joomlaupdate','com_languages','com_login','com_mailto','com_media','com_menus','com_messages','com_modules','com_newsfeeds','com_onecard','com_plugins','com_postinstall','com_redirect','com_search','com_tags','com_templates','com_users','com_wrapper');
SELECT extension_id AS id,element AS option,params,enabled FROM josde_extensions WHERE type = 'library' AND element = 'joomla';
UPDATE josde_extensions SET params = '{\"mediaversion\":\"1694b9a5c116897f5ed925ed75e1712a\"}' WHERE type = 'library' AND element = 'joomla';
SELECT template, s.params FROM josde_template_styles as s LEFT JOIN josde_extensions as e ON e.type='template' AND e.element=s.template AND e.client_id=s.client_id WHERE s.client_id = 1 AND home = '1' ORDER BY home;
SELECT enabled FROM josde_extensions WHERE type = 'plugin' AND folder = 'system' AND element = 'languagefilter';
SELECT a.id, a.title, a.name, a.checked_out, a.checked_out_time, a.note, a.state, a.access, a.created_time, a.created_user_id, a.ordering, a.language, a.fieldparams, a.params, a.type, a.default_value, a.context, a.group_id, a.label, a.description, a.required,l.title AS language_title, l.image AS language_image,uc.name AS editor,ag.title AS access_level,ua.name AS author_name,g.title AS group_title, g.access as group_access, g.state AS group_state FROM josde_fields AS a LEFT JOIN josde_languages AS l ON l.lang_code = a.language LEFT JOIN josde_users AS uc ON uc.id=a.checked_out LEFT JOIN josde_viewlevels AS ag ON ag.id = a.access LEFT JOIN josde_users AS ua ON ua.id = a.created_user_id LEFT JOIN josde_fields_groups AS g ON g.id = a.group_id WHERE a.context = 'com_config.application' AND a.state = 1 AND (a.group_id = 0 OR g.state = 1) ORDER BY a.ordering ASC;
SELECT element FROM josde_extensions WHERE type = 'component' AND enabled = 1;
SELECT m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid FROM josde_modules AS m LEFT JOIN josde_modules_menu AS mm ON mm.moduleid = m.id LEFT JOIN josde_extensions AS e ON e.element = m.module AND e.client_id = m.client_id WHERE m.published = 1 AND e.enabled = 1 AND (m.publish_up = '0000-00-00 00:00:00' OR m.publish_up <= '2018-03-26 13:03:08') AND (m.publish_down = '0000-00-00 00:00:00' OR m.publish_down >= '2018-03-26 13:03:08') AND m.access IN (1,1,2,3,6) AND m.client_id = 1 AND (mm.menuid = 0 OR mm.menuid <= 0) ORDER BY m.position, m.ordering;
SELECT element AS value, name AS text FROM josde_extensions WHERE folder = 'editors' AND enabled = 1 ORDER BY ordering, name;
SELECT element AS value, name AS text FROM josde_extensions WHERE folder = 'captcha' AND enabled = 1 ORDER BY ordering, name;
SELECT a.id AS value, a.title AS text FROM josde_viewlevels AS a GROUP BY a.id,a.title,a.ordering ORDER BY a.ordering ASC,title ASC;
SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level FROM josde_usergroups AS a LEFT JOIN josde_usergroups AS b on a.lft > b.lft AND a.rgt < b.rgt GROUP BY a.id, a.title, a.lft ORDER BY a.lft ASC;
SELECT count(id) FROM josde_usergroups;
SELECT * FROM josde_usergroups ORDER BY lft ASC;
SELECT COUNT(*) FROM josde_messages WHERE state = 0 AND user_id_to = 428;
SELECT COUNT(session_id) FROM josde_session WHERE guest = 0 AND client_id = 1;
SELECT COUNT(session_id) FROM josde_session WHERE guest = 0 AND client_id = 0;
SELECT m.*,e.element FROM josde_menu AS m LEFT JOIN josde_extensions AS e ON m.component_id = e.extension_id WHERE m.menutype = 'backend' AND m.client_id = 1 AND m.published = 1 AND m.id > 1 AND (e.enabled = 1 OR e.enabled IS NULL) ORDER BY m.lft;
SELECT m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element, m.menutype FROM josde_menu AS m INNER JOIN josde_extensions AS e ON m.component_id = e.extension_id WHERE m.menutype = 'main' AND m.client_id = 1 AND m.id > 1 AND m.id NOT IN (2, 7, 10, 13, 16, 17, 18, 19, 20, 21, 22, 105, 114, 119) AND m.parent_id NOT IN (2, 7, 10, 13, 16, 17, 18, 19, 20, 21, 22, 105, 114, 119) AND e.enabled = 1 ORDER BY m.lft;
SELECT * FROM josde_users WHERE id = 428;
SELECT g.id,g.title FROM josde_usergroups AS g INNER JOIN josde_user_usergroup_map AS m ON m.group_id = g.id WHERE m.user_id = 428;
