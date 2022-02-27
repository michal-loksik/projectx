<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use JsonSerializable;

class NewsDTO implements JsonSerializable
{

	private const FIELD_TITLE = 'title';
	private const FIELD_CREATED = 'created';
	private const FIELD_INTERNAL_LINK = 'internalLink';
	private const FIELD_EXTERNAL_LINK = 'externalLink';

	private string $title;

	private DateTime $created;

	private string $internalLink;

	private string $externalLink;

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	public function getCreated(): DateTime
	{
		return $this->created;
	}

	public function setCreated(DateTime $created): self
	{
		$this->created = $created;

		return $this;
	}

	public function getInternalLink(): string
	{
		return $this->internalLink;
	}

	public function setInternalLink(string $internalLink): self
	{
		$this->internalLink = $internalLink;

		return $this;
	}

	public function getExternalLink(): string
	{
		return $this->externalLink;
	}

	public function setExternalLink(string $externalLink): self
	{
		$this->externalLink = $externalLink;

		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function jsonSerialize(): array
	{
		return [
			self::FIELD_TITLE => $this->title,
			self::FIELD_CREATED => $this->created,
			self::FIELD_INTERNAL_LINK => $this->internalLink,
			self::FIELD_EXTERNAL_LINK => $this->externalLink,
		];
	}
}
