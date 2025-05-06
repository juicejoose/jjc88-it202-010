<?php

/**
 * This file is a wrapper for our API calls.
 * Here, each endpoint needed will be exposes as a function.
 * The function will take the parameters needed for the API call and return the result.
 * The function will also handle the API key and endpoint.
 * Requires the api_helper.php file and load_api_keys.php file.
 */

/**
 * Fetches the stock quote for a given symbol.
 */
function fetch_quote($currency)
{
    
    $data = [];
    $endpoint = "https://live-metal-prices.p.rapidapi.com/v1/latest/XAU,XAG,PA,PL,GBP,EUR/$currency";
    $isRapidAPI = true;
    $rapidAPIHost = "live-metal-prices.p.rapidapi.com";
    //$result = get($endpoint, "METAL_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    $result = ["status" => 200, "response" => '{"success":true,"validationMessage":[],"baseCurrency":"EUR","unit":"ounce","rates":{"XAU":2923.986937949945,"XAG":29.041837426148746,"PA":829.470731281654,"PL":866.13338003346,"GBP":0.8504812604132436,"EUR":1}}'];

    error_log("API Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }




    $transformedResult = [];
    // transform data to match our DB structure
    if (isset($result["rates"])) {
        $transformedResult["base_currency"] = $result["baseCurrency"] ?? $currency;
        $transformedResult["unit"] = $result["unit"] ?? "ounce";

        foreach ($result["rates"] as $key => $value) 
        {
            $transformedResult[$key] = round(floatval($value), 5);
        }
    }

    return $transformedResult;
}
    /*function search_companies($search){
    $data = ["function" => "SYMBOL_SEARCH", "keywords" => $search, "datatype" => "json"];
    $endpoint = "https://alpha-vantage.p.rapidapi.com/query";
    $isRapidAPI = true;
    $rapidAPIHost = "alpha-vantage.p.rapidapi.com";
    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    
    error_log("API Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    // transform data
    if(isset($result["bestMatches"])){
        $result = $result["bestMatches"];
        $transformedResult = [];
        foreach($result as $r){
            
            // fixed keys
            foreach($r as $k=>$v){
                $nk = str_replace(" ", "_", explode(" ", $k, 2)[1]);
                $r[$nk] = $v;
                unset($r[$k]);
            }
            if(strlen($r["symbol"]) > 6){
                continue;
            }
            // map/extract desired information
            $data = [
                "symbol"=>$r["symbol"],
                "name" =>$r["name"],
                "type"=>$r["type"],
                "region"=>$r["region"],
                "currency"=>$r["currency"]
            ];
            array_push($transformedResult, $data);
        }
    }
    return $transformedResult;
}*/