<?php
  namespace Xparse\Parser;

  /**
   *
   * @package Xparse\Parser
   */
  class ParserElementFinder extends \Xparse\ElementFinder\ElementFinder {


    /**
     * Convert relative links to absolute
     *
     * @param string $currentUrl
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function convertLinksToAbsolute($currentUrl) {
      $link = parse_url($currentUrl);
      $link['path'] = !empty($link['path']) ? $link['path'] : '/';
      $realDomain = $link['scheme'] . '://' . rtrim($link['host'], '/') . '/';
      $linkWithoutParams = $realDomain . trim($link['path'], '/');
      $linkPath = $realDomain . trim(preg_replace('!/([^/]+)$!', '', $link['path']), '/');
      $getBaseUrl = $this->attribute('//base/@href')->item(0);
      if (!empty($getBaseUrl)) {
        $getBaseUrl = rtrim($getBaseUrl, '/') . '/';
      }
      $srcElements = $this->elements('//*[@src] | //*[@href] | //form[@action]');
      foreach ($srcElements as $element) {
        if ($element->hasAttribute('src') == true) {
          $attrName = 'src';
        } elseif ($element->hasAttribute('href') == true) {
          $attrName = 'href';
        } elseif ($element->hasAttribute('action') == true and $element->tagName == 'form') {
          $attrName = 'action';
        } else {
          continue;
        }
        $oldPath = $element->getAttribute($attrName);
        # don`t change javascript in href
        if (preg_match('!^\s*javascript\s*:\s*!', $oldPath)) {
          continue;
        }
        if (empty($oldPath)) {
          # URL is empty. So current url is used
          $newPath = $currentUrl;
        } else if ((strpos($oldPath, './') === 0)) {
          # Current level
          $newPath = $linkPath . substr($oldPath, 2);
        } else if (strpos($oldPath, '//') === 0) {
          # Current level
          $newPath = $link['scheme'] . ':' . $oldPath;
        } else if ($oldPath[0] == '/') {
          # start with single slash
          $newPath = $realDomain . ltrim($oldPath, '/');
        } else if ($oldPath[0] == '?') {
          # params only
          $newPath = $linkWithoutParams . $oldPath;
        } elseif ((!preg_match('!^[a-z]+://!', $oldPath))) {
          # url without schema
          if (empty($getBaseUrl)) {
            $newPath = $linkPath . '/' . $oldPath;
          } else {
            $newPath = $getBaseUrl . $oldPath;
          }
        } else {
          $newPath = $oldPath;
        }
        $element->setAttribute($attrName, $newPath);
      }

      return $this;
    }

    /**
     * Get Default data from form.
     * Form is get by $path
     * Return key->value array where key is name of field
     *
     * @param string $path xpath to form
     * @return array
     */
    public function getDefaultFormData($path) {
      $formData = array();
      # textarea
      foreach ($this->elements($path . '//textarea') as $textArea) {
        $formData[$textArea->getAttribute('name')] = $textArea->nodeValue;
      }

      # radio and checkboxes
      foreach ($this->elements($path . '//input[@checked]') as $textArea) {
        $formData[$textArea->getAttribute('name')] = $textArea->getAttribute('value');
      }

      # hidden, text, submit
      $hiddenAndTextElements = $this->elements($path . '//input[@type="hidden" or @type="text" or @type="submit" or not(@type)]');
      foreach ($hiddenAndTextElements as $element) {
        $formData[$element->getAttribute('name')] = $element->getAttribute('value');
      }

      # select
      $selectItems = $this->object($path . '//select', true);
      foreach ($selectItems as $select) {
        $name = $select->attribute('//select/@name')->item(0);
        $firstValue = $select->value('//option[1]')->item(0);
        $selectedValue = $select->value('//option[@selected]')->item(0);
        $formData[$name] = !empty($selectedValue) ? $selectedValue : $firstValue;;
      }
      return $formData;
    }

    /**
     * @param $xpath
     * @param array $data
     */
    public function submitForm($xpath, $data = []) {

    }

  }
