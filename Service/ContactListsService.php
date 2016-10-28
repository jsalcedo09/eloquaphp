<?php

namespace EloquaApi\Service;


class ContactListsService extends AbstractService
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

    public function search($value, $term='', $operator='', $options=[]) {
        $search = $value;
        if(!empty($term) && !empty($operator)){
            $search = $term.$operator.$search;
        }
        $options = array_merge($options, array(
            'search'=>$search
        ));
        return $this->all($options);
    }
}