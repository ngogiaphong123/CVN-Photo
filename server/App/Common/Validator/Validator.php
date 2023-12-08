<?php

namespace App\Common\Validator;

use DateTime;

class Validator {
	static function validateDate ($date, $format = 'Y-m-d H:i:s'): bool {
		$date = explode('.', $date)[0];
		$dateTime = DateTime::createFromFormat($format, $date);
		return $dateTime->format($format) === $date;
	}
    static function validateInteger(array $data): bool {
        foreach ($data as $key => $value) {
            if (!is_numeric($value) || intval($value) === 0) {
                return false;
            }
        }
        return true;
    }

	static function validate ($array, $rules): string {
		$errors = [];
		foreach ($rules as $key => $rule) {
			if (!array_key_exists($key, $array)) {
				$array[$key] = '';
			}
			$value = $array[$key];
			$ruleComponents = explode('|', $rule);
			foreach ($ruleComponents as $component) {
				if ($component === 'required') {
					if (empty($value)) {
						$errors[$key][] = "must not be empty";
					}
				} else if (str_starts_with($component, 'min')) {
					$min = explode(':', $component)[1];
					if (strlen($value) < $min) {
						$errors[$key][] = "must be at least $min characters";
					}
				} else if (str_starts_with($component, 'max')) {
					$max = explode(':', $component)[1];
					if (strlen($value) > $max) {
						$errors[$key][] = "must be at most $max characters";
					}
				} else if ($component === 'email') {
					if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
						$errors[$key][] = "must be a valid email address";
					}
				}
			}
		}
		$errorsString = '';
		foreach ($errors as $key => $error) {
			$key = ucfirst($key);
			$errorsString .= "$key " . implode(', ', $error) . '; ';
		}
		return rtrim($errorsString, '; ');
	}
}
