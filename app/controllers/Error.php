<?php

class Error extends Controller {

    private $errors = [
        400 => [
            'Bad Request',
            'Your browser sent a request that this server could not understand.'],
        401 => array(
            'Authorization Required',
            'This server could not verify that you are authorized to 
            access the document requested. Either you supplied the 
            wrong credentials (e.g., bad password], or your browser 
            doesn\'t understand how to supply the credentials required.'),
        402 => [
            'Payment Required',
            'INTERROR'],
        403 => [
            'Forbidden',
            'You don\'t have permission to access REQURID on this 
            server.'],
        404 => [
            'Not Found',
            'We couldn\'t find <acronym title="REQURID">that uri
            </acronym> on our server, though it\'s most certainly not 
            your fault.'],
        405 => [
            'Method Not Allowed',
            'The requested method THEREQMETH is not allowed for the URL 
            REQURID.'],
        406 => [
            'Not Acceptable',
            'An appropriate representation of the requested resource 
            REQURID could not be found on this server.'],
        407 => [
            'Proxy Authentication Required',
            'This server could not verify that you are authorized to 
            access the document requested. Either you supplied the wrong 
            credentials (e.g., bad password], or your browser doesn\'t 
            understand how to supply the credentials required.'],
        408 => [
            'Request Time-out',
            'Server timeout waiting for the HTTP request from the client.'],
        409 => [
            'Conflict',
            'INTERROR'],
        410 => [
            'Gone',
            'The requested resourceREQURIDis no longer available on 
            this server and there is no forwarding address. Please remove 
            all references to this resource.'],
        411 => [
            'Length Required',
            'A request of the requested method GET requires a valid 
            Content-length.'],
        412 => [
            'Precondition Failed',
            'The precondition on the request for the URL REQURID 
            evaluated to false.'],
        413 => [
            'Request Entity Too Large',
            'The requested resource REQURID does not allow request 
            data with GET requests, or the amount of data provided in the 
            request exceeds the capacity limit.'],
        414 => [
            'Request-URI Too Large',
            'The requested URL\'s length exceeds the capacity limit for 
            this server.'],
        415 => [
            'Unsupported Media Type',
            'The supplied request data is not in a format acceptable for 
            processing by this resource.'],
        416 => [
            'Requested Range Not Satisfiable',
            ''],
        417 => [
            'Expectation Failed',
            'The expectation given in the Expect request-header field could 
            not be met by this server. The client sent <code>Expect:</code>'],
        422 => [
            'Unprocessable Entity',
            'The server understands the media type of the request entity, but 
           was unable to process the contained instructions.'],
        423 => [
            'Locked',
            'The requested resource is currently locked. The lock must be released 
            or proper identification given before the method can be applied.'],
        424 => [
            'Failed Dependency',
            'The method could not be performed on the resource because the requested 
            action depended on another action and that other action failed.'],
        425 => [
            'No code',
            'INTERROR'],
        426 => [
            'Upgrade Required',
            'The requested resource can only be retrieved using SSL. The server is 
            willing to upgrade the current connection to SSL, but your client 
            doesn\'t support it. Either upgrade your client, or try requesting 
            the page using https://'],
        500 => [
            'Internal Server Error',
            'INTERROR'],
        501 => [
            'Method Not Implemented',
            'GET to REQURID not supported.'],
        502 => [
            'Bad Gateway',
            'The proxy server received an invalid response from an upstream server.'],
        503 => [
            'Service Temporarily Unavailable',
            'The server is temporarily unable to service your request due to 
            maintenance downtime or capacity problems. Please try again later.'],
        504 => [
            'Gateway Time-out',
            'The proxy server did not receive a timely response from the 
            upstream server.'],
        505 => [
            'HTTP Version Not Supported',
            'INTERROR'],
        506 => [
            'Variant Also Negotiates',
            'A variant for the requested resource <code>REQURID</code> 
            is itself a negotiable resource. This indicates a configuration error.'],
        507 => [
            'Insufficient Storage',
            'The method could not be performed on the resource because the 
            server is unable to store the representation needed to successfully 
            complete the request. There is insufficient free space left in your 
            storage allocation.'],
        510 => [
            'Not Extended',
            'A mandatory extension policy in the request is not accepted by the 
            server for this resource.']
    ];

    public function index($errorCode) {

        $errorCode = empty($this->errors[$errorCode]) ? 404 : $errorCode;

        $this->view->title = $errorCode . ' ' . $this->errors[$errorCode][0];
        $this->view->explanation = $this->errors[$errorCode][1];                        
        $this->view->statusHTTP=$errorCode . ' ' . $this->errors[$errorCode][0];       
        $this->view->generatePage('error/index');
    }

}
