# php-shapeshiftio-api

php-shapeshiftio-api is a php library for interacting with ShapeShift.io api. It's still work in progress.

### Usage:

```
use ShapeShiftIO\ShapeShiftApi;
$api = new ShapeShiftApi();
$rate = $api->rate('btc_ltc');
```

Check out the documentation here: https://shapeshift.io/site/api