<?php
namespace EloquaApi\Service;

class CustomObjectsService extends AbstractService
{
    /**
     * Returns all campaigns
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function all( $options = array() ) {
        return $this->client->request( 'api/REST/1.0/assets/customObjects', 'get', $options );
    }

}