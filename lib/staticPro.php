<?php
    class StaticPro {
        // Error code defination
        const E_VERIFICATION_FAILED_GENERAL			= 0x10000101;
        const E_VERIFICATION_FAILED_SIGNATURE		= 0x10000102;
        const E_VERIFICATION_FAILED_NBF_IAT			= 0x10000103;
        const E_VERIFICATION_FAILED_EXPIRED			= 0x10000104;
        const E_VERIFICATION_FAILED_AUDIENCE		= 0x10000105;
        const E_VERIFICATION_FAILED_TAINTED_PAYLOAD	= 0x10000106;
        const E_VERIFICATION_FAILED_PAYLOAD_FORMAT	= 0x10000107;

        const ERROR_TYPE_NONE 		= 0x00;
        const ERROR_TYPE_TEMPORARY 	= 0x01;
        const ERROR_TYPE_PERMANENT 	= 0x02;

        /**
         * available statuses for the purchase class (prcStatus)
         */
        const STATUS_NEW 									= 1;	//0x01; //new purchase status
        const STATUS_OPENED 								= 2;	//OK //0x02; // specific to Model_Purchase_Card purchases (after preauthorization) and Model_Purchase_Cash
        const STATUS_PAID 									= 3;	//OK //0x03; // capturate (card)
        const STATUS_CANCELED 								= 4;	//0x04; // void
        const STATUS_CONFIRMED 								= 5;	//OK //0x05; //confirmed status (after IPN)
        const STATUS_PENDING 								= 6;	//0x06; //pending status
        const STATUS_SCHEDULED 								= 7;	//0x07; //scheduled status, specific to Model_Purchase_Sms_Online / Model_Purchase_Sms_Offline
        const STATUS_CREDIT 								= 8;	//0x08; //specific status to a capture & refund state
        const STATUS_CHARGEBACK_INIT 						= 9;	//0x09; //status specific to chargeback initialization
        const STATUS_CHARGEBACK_ACCEPT 						= 10;	//0x0a; //status specific when chargeback has been accepted
        const STATUS_ERROR 									= 11;	//0x0b; // error status
        const STATUS_DECLINED 								= 12;	//0x0c; // declined status
        const STATUS_FRAUD 									= 13;	//0x0d; // fraud status
        const STATUS_PENDING_AUTH 							= 14;	//0x0e; //specific status to authorization pending, awaiting acceptance (verify) | payment in review
        const STATUS_3D_AUTH 								= 15;	//0x0f; //3D authorized status, speficic to Model_Purchase_Card
        const STATUS_CHARGEBACK_REPRESENTMENT 				= 16;	//0x10;
        const STATUS_REVERSED 								= 17;	//0x11; //reversed status
        const STATUS_PENDING_ANY 							= 18;	//0x12; //dummy status
        const STATUS_PROGRAMMED_RECURRENT_PAYMENT 			= 19;	//0x13; //specific to recurrent card purchases
        const STATUS_CANCELED_PROGRAMMED_RECURRENT_PAYMENT 	= 20;	//0x14; //specific to cancelled recurrent card purchases
        const STATUS_TRIAL_PENDING							= 21;	//0x15; //specific to Model_Purchase_Sms_Online; wait for ACTON_TRIAL IPN to start trial period
        const STATUS_TRIAL									= 22;	//0x16; //specific to Model_Purchase_Sms_Online; trial period has started
        const STATUS_EXPIRED								= 23;	//0x17; //cancel a not payed purchase 
    }
?>