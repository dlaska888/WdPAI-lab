<?php

namespace src\LinkyRouting\enums;

enum HttpStatusCode : int
{
    // Informational responses
    case CONTINUE = 100;
    case SWITCHING_PROTOCOLS = 101;

    // Successful responses
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;

    // Redirection
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;

    // Client errors
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;

    // Server errors
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case SERVICE_UNAVAILABLE = 503;

}
