<?php 
    $term=urlencode($_GET["term"]);

    // Base uri for searching documents in Azure Search                         
    $uri = "https://<search service name>.search.windows.net/indexes/hotels/docs?search=$term&facet=rating&facet=category&facet=baseRate,values:100|200|500|1000&\$orderby=baseRate&api-version=2015-02-28";

    // Build the filters for Azure Search based on facets
    $uri = BuildFilters($uri);
    
    // Send the request to Azure Search                        
    $response = \Httpful\Request::get($uri)
        ->addHeaders(array(
            'api-key' => 'AC379CF777D48F4A0C4327B97E067630'))
        ->expectsJson()
        ->sendIt();
                        
    function BuildFilters($uri)
    {
        $filteredUri = $uri . "&\$filter=hotelId%20ne%20null";                        

        $rating = $_GET["rating"];
        if(isset($rating))
        {
            $filter=urlencode($rating);
            $filteredUri = $filteredUri . "%20and%20rating%20eq%20$filter";
        }

        $category = $_GET["category"];
        if(isset($category))
        {
            $filter=urlencode($category);
            $filteredUri = $filteredUri . "%20and%20category%20eq%20'$filter'";
        }

        $price = $_GET["price"];
        if(isset($price))
        {
            $pos = strpos($price, "|");
            if($pos==0)
            {
                // no from price
                $toprice = substr($price, $pos+1, strlen($price)-$pos-1);
                $filteredUri = $filteredUri . "%20and%20baseRate%20le%20$toprice";
            } elseif ($pos == strlen($price)-1) {
                // no to price
                $fromprice = substr($price, 0, $pos);
                $filteredUri = $filteredUri . "%20and%20baseRate%20gt%20$fromprice";
            } else {
                // from and to price provided
                $toprice = substr($price, $pos+1, strlen($price)-$pos-1);
                $fromprice = substr($price, 0, $pos);
                $filteredUri = $filteredUri . "%20and%20baseRate%20le%20$toprice%20and%20baseRate%20gt%20$fromprice";
            }
        }
        
        return $filteredUri;    
    }
                        
    function ShowResults($results)
    {
        foreach($results as $result)
        {
            $formattedPrice = sprintf("%.2f", $result->baseRate);
            echo "<TR><TD>$result->hotelId</TD><TD>$result->hotelName</TD><TD>$result->category</TD><TD>$result->rating</TD><TD>$$formattedPrice</TD></TR>";
        }    
    }
                        
    function ShowFacets($facets)
    {
        $baseuri = "/hotelsearch.php";
        $queryparams = "";

        //determine base URL
        foreach($_REQUEST as $param => $paramvalue)
        {
            if(strlen($queryparams)==0)
            {
                $queryparams .= "?";
            } else {
                $queryparams .= "&";
            }
                        
            $queryparams .= "$param=$paramvalue";
        }
        $baseuri .= $queryparams;
                        
        //Colors
        $ratings=$facets->rating;
        if(count($ratings)>0)
        {
            echo "<div class='sidebar-module'>";
            echo "<div class='panel panel-default'>";
            echo "<div class='panel-heading'>Rating</div>";
            echo "<ol class='list-group'>";
                        
            foreach($ratings as $rating)
            {
                $uri = $baseuri;
                if (strpos($queryparams,"rating")==0)
                {
                    if (strlen($queryparams)>0)
                    { 
                        $uri = "$baseuri&rating=$rating->value"; 
                    } else {
                        $uri = "$baseuri?rating=$rating->value";
                    }
                }
                echo "<li class='list-group-item'><a href='$uri'>$rating->value ($rating->count)</a></li>";
            }
            echo "</ol>";
            echo "</div>";
            echo "</div>";
        }
                        
        //Category
        $categories=$facets->category;
        if(count($categories)>0)
        {
            echo "<div class='sidebar-module'>";
            echo "<div class='panel panel-default'>";
            echo "<div class='panel-heading'>Category</div>";
            echo "<ol class='list-group'>";
                        
            foreach($categories as $category)
            {
                $uri = $baseuri;
                if (strpos($queryparams,"category")==0)
                {
                    if (strlen($queryparams)>0)
                    { 
                        $uri = "$baseuri&category=$category->value"; 
                    } else {
                        $uri = "$baseuri?category=$category->value";
                    }
                }
                echo "<li class='list-group-item'><a href='$uri'>$category->value ($category->count)</a></li>";
            }
            echo "</ol>";
            echo "</div>";
            echo "</div>";
        }

        //Price
        $prices=$facets->baseRate;
        if(count($prices)>0)
        {
            echo "<div class='sidebar-module'>";
            echo "<div class='panel panel-default'>";
            echo "<div class='panel-heading'>Price</div>";
            echo "<ol class='list-group'>";
                        
            foreach($prices as $price)
            {
                if (strpos($queryparams,"price")==0)
                {
                    if (strlen($queryparams)>0)
                    {
                        $uri = "$baseuri&price=$price->from|$price->to";
                    } else {
                        $uri = "$baseuri?price=$price->from|$price->to";
                    }
                }

                $from = "0";
                if(isset($price->from))
                {
                    $from = $price->from;
                }
                $to = "...";
                if(isset($price->to))
                {
                    $to = $price->to;
                }
                echo "<li class='list-group-item'><a href='$uri'>$from - $to ($price->count)</a></li>";
            }
            echo "</ol>";
            echo "</div>";
            echo "</div>";
        }
    }
