<?php
namespace EloquaApi\Service;

class FormDataService extends AbstractService
{

    /**
    *  Create Contact  
    */
    public function create($id, $options = array() ) {
        return $this->client->request( '/api/REST/1.0/data/form/'.$id, 'post', $options );
    }

}