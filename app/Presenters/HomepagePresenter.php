<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\NewsDTO;
use App\Model\YcombinatorPageParserFactory;
use Nette;

final class HomepagePresenter extends Nette\Application\UI\Presenter
{

	private YcombinatorPageParserFactory $ycombinatorPageParser;

	public function __construct(YcombinatorPageParserFactory $ycombinatorPageParserFactory)
	{
		parent::__construct();

		$this->ycombinatorPageParser = $ycombinatorPageParserFactory;
	}

	public function renderDefault(): void
	{
		assert($this->template instanceof Nette\Bridges\ApplicationLatte\DefaultTemplate);

		$this->template->setParameters([
			'latestNews' => $this->getNews(100)
		]);
	}

	/**
	 * @param int $limit
	 * @return NewsDTO[]
	 */
	private function getNews(int $limit): array
	{
		$parser = $this->ycombinatorPageParser->create();

		return $parser->getNews($limit);
	}
}
