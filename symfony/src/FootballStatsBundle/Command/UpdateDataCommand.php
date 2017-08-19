<?php

namespace FootballStatsBundle\Command;

use FootballStatsBundle\Entity\League;
use FootballStatsBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('football_stats:update_data')
            ->setDescription('Pulls in league and team data from football-data.org via the FootballDataApiBundle');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();
        $leagues = $this->getContainer()->getParameter("football_api.leagues");
        $api = $this->getContainer()->get("football_data_api");
        $teamRepository = $this->getContainer()->get("doctrine")->getRepository(Team::class);
        foreach ($leagues as $leagueApiId) {
            $leagueApiData = $api->getLeagueDataByLeagueId($leagueApiId);
            $league = new League();
            $league->setName($leagueApiData->caption);
            $em->merge($league);
            $em->persist($league);

            $teamApiData = $api->getTeamDataByLeagueId($leagueApiId);
            foreach($teamApiData as $teamApiDatum) {
                $apiId = basename($teamApiDatum->_links->self->href);
                /** @var Team $existingTeam */
                $existingTeam = $teamRepository->findOneBy([
                    "apiId" => $apiId
                ]);
                $team = $existingTeam ?? new Team();
                $team->setName($teamApiDatum->name);
                $team->setApiId($apiId);
                $team->setLeague($league);
                if ($teamApiDatum->shortName) {
                    $team->setShortName($teamApiDatum->shortName);
                } else {
                    $team->setShortName($teamApiDatum->name);
                }
                if ($teamApiDatum->code) {
                    $team->setCode($teamApiDatum->code);
                }
                $em->persist($team);
            }
        }
        $em->flush();
    }
}
