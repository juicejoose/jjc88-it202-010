<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["requestedCurrency"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    //jjc88 04/16/2025 Made data empty, and made a variable holding the data from $_GET.
    $currency = $_GET["requestedCurrency"];
    $data = [];
    //jjc88 04/16/2025 Edited URL end with variable to accept user inputs
    $endpoint = "https://live-metal-prices.p.rapidapi.com/v1/latest/XAU,XAG,PA,PL,GBP,EUR/$currency";
    $isRapidAPI = true;
    $rapidAPIHost = "live-metal-prices.p.rapidapi.com";
    //$result = get($endpoint, "METAL_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    $result = ["status" => 200, "response" => '{"success":true,"validationMessage":[],"baseCurrency":"EUR","unit":"ounce","rates":{"XAU":2923.986937949945,"XAG":29.041837426148746,"PA":829.470731281654,"PL":866.13338003346,"GBP":0.8504812604132436,"EUR":1}}'];
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

    if (isset($result["rates"])) {
    $quote = $result["rates"];
    $quote["base_currency"] = $result["baseCurrency"] ?? null;
    $quote["unit"] = $result["unit"] ?? null;
    /*$quote = array_reduce( 
        array_keys($quote),
        function ($temp, $key) use ($quote) {
            $k = explode(" ", $key)[1];
            if ($k === "change")
            {
                $k = "per_change";
            }
            $temp[$k] = str_replace('%', '', $quote[$key]);
            return $temp;
        }
    );*/
    //$result = [$quote];
    $db = getDB();
    $query = "INSERT INTO `Currency` ";
    $columns = [];
    $params = [];
    //per record 
    foreach ($quote as $k => $v) {
        //array_push($columns, "'$k'");
        //array_push (Sparams, ["=$k" => $v]);  
        $columns[] = "`$k`";
        $params [":$k"] = $v;
    }

    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",", array_keys ($params)) . ")";
    var_export($query);
    try {
        $stmt = $db-> prepare($query);
        $stmt->execute($params);
        flash("Inserted record", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occured", "danger");
    }
}
}
//jjc88 04/16/2025 Editedd html side to accept different variable and text names.
?>
<div class="container-fluid">
    <h1>Currency Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Currency Pair</label>
            <input name="requestedCurrency" />
            <input type="submit" value="Fetch Currency" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $requestedCurrency) : ?>
                <pre>
                    <?php var_export($requestedCurrency);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");