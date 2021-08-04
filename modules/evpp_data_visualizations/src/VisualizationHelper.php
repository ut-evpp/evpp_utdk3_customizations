<?php

namespace Drupal\evpp_data_visualizations;

use Drupal\node\Entity\Node;

/**
 * Business logic for rendering the content type.
 */
class VisualizationHelper {

  /**
   * Helper function to parse script/param tags & return a URL.
   *
   * @param string $string
   *   The user-supplied embed code (or possibly just a URL).
   *
   * @return string
   *   A URL to render in an iframe
   */
  public static function prepareEmbed($string) {
    // If the user supplied a URL, just use that.
    if (filter_var($string, FILTER_VALIDATE_URL)) {
      // https://iq-analytics.austin.utexas.edu/views/Facultydiversitydashboard/Facultyraceandethnicity?:showAppBanner=false&amp;:display_count=n&amp;:showVizHome=n&amp;:origin=viz_share_link&amp;:size=800,900&amp;:embed=y&amp;:showVizHome=n&amp;:bootstrapWhenNotified=y&amp;:tabs=n&amp;:toolbar=n&amp;:apiID=host0#navType=1&amp;navSrc=Parse
      $url_parts = parse_url($string);
      if (!empty($url_parts['host']) && !empty($url_parts['path'])) {
        $url = 'https://' . $url_parts['host'] . $url_parts['path'];
      }
    }
    else {
      // Otherwise, retrieve the URL from the embed code.
      // Example:
      /*
      <script type='text/javascript' src='https://iq-analytics.austin.utexas.edu/javascripts/api/viz_v1.js'></script>
      <div class='tableauPlaceholder' style='width: 760px; height: 627px;'>
        <object class='tableauViz' width='760' height='627' style='display:none;'>
          <param name='host_url' value='https%3A%2F%2Fiq-analytics.austin.utexas.edu%2F' />
          <param name='embed_code_version' value='3' />
          <param name='site_root' value='' />
          <param name='name' value='Facultydiversitydashboard&#47;TTNTTnumbers' />
          <param name='tabs' value='no' />
          <param name='toolbar' value='yes' />
        </object>
      </div>
      */
      // LibXML requires that the html is wrapped in a root node.
      $string = '<root>' . $string . '</root>';
      $dom = new \DOMDocument();
      libxml_use_internal_errors(TRUE);
      $dom->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
      $params = $dom->getElementsByTagName('param');
      foreach ($params as $param) {
        $name = $param->getAttribute('name');
        $value = $param->getAttribute('value');
        if ($name === 'host_url') {
          $host = urldecode($value);
        }
        if ($name === 'name') {
          $dashboard = urldecode($value);
        }
      }
      if (filter_var($host . $dashboard, FILTER_VALIDATE_URL)) {
        $url = $host . 'views/' . $dashboard;
      }
    }
    if ($url) {
      return '<iframe src="' . $url . '?:showVizHome=no&:embed=true&:toolbar=n"></iframe>';
    }
    return '';
  }

}
