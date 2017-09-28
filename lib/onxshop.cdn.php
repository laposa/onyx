<?php
/**
 * Copyright (c) 2012-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Cdn {

    /**
     * Process output HTML code and remap static content URIs
     * 
     * @param  string $html HTML code to process.
     * @return string       Processed HTML code
     */
    public function processOutputHtml($html)
    {
        $allowed_tags = array();
        if (strpos(ONXSHOP_CDN_ALLOWED_CONTEXT, 'img') !== FALSE) $allowed_tags[] = '<img.*?src="';
        if (strpos(ONXSHOP_CDN_ALLOWED_CONTEXT, 'link') !== FALSE) $allowed_tags[] = '<link.*?href="';
        if (strpos(ONXSHOP_CDN_ALLOWED_CONTEXT, 'a') !== FALSE) $allowed_tags[] = '<a.*?href="';
        if (strpos(ONXSHOP_CDN_ALLOWED_CONTEXT, 'script') !== FALSE) $allowed_tags[] = '<script.*?src="';

        // html tag and attribute, shloud be available as $matches[1]
        $regexp_tag = "(" . implode("|", $allowed_tags) . ")";

        // rest of the tag, shloud be available as $matches[3]
        $regexp_rest = '(\".*?>)';

        $html = preg_replace_callback("/{$regexp_tag}(.*?){$regexp_rest}/",
            array($this, 'processUri'), $html);

        return $html;
    }



    /**
     * Callback function to process matched elements
     * 
     * @param  array $matches Matched elements in the subject string
     * @return string         Replacement string
     */
    protected function processUri($matches)
    {
        $type = $this->getContentType($matches[2]);

        if (strlen($type) == 0 || strpos(ONXSHOP_CDN_ALLOWED_TYPES, $type) === FALSE) 
            return $matches[0];

        $result = $matches[1] . 
            $this->getServiceNodeUrl($matches[2], null) . 
            $matches[2] .$matches[3];

        return $result;
    }



    /**
     * Get file extension from given URI
     * @param  string $uri Resource URI
     * @return string      Lowercase extension
     */
    protected function getContentType($uri)
    {
        // remove query string
        $result = preg_replace('/\?.*/', '', $uri);
        // remove path
        $result = basename($result);
        // convert to lowercase
        $result = strtolower($result);
        // get extension
        $result = substr($result, strrpos($result, '.') + 1);

        return $result;
    }



    /**
     * Override this function to implement customized service node
     * detection (load balancing, location based detection, 
     * content type based detection etc.)
     * 
     * @param  string $relativeUri Relative Uri of linked content
     * @param  string $context     Context of linked content (html tag)
     * @return [type] [description]
     */
    protected function getServiceNodeUrl($relativeUri, $context)
    {
        return ONXSHOP_CDN_HOST;
    }

}