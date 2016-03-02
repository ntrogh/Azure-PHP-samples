<?php
    require 'vendor/autoload.php';
    $term=$_GET["term"];
    
    $uri = "https://<search service name>.search.windows.net/indexes/hotels/docs/suggest?\$select=hotelName,description&search=$term&\$top=10&api-version=2015-02-28";
    
    $response = \Httpful\Request::get($uri)
        ->addHeaders(array(
            'api-key' => 'AC379CF777D48F4A0C4327B97E067630'))
        ->expectsJson()
        ->sendIt();
    
    $json=array();
    
    $suggestions = $response->body->value;
    
    foreach($suggestions as $suggestion)
    {
        $json[]=array(
            'value'=> $suggestion->hotelName,
            'label'=> "$suggestion->hotelName ($suggestion->description)"
            );
    }
    
    echo json_encode($json);
    
?>
