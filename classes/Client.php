<?php

namespace classes;

class Client
{
    /**
     * Returns User Agent
     * @return string
     */
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Returns requested url
     * @return string
     */
    public function getRequestUrl(): string
    {
        if (isset($_SERVER['HTTP_REFERER'])){
            return $_SERVER['HTTP_REFERER'];
        }

        return (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns client's ip address
     * @return string
     */
    public function getIpAddress(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

}