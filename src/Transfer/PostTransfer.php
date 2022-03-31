<?php

declare(strict_types=1);

namespace App\Transfer;

class PostTransfer implements TransferInterface
{
    protected string $id;
    protected int $postNumber;
    protected string $channelTitle;
    protected string $meta;
    protected ChannelTransfer $channel;

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
     * @return int
     */
    public function getPostNumber(): int
    {
        return $this->postNumber;
    }

    /**
     * @param int $postNumber
     */
    public function setPostNumber(int $postNumber): void
    {
        $this->postNumber = $postNumber;
    }

    /**
     * @return string
     */
    public function getChannelTitle(): string
    {
        return $this->channelTitle;
    }

    /**
     * @param string $channelTitle
     */
    public function setChannelTitle(string $channelTitle): void
    {
        $this->channelTitle = $channelTitle;
    }

    /**
     * @return string
     */
    public function getMeta(): string
    {
        return $this->meta;
    }

    /**
     * @param string $meta
     */
    public function setMeta(string $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return \App\Transfer\ChannelTransfer
     */
    public function getChannel(): ChannelTransfer
    {
        return $this->channel;
    }

    /**
     * @param \App\Transfer\ChannelTransfer $channel
     */
    public function setChannel(ChannelTransfer $channel): void
    {
        $this->channel = $channel;
    }
}
