<?php

require_once __DIR__ . '/autoloader.php';

$subtypes = array(
	\hypeJunction\Discussion::SUBTYPE => \hypeJunction\Discussion::class,
	\hypeJunction\DiscussionReply::SUBTYPE => \hypeJunction\DiscussionReply::class,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}