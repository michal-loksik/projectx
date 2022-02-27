<?php

declare(strict_types=1);

namespace App\Model;

use DOMDocument;

class DOMLoader
{

	public static function loadHtmlFromUrl(string $url): DOMDocument
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);

		$content = file_get_contents($url);
		$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'); // fixed encoding

		$dom->loadHTML($content);

		return $dom;
	}
}
