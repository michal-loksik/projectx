<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\NewsDTO;
use App\Model\YcombinatorPageParserFactory;
use Nette;

final class HomepagePresenter extends Nette\Application\UI\Presenter
{

	private const CACHE_KEY_YCOMBINATOR = 'ycombinator';
	private const CACHE_TTL_YCOMBINATOR = '5 minutes';

	private YcombinatorPageParserFactory $ycombinatorPageParser;

	private Nette\Caching\Cache $cache;

	public function __construct(
		YcombinatorPageParserFactory $ycombinatorPageParserFactory,
		Nette\Caching\Cache $cache
	) {
		parent::__construct();

		$this->ycombinatorPageParser = $ycombinatorPageParserFactory;
		$this->cache = $cache;
	}

	public function renderDefault(): void
	{
		assert($this->template instanceof Nette\Bridges\ApplicationLatte\DefaultTemplate);

		$this->template->setParameters([
			'latestNews' => $this->getNews(100)
		]);
	}

	public function renderJson(): void
	{
		$this->sendJson(
			$this->getNews(100)
		);
	}

	/**
	 * @param int $limit
	 * @return NewsDTO[]
	 */
	private function getNews(int $limit): array
	{
		/** @var NewsDTO[]|null $cachedNews */
		$cachedNews = $this->cache->load(self::CACHE_KEY_YCOMBINATOR);
		if ($cachedNews !== null) {
			return $cachedNews;
		}

		$parser = $this->ycombinatorPageParser->create();
		$news = $parser->getNews($limit);

		$this->cache->save(
			self::CACHE_KEY_YCOMBINATOR,
			$news,
			[
				Nette\Caching\Cache::EXPIRATION => self::CACHE_TTL_YCOMBINATOR,
			]
		);

		return $news;
	}
}
