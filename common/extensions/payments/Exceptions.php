<?php

//! NAMESPACES DER ARE U?????
class PaymentError extends Exception{}
// Got less params from caller than we need to continue
class RequestError extends PaymentError{}
// Cant verify request signature
class SignatureError extends PaymentError{}
// Means we have all or some segments in
// state when we can not continue execution w/o consequencses
class WrongOrderStateError extends PaymentError{}
// Segment refund failed
class RefundError extends PaymentError{}
