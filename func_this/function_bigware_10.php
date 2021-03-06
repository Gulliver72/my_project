<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015
  
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015  Bigware LTD

  Copyrightvermerke duerfen nicht entfernt werden.
  ------------------------------------------------------------------------
  Dieses Programm ist freie Software. Sie koennen es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation
  veroeffentlicht, weitergeben und/oder modifizieren, entweder gemaess Version 2 
  der Lizenz oder (nach Ihrer Option) jeder spaeteren Version.
  Die Veroeffentlichung dieses Programms erfolgt in der Hoffnung, dass es Ihnen
  von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die
  implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT FUER EINEN
  BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.
  
  Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
  Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

  Infos:
  ------------------------------------------------------------------------
  Der Bigware Shop wurde vor vielen Jahren bereits aus dem bekannten Shopsystem osCommerce
  weiter- und neuentwickelt.
  Der Bigware Shop legt im hohen Masse Wert auf Bedienerfreundlichkeit, beinhaltet eine leichte
  Installation, viele neue professionelle Werkzeuge und zeichnet sich aus durch eine grosse 
  Community, die bei Problemen weiterhelfen kann.
  
  Der Bigware Shop ist auf jedem System lauffaehig, welches eine PHP Umgebung
  (ab PHP 4.1.3) und mySQL zur Verfuegung stellt und auf Linux basiert.
 
  Hilfe erhalten Sie im Forum auf www.bigware.de 
  
  -----------------------------------------------------------------------
  
 ##################################################################################




