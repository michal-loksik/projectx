<?php

declare(strict_types=1);

namespace App\Model;

class YcombinatorPageParserFactory
{

	private string $baseUrl;

	private string $nextPage;

	public function __construct(string $baseUrl, string $nextPage)
	{
		$this->baseUrl = $baseUrl;
		$this->nextPage = $nextPage;
	}

	public function create(): YcombinatorPageParser
	{
		return new YcombinatorPageParser(
			$this->baseUrl,
			$this->nextPage
		);
	}
}
