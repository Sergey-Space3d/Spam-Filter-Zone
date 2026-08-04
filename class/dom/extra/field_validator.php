<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

class FieldValidator
{
    protected $m_errors = array();
    
    public $MinPassword = 7;
    public $MaxPassword = 32;
    public $PasswordWhitespaceAllowed = false;
    
    /** The the validation errors */
    public function get_errors() { return $this->m_errors; }
    
    /** Get named error */
    public function get_error($field_name)
    {
        return isset($this->m_errors[$field_name]) ? $this->m_errors[$field_name] : null;
    }
    
    /** Returns true if the password is valid */
    public function is_password($password1, $password2 = null, $field_name = 'Password')
    {
        if ($password2 !== null && $password1 != $password2)
        {
            $this->m_errors[$field_name] = "{$field_name} mismatch";
            return false;
        }
        
        $len = strlen($password1);
        
        if ($len < $this->MinPassword || $len > $this->MaxPassword)
        {
            $this->m_errors[$field_name] = "{$field_name} should be between {$this->MinPassword} and {$this->MaxPassword} characters";
            return false;
        }
        
        if (!$this->PasswordWhitespaceAllowed && preg_match("/\\s/", $password1))
        {
            $this->m_errors[$field_name] = "Whitespace is not allowed in password";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if domain's name is valid */
    public function is_domain($domain, $field_name = 'Domain')
    {
        $domain = trim(strtolower($domain));
        
        if (!$domain)
        {
            $this->m_errors[$field_name] = "Invalid domain's name";
            return false;
        }
        
        $is_domain = 
        @preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domain) &&
        @preg_match('/^.{1,253}$/', $domain) &&
        @preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domain);
        
        if (!$is_domain)
        {
            $this->m_errors[$field_name] = "'{$domain}' domain's name is not valid";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the email is valid */
    public function is_email($email, $field_name = 'Email address')
    {
        $email = trim(strtolower($email));
        $n_chars = strlen($email);
        
        if ($n_chars < 6 || // consider minimum 6 characters (a@b.tv)
            ctype_alpha($email[0]) == false || // first char to be alpha
            ctype_alnum($email[$n_chars - 1]) == false) // last char to be alphanum
        {
            $this->m_errors[$field_name] = "{$field_name} is invalid";
            return false;
        }
        
        // Check the pattern
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i';
        
        if (preg_match($regex, $email) == false)
        {
            $this->m_errors[$field_name] = "{$field_name} '{$email}' is invalid";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the phone is valid (US numbers only) */
    public function is_phone($phone, $field_name = 'US phone number')
    {
        // Eliminate every char except 0-9
        $justNums = preg_replace("/[^0-9]/", '', $phone);
        
        // Eliminate leading 1 if its there
        if (strlen($justNums) == 11)
        {
            $justNums = preg_replace("/^1/", '', $justNums);
        }
        
        // If we have 10 digits left, it's probably valid.
        if (strlen($justNums) != 10)
        {
            $this->m_errors[$field_name] = "{$field_name} should be 10 digits";
            return false;
        }
        
        return true;
    }
    
    /** Normalize phone string */
    public static function normalize_phone($phone)
    {
        // Eliminate every char except 0-9
        $phone = preg_replace("/[^0-9]/", '', $phone);
        
        // Eliminate leading 1 if its there
        if (strlen($phone) == 11)
        {
            $phone = preg_replace("/^1/", '', $phone);
        }
        
        // If we have 10 digits left, it's probably valid.
        if (strlen($phone) == 10)
        {
            $phone = '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, -4);
        }
        
        return $phone;
    }
}
?>