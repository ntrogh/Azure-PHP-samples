<?php
    require 'vendor/autoload.php';
    require_once 'azuresearch.php';
?>

<html>
    <head>
        <title>PHP Azure Search</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
        <script type="text/javascript"
        src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript"
        src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
        <link rel="stylesheet" type="text/css"
        href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" />

        <!-- Autocomplete script -->
        <script type="text/javascript">
                $(document).ready(function(){
                    $("#term").autocomplete({
                        source:'/gethotelautocomplete.php',
                        minLength:3
                    });
                });
        </script>

    </head>

    <body>        
        <div class="container">
            <div class="page-header">
                <h1>Azure Search using PHP</h1>
                <div class="well">
                    <p>Sample on how to use the Azure Search service to add faceted search to your website.</p>
                </div>

                <div class="panel panel-default">
                    <div class='panel-heading'>Enter search terms to find products</div>
                    <div class='panel-body'>
                        <form action='hotelsearch.php' method='GET'>
                            <table>
                            <tr>
                                <td><input type='text' id='term' name='term' size='40'/></td>
                                <td>
                                    <button class="btn btn-xs btn-primary" style="margin-left: 10px" type="submit">Search</button>
                                </td>
                            </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <table class='table table-striped'>
                        <thead><tr>
                            <th>Hotel ID</th>
                            <th>Hotel Name</th>
                            <th>Category</th>
                            <th>Rating</th>
                            <th>Price</th>
                        </tr></thead>
                        <tbody>
                            <?php
                                ShowResults($response->body->value);                        
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3 col-sm-offset-1">
                    <?php
                        $propname ="@search.facets";                        
                        ShowFacets($response->body->$propname);
                    ?>

                </div>
            </div>
        </div>

    </body>
</html>