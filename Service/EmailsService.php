<?php
namespace EloquaApi\Service;

class EmailsService extends AbstractService
{
    /**
     * Returns all campaigns
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function all( $options = array() ) {

        return $this->client->request( '/api/REST/1.0/assets/emails', 'get', $options );

    }

    public function get($id, $options = array() ) {

        return $this->client->request( '/api/REST/1.0/assets/email/'.$id, 'get', $options );

    }
}