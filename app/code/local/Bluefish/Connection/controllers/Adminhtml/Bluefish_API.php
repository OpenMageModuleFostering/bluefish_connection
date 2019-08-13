<?php 
include("Bluefish_Error_Reporting.php");
/**
 * Defines the different OAuth Signing algorithms. You 
 * should use this instead of writing them out each time.
 */
 
class OAUTH_ALGORITHMS
{
   const RSA_SHA1 = 'RSA-SHA1';
}

 
function sign_rsa_sha1($method, $baseurl, $certfile, array $parameters)
{
	$fp = fopen($certfile, "r");
    $private = fread($fp, 8192);
    fclose($fp);

    $data = $method.'&';
     $data .= urlencode($baseurl).'&';
    $oauth = '';
	
    ksort($parameters);
    foreach($parameters as $key => $value)
        $oauth .= "&{$key}={$value}";
    	$data .= rawurlencode(substr($oauth, 1));
    $keyid = openssl_get_privatekey($private);
    @openssl_sign($data, $signature, $keyid);
    @openssl_free_key($keyid);
	return base64_encode($signature);
}


/**
 * Assemble an associative array with oauth values
 *
 * @param string $baseurl the base url we are authenticating against.
 * @param string $key your consumer key
 * @param string $secret either your consumer secret key or the file location of your rsa private key.
 * @param array $extra additional oauth parameters that should be included (you must urlencode, if appropriate, before calling this function)
 * @param string $method either GET or POST
 * @param string $algo either HMAC-SHA1 or RSA-SHA1 (NOTE: this affects what you put in for the secret parameter)
 * @return array of all the oauth parameters
 */
 
function build_auth_array($baseurl, $key, $secret, $extra = array(), $method = 'GET', $algo = OAUTH_ALGORITHMS::RSA_SHA1)
{
	$credentials=Mage::getStoreConfig('mycustom_section/mycustom_auth_group');
	$auth['oauth_consumer_key'] = $key;
    $auth['oauth_signature_method'] = $algo;
    $auth['oauth_timestamp'] = time();
	$auth['oauth_token'] = $credentials['mycustom_codeapitoken'];
    $auth['oauth_nonce'] = md5(uniqid(rand(), true));
    $auth['oauth_version'] = '1.0';

	if(isset($extra) && !empty($extra))
	{
		$auth = array_merge($extra, $auth);
	}

    if(strtoupper($algo) == OAUTH_ALGORITHMS::RSA_SHA1)
	$auth['oauth_signature'] = sign_rsa_sha1($method, $baseurl, $secret, $auth);
	
	$auth['oauth_signature'] = urlencode($auth['oauth_signature']);

    return $auth;
}

function get_auth_header($baseurl, $key, $secret, $extra = array(), $method = 'GET', $algo = OAUTH_ALGORITHMS::RSA_SHA1)
{
    $auth = build_auth_array($baseurl, $key, $secret, $extra, $method, $algo);
}

function build_auth_string(array $authparams)
{
    $header = "Authorization: OAuth ";
    foreach($authparams AS $key=>$value)
        $header .= ",{$key}=\"{$value}\"";
	
	return array($header);
}


function _build_http_query($params)
{
	if (!$params) return '';

	$keys = array_keys($params);
	$values = array_values($params);
	$params = array_combine($keys, $values);

	// Parameters are sorted by name, using lexicographical byte value ordering.
	// Ref: Spec: 9.1.1 (1)
	//uksort($params, 'strcmp');

	$pairs = array();
	foreach ($params as $parameter => $value)
	{
		if (is_array($value))
		{
			// If two or more parameters share the same name, they are sorted by their value
			// Ref: Spec: 9.1.1 (1)
			// June 12th, 2010 - changed to sort because of issue 164 by hidetaka
			sort($value, SORT_STRING);
			foreach ($value as $duplicate_value)
			{
				$pairs[] = $parameter . '=' . $duplicate_value;
			}
		}

		else
		{
			$pairs[] = $parameter . '=' . $value;
		}
	}
	// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
	// Each name-value pair is separated by an '&' character (ASCII code 38)
	return implode('&', $pairs);
}
?>  