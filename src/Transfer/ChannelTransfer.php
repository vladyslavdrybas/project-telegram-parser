<?php

declare(strict_types=1);

namespace App\Transfer;

class ChannelTransfer implements TransferInterface
{
    protected string $title;
    protected string $id;
    protected SourceTransfer $source;
    protected bool $active;
    protected string $mainLink;
    protected string $messageLink;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \App\Transfer\SourceTransfer
     */
    public function getSource(): SourceTransfer
    {
        return $this->source;
    }

    /**
     * @param \App\Transfer\SourceTransfer $source
     */
    public function setSource(SourceTransfer $source): void
    {
        $this->source = $source;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getMainLink(): string
    {
        return $this->mainLink;
    }

    /**
     * @param string $mainLink
     */
    public function setMainLink(string $mainLink): void
    {
        $this->mainLink = $mainLink;
    }

    /**
     * @return string
     */
    public function getMessageLink(): string
    {
        return $this->messageLink;
    }

    /**
     * @param string $messageLink
     */
    public function setMessageLink(string $messageLink): void
    {
        $this->messageLink = $messageLink;
    }
}