*/
?>
<?php
function implode_assoc($array, $inner_glue='=', $outer_glue='&') {
       $output = array();
       foreach( $array as $key => $item )
               $output[] = $key . $inner_glue . $item;
       return implode($outer_glue, $output);
}
function short_name($str, $limit=3){
  if (defined('SEO_URLS_FILTER_SHORT_WORDS')) $limit = SEO_URLS_FILTER_SHORT_WORDS;
  $foo = explode('-', $str);
  foreach($foo as $index => $value){
    switch (true){
      case ( strlen($value) <= $limit ):
        continue;
      default:
        $container[] = $value;
        break;
    }    
  } # end foreach
  $container = ( sizeof($container) > 1 ? implode('-', $container) : $str );
  return $container;
}  
  function go_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $SID;
  $seo = ( defined('SEO_URLS') ? SEO_URLS : false );
  $seo_rewrite_type = ( defined('SEO_URLS_TYPE') ? SEO_URLS_TYPE : false );
  $seo_pages = array($GLOBALS['CONFIG_NAME_FILE']['main_bigware_29'], $GLOBALS['CONFIG_NAME_FILE']['main_bigware_34']);
  if ( !in_array($page, $seo_pages) ) $seo = false;
    if (!go_not_null($page)) {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>');
    }
  
  if ($page == '/') $page = '';
  
    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
      $seo_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
    $seo_rewrite_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == true) {
        $link = HTTPS_SERVER . FOLDER_RELATIV_HTTPS_CATALOG;
        $seo_link = HTTPS_SERVER . FOLDER_RELATIV_HTTPS_CATALOG;
        $seo_rewrite_link = HTTPS_SERVER . FOLDER_RELATIV_HTTPS_CATALOG;
      } else {
        $link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
        $seo_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
        $seo_rewrite_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL</b><br><br>');
    }
    if (go_not_null($parameters)) {
      $link .= $page . '?' . go_output_string($parameters);      
    $separator = '&';
    # Start exploding the parameters to extract the values
    # Also, we could use analysis_str($parameters) and would probably be more clean
    if ($seo == 'true'){
      $p = explode('&', $parameters);
      krsort($p);
      $params = array();
      
      if ( $seo_rewrite_type == 'Rewrite' ){
        foreach ($p as $index => $valuepair) {
          $p2 = explode('=', $valuepair);          
          switch ($p2[0]){        
          case 'items_id':
            $rewrite_item = true;           
            if ( defined('ITEM_NAME_'.$p2[1]) ){
              $rewrite_page_item = short_name(constant('ITEM_NAME_'.$p2[1])) . '-p-' . $p2[1] . '.html';
            } else { $seo = false; }
            break;    
          
          case 'bigPfad': 
            $rewrite_category = true;
            if ( defined('CATEGORY_NAME_'.$p2[1]) ){
              $rewrite_page_category = short_name(constant('CATEGORY_NAME_'.$p2[1])) . '-c-' . $p2[1] . '.html';
            } else { $seo = false; }
            break; 
          case 'producers_id': 
            $rewrite_producer = true;
            if ( defined('PRODUCER_NAME_'.$p2[1]) ){
              $rewrite_page_producer = short_name(constant('PRODUCER_NAME_'.$p2[1])) . '-m-' . $p2[1] . '.html';
            } else { $seo = false; }
            break; 
          default:
            $params[$p2[0]] = $p2[1]; 
            break;
          } # switch
        } # end foreach
        $params_stripped = implode_assoc($params);
        switch (true){
          case ( $rewrite_item && $rewrite_category ):
          case ( $rewrite_item ):
            $rewrite_page = $rewrite_page_item;
            $rewrite_category = false;
            break;
          case ( $rewrite_category ):
            $rewrite_page = $rewrite_page_category;
            break; 
          case ( $rewrite_producer ):
            $rewrite_page = $rewrite_page_producer;
            break; 
          default:
            $seo = false;
            break;
        } #end switch true  
        $seo_rewrite_link .= $rewrite_page . ( go_not_null($params_stripped) ? '?'.go_output_string($params_stripped) : '' );   
        $separator = ( go_not_null($params_stripped) ? '&' : '?' );
      } else {
        foreach ($p as $index => $valuepair) {
          $p2 = explode('=', $valuepair);          
          switch ($p2[0]){        
          case 'items_id':           
            if ( defined('ITEM_NAME_'.$p2[1]) ){
              $params['pName'] = constant('ITEM_NAME_'.$p2[1]);
            } else { $seo = false; }
            break;    
          
          case 'bigPfad': 
            if ( defined('CATEGORY_NAME_'.$p2[1]) ){
              $params['cName'] = constant('CATEGORY_NAME_'.$p2[1]);
            } else { $seo = false; }
            break; 
          case 'producers_id': 
            if ( defined('PRODUCER_NAME_'.$p2[1]) ){
              $params['mName'] = constant('PRODUCER_NAME_'.$p2[1]);
            } else { $seo = false; }
            break; 
          default:
            $params[$p2[0]] = $p2[1]; 
            break;
          } # switch
        } # end foreach      
        $params_stripped = implode_assoc($params);  
        $seo_link .= $page . '?'.go_output_string($params_stripped);   
        $separator = '&';
      } # end if/else
    } # end if $seo
  } else {
      $link .= $page;
      $separator = '?';
    $seo = false;
    } # end if(go_not_null($parameters)
    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1); 
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (go_not_null($SID)) {
        $_sid = $SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
          $_sid = go_session_name() . '=' . go_session_id();
        }
      }
    }
    if ( ('SEARCH_ENGINE_FRIENDLY_URLS' == 'true') && ($search_engine_safe == true) ) {
      while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);
      while (strstr($seo_link, '&&')) $seo_link = str_replace('&&', '&', $seo_link);
      $link = str_replace('?', '/', $link);
      $link = str_replace('&', '/', $link);
      $link = str_replace('=', '/', $link);
      $seo_link = str_replace('?', '/', $seo_link);
      $seo_link = str_replace('&', '/', $seo_link);
      $seo_link = str_replace('=', '/', $seo_link);
      $seo_rewrite_link = str_replace('?', '/', $seo_rewrite_link);
      $seo_rewrite_link = str_replace('&', '/', $seo_rewrite_link);
      $seo_rewrite_link = str_replace('=', '/', $seo_rewrite_link);
      $separator = '?';
    }
  if (isset($_sid)) {
      $link .= $separator . go_output_string($_sid);
      $seo_link .= $separator . go_output_string($_sid);
      $seo_rewrite_link .= $separator . go_output_string($_sid);
  }
   
  if ($seo == 'true') {
    return ($seo_rewrite_type == 'Rewrite' ? $seo_rewrite_link : $seo_link);
  } else {
    return $link;
  }
  }  
  function go_picture($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if ( (empty($src) || ($src == FOLDER_RELATIV_PICTURES)) && (PICTURE_REQUIRED == 'false') ) {
      return false;
    }  
    
    //////// if in the database a picture with "http://" from extern.
      $control_is_extern = $src;
      $pos1 = substr_count($control_is_extern, 'http://');
      $pos2 = substr_count($control_is_extern, 'https://');
      if ($pos1 > 1) {
        $control_is_extern = substr($control_is_extern, 7);
        $src = strstr($control_is_extern, 'http://');
      }
      elseif($pos1 > 0) {
        $src = strstr($control_is_extern, 'http://');
      }
      if ($pos2 > 1) {
        $control_is_extern = substr($control_is_extern, 8);
        $src = strstr($control_is_extern, 'https://');
      }
      elseif($pos2 > 0) {
        $src = strstr($control_is_extern, 'https://');
      }
    /////////////////// extern end

    $picture = '<img src="' . go_output_string($src) . '" alt="' . go_output_string($alt) . '"';
    if (go_not_null($alt)) {
      $picture .= ' title=" ' . go_output_string($alt) . ' "';
    } 
  global $binary_gateway;
    if ($binary_gateway == '') { 
      if ( (CONFIG_CALCULATE_PICTURE_SIZE == 'true') && (empty($width) || empty($height)) ) {
        if ($picture_size = @getimagesize($src)) {
          if (empty($width) && go_not_null($height)) {
            $ratio = $height / $picture_size[1];
            $width = $picture_size[0] * $ratio;
          } elseif (go_not_null($width) && empty($height)) {
            $ratio = $width / $picture_size[0];
            $height = $picture_size[1] * $ratio;
          } elseif (empty($width) && empty($height)) {
            $width = $picture_size[0];
            $height = $picture_size[1];
          }
        } elseif (PICTURE_REQUIRED == 'false') {
          return false;
        }
      } 
  } 
    if (go_not_null($width) && go_not_null($height)) {
      $picture .= ' width="' . go_output_string($width) . '" height="' . go_output_string($height) . '"';
    }
    if (go_not_null($parameters)) $picture .= ' ' . $parameters;
    $picture .= '>';
    return $picture;
  }   
  function go_picture_submit($picture, $alt = '', $parameters = '') { 
  global $language, $binary_gateway;
    if (defined('BUTTON_STYLE_CSS') && BUTTON_STYLE_CSS == '1'){
      if ($picture == 'button_add_address.gif'){ $input_value = BUTTON_ADD_ADDRESS; }
      if ($picture == 'button_affiliate_build_a_li.gif' OR $picture == 'button_affiliate_build_a_link.gif'){ $input_value = BUTTON_AFFILI_BUILD_LINK; }
      if ($picture == 'button_back.gif'){ $input_value = BUTTON_BACK; }
      if ($picture == 'button_buy_now.gif'){ $input_value = BUTTON_BUY_NOW; }
      if ($picture == 'button_buy_now_domain.gif'){ $input_value = BUTTON_BUY_NOW_DOMAIN; }
      if ($picture == 'button_change_address.gif'){ $input_value = BUTTON_CHANGE_ADRESS; }
      if ($picture == 'button_change_member.gif'){ $input_value = BUTTON_CHANGE_MEMBER; }
      if ($picture == 'button_checkout.gif'){ $input_value = BUTTON_CHECKOUT; }
      if ($picture == 'button_confirm.gif'){ $input_value = BUTTON_CONFIRM; }
      if ($picture == 'button_confirm_order.gif'){ $input_value = BUTTON_CONFIRM_ORDER; }
      if ($picture == 'button_continue.gif'){ $input_value = BUTTON_CONTINUE; }
      if ($picture == 'button_continue_b2b.gif'){ $input_value = BUTTON_CONTINUE_BTOB; }
      if ($picture == 'button_continue_shopping.gif'){ $input_value = BUTTON_CONTINUE_SHOPPING; }
      if ($picture == 'button_directory_to_address.gif'){ $input_value = BUTTON_DIRECTORY_TO_ADRESS; }
      if ($picture == 'button_fragen_now.gif'){ $input_value = BUTTON_FRAGEN_NOW; }
      if ($picture == 'button_history.gif'){ $input_value = BUTTON_HISTORY; }
      if ($picture == 'button_in_cart.gif'){ $input_value = BUTTON_IN_CART; }
      if ($picture == 'button_info.gif'){ $input_value = BUTTON_INFO; }
      if ($picture == 'button_informs.gif'){ $input_value = BUTTON_INFORMS; }
      if ($picture == 'button_login.gif'){ $input_value = BUTTON_LOGIN; }
      if ($picture == 'button_next.gif'){ $input_value = BUTTON_NEXT; }
      if ($picture == 'button_preview.gif'){ $input_value = BUTTON_PREVIEW; }
      if ($picture == 'button_redeem.gif'){ $input_value = BUTTON_REDEEM; }
      if ($picture == 'button_remove_informs.gif'){ $input_value = BUTTON_REMOVE_INFORM; }
      if ($picture == 'button_reviews.gif'){ $input_value = BUTTON_REVIEWS; }
      if ($picture == 'button_search.gif'){ $input_value = BUTTON_SEARCH; }
      if ($picture == 'button_send.gif'){ $input_value = BUTTON_SEND; }
      if ($picture == 'button_update.gif'){ $input_value = BUTTON_UPDATE; }
      if ($picture == 'button_update_cart.gif'){ $input_value = BUTTON_UPDATE_CART; }
      if ($picture == 'button_write_review.gif'){ $input_value = BUTTON_WRITEREVIEW; }
      if ($picture == 'small_delete.gif'){ $input_value = BUTTON_DELETE; }
      if ($picture == 'small_change.gif'){ $input_value = BUTTON_CHANGE; }
      if ($picture == 'small_view.gif'){ $input_value = BUTTON_VIEW; }
      if ($picture == 'button_quick_find.gif'){ $input_value = GO_BUTTON; }
      if ($picture == 'button_tell_a_friend.gif'){ $input_value = TELL_A_FRIEND_BUTTON; }
      if (go_not_null($input_value)){
        $picture_submit = '';
        $picture_submit .= '
        <button class="big_button" type="submit"';  
      }else{
        if ($binary_gateway == '') {
            $picture_submit = '<input class="big_button innet" type="image" src="' . go_output_string(FOLDER_RELATIV_LANG_TEMPLATES . $language . '/picture/buttons/' . $picture) . '" border="0" alt="' . go_output_string($alt) . '"';
        } else {
            $picture_submit = '<input class="big_button innet" type="image" src="' . go_output_string($binary_gateway . HTTP_SERVER . FOLDER_RELATIV_LANG_TEMPLATES . $language . '/picture/buttons/' . $picture) . '" border="0" alt="' . go_output_string($alt) . '"';
        } 
      }

      if (go_not_null($alt)) $picture_submit .= ' title=" ' . go_output_string($alt) . ' "';
      if (go_not_null($parameters)) $picture_submit .= ' ' . $parameters;
      if (go_not_null($input_value)){
        $picture_submit .= '>
           <span class="innet">
              <span class="l"></span>
              <span class="r"></span>
              <span class="t">
                ' . $input_value . '
              </span>
           </span>
        </button>';
        //$picture_submit .= '>';
      }else{
        $picture_submit .= '>';
      }
    }else{
      if ($binary_gateway == '') {
          $picture_submit = '<input type="image" src="' . go_output_string(FOLDER_RELATIV_LANG_TEMPLATES . $language . '/picture/buttons/' . $picture) . '" border="0" alt="' . go_output_string($alt) . '"';
      } else {
          $picture_submit = '<input type="image" src="' . go_output_string($binary_gateway . HTTP_SERVER . FOLDER_RELATIV_LANG_TEMPLATES . $language . '/picture/buttons/' . $picture) . '" border="0" alt="' . go_output_string($alt) . '"';
      } 
      if (go_not_null($alt)) $picture_submit .= ' title=" ' . go_output_string($alt) . ' "';
      if (go_not_null($parameters)) $picture_submit .= ' ' . $parameters;
      $picture_submit .= '>';
    }
    return $picture_submit;
  }  
  function go_picture_button($picture, $alt = '', $parameters = '') { 
  global $language, $binary_gateway;

    if (BUTTON_STYLE_CSS == '1'){
      if ($picture == 'button_add_address.gif'){ $input_value = BUTTON_ADD_ADDRESS; }
      if ($picture == 'button_affiliate_build_a_li.gif' OR $picture == 'button_affiliate_build_a_link.gif'){ $input_value = BUTTON_AFFILI_BUILD_LINK; }
      if ($picture == 'button_back.gif'){ $input_value = BUTTON_BACK; }
      if ($picture == 'button_buy_now.gif'){ $input_value = BUTTON_BUY_NOW; }
      if ($picture == 'button_buy_now_domain.gif'){ $input_value = BUTTON_BUY_NOW_DOMAIN; }
      if ($picture == 'button_change_address.gif'){ $input_value = BUTTON_CHANGE_ADRESS; }
      if ($picture == 'button_change_member.gif'){ $input_value = BUTTON_CHANGE_MEMBER; }
      if ($picture == 'button_checkout.gif'){ $input_value = BUTTON_CHECKOUT; }
      if ($picture == 'button_confirm.gif'){ $input_value = BUTTON_CONFIRM; }
      if ($picture == 'button_confirm_order.gif'){ $input_value = BUTTON_CONFIRM_ORDER; }
      if ($picture == 'button_continue.gif'){ $input_value = BUTTON_CONTINUE; }
      if ($picture == 'button_continue_b2b.gif'){ $input_value = BUTTON_CONTINUE_BTOB; }
      if ($picture == 'button_continue_shopping.gif'){ $input_value = BUTTON_CONTINUE_SHOPPING; }
      if ($picture == 'button_directory_to_address.gif'){ $input_value = BUTTON_DIRECTORY_TO_ADRESS; }
      if ($picture == 'button_fragen_now.gif'){ $input_value = BUTTON_FRAGEN_NOW; }
      if ($picture == 'button_history.gif'){ $input_value = BUTTON_HISTORY; }
      if ($picture == 'button_in_cart.gif'){ $input_value = BUTTON_IN_CART; }
      if ($picture == 'button_info.gif'){ $input_value = BUTTON_INFO; }
      if ($picture == 'button_informs.gif'){ $input_value = BUTTON_INFORMS; }
      if ($picture == 'button_login.gif'){ $input_value = BUTTON_LOGIN; }
      if ($picture == 'button_next.gif'){ $input_value = BUTTON_NEXT; }
      if ($picture == 'button_preview.gif'){ $input_value = BUTTON_PREVIEW; }
      if ($picture == 'button_redeem.gif'){ $input_value = BUTTON_REDEEM; }
      if ($picture == 'button_remove_informs.gif'){ $input_value = BUTTON_REMOVE_INFORM; }
      if ($picture == 'button_reviews.gif'){ $input_value = BUTTON_REVIEWS; }
      if ($picture == 'button_search.gif'){ $input_value = BUTTON_SEARCH; }
      if ($picture == 'button_send.gif'){ $input_value = BUTTON_SEND; }
      if ($picture == 'button_update.gif'){ $input_value = BUTTON_UPDATE; }
      if ($picture == 'button_update_cart.gif'){ $input_value = BUTTON_UPDATE_CART; }
      if ($picture == 'button_write_review.gif'){ $input_value = BUTTON_WRITEREVIEW; }
      if ($picture == 'small_delete.gif'){ $input_value = BUTTON_DELETE; }
      if ($picture == 'small_change.gif'){ $input_value = BUTTON_CHANGE; }
      if ($picture == 'small_view.gif'){ $input_value = BUTTON_VIEW; }
      if ($picture == 'button_quick_find.gif'){ $input_value = GO_BUTTON; }
      if ($picture == 'button_tell_a_friend.gif'){ $input_value = TELL_A_FRIEND_BUTTON; }
       $picture_button = '
       <span class="big_button">
         <span class="innet">
            <span class="l"></span>
            <span class="r"></span>
            <span class="t">
      ';
      if (go_not_null($input_value)){
        $picture_button .= $input_value;        
      }else{
        if ($binary_gateway == '') {  
            return go_picture(FOLDER_RELATIV_LANG_TEMPLATES . $language . '/picture/buttons/' . $picture, $alt, '', '', $parameters);
        } else {
            return go_picture($binary_gateway . HTTP_SERVER . FOLDER_RELATIV_TEMPLATES . 'lang_images/' . $language . '/picture/buttons/' . $picture, $alt, '', '', $parameters);
        } 
      }
      $picture_button .= '
            </span>
        </span>
      </span>';
        return $picture_button;
    }else{  
    
      if ($binary_gateway == '') {  
          return go_picture(FOLDER_RELATIV_LANG_TEMPLATES . $language . '/picture/buttons/' . $picture, $alt, '', '', $parameters);
      } else {
          return go_picture($binary_gateway . HTTP_SERVER . FOLDER_RELATIV_TEMPLATES . 'lang_images/' . $language . '/picture/buttons/' . $picture, $alt, '', '', $parameters);
      } 
    }
  }  
  function go_fetch_dividing_up($picture = 'pixel_black.gif', $width = '100%', $height = '1') {
    return go_picture(FOLDER_RELATIV_PIC_SYSTEM_TEMPLATES . $picture, '', $width, $height);
  }  
  function go_fetch_form($name, $action, $method = 'post', $parameters = '', $secure = false) {
    $form = '<form name="' . go_output_string($name) . '" action="' . go_output_string($action) . '" method="' . go_output_string($method) . '"';
    if (go_not_null($parameters)) $form .= ' ' . $parameters;
    $form .= '>';
    if ($secure === true) $form .= '<input type="hidden" name="csrf" value="' . $_SESSION['csrf_token'] . '">';
    
    return $form;
  }  
  function go_fetch_inputfeld($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {

    $field = '<input type="' . go_output_string($type) . '" name="' . go_output_string($name) . '"';

    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      
    if (is_array($GLOBALS[$name]) AND go_output_string($name) == 'keywords'){
      $field .= ' value=""';
    }  
    else{
    
      $field .= ' value="' . go_output_string(stripslashes($GLOBALS[$name])) . '"';
    }    
         
    } elseif (go_not_null($value)) {
      
     if (is_array($value) AND go_output_string($name) == 'keywords'){
      $field .= ' value=""';
    }  
    else{
      $field .= ' value="' . go_output_string($value) . '"';
    }      
    }
    if (go_not_null($parameters)) $field .= ' ' . $parameters;
    $field .= '>';
    return $field;
  }  
  function go_fetch_password_field($name, $value = '', $parameters = 'maxlength="40"') {
    return go_fetch_inputfeld($name, $value, $parameters, 'password', false);
  }  
  function go_fetch_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
    $selection = '<input type="' . go_output_string($type) . '" name="' . go_output_string($name) . '"';
    if (go_not_null($value)) $selection .= ' value="' . go_output_string($value) . '"';
    if ( ($checked == true) || ( isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) && ( ($GLOBALS[$name] == 'on') || (isset($value) && (stripslashes($GLOBALS[$name]) == $value)) ) ) ) {
      $selection .= ' CHECKED';
    }
    if (go_not_null($parameters)) $selection .= ' ' . $parameters;
    $selection .= '>';
    return $selection;
  }  
  function go_fetch_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
    return go_fetch_selection_field($name, 'checkbox', $value, $checked, $parameters);
  }  
  function go_fetch_radio_field($name, $value = '', $checked = false, $parameters = '') {
    return go_fetch_selection_field($name, 'radio', $value, $checked, $parameters);
  }  
  function go_fetch_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea name="' . go_output_string($name) . '" wrap="' . go_output_string($wrap) . '" cols="' . go_output_string($width) . '" rows="' . go_output_string($height) . '"';
    if (go_not_null($parameters)) $field .= ' ' . $parameters;
    $field .= '>';
    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= stripslashes($GLOBALS[$name]);
    } elseif (go_not_null($text)) {
      $field .= $text;
    }
    $field .= '</textarea>';
    return $field;
  }  
  function go_fetch_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" name="' . go_output_string($name) . '"';
    if (go_not_null($value)) {
      $field .= ' value="' . go_output_string($value) . '"';
    } elseif (isset($GLOBALS[$name])) {
      $field .= ' value="' . go_output_string(stripslashes($GLOBALS[$name])) . '"';
    }
    if (go_not_null($parameters)) $field .= ' ' . $parameters;
    $field .= '>';
    return $field;
  }  
  function go_hide_session_id() {
    global $session_started, $SID;
    if (($session_started == true) && go_not_null($SID)) {
      return go_fetch_hidden_field(go_session_name(), go_session_id());
    }
  }  
  function go_fetch_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . go_output_string($name) . '"';
    if (go_not_null($parameters)) $field .= ' ' . $parameters;
    $field .= '>';
    if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);
    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . go_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' SELECTED';
      }
      $field .= '>' . go_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';
    if ($required == true) $field .= TEXT_FIELD_REQUIRED;
    return $field;
  }   
  function go_get_land_list($name, $selected = '', $parameters = '') {
    $lands_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    $lands = go_get_lands();
    for ($i=0, $n=sizeof($lands); $i<$n; $i++) {
      $lands_array[] = array('id' => $lands[$i]['lands_id'], 'text' => $lands[$i]['lands_name'], 'iso' => $lands[$i]['lands_iso_code_2']);
    }
    return go_fetch_pull_down_menu($name, $lands_array, $selected, $parameters);
  } 
  function go_get_iso_list($name, $selected = '', $parameters = '') {
    $lands_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    $lands = go_get_lands();
    for ($i=0, $n=sizeof($lands); $i<$n; $i++) {
      $lands_array[] = array('id' => $lands[$i]['lands_iso_code_2'], 'text' => $lands[$i]['lands_name']);
    }
    return go_fetch_pull_down_menu($name, $lands_array, $selected, $parameters);
  }   
  function go_get_source_list($name, $show_other = false, $selected = '', $parameters = '') {
    $sources_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    $sources = go_get_sources();
    for ($i=0, $n=sizeof($sources); $i<$n; $i++) {
      $sources_array[] = array('id' => $sources[$i]['sources_id'], 'text' => $sources[$i]['sources_name']);
    }
    if ($show_other == 'true') {
      $sources_array[] = array('id' => '9999', 'text' => PULL_DOWN_OTHER);
    }
    return go_fetch_pull_down_menu($name, $sources_array, $selected, $parameters);
  }
  //by EDE
  function go_fetch_captcha_field($name, $value = '', $parameters = 'maxlength="40"') {
    return go_fetch_inputfeld($name, $value, $parameters, 'text', false);
  }  
  // END
?>
