<?php

namespace EloquaApi\Service;


class ContactLists extends AbstractService
{
    public function all( $options = array() ) {

        return $this->client->request( '/api/REST/1.0/assets/contact/lists', 'get', $options );

    }

    public function addContacts($id, $contact_ids = array(), $options = array() ) {

        $options = array_merge($options, array(
            "id"=>$id,
            "membershipAdditions" => $contact_ids
        ));

        return $this->client->request( '/api/REST/1.0/assets/contact/lists', 'put', $options );

    }
}