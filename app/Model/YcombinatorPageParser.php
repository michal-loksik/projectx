<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use DomXPath;
use RuntimeException;

class YcombinatorPageParser extends PageParser
{
	private const ATTRIBUTE_CLASS = 'class';
	private const ATTRIBUTE_HREF = 'href';
	private const ATTRIBUTE_TITLE = 'title';
	private const ATTRIBUTE_VALIGN = 'valign';

	/**
	 * @param int $limit
	 * @return NewsDTO[]
	 */
	public function getNews(int $limit): array
	{
		$returnArray = [];
		$newsCounter = 0;
		$page = 1;

		while ($newsCounter < $limit) {
			$remainingLimit = $limit - count($returnArray);

			if ($remainingLimit <= 0) {
				break;
			}

			$news = $this->getNewsFromSinglePage(
				$this->getPageUrl($page),
				$remainingLimit
			);
			array_push($returnArray, ...$news);

			$newsCounter += count($news);
			$page++;
		}

		return $returnArray;
	}

	private function getPageUrl(int $page): string
	{
		if ($page === 1) {
			return $this->baseUrl;
		}

		return sprintf(
			'%s%d',
			$this->nextPage,
			$page
		);
	}

	/**
	 * @param string $url
	 * @param int $limit
	 * @return NewsDTO[]
	 */
	private function getNewsFromSinglePage(string $url, int $limit): array
	{
		$pageContentDOM = $this->getPageContentDOM($url);
		$mainTableNode = $this->getMainTableNode($pageContentDOM);

		if ($mainTableNode === null) {
			return [];
		}

		$returnArray = [];
		$counter = 0;

		foreach ($mainTableNode->childNodes as $node) {
			if ($counter >= $limit) {
				break;
			}

			if ($node instanceof DOMElement === false) {
				continue;
			}

			if ($this->isPrimaryTableRow($node)) {
				[$title, $externalLink] = $this->getTitleAndExternalLink($node);
			}

			if ($this->isSecondaryTableRow($node)) {
				[$created, $internalLink] = $this->getCreatedDateTimeAndInternalLink($node);
			}

			if (isset($title, $externalLink, $created, $internalLink)) {
				$returnArray[] = (new NewsDTO())->setTitle($title)
					->setCreated($created)
					->setExternalLink($externalLink)
					->setInternalLink($internalLink);

				unset($title, $externalLink, $created, $internalLink);
				$counter++;
			}
		}

		return $returnArray;
	}

	private function isPrimaryTableRow(DOMElement $element): bool
	{
		if ($element->hasAttribute(self::ATTRIBUTE_CLASS) === false) {
			return false;
		}

		return $element->getAttribute(self::ATTRIBUTE_CLASS) === 'athing';
	}

	private function isSecondaryTableRow(DOMElement $element): bool
	{
		$hasClassAttribute = $element->hasAttribute(self::ATTRIBUTE_CLASS);

		if ($hasClassAttribute) {
			return false;
		}

		return $element->lastChild instanceof DOMElement &&
			$element->lastChild->hasAttribute(self::ATTRIBUTE_TITLE) === false;
	}

	/**
	 * @param DOMElement $element
	 * @return string[]
	 */
	private function getTitleAndExternalLink(DOMElement $element): array
	{
		/** @var DOMElement|DOMText $tdNode */
		foreach ($element->childNodes as $tdNode) {
			if ($tdNode instanceof DOMElement === false || $tdNode->getAttribute(self::ATTRIBUTE_VALIGN) === 'top') {
				continue;
			}

			/** @var DOMElement $node */
			foreach ($tdNode->childNodes as $node) {
				if ($node->getAttribute(self::ATTRIBUTE_CLASS) === 'titlelink') {
					$title = (string) $node->nodeValue;
					$externalLink = $node->getAttribute(self::ATTRIBUTE_HREF);

					return [$title, $externalLink];
				}
			}
		}

		throw new RuntimeException('Unexpected structure of DOM document. Please modify your code.');
	}

	private function getCreatedDateTimeAndInternalLink(DOMElement $element): array
	{
		/** @var DOMElement|DOMText $tdNode */
		foreach ($element->childNodes as $tdNode) {
			if ($tdNode instanceof DOMElement === false || $tdNode->getAttribute(self::ATTRIBUTE_CLASS) !== 'subtext') {
				continue;
			}

			/** @var DOMElement|DOMText $node */
			foreach ($tdNode->childNodes as $node) {
				if ($node instanceof DOMElement === false) {
					continue;
				}

				if ($node->getAttribute(self::ATTRIBUTE_CLASS) === 'age') {
					$created = DateTime::createFromFormat(
						'Y-m-d\TH:i:s',
						$node->getAttribute(self::ATTRIBUTE_TITLE)
					);

					foreach ($node->childNodes as $aNode) {
						if ($aNode instanceof DOMElement === false) {
							continue;
						}

						$internalLink = sprintf(
							'%s%s',
							$this->baseUrl,
							$aNode->getAttribute(self::ATTRIBUTE_HREF)
						);

						return [$created, $internalLink];
					}

					break;
				}
			}
		}

		throw new RuntimeException('Unexpected structure of DOM document. Please modify your code.');
	}

	private function getMainTableNode(DOMDocument $pageContentDOM): ?DOMNode
	{
		$finder = new DomXPath($pageContentDOM);
		$classname = 'itemlist';
		$nodes = $finder->query("//*[contains(@class, '$classname')]");

		if ($nodes === false) {
			return null;
		}

		return $nodes->item(0);
	}
}
