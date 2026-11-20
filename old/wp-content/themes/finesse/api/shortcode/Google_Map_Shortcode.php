<?php

class Google_Map_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $LOC_TYPE_ATTR = "loc_type";
    static $ADDRESS_ATTR = "address";
    static $LATITUDE_ATTR = "lat";
    static $LONGITUDE_ATTR = "lng";
    static $ZOOM_ATTR = "zoom";
    static $HEIGHT_ATTR = "height";

    function render($attr, $inner_content = null, $code = '')
    {
        extract(shortcode_atts(array(
            Google_Map_Shortcode::$LOC_TYPE_ATTR => 'address',
            Google_Map_Shortcode::$ADDRESS_ATTR => '',
            Google_Map_Shortcode::$LATITUDE_ATTR => '',
            Google_Map_Shortcode::$LONGITUDE_ATTR => '',
            Google_Map_Shortcode::$ZOOM_ATTR => '17',
            Google_Map_Shortcode::$HEIGHT_ATTR => '400'), $attr));

        if ($loc_type == 'address' && empty($address)) {
            return $this->get_error('The <em>address</em> attribute must not be empty!');
        }

        if ($loc_type != 'address' && (empty($lat) || empty($lng))) {
            return $this->get_error('The <em>lat</em> (latitude) and the <em>lng</em> (longitude) attributes must not be empty!');
        }

        if (empty($zoom)) {
            return $this->get_error('The <em>zoom</em> attribute is required!');
        }

        if (empty($height)) {
            return $this->get_error('The <em>height</em> attribute is required!');
        }

        if ($loc_type == 'address') {
            $data = 'data-address="' . $address . '"';
        } else {
            $data = 'data-lat="' . $lat . '" data-lng="' . $lng . '"';
        }
        return '<section><div class="map" ' . $data . ' data-zoom="' . $zoom . '" style="width: 100%; height: ' . $height . 'px;"></div></section>';
    }

    function get_names()
    {
        return array('gmap');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-gmap-form" class="generic-form" method="post" action="#" data-sc="gmap">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-gmap-type">' . __('Localization Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-gmap-type" name="sc-gmap-type">';
        $content .= '<option value="address">' . __('By Address', 'finesse') . '</option>';
        $content .= '<option value="coord">' . __('By Coordinates', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div id="sc-gmap-address-wrapper">';
        $content .= '<label for="sc-gmap-address">' . __('Address', 'finesse') . ':</label>';
        $content .= '<input id="sc-gmap-address" name="sc-gmap-address" type="text">';
        $content .= '</div>';

        $content .= '<div id="sc-gmap-lat-wrapper" style="display:none">';
        $content .= '<label for="sc-gmap-lat">' . __('Latitude', 'finesse') . ':</label>';
        $content .= '<input id="sc-gmap-lat" name="sc-gmap-lat" type="text">';
        $content .= '</div>';
        $content .= '<div id="sc-gmap-lng-wrapper" style="display:none">';
        $content .= '<label for="sc-gmap-lng">' . __('Longitude', 'finesse') . ':</label>';
        $content .= '<input id="sc-gmap-lng" name="sc-gmap-lng" type="text">';
        $content .= '</div>';

        $content .= '<div>';
        $content .= '<label for="sc-gmap-zoom">' . __('Map Zoom', 'finesse') . ':</label>';
        $content .= '<input id="sc-gmap-zoom" name="sc-gmap-zoom" type="text" value="17" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-gmap-height">' . __('Map Height', 'finesse') . ':</label>';
        $content .= '<input id="sc-gmap-height" name="sc-gmap-height" type="text" value="400" class="required">';
        $content .= '</div>';

        $content .= '<div >';
        $content .= '<input id="sc-gmap-form-submit" type="submit" name="submit" value="' . __('Insert Google Map', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Others', 'finesse');
    }

    function get_title()
    {
        return __('Google Map', 'finesse');
    }
}
