<?php

class ControllerSaleManageOrder extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('sale/order');
        $this->load->language('sale/manageorder');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/manageorder');

        $this->getList();
    }

    public function delete() {
        $this->load->language('sale/order');
        $this->load->language('sale/manageorder');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        if (isset($this->request->post['selected']) && ($this->validateDelete())) {
            foreach ($this->request->post['selected'] as $order_id) {
                $this->model_sale_order->deleteOrder($order_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['pre_page'])) {
                $url .= '&pre_page=' . $this->request->get['pre_page'];
            }

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_invoice_id'])) {
                $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . $this->request->get['filter_email'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . $this->request->get['filter_customer'];
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }

            if (isset($this->request->get['filter_total'])) {
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function printpopup() {
        $this->load->model('sale/manageorder');
        $this->data['pendingOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount(1);

        if (empty($this->data['pendingOrderCount'])) {
            $this->data['pendingOrderCount'] = 0;
        }

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('Pending');

        $data = array(
            'filter_order_status_id' => $order_status_id
        );
        if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {
            $data['filter_order_ids'] = $this->request->post['selected'];
            unset($data['filter_order_status_id']);
        }

        $url = '';
        $this->data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['print_picklist'] = $this->url->link('sale/manageorder/printpicklist', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['print_labels'] = $this->url->link('sale/manageorder/printlabels', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $orders_data = array();
        $results = $this->model_sale_manageorder->getOrders($data);
        $this->load->model('sale/order');

        foreach ($results as $result) {
            $orders_data[] = array(
                'order_id' => $result['order_id'],
                'email' => $result['email']
            );
        }

        $this->data['orders'] = $orders_data;
        $this->template = 'sale/manageorder_popup.tpl';
        $this->response->setOutput($this->render());
    }

    public function pickedorderpopup() {
        $this->load->model('sale/manageorder');

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('processing');

        $this->data['order_status_id'] = $this->model_sale_manageorder->getOrderStatusId('orders picked');

        $this->data['pendingOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount($order_status_id);

        if (empty($this->data['pendingOrderCount'])) {
            $this->data['pendingOrderCount'] = 0;
        }

        $data = array(
            'filter_order_status_id' => $order_status_id
        );

        if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {
            $data['filter_order_ids'] = $this->request->post['selected'];
        }

        $url = '';
        $this->data['addhistory'] = $this->url->link('sale/manageorder/addhistory', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $orders_data = array();
        $results = $this->model_sale_manageorder->getOrders($data);
        $this->load->model('sale/order');

        foreach ($results as $result) {
            $orders_data[] = array(
                'order_id' => $result['order_id'],
                'email' => $result['email']
            );
        }

        $this->data['orders'] = $orders_data;
        $this->template = 'sale/orderpicked_popup.tpl';
        $this->response->setOutput($this->render());
    }

    public function exportpopup() {
        $this->load->model('sale/manageorder');

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('orders picked');

        $this->data['pendingOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount($order_status_id);

        if (empty($this->data['pendingOrderCount'])) {
            $this->data['pendingOrderCount'] = 0;
        }

        $data = array(
            'filter_order_status_id' => $order_status_id
        );

        if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {
            $data['filter_order_ids'] = $this->request->post['selected'];
        }

        $url = '';
        $this->data['export'] = $this->url->link('sale/manageorder/export', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $orders_data = array();
        $results = $this->model_sale_manageorder->getOrders($data);
        $this->load->model('sale/order');

        foreach ($results as $result) {
            $orders_data[] = array(
                'order_id' => $result['order_id'],
                'email' => $result['email']
            );
        }

        $this->data['orders'] = $orders_data;
        $this->template = 'sale/exportorder_popup.tpl';
        $this->response->setOutput($this->render());
    }

    public function printlabels() {
        if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {

            require_once('../tcpdf/config/tcpdf_config.php');
            require_once('../tcpdf/tcpdf.php');

// create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->header_line_color = array(255, 255, 255);

// set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(0);
            $pdf->SetFooterMargin(0);

// remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

// set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
            if (@file_exists('../tcpdf/lang/eng.php')) {
                require_once('../tcpdf/lang/eng.php');
//$pdf->setLanguageArray($l);
            }

            $this->load->model('sale/order');
            $this->load->model('sale/manageorder');

            $order_status_id = $this->model_sale_manageorder->getOrderStatusId('processing');

            $i = 1;
            foreach ($this->request->post['selected'] as $key => $order_id) {
                $this->model_sale_manageorder->update($order_id, $order_status_id);
                for ($k = 1; $k <= $this->request->post['parcels'][$order_id]; $k++) {
                    $orderinfo = $this->model_sale_order->getOrder($order_id);

                    if ($i % 8 == 0) {
                        $pageVal = 8;
                    } else {
                        $pageVal = $i % 8;
                    }


// add a page
                    if ($pageVal == 1) {
                        $pdf->AddPage();
                    }

                    if ($i % 2 == 0) {
                        $x = 106;
                        $y = abs(floor(($pageVal - 1) / 2)) * 68;
                    } else {
                        $x = 0;
                        $y = abs(floor($pageVal / 2)) * 68;
                    }



                    $pdf->SetFont('arial', '', 8);
                    $txt = "Bloembollen vooor de tuin\n";
                    $pdf->MultiCell(70, 50, $txt, 0, 'J', false, 1, $x + 6, $y + 15, true, 0, false, true, 0, 'T', false);

                    $pdf->SetFont('arialb', 'B', 8);
                    $txt = "ParcelPlus\n";
                    $pdf->MultiCell(70, 50, $txt, 0, 'J', false, 1, $x + 82, $y + 15, true, 0, true, true, 0, 'T', false);

                    $pdf->SetFont('arial', '', 8);
                    $txt = $orderinfo['shipping_firstname'] . ' ' . $orderinfo['shipping_lastname'] . '<br/>'
                            . $orderinfo['shipping_address_1'] . ' ' . $orderinfo['shipping_address_2'] . '<br/>'
                            . $orderinfo['shipping_city'] . ' ' . $orderinfo['shipping_zone'] . '<br/>'
//                        . $orderinfo['shipping_postcode'] . '<br/>'
                            . $orderinfo['shipping_country'];
//                        . $orderinfo['telephone'];

                    $pdf->MultiCell('', '', $txt, 0, 'J', false, 1, $x + 16, $y + 22, true, 0, true, true, 0, 'T', false);
                    $pdf->SetY(15);

// -----------------------------------------------------------------------------

                    $pdf->SetFont('arial', '', 10);

// define barcode style
                    $style = array(
                        'position' => '',
                        'align' => '',
                        'stretch' => false,
                        'fitwidth' => false,
                        'cellfitalign' => '',
                        'border' => false,
//                    'hpadding' => 'auto',
//                    'vpadding' => 'auto',
                        'fgcolor' => array(0, 0, 0),
                        'bgcolor' => array(255, 255, 255),
                        'text' => true,
                        'font' => 'helvetica',
                        'fontsize' => 8,
                        'stretchtext' => 0
                    );
// PRINT VARIOUS 1D BARCODES
// EAN 8
                    $pdf->write1DBarcode($order_id, 'C39', $x + 10, $y + 45, 60, 18, 0.4, $style, 'N');

                    $i++;
                }
            }

//Close and output PDF document
            $pdf->Output('label.pdf', 'I');
        }
    }

    public function printpicklist() {

        if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {
            require_once('../tcpdf/config/tcpdf_config.php');
            require_once('../tcpdf/tcpdf.php');

// create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->header_line_color = array(0, 0, 0);

// set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(false);
            $pdf->SetFooterMargin(false);

// remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

// set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $this->load->model('sale/order');
            $this->load->model('sale/manageorder');

            $pdf->AddPage();

            $pdf->SetFont('arial', '', 8);

//            $pdf->MultiCell(70, 50, "Order Id\n", 0, 'J', false, 1, PDF_MARGIN_LEFT, 15, true, 0, false, true, 0, 'T', false);
//            $pdf->MultiCell('', '', "Customer\n", 0, 'J', false, 1, PDF_MARGIN_LEFT + 20, 15, true, 0, true, true, 0, 'T', false);
//            $pdf->MultiCell('', '', "Products\n", 0, 'J', false, 1, PDF_MARGIN_LEFT + 80, 15, true, 0, true, true, 0, 'T', false);

            $html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>
	table.first {
		color: #000000;
	}
	td {		
                text-align:left;
                vertical-align:text-top;
                border-bottom: 1px solid grey;
	}
	
        div{
                padding-bottom: 5px;
                padding-left: 5px;
        }
</style>
<table class="first" cellpadding="4" cellspacing="6">
   <thead> 
   <tr>
        <td width="60" align="left"><b>Order Id</b></td>
        <td align="left"><b>Customer</b></td>
        <td align="left"><b>Products</b></td>
        <td align="left"><b>SKU</b></td>
        <td align="left"><b>Quantity</b></td>
    </tr>
                    </thead>
                    <tbody>
EOF;
            foreach ($this->request->post['selected'] as $key => $order_id) {

                $orderinfo = $this->model_sale_order->getOrder($order_id);

                $shipping_info = $orderinfo['shipping_firstname'] . ' ' . $orderinfo['shipping_lastname'] . '<br/>'
                        . $orderinfo['shipping_address_1'] . ' ' . $orderinfo['shipping_address_2'] . '<br/>'
                        . $orderinfo['shipping_city'] . ' ' . $orderinfo['shipping_zone'] . '<br/>'
                        . $orderinfo['shipping_country'];

                $products = $this->model_sale_manageorder->getOrderProductsInfo($order_id);
                $i = 0;
                foreach ($products as $product) {
                    $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
                    $html .= <<<EOF
    <tr>
EOF;
                    if ($i == 0) {
                        $html .= '<td width="60" rowspan="' . count($products) . '" align="left">' . $order_id . '</td>';
                        $html .= '<td rowspan="' . count($products) . '" align="left">' . $shipping_info . '</td>';
                    }
                    $html .= '<td align="left">' . $product['name'];
                    if ($options) {
                        foreach ($options as $option) {
                            if ($option['type'] != 'file') {
                                $value = $option['value'];
                            } else {
                                $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                            }
                            $html .= '<div>' . $option['name'] . " ( " . $value . " ) " . "</div>";
                        }
                    }
                    $html .= <<<EOF
        </td>
EOF;
                    $html .= '<td align="left">' . $product['sku'] . '</td>';
                    $html .= '<td align="left">' . $product['quantity'] . '</td>';
                    $html .= <<<EOF
    </tr>
EOF;
                    $i++;
                }
            }
            $html .= <<<EOF
</tbody></table> 
EOF;

            // echo $html;
// output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');
//Close and output PDF document
            $pdf->Output('label.pdf', 'I');
        }
    }

    public function updatestatus() {

        include('abcmail/classes/Socket.php');
        include('abcmail/classes/URL.php');
        include('abcmail/classes/Request.php');

        if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $order_id) {

                $RQ = new HTTP_Request('https://customer.abcmailgroup.com/test/cgi-bin/ParcelInfo.dll/track');
                $RQ->setMethod(HTTP_REQUEST_METHOD_POST);
                $RQ->addPostData('userid', 'Blum01');
                $RQ->addPostData('Password', 'Welkom01');
                $RQ->addPostData('keycode', $order_id . '-1');
                $RQ->sendRequest();

                $response = $RQ->getResponseBody();

                $p = xml_parser_create();
                xml_parse_into_struct($p, $response, $vals);
                xml_parser_free($p);
                if ($vals) {
                    foreach ($vals as $val) {
                        if ($val['tag'] == 'PODSTATUS') {
                            echo $update_status = "UPDATE  `" . DB_PREFIX . "order`  SET delivery_status = '" . $val['value'] . "' WHERE `order_id`=" . $order_id;
                            $this->db->query($update_status);
                        }
                    }
                }
            }

            $url = '';

            if (isset($this->request->get['pre_page'])) {
                $url .= '&pre_page=' . $this->request->get['pre_page'];
            }

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_invoice_id'])) {
                $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . $this->request->get['filter_email'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . $this->request->get['filter_customer'];
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }

            if (isset($this->request->get['filter_total'])) {
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
        }
        if (!isset($this->error['warning']))
            $this->error['warning'] = $this->language->get('error_noselected');

        $this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    public function export() {

        include('abcmail/classes/Socket.php');
        include('abcmail/classes/URL.php');
        include('abcmail/classes/Request.php');

        $this->load->language('sale/order');
        $this->load->language('sale/manageorder');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/manageorder');

        if (isset($this->request->post['selected']) && ($this->validateDelete())) {
            $selectid = '';
            foreach ($this->request->post['selected'] as $order_id) {
                $selectid.=$order_id . ',';
            }
            $orderid = substr($selectid, 0, strlen($selectid) - 1);

            $query = "SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` IN ( " . $orderid . " )";

            $result = $this->db->query($query);

            $theXml = '<?xml version="1.0" encoding="UTF-8"?>
                               <Shipment>
                               <Userid>Blum01</Userid>
                               <Password>Welkom01</Password>';
            foreach ($result->rows as $row) {
                $query_total = "SELECT text FROM `" . DB_PREFIX . "order_total` WHERE `order_id` =" . $row['order_id'] . " and code='total'";
                $result_total = $this->db->query($query_total);
                $total_val = str_replace(",", ".", preg_replace('/^[^0-9]+/', '', $result_total->row['text']));
                if (isset($this->request->post['select']['parcels'][$row['order_id']]) && !empty($this->request->post['select']['parcels'][$row['order_id']])) {
                    for ($i = 1; $i <= $this->request->post['select']['parcels'][$row['order_id']]; $i++) {
                        $theXml .='<Parcel>';
                        $theXml .='<Keycode>' . $row['order_id'] . '</Keycode>';
                        $theXml .='<Name1>' . $row['firstname'] . '</Name1>';
                        $theXml .='<Name2>' . $row['lastname'] . '</Name2>';
                        $theXml .='<Address1>' . $row['shipping_address_1'] . '</Address1>';
                        $theXml .='<Address2>' . $row['shipping_address_2'] . '</Address2>';
                        $theXml .='<Address3>' . $row['shipping_city'] . '</Address3>';
                        $theXml .='<Postalcode>' . $row['shipping_postcode'] . '</Postalcode>';
                        $theXml .='<Countrycode>' . $row['shipping_country'] . '</Countrycode>';
                        $theXml .='<Value>' . $total_val . '</Value>';
                        $theXml .='<Phone>' . $row['telephone'] . '</Phone>';
                        $theXml .='<Email>' . $row['email'] . '</Email>';
                        $theXml .='<ShippingMethod>PPLUS</ShippingMethod>';
                        $theXml .='</Parcel>';
                    }
                }
            }

            $theXml .=' </Shipment>';

            $RQ = new HTTP_Request('https://www.customer-pages.com/test/cgi-bin/ProcessXML.dll/parcel');
            $RQ->setMethod(HTTP_REQUEST_METHOD_POST);
            $RQ->addRawPostData($theXml);
            $RQ->sendRequest();

            $response = $RQ->getResponseBody();
            $fp = fopen('abcmail.xml', 'w');
            fwrite($fp, $response);
            fclose($fp);


            if (stripos($response, 'Shipmentno') !== false) {
                $orderID = explode(",", $orderid);

                foreach ($orderID as $id) {
                    $order_status_id = $this->model_sale_manageorder->getOrderStatusId('Processed');
                    $this->model_sale_manageorder->update($id, $order_status_id);
                }
                $this->session->data['success'] = $this->language->get('text_success');
            } else {

                $this->session->data['warning'] = 'Failed to ship the selected orders';
            }


//$this->model_sale_manageorder->exportOrder(substr($selectid,0,strlen($selectid)-1));



            $url = '';

            if (isset($this->request->get['pre_page'])) {
                $url .= '&pre_page=' . $this->request->get['pre_page'];
            }

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_invoice_id'])) {
                $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . $this->request->get['filter_email'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . $this->request->get['filter_customer'];
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }

            if (isset($this->request->get['filter_total'])) {
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        if (!isset($this->error['warning']))
            $this->error['warning'] = $this->language->get('error_noselected');
        $this->getList();
    }

    public function addhistory() {
        $this->load->language('sale/order');
        $this->load->language('sale/manageorder');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/order');

        if (isset($this->request->post['selected']) && ($this->validateDelete())) {
            $this->request->post['notify'] = isset($this->request->post['notify']) ? $this->request->post['notify'] : 0;
            if (is_array($this->request->post['selected'])) {
                $selectid = $this->request->post['selected'];
            } else {
                $selectid = explode('/', $this->request->post['selected']);
            }
            foreach ($selectid as $order_id) {
                $this->model_sale_order->addOrderHistory($order_id, $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['pre_page'])) {
                $url .= '&pre_page=' . $this->request->get['pre_page'];
            }

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . $this->request->get['filter_customer'];
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }

            if (isset($this->request->get['filter_total'])) {
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        if (!isset($this->error['warning']))
            $this->error['warning'] = $this->language->get('error_noselected');
        $this->getList();
    }

    private function getList() {
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_invoice_id'])) {
            $filter_invoice_id = $this->request->get['filter_invoice_id'];
        } else {
            $filter_invoice_id = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = null;
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = null;
        }

        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = null;
        }

        if (isset($this->request->get['pre_page'])) {
            $pre_page = $this->request->get['pre_page'];
        } else {
            $pre_page = $this->config->get('config_admin_limit');
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_invoice_id'])) {
            $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }



        $this->data['pendingOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount(1);
        if (empty($this->data['pendingOrderCount'])) {
            $this->data['pendingOrderCount'] = 0;
        }

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('processing');
        $this->data['processingOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount($order_status_id);
        if (empty($this->data['processingOrderCount'])) {
            $this->data['processingOrderCount'] = 0;
        }

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('orders picked');
        $this->data['orderspickedOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount($order_status_id);
        if (empty($this->data['orderspickedOrderCount'])) {
            $this->data['orderspickedOrderCount'] = 0;
        }

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('processed');
        $this->data['processedOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount($order_status_id);
        if (empty($this->data['processedOrderCount'])) {
            $this->data['processedOrderCount'] = 0;
        }

        $order_status_id = $this->model_sale_manageorder->getOrderStatusId('shipped');
        $this->data['shippedOrderCount'] = $this->model_sale_manageorder->getOrderStatusCount($order_status_id);
        if (empty($this->data['shippedOrderCount'])) {
            $this->data['shippedOrderCount'] = 0;
        }


        $this->data['show50'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=50' . $url, 'SSL');
        $this->data['show200'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=200' . $url, 'SSL');
        $this->data['show500'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=500' . $url, 'SSL');
        $this->data['show1000'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=1000' . $url, 'SSL');

        if (isset($this->request->get['pre_page'])) {
            $url .= '&pre_page=' . $this->request->get['pre_page'];
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->data['print_popup'] = $this->url->link('sale/manageorder/printpopup', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['pickedorder_popup'] = $this->url->link('sale/manageorder/pickedorderpopup', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['export_popup'] = $this->url->link('sale/manageorder/exportpopup', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['print_labels'] = $this->url->link('sale/manageorder/printlabels', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['update_status'] = $this->url->link('sale/manageorder/updatestatus', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['export_select'] = $this->url->link('sale/manageorder/export', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['update_select'] = $this->url->link('sale/manageorder/updatehistory', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], 'SSL');
//version
        $version = $this->model_sale_manageorder->versiontoint();
        if ($version >= 1520) {
            $this->data['insert'] = $this->url->link('sale/order/insert', 'token=' . $this->session->data['token'], 'SSL');
            $this->data['button_insert'] = $this->language->get('button_insert');
        }
        $this->data['delete'] = $this->url->link('sale/manageorder/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');


        $this->data['orders'] = array();

        $data = array(
            'filter_order_id' => $filter_order_id,
            'filter_invoice_id' => $filter_invoice_id,
            'filter_customer' => $filter_customer,
            'filter_email' => $filter_email,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_total' => $filter_total,
            'filter_date_added' => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $pre_page,
            'limit' => $pre_page
        );

        $order_total = $this->model_sale_manageorder->getTotalOrders($data);
        $orders_data = array();
        $results = $this->model_sale_manageorder->getOrders($data);
        $this->load->model('sale/order');
        $this->load->model('sale/affiliate');
        $this->load->model('sale/customer');

        foreach ($results as $result) {
            $action = array();
            $orderinfo = $this->model_sale_order->getOrder($result['order_id']);
            $orderproducts = $this->model_sale_order->getOrderProducts($result['order_id']);
            $orderproductstr = '';
            foreach ($orderproducts as $orderproduct) {
                $orderproductstr .= '<br />' . $orderproduct['model'] . '*' . $orderproduct['quantity'];
            }

            $action[] = array(
                'text' => $this->language->get('text_view'),
                'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
            );

            if ($version >= 1520 && (strtotime($result['date_added']) > strtotime('-' . (int) $this->config->get('config_order_edit') . ' day'))) {
                $action[] = array(
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->url->link('sale/order/update', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
                );
            }

            $orders_data[] = array(
                'order_id' => $result['order_id'],
                'invoice_id' => $result['invoice_id'],
                'customer' => $result['customer'],
                'email' => $result['email'],
                'delivery_status' => $result['delivery_status'],
                'status' => $result['status'],
                'payment_method' => $result['payment_method'],
                'sub_total' => $this->currency->format($result['sub_total'], $result['currency_code'], $result['currency_value']),
                'store_credit' => $this->currency->format($result['store_credit'], $result['currency_code'], $result['currency_value']),
                'reward' => $orderinfo['customer_id'] ? $orderinfo['reward'] : 0,
                'reward_total' => $this->model_sale_customer->getTotalCustomerRewardsByOrderId($result['order_id']),
                'affiliate' => $orderinfo['affiliate_id'],
                'commission' => $this->currency->format($orderinfo['commission'], $orderinfo['currency_code'], $orderinfo['currency_value']),
                'commission_total' => $this->model_sale_affiliate->getTotalTransactionsByOrderId($result['order_id']),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                'selected' => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $this->data['orders'] = $orders_data;

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['text_abandoned_orders'] = ($this->language->get('text_abandoned_orders') == 'text_abandoned_orders') ? $this->language->get('text_missing') : $this->language->get('text_abandoned_orders');

        $this->data['column_order_id'] = $this->language->get('column_order_id');
        $this->data['column_invoice_id'] = $this->language->get('column_invoice_id');
        $this->data['column_customer'] = $this->language->get('column_customer');
        $this->data['column_email'] = $this->language->get('column_email');
        $this->data['column_delivery_status'] = $this->language->get('column_delivery_status');
        $this->data['button_delivery_status'] = $this->language->get('button_delivery_status');
        $this->data['column_status'] = $this->language->get('column_manage_order_status');
        $this->data['column_payment_method'] = $this->language->get('column_payment_method');
        $this->data['column_sub_total'] = $this->language->get('column_sub_total');
        $this->data['column_store_credit'] = $this->language->get('column_store_credit');
        $this->data['column_total'] = $this->language->get('column_total');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_date_modified'] = $this->language->get('column_date_modified');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_export'] = $this->language->get('button_export');
        $this->data['button_updateorder'] = $this->language->get('button_updateorder');
        $this->data['button_invoice'] = $this->language->get('button_invoice');
        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_filter'] = $this->language->get('button_filter');

        $this->data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }


        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];

            unset($this->session->data['warning']);
        } else {
            $this->data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['pre_page'])) {
            $url .= '&pre_page=' . $this->request->get['pre_page'];
        }

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_invoice_id'])) {
            $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
        }


        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if ($order == 'ASC') {
            $url .= '&order=' . 'DESC';
        } else {
            $url .= '&order=' . 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_order'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
        $this->data['sort_invoice'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=invoice_id' . $url, 'SSL');
        $this->data['sort_customer'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
        $this->data['sort_email'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.email' . $url, 'SSL');
        $this->data['sort_status'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $this->data['sort_total'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
        $this->data['sort_date_added'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
        $this->data['sort_date_modified'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');


        $url = '';

        if (isset($this->request->get['pre_page'])) {
            $url .= '&pre_page=' . $this->request->get['pre_page'];
        }

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_invoice_id'])) {
            $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }


        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $pre_page;
        $pagination->text = $this->language->get('text_showperpage');
        $pagination->url = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_order_id'] = $filter_order_id;
        $this->data['filter_invoice_id'] = $filter_invoice_id;
        $this->data['filter_customer'] = $filter_customer;
        $this->data['filter_email'] = $filter_email;
        $this->data['filter_order_status_id'] = $filter_order_status_id;
        $this->data['filter_total'] = $filter_total;
        $this->data['filter_date_added'] = $filter_date_added;
        $this->data['filter_date_modified'] = $filter_date_modified;

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->template = 'sale/manageorder_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sale/manageorder')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function updatehistory() {

        $this->language->load('sale/order');
        $this->language->load('sale/manageorder');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/manageorder');
        $this->load->model('sale/order');

        if (isset($this->request->post['selected']) && ($this->validateDelete())) {

            $url = '';

            if (isset($this->request->get['pre_page'])) {
                $url .= '&pre_page=' . $this->request->get['pre_page'];
            }

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_invoice_id'])) {
                $url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . $this->request->get['filter_email'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . $this->request->get['filter_customer'];
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }

            if (isset($this->request->get['filter_total'])) {
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->data['submit_url'] = $this->url->link('sale/manageorder/addhistory', 'token=' . $this->session->data['token'] . $url, 'SSL');

            $selectid = '';
            foreach ($this->request->post['selected'] as $order_id) {
                $selectid.=$order_id . '/';
            }

            $this->data['order_status'] = 0;

            if (count($this->request->post['selected']) == 1) {
                $this->data['order_status'] = $this->model_sale_manageorder->getOrderStatusById($this->request->post['selected'][0]);
            }

            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['entry_order_status'] = $this->language->get('entry_order_status');
            $this->data['entry_notify'] = $this->language->get('entry_notify');
            $this->data['entry_comment'] = $this->language->get('entry_comment');

            $this->data['button_add_history'] = $this->language->get('button_add_history');

            $this->data['tab_order_history'] = $this->language->get('tab_order_history');

            $this->data['token'] = $this->session->data['token'];
            $this->data['order_selectid'] = substr($selectid, 0, strlen($selectid) - 1);
            $this->data['comments'] = $this->model_sale_manageorder->getOrderStatusComment();

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
                'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'], 'SSL'),
                'separator' => ' :: '
            );

            $this->load->model('localisation/order_status');

            $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


            $this->template = 'sale/manageorder_history.tpl';
            $this->children = array(
                'common/header',
                'common/footer',
            );

            $this->response->setOutput($this->render());
        } else {
            if (!isset($this->error['warning']))
                $this->error['warning'] = $this->language->get('error_noselected');
            $this->getList();
        }
    }

    public function ini() {
        if (trim($this->request->get['route']) == 'sale/order') {
            $this->request->get['route'] = 'sale/manage';
            return $this->forward($this->request->get['route']);
        }
    }

}

?>