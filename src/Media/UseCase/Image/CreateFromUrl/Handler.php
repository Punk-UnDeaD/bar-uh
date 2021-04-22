<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\CreateFromUrl;

use App\Media\Entity\Image;
use App\Media\UseCase\Image\BaseCreateHandler;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler extends BaseCreateHandler implements MessageHandlerInterface
{
    #[Required] public HttpClientInterface $client;

    public function __invoke(Command $command): Image
    {
        $response = $this->client->request('GET', $command->url);
        $content = $response->getContent();
        /** @var resource $temp */
        $temp = tmpfile();
        fwrite($temp, $content);
        $file = new File(stream_get_meta_data($temp)['uri']);
        $name = urldecode(
            pathinfo(basename(parse_url($command->url, PHP_URL_PATH) ?: md5($command->url)), PATHINFO_FILENAME)
        );

        return $this->persist($file, $name);
    }
}
