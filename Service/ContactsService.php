<?php
namespace EloquaApi\Service;

class ContactFieldsService extends AbstractService
{
    /**
     * Returns all campaigns
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function all( $options = array() ) {

        return $this->client->request( '/api/REST/1.0/data/contacts', 'get', $options );

    }

    public function search($value, $term='', $operator='', $options=[]) {
        $search = $value;
        if(!empty($term) && !empty($operator)){
            $search = "[".$term.$operator."]".$search;
        }
        $options = array_merge($options, array(
            'search'=>$search
        ));
        return $this->all($options);
    }

}