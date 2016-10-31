<?php
namespace EloquaApi\Service;

class FormsService extends AbstractService
{
    /**
     * Returns all campaigns
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function all( $options = array() ) {

        return $this->client->request( '/api/REST/1.0/assets/forms', 'get', $options );

    }

    public function get($id, $options = array() ) {

        return $this->client->request( '/api/REST/1.0/assets/form/'.$id, 'get', $options );

    }
}