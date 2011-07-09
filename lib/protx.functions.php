<?
/**********************************************************************
** Script Name:   Functions.php                                      **
** Version:       1.0	- 29/07/2002                                 **
** Author:        Pat Fox                                            **
** Function:	Contains simple procedures to encode, encrypt,       **
**              decode, decrypt and split the information POSTed     **
**              to and from VSP Form.                                **
**                                                                   **
** Revision History:                                                 **
** Version  Author      Date and notes                               **
**    1.0   Mat Peck	18/01/2002 - First ASP release               **
**    1.1   Mat Peck    07/03/2002 - Base64 routines patched         **
**    1.2   Pat Fox     29/07/2002 - PHP version                     **
**    1.2 Tony Welch    9/07/2003 - Addition of post code fields 2.21**
**********************************************************************/

// ** Set variables to indentify the vendor **

	$VendorName="TestVendor";
	$Password="testvendor";

//** Your server's IP address or dns name and web app directory.  Fully qualified **
//** Examples : $MyServer="https://www.newco.com/PHPFormKit/", $MyServer="192.168.0.1/PHPFormKit", $MyServer="http://localhost/PHPFormKit/" **

	$MyServer="http://localhost/PHPFormKit/";

//** The protx site to send information to **

//** Test site **
	$vspsite="https://ukvpstest.protx.com/vps2form/submit.asp";
	
//** Live site - ONLY uncomment when going live **
	// $vspsite="https://ukvps.protx.com/vps2form/submit.asp";


// ** Base 64 Encoding function **
// PHP does it natively but just for consistency and ease of maintenance, let's declare our own function
function base64Encode($plain) {
	// Initialise output variable
	$output = "";
	
	// Do encoding
	$output = base64_encode($plain);
	
	// Return the result
	return $output;
}


// ** Base 64 decoding function **
// PHP does it natively but just for consistency and ease of maintenance, let's declare our own function

function base64Decode($scrambled) {
	// Initialise output variable
	$output = "";
	
	// Do encoding
	$output = base64_decode($scrambled);
	
	// Return the result
	return $output;
}


/*  The SimpleXor encryption algorithm                                                                                **
**  NOTE: This is a placeholder really.  Future releases of VSP Form will use AES or TwoFish.  Proper encryption      **
** 			This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering    **
**			It won't stop a half decent hacker though, but the most they could do is change the amount field to something **
**			else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still      **
**			more secure than the other PSPs who don't both encrypting their forms at all                                  */

function simpleXor($InString, $Key) {
	// Initialise key array
	$KeyList = array();
	// Initialise out variable
	$output = "";
	
	// Convert $Key into array of ASCII values
	for($i = 0; $i < strlen($Key); $i++){
		$KeyList[$i] = ord(substr($Key, $i, 1));
	}

	// Step through string a character at a time
	for($i = 0; $i < strlen($InString); $i++) {
		// Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
		// % is MOD (modulus), ^ is XOR
		$output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
	}

	// Return the result
	return $output;
}

/* The getToken function.                                                                                         **
** NOTE: A function of convenience that extracts the value from the "name=value&name2=value2..." VSP reply string **
**     Works even if one of the values is a URL containing the & or = signs.                                      */

function getToken($thisString) {

	// List the possible tokens
	$Tokens = array("Status","StatusDetail","VendorTxCode","VPSTxID","TxAuthNo","Amount","AVSCV2");

	// Initialise arrays
	$output = array();
	$resultArray = array();
	
	// Get the next token in the sequence
	for ($i = count($Tokens)-1; $i >= 0 ; $i--){
		// Find the position in the string
		$start = strpos($thisString, $Tokens[$i]);
		// If it's present
		if ($start !== false){
			// Record position and token name
			$resultArray[$i]->start = $start;
			$resultArray[$i]->token = $Tokens[$i];
		}
	}
	
	// Sort in order of position
	sort($resultArray);

	// Go through the result array, getting the token values
	for ($i = 0; $i<count($resultArray); $i++){
		// Get the start point of the value
		$valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
		// Get the length of the value
		if ($i==(count($resultArray)-1)) {
			$output[$resultArray[$i]->token] = substr($thisString, $valueStart);
		} else {
			$valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
			$output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
		}			

	}

	// Return the ouput array
	return $output;

}

// Randomise based on time
function randomise() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}


?>
