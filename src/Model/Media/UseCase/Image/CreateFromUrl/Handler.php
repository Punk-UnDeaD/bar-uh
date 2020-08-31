<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\CreateFromUrl;

use App\Model\Media\Entity\Image;
use App\Model\Media\UseCase\Image\BaseCreateHandler;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Webmozart\Assert\Assert;

class Handler extends BaseCreateHandler implements MessageHandlerInterface
{

    #[Required] public HttpClientInterface $client;

    public function __invoke(Command $command): Image
    {
        $response = $this->client->request('GET', $command->url);
        $content = $response->getContent();
        $temp = tmpfile();
        fwrite($temp, $content);
        $uploadedFile = new File(stream_get_meta_data($temp)['uri']);
        $mimeType = $uploadedFile->getMimeType();
        Assert::regex($mimeType, '/^image/', "{$command->url} not image");
        $name = urldecode(pathinfo(basename(parse_url($command->url, PHP_URL_PATH)), PATHINFO_FILENAME)).
            '.'.$uploadedFile->guessExtension();

        return $this->persist($uploadedFile, $name, $mimeType);
    }

}
