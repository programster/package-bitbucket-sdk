<?php

namespace Programster\Bitbucket;


enum HttpMethod : string
{
    // requests a representation of the specified resource. Requests using GET should only retrieve data.
    case GET = "GET";

    // replaces all current representations of the target resource with the request payload.
    case PUT = "PUT";

    // submits an entity to the specified resource, often causing a change in state or side effects on the server.
    case POST = "POST";

    // deletes the specified resource.
    case DELETE = "DELETE";

    // applies partial modifications to a resource.
    case PATCH = "PATCH";

    // describes the communication options for the target resource.
    case OPTIONS = "OPTIONS";

    // asks for a response identical to a GET request, but without the response body.
    case HEAD = "HEAD";

    // establishes a tunnel to the server identified by the target resource.
    case CONNECT = "CONNECT";

    // performs a message loop-back test along the path to the target resource.
    case TRACE = "TRACE";
}