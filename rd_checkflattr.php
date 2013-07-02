<?php

function get_flattrs() {
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, "https://api.flattr.com/rest/v2/users/deusfigendi/things");
         
        $content = curl_exec($ch);
        curl_close($ch);
        
        
        // Speicher das in den Cache
        return json_decode($content);
}

$flattr_object = get_flattrs();

?>
