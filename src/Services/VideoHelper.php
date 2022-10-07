<?php

namespace App\Services;

use App\Entity\Trick;
use App\Repository\VideoRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validation;

class VideoHelper
{
    private VideoRepository $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function saveVideoUrl(
        ?Collection $videoData,
        Trick $trick
    ) {
        foreach ($videoData as $video) {
            $validator = Validation::createValidator();
            $violations = $validator->validate($video->getVideoUrl(), new Url());

            if (0 !== count($violations)) {
                foreach ($violations as $violation) {
                    echo $violation->getMessage() . '<br>';
                }
            }
            $embedUrl = $this->getYoutubeEmbedUrl($video->getVideoUrl());
            $video->setVideoUrl($embedUrl);

            $video->setTrick($trick);
            $this->videoRepository->add($video);
        }
    }

    private function getYoutubeEmbedUrl($url): string
    {
        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
        $youtube_id = '';

        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        return 'https://www.youtube.com/embed/' . $youtube_id;
    }
}
