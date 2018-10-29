<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;

/**
 * Class for validating mail addresses
 *
 * Class MailValidator
 */
class MailValidationService
{

    /**
     * checks whether a mail address is valid
     *
     * @param $email
     * @return mixed
     */
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

}