<?php

class ModelPaymentAdyenGiropay extends Model {

    public function getMethod($address, $total) {
        $this->language->load('payment/adyen_giropay');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('adyen_giropay_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('adyen_giropay_total') > 0 && $this->config->get('adyen_giropay_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('adyen_giropay_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $currencies = array(
            'AUD',
            'CAD',
            'EUR',
            'GBP',
            'JPY',
            'USD',
            'NZD',
            'CHF',
            'HKD',
            'SGD',
            'SEK',
            'DKK',
            'PLN',
            'NOK',
            'HUF',
            'CZK',
            'ILS',
            'MXN',
            'MYR',
            'BRL',
            'PHP',
            'TWD',
            'THB',
            'TRY'
        );

        if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'adyen_giropay',
                'title' => $this->language->get('text_title'),
                'sort_order' => $this->config->get('adyen_giropay_sort_order'),
                'html'=>'<span class="giro-img"></span>'
            );
        }

        return $method_data;
    }

}

?>