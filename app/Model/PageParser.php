<?php

declare(strict_types=1);

namespace App\Model;

use DOMDocument;

abstract class PageParser
{

	protected string $baseUrl;

	protected string $nextPage;

	public function __construct(string $baseUrl, string $nextPage)
	{
		$this->baseUrl = $baseUrl;
		$this->nextPage = $nextPage;
	}

	protected function getPageContentDOM(string $url): DOMDocument
	{
		return DOMLoader::loadHtmlFromUrl($url);
	}

	/**
	 * @param int $limit
	 * @return NewsDTO[]
	 */
	abstract public function getNews(int $limit): array;
}
