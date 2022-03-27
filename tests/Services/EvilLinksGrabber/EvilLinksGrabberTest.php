<?php

declare(strict_types=1);

namespace App\Tests\Services\EvilLinksGrabber;

use App\Services\EvilLinks\AuthorPostTransformer;
use App\Services\EvilLinks\CanHaveLinksPostTransformer;
use App\Services\EvilLinks\EvilLinksGrabber;
use App\Services\EvilLinks\HasWordsPostTransformer;
use App\Services\EvilLinks\NoEmptyLinksPostTransformer;
use App\Services\EvilLinks\TelegramLinksPostTransformer;
use App\Services\EvilLinks\TimePostTransformer;
use App\Services\EvilLinks\TwitterLinksPostTransformer;
use App\Services\EvilLinks\ViewsPostTransformer;
use App\Services\EvilLinks\YoutubeLinksPostTransformer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function file_get_contents;
use function json_decode;

class EvilLinksGrabberTest extends TestCase
{
    protected function stopRussiaChannelOrigin(): array
    {
        return [
            'source' => '["https://t.me/s/stoprussiachannel"]',
            'whitelist' => file_get_contents(__DIR__ . '/_data/whitelist_links.json'),
            'words' => file_get_contents(__DIR__ . '/_data/words_filter_phrases.json'),
        ];
    }

    public function stopRussiaChannelDataProvider(): \Generator
    {
        ['source' => $source, 'whitelist' => $whitelist, 'words' => $words] = $this->stopRussiaChannelOrigin();

        $transformers =
        [
            new HasWordsPostTransformer($words),
            new AuthorPostTransformer(),
            new TelegramLinksPostTransformer(),
            new YoutubeLinksPostTransformer(),
            new TwitterLinksPostTransformer(),
            new CanHaveLinksPostTransformer($whitelist),
            new NoEmptyLinksPostTransformer(),
            new TimePostTransformer(),
            new ViewsPostTransformer(),
        ];

        yield 'stoprussiachannel' => [
            $source,
            $transformers,
            json_decode(file_get_contents(__DIR__ . '/_data/stoprussiachannel.json' )),
            29
        ];

        yield 'stoprussiachannel_one_message' => [
            $source,
            $transformers,
            json_decode(file_get_contents(__DIR__ . '/_data/stoprussiachannel_one_message.json' )),
            8
        ];
    }

    /**
     * @dataProvider stopRussiaChannelDataProvider
     * @param $html
     * @return void
     */
    public function testCrawler($sources, $transformers, $html, $expected)
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getContent')->willReturn($html);
        $response->method('getStatusCode')->willReturn(200);

        $httpClientMock->method('request')->willReturn($response);

        $grabber = new EvilLinksGrabber($loggerMock, $parameterBagMock, $httpClientMock, $sources, $transformers);
        $links = $grabber->execute();

        $this->assertCount($expected, $links);
    }
}
