<?php

declare(strict_types=1);

namespace App\Transfer;

class TelegramPostTransfer implements TransferInterface
{
    protected string $authorName = '';
    protected string $authorLink = '';
    protected string $postId;
    protected string $postHtml;
    protected array $links = [];
    protected string $datetime = '';
    protected string $wordTrigger = '';
    protected int $views = 0;

    public function __construct(
        string $postId,
        string $postHtml,
    ) {
        $this->postId = $postId;
        $this->postHtml = $postHtml;
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
    public function getPostHtml(): string
    {
        return $this->postHtml;
    }

    /**
     * @param string $postHtml
     */
    public function setPostHtml(string $postHtml): void
    {
        $this->postHtml = $postHtml;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function addLink(string $link): void
    {
        $this->links[] = $link;
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
}
