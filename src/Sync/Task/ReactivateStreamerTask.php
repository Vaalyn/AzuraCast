<?php

namespace App\Sync\Task;

use App\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ReactivateStreamerTask extends AbstractTask
{
    protected Entity\Repository\StationStreamerRepository $streamerRepo;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        Entity\Settings $settings,
        Entity\Repository\StationStreamerRepository $streamerRepo
    ) {
        parent::__construct($em, $logger, $settings);

        $this->streamerRepo = $streamerRepo;
    }

    public function run(bool $force = false): void
    {
        $deactivated_streamers = $this->streamerRepo->getStreamersDueForReactivation();

        foreach ($deactivated_streamers as $streamer) {
            $streamer->setIsActive(true);
            $this->em->persist($streamer);
            $this->em->flush();
        }
    }
}
