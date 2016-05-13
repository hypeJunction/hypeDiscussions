<?php

namespace hypeJunction\Interactions;

$subtypes = array(
	\hypeJunction\Discussion::SUBTYPE,
	\hypeJunction\DiscussionReply::SUBTYPE,
);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}
