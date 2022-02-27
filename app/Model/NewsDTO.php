<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

class NewsDTO
{

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
}
