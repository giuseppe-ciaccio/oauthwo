<?php

/**
 * 
 * JWS.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Builder class to create JSON Web Signature objects
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */

class Oauth_Builder_JWS extends Oauth_Builder_JWT {
    
    /**
     * This JWS header
     *
     * @var string
     */
    protected $header;
    /**
     * private key location. Used to sign the secured payload
     *
     * @var string
     */
    protected $key;
    
    /**
     * Construct a JWS builder given a sign key location
     *
     * @param string $private_key_location 
     */
    public function __construct($private_key_location) {
        $this->header = array();
        //type is JSON Web Token
        $this->set_header('typ', 'JWT');
        //Signing algo is RSA with 256bit key
        $this->set_header('alg', 'RS256');
        $this->key=$private_key_location;
    }
    
    /**
     * Builds a JWS signing the secured input value
     *
     * @param string $plaintext
     * @return string
     */
    public function get_token($payload){
        $secured_input = $this->build_secured_input($payload);
        $signature = $this->sign($secured_input);
        if ($signature == NULL) return NULL;
        $signed_token = sprintf("%s.%s",$secured_input,$signature);
        return $signed_token;
    }
      
    /**
     * Sign the secured input.
     *
     * @param string $secured_input
     * @return string
     */
    private function sign($secured_input){
        $signature = NULL;

        //signing algorithm
        $algo = "sha256";

        //obtain key from location
        $fp = fopen($this->key, "r");
        if ($fp == NULL) return FALSE;
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);

        $res = openssl_sign($secured_input, $signature, $pkeyid, $algo);

        openssl_free_key($pkeyid);
        if (!$res) return FALSE;
        return $this->get_base64_encode($signature);
    }
    
    /**
     * Internal/helper function to construct the secured input, which is a 
     * "." concatenation of the base64 encoding of header json encoding and 
     * payload
     *
     * @param string $payload
     * @return string
     */
    private function build_secured_input($payload){
        //base 64 encoding of the payload
        $enc_payload = $this->get_base64_encode($payload);
        //json encoding of the header
        $header = json_encode($this->header);
        //base 64 encoding of the jsonized header
        $enc_header = $this->get_base64_encode($header);
        //concat with a dot
        return sprintf("%s.%s",$enc_header,$enc_payload);
    }

}
