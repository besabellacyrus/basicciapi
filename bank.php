<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Added by Cyrus
 * Controller for Spree Sprint 3.
 */

class Bank extends CI_Controller {


    function __construct() {
        parent::__construct();
        $this->load->model('rebate_model', 'bank');
    }


    // sai.
    //api for grabbing bank account details jan 7 2016

    public function setup() {
        $entry = $this->input->post('data');

        if (!empty($entry)) {

            $json_data = json_decode($entry);

            if (isset($json_data->user_id) && isset($json_data->baccount_number)) {

                $data = array(
                    'user_id' => $json_data->user_id,
                    'baccount_number' => $json_data->baccount_number
                );

                // validate data first before adding to the database
                if($this->bank->validateAccountDetails(array($json_data->user_id, $json_data->baccount_number))) {
                    $response['code']     = 401;
                    $response['message']  = 'duplicate bank account';

                } else {

                    $res = $this->bank->insertBankAccountDetails($data);
                    if ($res) {
                        $response['code']     = 200;
                        $response['message']  = 'Success';

                    } else {

                        $response['code']     = 402;
                        $response['message']  = 'Insert Failed!';
                    }

                }

            } else {

                $response['code']     = 401;
                $response['message']  = 'Invalid Parameters';
            }

        } else {

            $response['code']     = 400;
            $response['message']  = 'Empty parameters';

        }

        echo json_encode($response);
    }

// uploading image if the user uses a coupon with a rebate

    public function uploadReceipt() {
        $entry = $this->input->post('data');
        $response = '';

        if (isset($_FILES['receipt']) && !empty($_FILES['receipt'])) {

            $json_data = json_decode($entry);

            if (isset($json_data->acctid) && isset($json_data->couponid) ) {

                $this->bank->uploadReceipt('receipt', $json_data->acctid, $json_data->couponid);

                $response['code'] = 200;
                $response['message'] = 'Success';

            } else {

                $response['code'] = 400;
                $response['message'] = 'Account ID or Coupon ID missing.';
            }

        } else {

            $response['code'] = 400;
            $response['message'] = 'Invalid parameter / missing.';
        }

        echo json_encode($response);
    }


    public function withdrawRequest() {
        $entry = $this->input->post('data');
        $response = '';

        if (!empty($entry)) {

            $json_data = json_decode($entry);

            if (isset($json_data->acctid)) {

                 if ( $this->bank->withdrawRequest($json_data->acctid)) {

                     $response['code'] = 200;
                     $response['withdraw_status'] = 'Proccessing';

                 } else {
                     $response['code'] = 400;
                     $response['withdraw_status'] = 'Withdraw failed/Invalid parameters';
                 }

            } else {
                $response['code'] = 400;
                $response['message'] = 'account id missing';
            }

        } else {
            $response['code'] = 400;
            $response['message'] = 'Missing parameters';
        }

        echo json_encode($response);
    }


    public function myWallet() {

        $entry = $this->input->post('data');

        if(!empty($entry)) {
            $json_data = json_decode($entry);

            if(!empty($json_data)) {

                $response['bank_details'] = $this->bank->getBankDetails($json_data->acctid);

                    if($response['bank_details']) {
                        $response['code'] = 200;
                        $response['message'] = 'Success.';

                    } else {
                        $response['code'] = 400;
                        $response['message'] = 'No results found.';
                    }

            } else {
                $response['code'] = 400;
                $response['message'] = 'Missing Parameter/ Incorrect.';
            }

        } else {
            $response['code'] = 401;
            $response['message'] = 'Empty Array';
        }

    echo json_encode($response);

    }




}
