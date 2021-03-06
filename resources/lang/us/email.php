<?php

return [
    'id'               => 'ID',
    'sort'             => 'Sort',
    'status'           => 'Status',
    'smtp_host'        => 'SMTP host',
    'smtp_user'        => 'SMTP user',
    'smtp_password'    => 'SMTP password',
    'smtp_security'    => 'SMTP security',
    'smtp_port'        => 'SMTP port',
    'smtp_load_config' => 'SMTP load config',
    'smtp_load_config_file' => 'Load file config',
    'smtp_load_config_database' => 'Load from database',
    'smtp_name'        => 'Email name',
    'smtp_from'        => 'Send email from',
    'admin'            => [
        'title'          => 'Config Email',
        'create_success' => 'Create new item success!',
        'edit_success'   => 'Edit item success!',
        'list'           => 'Config email list',
        'field'          => 'Field',
        'value'          => 'Value',
        'config_mode'    => 'Config mode',
        'config_smtp'    => 'Config SMTP',
        'status'         => 'Status',
        'action'         => 'Action',
        'edit'           => 'Edit',
        'export'         => 'Export',
        'delete'         => 'Delete',
        'refresh'        => 'Refresh',
        'result_item'    => 'Showing <b>:item_from</b> to <b>:item_to</b> of <b>:item_total</b> items</b>',
        'sort'           => 'Sort',
        'search'         => 'Search',
        'add_new'        => 'Add new',
        'add_new_title'  => 'Add config',
        'add_new_des'    => 'Create a new config',
        'smtp_mode'      => 'Use SMTP',
        'smtp_port'      => 'Port SMTP',
        'smtp_security'  => 'Security SMTP',
        'smtp_password'  => 'Password SMTP',
        'smtp_user'      => 'Tài khoản SMTP',
        'smtp_host'      => 'Server SMTP',
        'help_note'      => '<span class="text-red">(*)</span>: Emails will not be sent directly, but through a queue. You need to set up "artisan schedule: run" first, details <a href="https://black-cart.org/docs/'.config('BlackCart.version').'/email.html">HERE</a>',


    ],
    'email_action'     => [
        'customer_verify' => 'Gửi email xác thực tài khoản',
        'order_success_to_cutomer_pdf' => 'Successfully sent orders to customers with PDF',
        'manager'                  => 'Email action manager',
        'type'                     => 'Action type',
        'mode'                     => 'Action mode',
        'sort'                     => 'Action sort',
        'order_success_to_admin'   => 'Send order success to admin',
        'order_success_to_cutomer' => 'Send order success to customer',
        'forgot_password'          => 'Send email forgot',
        'welcome_customer'         => 'Send email welcome',
        'contact_to_customer'      => 'Send email contact to customer',
        'contact_to_admin'         => 'Send email contact to admin',
        'email_action_mode'        => 'ON/OFF send mail',
        'email_action_queue'       => 'Use send mail queue <span class="text-red">(*)</span>',
        'email_action_smtp_mode'   => 'SMTP mode',
        'config_smtp'              => 'Config SMTP',
        'other'                    => 'Other',
    ],
    'contact_to_admin' => [
        'mean_title' => '{{$title}} (Title form email)',
        'mean_name' => '{{$name}} (User name)',
        'mean_email' => '{{$email}} (User email)',
        'mean_phone' => '{{$phone}} (User phone)',
        'mean_content' => '{{$content}} (Email Content)',
    ],
    'welcome_customer' => [
        'title' => 'Welcome!',
        'mean_title' => '{{$title}} (Title form email)',
        'mean_first_name' => '{{$first_name}} (Customer first name)',
        'mean_last_name' => '{{$last_name}} (Customer last name)',
        'mean_email' => '{{$email}} (Customer email)',
        'mean_phone' => '{{$phone}} (Customer phone)',
        'mean_password' => '{{$password}} (Customer password)',
        'mean_address1' => '{{$address1}} (Customer Province)',
        'mean_address2' => '{{$address2}} (Customer District)',
        'mean_address3' => '{{$address3}} (Customer Address)',
        'mean_country' => '{{$country}} (Country customer)',
    ],
    'customer_verify' => [
        'mean_title' => '{{$title}} (Title form email)',
        'mean_reason_sednmail' => '{{$reason_sednmail}} (Reasion send email)',
        'mean_note_sendmail' => '{{$note_sendmail}} (Email notes)',
        'mean_note_access_link' => '{{$note_access_link}} (Access link description)',
        'mean_url_verify' => '{{$url_verify}} (URL to verify)',
        'mean_button' => '{{$button}} (Button to verify)',
    ],
    'forgot_password'  => [
        'title'           => 'Hello!',
        'reset_button'    => 'Reset password',
        'reason_sendmail' => 'You are receiving this email because we received a password reset request for your account.',
        'note_sendmail'           => 'This password reset link will expire in 60 minutes.<br><br>If you did not request a password reset, no further action is required.<br><br>Regards,<br>:site_admin',
        'note_access_link'        => 'If you’re having trouble clicking the ":reset_button" button, copy and paste the URL below into your web browser:',
        'mean_title' => '{{$title}} (Title of form email)',
        'mean_reason_sednmail' => '{{$reason_sednmail}} (Reasion send email)',
        'mean_note_sendmail' => '{{$note_sendmail}} (Email notes)',
        'mean_note_access_link' => '{{$note_access_link}} (Access link description)',
        'mean_reset_link' => '{{$reset_link}} (Link reset)',
        'mean_reset_button' => '{{$reset_button}} (Button reset)',
    ],
    'order'            => [
        'title_1'      => 'Hi! Website :website has new order!',
        'order_id'     => 'Order ID',
        'toname'       => 'Customer name',
        'address'      => 'Address',
        'phone'        => 'Phone',
        'note'         => 'Note',
        'order_detail' => 'Order detail',
        'image'        => 'Image',
        'attribute'    => 'Attribute',
        'sort'         => 'No.',
        'sku'          => 'SKU',
        'price'        => 'Price',
        'name'         => 'Name',
        'qty'          => 'Qty',
        'total'        => 'Total',
        'sub_total'    => 'Sub total',
        'shipping_fee' => 'Shipping fee',
        'discount'     => 'Discount',
        'order_total'  => 'Order total',
        'mean_title'   => '{{$title}} (Title of order)',
        'mean_logo'    => '{{$logo}} (Logo of store)',
        'mean_orderID' => '{{$orderID}} (Order number)',
        'mean_toname'  => '{{$toname}} (Customer Name)',
        'mean_address' => '{{$address}} (Shipping to this address)',
        'mean_email' => '{{$email}} (Customer email)',
        'mean_phone' => '{{$phone}} (Customer phone)',
        'mean_comment' => '{{$comment}} (Customer notes)',
        'mean_orderDetail' => '{{$orderDetail}} (Customer cart information)',
        'mean_subtotal' => '{{$subtotal}} (Subtotal)',
        'mean_shipping_fee' => '{{$shipping}} (Shipping fee)',
        'mean_discount' => '{{$discount}} (Discount)',
        'mean_total' => '{{$total}} (Total)',
        'mean_invoice_date' => '{{$invoice_date}} (Date export invoice)',
        'mean_currency' => '{{$currency}} (Currency)',
        'mean_payment' => '{{$paymentMethod}} (Payment method)',
        'mean_shipping' => '{{$shippingMethod}} (Shipping method)',
        'mean_tax' => '{{$tax}} (Tax charge)',
    ],
];