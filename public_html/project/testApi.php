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
    $result = get($endpoint, "API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
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