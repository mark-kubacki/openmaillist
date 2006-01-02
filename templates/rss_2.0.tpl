<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
	<title>Openmaillist: <?= $list->get_name() ?></title>
	<link><?= $cfg['AbsoluteUri'] ?>threads.php?lid=<?= $list->get_unique_value() ?></link>
	<description><?= $list->get_description() ?></description>
	<ttl><?= $cfg['rss']['min_age'] ?></ttl>
	<docs>http://blogs.law.harvard.edu/tech/rss</docs>
	<generator>Openmaillist <?= $version ?></generator>
<?php if(is_array($messages)) foreach($messages as $item) { ?>
	<item>
		<title><?= $item->get_subject() ?></title>
		<link><?= $cfg['AbsoluteUri'] ?>messages.php?tid=<?= $item->get_owning_thread()->get_unique_value() ?>#mid<?= $item->get_unique_value() ?></link>
		<description><?= format_for_rss($item->get_text(), $cfg['rss']['max_description_length']) ?></description>
		<pubDate><?= $item->get_date_received('r'); ?></pubDate>
		<author><?= $list->get_address() ?> (<?= format_for_rss($item->get_author()) ?>)</author>
		<guid isPermaLink="false">&lt;<?= format_for_rss($item->get_message_id()) ?>&gt;</guid>
	</item>
<?php } ?>
</channel>
</rss>