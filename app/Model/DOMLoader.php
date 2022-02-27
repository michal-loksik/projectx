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
		$dom->loadHTMLFile($url);

		return $dom;
	}
}
