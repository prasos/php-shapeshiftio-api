<?php
/**
 *
 *
 * php-shapeshiftio-api 2015
 *
 * @author Sebastian Mäki <sebastian@tico.fi>
 * @package php-shapeshiftio-api
 *
 *
 */
namespace ShapeShiftIO;

use ShapeShiftIO\HttpClient;

/**
 * PHP ShapeShift.io api.
 * https://shapeshift.io/site/api
 *
 * @author Sebastian Mäki <sebastian@tico.fi>
 *
 */
class ShapeShiftApi
{

    /**
     * The Guzzle instance is used to communicate with ShapeShift.io.
     *
     * @var Guzzle\Http\Client
     */
    private $httpClient;

    /**
     * Instantiate a new ShapeShift.io client.
     *
     * @param null|ShapeShiftIO\HttpClient $httpClient http client
     */
    public function __construct(HttpClient $httpClient = null)
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient();
        }
    }

    /** GET Requests */
    
    /**
     * Rate
     * 
     * Gets the current rate offered by Shapeshift
     * 
     * @param string $pair
     * @return array Rate
     */
    public function rate($pair)
    {
        return $this->httpClient->get('/rate/'.$pair);
    }
    
    /**
     * Deposit Limit
     * 
     * Gets the current deposit limit set by Shapeshift
     *
     * @param string $pair
     */
    public function limit($pair)
    {
        return $this->httpClient->get('/limit/'.$pair);
    }
    
    /**
     * Market Info
     * 
     * This gets the market info (pair, rate, limit, minimum limit, miner fee)
     *
     * @param string $pair
     */
    public function marketinfo($pair)
    {
        return $this->httpClient->get('/marketinfo/'.$pair);
    }
    
    /**
     * Recent Transaction List
     * 
     * Get a list of the most recent transactions.
     *
     * @param string|int $max an optional maximum number of transactions to return. Must be a number between 1 and 50
     */
    public function recenttx($max = 5)
    {
        return $this->httpClient->get('/recenttx/'.$max);
    }
    
    /**
     * Status of deposit to address
     * 
     * This returns the status of the most recent deposit transaction to the address.
     *
     * @param string $address is the deposit address to look up.
     */
    public function txStat($address)
    {
        return $this->httpClient->get('/txStat/'.$address);
    }
    
    /**
     * Time Remaining on Fixed Amount Transaction
     * 
     * This api call returns how many seconds are left before the transaction expires.
     *
     * @param string $address is the deposit address to look up.
     */
    public function timeremaining($address)
    {
        return $this->httpClient->get('/timeremaining/'.$address);
    }
    
    /**
     * Get List of Supported Coins with Icon Links
     * 
     * Allows anyone to get a list of all the currencies that Shapeshift currently  supports at any given time. 
     * The list will include the name, symbol, availability status, and an icon link for each.
     *
     */
    public function getcoins()
    {
        return $this->httpClient->get('/getcoins');
    }

    /**
     * Get List of Transactions with a PRIVATE API KEY
     *
     * Allows vendors to get a list of all transactions that have ever been done using a specific API key
     *
     * @param string $apikey is the affiliate's PRIVATE api key.
     */
    public function txbyapikey($apikey)
    {
        return $this->httpClient->get('/txbyapikey/'.$apikey);
    }
    
    /**
     * Get List of Transactions with a Specific Output Address
     *
     * Allows vendors to get a list of all transactions that have ever been sent to one of their addresses. 
     *
     * @param string $address the address that output coin was sent to. Please note that if the address is a ripple address and it includes the "?dt=destTagNUM" appended on the end, you will need to use the URIEncodeComponent() function on the address before sending it in as a param, to get a successful response.
     * @param string $apikey is the affiliate's PRIVATE api key.
     */
    public function txbyaddress($address, $apikey)
    {
        return $this->httpClient->get('/txbyaddress/'.$address.'/'.$apikey);
    }
    
    /**
     * Get List of Supported Coins with Icon Links
     *
     * Allows anyone to get a list of all the currencies that Shapeshift currently  supports at any given time.
     * The list will include the name, symbol, availability status, and an icon link for each.
     * @param string $address the address that the user wishes to validate
     * @param string $coinSymbol the currency symbol of the coin
     */
    public function validateAddress($address, $coinSymbol)
    {
        return $this->httpClient->get('/validateAddress/'.$address.'/'.$coinSymbol);
    }
    
    /** POST Requests */
    
    /**
     * Normal Transaction
     * 
     * This is the primary data input into ShapeShift.
     * 
     * @param string $withdrawal the address for resulting coin to be sent to
     * @param string $pair what coins are being exchanged in the form [input coin]_[output coin]  ie btc_ltc
     * @param string $returnAddress (Optional) address to return deposit to if anything goes wrong with exchange
     * @param string $destTag (Optional) Destination tag that you want appended to a Ripple payment to you
     * @param string $rsAddress (Optional) For new NXT accounts to be funded, you supply this on NXT payment to you
     * @param string $apiKey (Optional) Your affiliate PUBLIC KEY, for volume tracking, affiliate payments, split-shifts, etc...
     */
    public function shift($withdrawal, $pair, $returnAddress = null, $destTag = null, $rsAddress = null, $apiKey = null)
    {
        $query = [
            'withdrawal' => $withdrawal,
            'pair' => $pair
        ];
        
        if(isset($returnAddress)) $query['returnAddress'] = $returnAddress;
        if(isset($destTag)) $query['destTag'] = $destTag;
        if(isset($rsAddress)) $query['rsAddress'] = $rsAddress;
        if(isset($apiKey)) $query['apiKey'] = $apiKey;
        
        return $this->httpClient->post('/shift/', $query);
    }
    
    /**
     * Request Email Receipt
     * 
     * This call requests a receipt for a transaction. 
     * The email address will be added to the conduit associated with that transaction as well. 
     * (Soon it will also send receipts to subsequent transactions on that conduit)
     * 
     * @param string $email the address for receipt email to be sent to
     * @param string $txid the transaction id of the transaction TO the user (ie the txid for the withdrawal NOT the deposit)
     */
    public function mail($email, $txid)
    {
        $query = [
            'email' => $email,
            'txid' => $txid
        ];
        
        return $this->httpClient->post('/mail/', $query);
    }
    
    /**
     * Fixed Amount Transaction
     *
     * This call allows you to request a fixed amount to be sent to the withdrawal address. 
     * You provide a withdrawal address and the amount you want sent to it. We return the amount 
     * to deposit and the address to deposit to. This allows you to use shapeshift as a payment mechanism. 
     * This call also allows you to request a quoted price on the amount of a transaction without a withdrawal address.
     *
     * @param float $amount the amount to be sent to the withdrawal address
     * @param string $withdrawal the address for resulting coin to be sent to
     * @param string $pair what coins are being exchanged in the form [input coin]_[output coin]  ie btc_ltc
     * @param string $returnAddress (Optional) address to return deposit to if anything goes wrong with exchange
     * @param string $destTag (Optional) Destination tag that you want appended to a Ripple payment to you
     * @param string $rsAddress (Optional) For new NXT accounts to be funded, you supply this on NXT payment to you
     * @param string $apiKey (Optional) Your affiliate PUBLIC KEY, for volume tracking, affiliate payments, split-shifts, etc...
     */
    public function sendAmount($amount, $pair, $withdrawal = null,  $returnAddress = null, $destTag = null, $rsAddress = null, $apiKey = null)
    {
        $query = [
            'amount' => $amount,
            'pair' => $pair
        ];
        
        if(isset($withdrawal)) $query['withdrawal'] = $withdrawal;
        if(isset($returnAddress)) $query['returnAddress'] = $returnAddress;
        if(isset($destTag)) $query['destTag'] = $destTag;
        if(isset($rsAddress)) $query['rsAddress'] = $rsAddress;
        if(isset($apiKey)) $query['apiKey'] = $apiKey;
        
        return $this->httpClient->post('/sendamount/', $query);
    }
    
    /**
     * Quote Send Exact Price
     * 
     * This call allows you to request a quoted price on the amount of a transaction.
     * 
     * @param float $amount the amount for witch to provide a quote
     * @param string $pair what coins are being exchanged in the form [input coin]_[output coin]  ie btc_ltc
     */
    public function quotedPrice($amount, $pair)
    {
        return $this->sendAmount($amount, $pair);
    }
    
    public function cancelPending($address)
    {
        $query = [
            'address' => $address,
        ];
        
        return $this->httpClient->post('/cancelpending/', $query);
    }
}