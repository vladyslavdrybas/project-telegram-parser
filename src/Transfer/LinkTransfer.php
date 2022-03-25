<?php

declare(strict_types=1);

namespace App\Transfer;


use function str_contains;

class LinkTransfer
{
    protected string $authorName;
    protected string $authorLink;
    protected string $postId;
    protected string $link;
    protected string $datetime;
    protected string $wordTrigger;
    protected string $platform = 'unknown';
    protected int $views = 0;

    public function __construct(
        string $authorName,
        string $authorLink,
        string $postId,
        string $link,
        string $datetime,
        string $wordTrigger,
        int $views = 0
    ) {
        $this->authorName = $authorName;
        $this->authorLink = $authorLink;
        $this->postId = $postId;
        $this->link = $link;
        $this->datetime = $datetime;
        $this->wordTrigger = $wordTrigger;
        $this->views = $views;

        if (str_contains($link, 't.me')) {
            $this->platform = 'telegram';
        } elseif (str_contains($link, 'youtube.com')) {
            $this->platform = 'youtube';
        } elseif (str_contains($link, 'twitter.com')) {
            $this->platform = 'twitter';
        }
    }

    /**
     * @return string
     */
    public function getPostId(): string
    {
        return $this->postId;
    }

    /**
     * @param string $postId
     */
    public function setPostId(string $postId): void
    {
        $this->postId = $postId;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getDatetime(): string
    {
        return $this->datetime;
    }

    /**
     * @param string $datetime
     */
    public function setDatetime(string $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return string
     */
    public function getWordTrigger(): string
    {
        return $this->wordTrigger;
    }

    /**
     * @param string $wordTrigger
     */
    public function setWordTrigger(string $wordTrigger): void
    {
        $this->wordTrigger = $wordTrigger;
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * @return string
     */
    public function getAuthorLink(): string
    {
        return $this->authorLink;
    }

    /**
     * @param string $authorLink
     */
    public function setAuthorLink(string $authorLink): void
    {
        $this->authorLink = $authorLink;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     */
    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }
}
