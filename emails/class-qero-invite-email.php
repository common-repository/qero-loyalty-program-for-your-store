<?php

class Qero_Invite_Email extends WC_Email {

    function __construct() {

        // Add email ID, title, description, heading, subject
        $this->id                   = 'qero_invite_email';
        $this->title                = __( 'Invite for loyalty', 'qero-for-wp' );
        $this->description          = __( 'This email is received when a customer registers.', 'qero-for-wp' );

        $this->heading              = __( 'Become a loyal customer!', 'qero-for-wp' );
        $this->subject              = __( 'Hello {name}, Now you can earn points with Qero!', 'qero-for-wp' );

        // email template path
        $this->template_html    = 'html/qero-invite-item-email.php';
        $this->template_plain   = 'plain/qero-invite-item-email.php';

        add_action( 'qero_invite_email_notification', array( $this, 'trigger' ) );

        // Call parent constructor
        parent::__construct();

        // Other settings
        $this->template_base = QERO_TEMPLATE_PATH;
        $this->customer_email = true;

    }

    // This function collects the data and sends the email
    function trigger( $user_id ) {

        $this->data = $this->create_object( $user_id );
        $send_email = in_array('customer', $this->data->roles);

        if ( $user_id && $send_email ) {

            if(!empty($this->data->first_name)){
                $this->find[]    = '{name}';
                $this->replace[] = $this->data->first_name;
            }else{
                $this->find[]    = '{name}';
                $this->replace[] = '';
            }
            $this->recipient = $this->data->user_email;

            if ( ! $this->get_recipient() ) {
                return;
            }

            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), [] );
        }
    }

    public static function create_object( $user_id ) {

        return get_userdata( $user_id );

    }

    // return the html content
    function get_content_html() {
        ob_start();
        wc_get_template( $this->template_html, array(
            'data'       => $this->data,
            'email_heading' => $this->get_heading()
        ), 'my-custom-email/', $this->template_base );
        return ob_get_clean();
    }
    // return the plain content
    function get_content_plain() {
        ob_start();
        wc_get_template( $this->template_plain, array(
            'data'       => $this->data,
            'email_heading' => $this->get_heading()
        ), 'my-custom-email/', $this->template_base );
        return ob_get_clean();
    }

    // return the subject
    function get_subject() {

        $order = new WC_order( $this->object->order_id );
        return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->subject ), $this->object );

    }

    // return the email heading
    public function get_heading() {

        $order = new WC_order( $this->object->order_id );
        return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );

    }

    // form fields that are displayed in WooCommerce->Settings->Emails
    function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' 		=> __( 'Enable/Disable', 'qero-for-wp' ),
                'type' 			=> 'checkbox',
                'label' 		=> __( 'Enable this email notification', 'qero-for-wp' ),
                'default' 		=> 'yes'
            ),
            'recipient' => array(
                'title'         => __( 'Recipient', 'qero-for-wp' ),
                'type'          => 'text',
                'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s', 'qero-for-wp' ), get_option( 'admin_email' ) ),
                'default'       => get_option( 'admin_email' )
            ),
            'subject' => array(
                'title' 		=> __( 'Subject', 'qero-for-wp' ),
                'type' 			=> 'text',
                'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'qero-for-wp' ), $this->subject ),
                'placeholder' 	=> '',
                'default' 		=> ''
            ),
            'heading' => array(
                'title' 		=> __( 'Email Heading', 'qero-for-wp' ),
                'type' 			=> 'text',
                'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'qero-for-wp' ), $this->heading ),
                'placeholder' 	=> '',
                'default' 		=> ''
            ),
            'email_type' => array(
                'title' 		=> __( 'Email type', 'qero-for-wp' ),
                'type' 			=> 'select',
                'description' 	=> __( 'Choose which format of email to send.', 'qero-for-wp' ),
                'default' 		=> 'html',
                'class'			=> 'email_type',
                'options'		=> array(
                    'plain'		 	=> __( 'Plain text', 'qero-for-wp' ),
                    'html' 			=> __( 'HTML', 'qero-for-wp' ),
                    'multipart' 	=> __( 'Multipart', 'qero-for-wp' ),
                )
            )
        );
    }

}
return new Qero_Invite_Email();
?>