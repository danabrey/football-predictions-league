<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 13/08/17
 * Time: 13:43
 */

namespace DanAbrey\FootballDataApiBundle\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class FootballDataApi
{
    private $container;
    private $httpClient;
    private $options;
    private $baseUri;
    private $apiToken;

    public function __construct(ContainerInterface $container, $baseUri, $apiToken)
    {
        $this->container = $container;
        $this->httpClient = new Client();
        $this->options = array();
        $this->options['headers']['X-Auth-Token'] = $apiToken;
        $this->baseUri = $baseUri;
        $this->apiToken = $apiToken;
    }

    /**
     * Get response for GET request to given URL
     *
     * @param $url
     * @return mixed
     */
    public function getResponse($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return new Response(400);
        }
        try {
            $response = $this->httpClient->get($url, $this->options);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    /**
     * Process fetched response depending on status code
     *
     * @param Response $response
     * @param null $bodyProperty
     * @return array|mixed
     */
    public function processResponse(Response $response, $bodyProperty = null)
    {
        if ($response->getStatusCode() !== 200) {
            $this->container->get('session')
                ->getFlashBag()
                ->add('message', $response->getReasonPhrase());
            return array();
        }
        return ($bodyProperty)
            ? json_decode($response->getBody())->$bodyProperty
            : json_decode($response->getBody());
    }

    /**
     * Fetch league data by API competition ID
     *
     * @param $apiCompetitionId
     * @return mixed
     */
    public function getLeagueDataByLeagueId($apiCompetitionId)
    {
        $uri = $this->baseUri.'/competitions/'.$apiCompetitionId;
        return $this->processResponse($this->getResponse($uri));
    }

    /**
     * Fetch team data by API competition ID
     *
     * @param $apiCompetitionId
     * @return mixed
     */
    public function getTeamDataByLeagueId($apiCompetitionId)
    {
        $uri = $this->baseUri.'/competitions/'.$apiCompetitionId.'/teams';
        return $this->processResponse($this->getResponse($uri), 'teams');
    }

    /**
     * Fetch league table by API competition ID
     *
     * @param $apiCompetitionId
     * @return mixed
     */
        public function getLeagueTableByLeagueId($apiCompetitionId)
    {
        $uri = $this->baseUri.'/competitions/'.$apiCompetitionId.'/leagueTable';
        return $this->processResponse($this->getResponse($uri), 'standing');
    }
}