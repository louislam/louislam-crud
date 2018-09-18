<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Field;

class GoogleMap extends FieldType
{
    protected static $apiKey = null;
    protected $type = "text";
    protected $longitudeField;

    /**
     * GoogleMap constructor.
     * @param Field $longitudeField Field for storing longitude
     */
    function __construct($longitudeField) {
        $longitudeField->setFieldType(new Hidden());
        $this->longitudeField = $longitudeField;
    }

    /**
     * Render Field for Create/Edit
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = false)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $crud = $this->field->getCRUD();
        $key = self::$apiKey;
        $longitudeFieldName = $this->longitudeField->getName();

        $html  = <<< HTML
        <div class="form-group">
            <label for="field-$name">$display</label>
            <input id="field-$name" class="form-control"  type="hidden" name="$name" value="$value" $readOnly $required />
            <div id="map-$name" style="width:100%; height:500px"></div>
        </div>
HTML;

        $crud->addScript(<<< HTML
        

<script src="https://maps.googleapis.com/maps/api/js?key=$key"></script>
        
<script>
    $(document).ready(function () {
        var latitude =  $("#field-$name").val();
        var longitude = $("#field-$longitudeFieldName").val();
        var zoom = 12;
        var LatLng = new google.maps.LatLng(latitude, longitude);

        var mapOptions = {
              zoom: zoom,
              center: LatLng,
              panControl: false,
              zoomControl: false,
              scaleControl: true,
              mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("map-$name"), mapOptions);

        var marker = new google.maps.Marker({
            position: LatLng,
            map: map,
            title: 'Drag Me!',
            draggable: true
        });
        
        google.maps.event.addListener(map, 'click', function(event) {
           marker.setPosition(event.latLng);
           $("#field-$name").val(latLng.lat());
           $("#field-$longitudeFieldName").val(latLng.lng());
        });

        google.maps.event.addListener(marker, 'dragend', function(marker) {
            var latLng = marker.latLng;
            $("#field-$name").val(latLng.lat());
            $("#field-$longitudeFieldName").val(latLng.lng());
        });
    });
</script>
HTML
        );

        if ($echo)
            echo $html;

        return $html;
    }

    public static function setAPIKey($apiKey) {
        self::$apiKey = $apiKey;
    }

}