<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 14.07.2019
 * Time: 02:07
 */

namespace App;


class CustomRequestCaptcha
{
    /**
     * @return \ReCaptcha\RequestMethod\Post
     */
    public function custom()
    {
        return new \ReCaptcha\RequestMethod\Post();
    }
}
