<?php

namespace zaporylie\Vipps\Tests\Integration\Api;

use zaporylie\Vipps\Exceptions\VippsException;
use zaporylie\Vipps\Model\Payment\Address;
use zaporylie\Vipps\Model\Payment\PaymentShippingDetails;
use zaporylie\Vipps\Model\Payment\TransactionLog;
use zaporylie\Vipps\Model\Payment\TransactionSummary;
use zaporylie\Vipps\Model\Payment\UserDetails;
use zaporylie\Vipps\Tests\Integration\IntegrationTestBase;

/**
 * Class PaymentsTest
 *
 * @package Vipps\Tests\Integration\Api
 */
class PaymentsTest extends IntegrationTestBase
{

    /**
     * @var string
     */
    protected $merchantSerialNumber = 'test_merchant_serial_number';

    /**
     * @var \zaporylie\Vipps\Api\PaymentInterface
     */
    protected $api;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->api = $this->vipps->payment('test_subscription_key', $this->merchantSerialNumber);
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::initiatePayment()
     */
    public function testValidInitiatePayment()
    {
        $this->mockResponse(parent::getResponse([
            'orderId' => 'test_order_id',
            'url' => 'https://www.example.com/vipps'
        ]));

        // Do request.
        $response = $this->api->initiatePayment(
            'test_order_id',
            1200,
            'test_text',
            'https://www.example.com/callback',
            'https://www.example.com/fallback',
            [
                'mobileNumber' => '123',
                'authToken' => '123',
                'consentRemovalPrefix' => '123',
                'isApp' => '123',
                'paymentType' => '123',
                'shippingDetailsPrefix' => '123',
                'refOrderId' => '123',
                'timeStamp' => new \DateTime(),
            ]
        );

        // Assert response.
        $this->assertEquals('test_order_id', $response->getOrderId());
        $this->assertEquals('https://www.example.com/vipps', $response->getURL());
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::initiatePayment()
     */
    public function testInvalidInitiatePayment()
    {
        $this->mockResponse(parent::getErrorResponse());
        $this->expectException(VippsException::class);
        $this->api->initiatePayment(
            'test_client_secret',
            1200,
            'test_text',
            'http://www.example.com/callback',
            'https://www.example.com/fallback'
        );
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::capturePayment()
     */
    public function testValidCapturePayment()
    {
        // Mock response.
        $this->mockResponse(parent::getResponse([
            'orderId' => 'test_order_id',
            'transactionSummary' => [
                'capturedAmount' => 10,
                'remainingAmountToCapture' => 11,
                'refundedAmount' => 12,
                'remainingAmountToRefund' => 13,
            ],
            'transactionInfo' => [
                'amount' => 1200,
                'timeStamp' => '2017-07-31T15:07:37.100Z',
                'status' => 'test_status',
                'transactionId' => 'test_transaction_id',
                'transactionText' => 'test_transaction_text'
            ],
        ]));

        // Do request.
        $response = $this->api->capturePayment(
            'test_order_id',
            'test_text',
            12
        );

        // Assert response.
        $this->assertEquals('test_order_id', $response->getOrderId());
        $this->assertEquals(10, $response->getTransactionSummary()->getCapturedAmount());
        $this->assertEquals(11, $response->getTransactionSummary()->getRemainingAmountToCapture());
        $this->assertEquals(12, $response->getTransactionSummary()->getRefundedAmount());
        $this->assertEquals(13, $response->getTransactionSummary()->getRemainingAmountToRefund());
        $this->assertEquals(1200, $response->getTransactionInfo()->getAmount());
        $this->assertEquals('test_transaction_text', $response->getTransactionInfo()->getTransactionText());
        $this->assertEquals(
            '2017-07-31T15:07:37',
            $response->getTransactionInfo()->getTimeStamp()->format('Y-m-d\TH:i:s')
        );
        $this->assertEquals('test_status', $response->getTransactionInfo()->getStatus());
        $this->assertEquals('test_transaction_id', $response->getTransactionInfo()->getTransactionId());
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::cancelPayment()
     */
    public function testValidCancelPayment()
    {

        // Mock response.
        $this->mockResponse(parent::getResponse([
            'orderId' => 'test_order_id',
            'transactionSummary' => [
                'capturedAmount' => 10,
                'remainingAmountToCapture' => 11,
                'refundedAmount' => 12,
                'remainingAmountToRefund' => 13,
            ],
            'transactionInfo' => [
                'amount' => 1200,
                'timeStamp' => '2017-07-31T15:07:37.100Z',
                'status' => 'test_status',
                'transactionId' => 'test_transaction_id',
                'transactionText' => 'test_transaction_text'
            ],
        ]));

        // Do request.
        $response = $this->api->cancelPayment(
            'test_order_id',
            'test_text'
        );

        // Assert response.
        $this->assertEquals('test_order_id', $response->getOrderId());
        $this->assertEquals(10, $response->getTransactionSummary()->getCapturedAmount());
        $this->assertEquals(11, $response->getTransactionSummary()->getRemainingAmountToCapture());
        $this->assertEquals(12, $response->getTransactionSummary()->getRefundedAmount());
        $this->assertEquals(13, $response->getTransactionSummary()->getRemainingAmountToRefund());
        $this->assertEquals(1200, $response->getTransactionInfo()->getAmount());
        $this->assertEquals('test_transaction_text', $response->getTransactionInfo()->getTransactionText());
        $this->assertEquals(
            '2017-07-31T15:07:37',
            $response->getTransactionInfo()->getTimeStamp()->format('Y-m-d\TH:i:s')
        );
        $this->assertEquals('test_status', $response->getTransactionInfo()->getStatus());
        $this->assertEquals('test_transaction_id', $response->getTransactionInfo()->getTransactionId());
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::refundPayment()
     */
    public function testValidRefundPayment()
    {

        // Mock response.
        $this->mockResponse(parent::getResponse([
            'orderId' => 'test_order_id',
            'transactionSummary' => [
                'capturedAmount' => 10,
                'remainingAmountToCapture' => 11,
                'refundedAmount' => 12,
                'remainingAmountToRefund' => 13,
            ],
            'transactionInfo' => [
                'amount' => 1200,
                'transactionText' => 'test_transaction_text',
                'timeStamp' => '2017-07-31T15:07:37.100Z',
                'status' => 'test_status',
                'transactionId' => 'test_transaction_id',
            ],
        ]));

        // Do request.
        $response = $this->api->refundPayment(
            'test_order_id',
            'test_text',
            12
        );

        // Assert response.
        $this->assertEquals('test_order_id', $response->getOrderId());
        $this->assertEquals(10, $response->getTransactionSummary()->getCapturedAmount());
        $this->assertEquals(11, $response->getTransactionSummary()->getRemainingAmountToCapture());
        $this->assertEquals(12, $response->getTransactionSummary()->getRefundedAmount());
        $this->assertEquals(13, $response->getTransactionSummary()->getRemainingAmountToRefund());
        $this->assertEquals(1200, $response->getTransactionInfo()->getAmount());
        $this->assertEquals('test_transaction_text', $response->getTransactionInfo()->getTransactionText());
        $this->assertEquals(
            '2017-07-31T15:07:37',
            $response->getTransactionInfo()->getTimeStamp()->format('Y-m-d\TH:i:s')
        );
        $this->assertEquals('test_status', $response->getTransactionInfo()->getStatus());
        $this->assertEquals('test_transaction_id', $response->getTransactionInfo()->getTransactionId());
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::getOrderStatus()
     */
    public function testValidGetOrderStatus()
    {

        // Mock response.
        $this->mockResponse(parent::getResponse([
            'orderId' => 'test_order_id',
            'transactionInfo' => [
                'amount' => 1200,
                'timeStamp' => '2017-07-31T15:07:37.100Z',
                'status' => 'test_status',
                'transactionId' => 'test_transaction_id',
            ],
        ]));

        // Do request.
        $response = $this->api->getOrderStatus(
            'test_order_id'
        );

        // Assert response.
        $this->assertEquals('test_order_id', $response->getOrderId());
        $this->assertEquals(1200, $response->getTransactionInfo()->getAmount());
        $this->assertEquals(
            '2017-07-31T15:07:37',
            $response->getTransactionInfo()->getTimeStamp()->format('Y-m-d\TH:i:s')
        );
        $this->assertEquals('test_status', $response->getTransactionInfo()->getStatus());
        $this->assertEquals('test_transaction_id', $response->getTransactionInfo()->getTransactionId());
    }

    /**
     * @covers \zaporylie\Vipps\Api\Payment::getPaymentDetails()
     * @covers \zaporylie\Vipps\Model\Payment\PaymentShippingDetails::getAddress()
     */
    public function testValidGetPaymentDetails()
    {

        // Mock response.
        $this->mockResponse(parent::getResponse([
            'orderId' => 'test_order_id',
            'shippingDetails' => [
                'address' => [
                    'addressLine1' => 'Dronning Eufemias gate 42',
                    'addressLine2' => 'Att: Rune Garborg',
                    'city' => 'Oslo',
                    'country' => 'Norway',
                    'postCode' => '0191'
                ],
                'shippingCost' => 1500,
                'shippingMethod' => 'Posten',
                'shippingMethodId' => 'string'
            ],
            'transactionLogHistory' => [
                [
                    'amount' => 1200,
                    'operation' => 'RESERVE',
                    'operationSuccess' => true,
                    'requestId' => 'test_request_id',
                    'timeStamp' => '2017-07-31T15:07:37.0Z',
                    'transactionId' => 'test_transaction_id',
                    'transactionText' => 'test_transaction_text'
                ],
            ],
            'transactionSummary' => [
                'capturedAmount' => 10,
                'refundedAmount' => 12,
                'remainingAmountToCapture' => 11,
                'remainingAmountToRefund' => 13,
            ],
            'userDetails' => [
                'bankIdVerified' => 'Y',
                'dateOfBirth' => '12-3-1988',
                'email' => 'user@example.com',
                'firstName' => 'Ada',
                'lastName' => 'Lovelace',
                'mobileNumber' => '12345678',
                'ssn' => '12345678901',
                'userId' => 'uiJskNQ6qNN1iwN891uuob=='
            ]
        ]));

        // Do request.
        $response = $this->api->getPaymentDetails(
            'test_order_id'
        );

        // Assert response.
        $this->assertEquals('test_order_id', $response->getOrderId());

        $this->assertInstanceOf(PaymentShippingDetails::class, $response->getShippingDetails());
        $this->assertInstanceOf(Address::class, $response->getShippingDetails()->getAddress());
        $this->assertEquals(
            'Dronning Eufemias gate 42',
            $response->getShippingDetails()->getAddress()->getAddressLine1()
        );
        $this->assertEquals('Att: Rune Garborg', $response->getShippingDetails()->getAddress()->getAddressLine2());
        $this->assertEquals('Oslo', $response->getShippingDetails()->getAddress()->getCity());
        $this->assertEquals('Norway', $response->getShippingDetails()->getAddress()->getCountry());
        $this->assertEquals('0191', $response->getShippingDetails()->getAddress()->getPostCode());
        $this->assertEquals(1500, $response->getShippingDetails()->getShippingCost());
        $this->assertEquals('Posten', $response->getShippingDetails()->getShippingMethod());
        $this->assertEquals('string', $response->getShippingDetails()->getShippingMethodId());

        $this->assertInstanceOf(TransactionSummary::class, $response->getTransactionSummary());
        $this->assertEquals(10, $response->getTransactionSummary()->getCapturedAmount());
        $this->assertEquals(11, $response->getTransactionSummary()->getRemainingAmountToCapture());
        $this->assertEquals(12, $response->getTransactionSummary()->getRefundedAmount());
        $this->assertEquals(13, $response->getTransactionSummary()->getRemainingAmountToRefund());

        $this->assertInstanceOf(TransactionLog::class, $response->getTransactionLogHistory()[0]);
        $this->assertEquals(1200, $response->getTransactionLogHistory()[0]->getAmount());
        $this->assertEquals('RESERVE', $response->getTransactionLogHistory()[0]->getOperation());
        $this->assertEquals(true, $response->getTransactionLogHistory()[0]->getOperationSuccess());
        $this->assertEquals('test_request_id', $response->getTransactionLogHistory()[0]->getRequestId());
        $this->assertEquals(
            '2017-07-31T15:07:37',
            $response->getTransactionLogHistory()[0]->getTimeStamp()->format('Y-m-d\TH:i:s')
        );
        $this->assertEquals('test_transaction_id', $response->getTransactionLogHistory()[0]->getTransactionId());
        $this->assertEquals('test_transaction_text', $response->getTransactionLogHistory()[0]->getTransactionText());

        $this->assertInstanceOf(UserDetails::class, $response->getUserDetails());
        $this->assertEquals('Y', $response->getUserDetails()->getBankIdVerified());
        $this->assertEquals('12-3-1988', $response->getUserDetails()->getDateOfBirth());
        $this->assertEquals('user@example.com', $response->getUserDetails()->getEmail());
        $this->assertEquals('Ada', $response->getUserDetails()->getFirstName());
        $this->assertEquals('Lovelace', $response->getUserDetails()->getLastName());
        $this->assertEquals('12345678', $response->getUserDetails()->getMobileNumber());
        $this->assertEquals('12345678901', $response->getUserDetails()->getSsn());
        $this->assertEquals('uiJskNQ6qNN1iwN891uuob==', $response->getUserDetails()->getUserId());
    }
}
