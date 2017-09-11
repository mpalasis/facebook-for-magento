<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree. An additional grant
 * of patent rights can be found in the PATENTS file in the code directory.
 */

if (file_exists(__DIR__.'/../lib/fb.php')) {
  include_once __DIR__.'/../lib/fb.php';
} else {
  include_once 'Facebook_AdsExtension_lib_fb.php';
}

if (file_exists(__DIR__.'/FacebookProductFeed.php')) {
  include_once 'FacebookProductFeed.php';
} else {
  include_once 'Facebook_AdsExtension_Model_FacebookProductFeed.php';
}

class FacebookProductFeedXML extends FacebookProductFeed {

  const XML_FEED_FILENAME = 'facebook_adstoolbox_product_feed.xml';

  const XML_HEADER = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:g="http://base.google.com/ns/1.0">
  <!-- auto generated by Facebook Marketing Solutions, v%s, time: %s -->
  <title>%s</title>
  <link rel="self" href="%s"/>
EOD;

  const XML_SHIPPINGTMP = <<<EOD
    <g:shipping>
        <g:country>%s</g:country>
        <g:service>%s</g:service>
        <g:price>%s</g:price>
    </g:shipping>
EOD;

  const XML_FOOTER = <<<EOD
</feed>
EOD;


  protected function getFileName() {
    return self::XML_FEED_FILENAME;
  }

  protected function buildHeader() {
    return sprintf(
      self::XML_HEADER,
      FacebookAdsExtension::version(),
      date('F j, Y, g:i a'),
      Mage::app()->getStore()->getName(),
      Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
  }

  protected function buildFooter() {
    return self::XML_FOOTER;
  }

  protected function xmlescape($t) {
    return htmlspecialchars($t, ENT_XML1);
  }

  protected function buildProductAttr($attr_name, $attr_value) {
    $text = $this->buildProductAttrText($attr_name, $attr_value, 'xmlescape');
    if ($text) {
      return sprintf('    <g:%s>%s</g:%s>', $attr_name, $text, $attr_name);
    } else {
      return '';
    }
  }

  protected function buildProductEntry($product, $product_name) {
    $items = array_values(parent::buildProductEntry($product, $product_name));
    array_unshift($items, "<entry>");
    $items[] = "</entry>";
    return implode("\n", array_filter(array_values($items)));
  }
}
