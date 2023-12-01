<?php

namespace App\Common\Validator;

class Validator {
	static function validate ($array, $rules): string {
		$errors = [];
		foreach ($rules as $key => $rule) {
			if (!array_key_exists($key, $array)) {
				$array[$key] = '';
			}
			$value = $array[$key];
			$rules = explode('|', $rule);
			foreach ($rules as $rule) {
				if ($rule === 'required') {
					if (empty($value)) {
						$errors[$key][] = "must not be empty";
					}
				} else if (str_starts_with($rule, 'min')) {
					$min = explode(':', $rule)[1];
					if (strlen($value) < $min) {
						$errors[$key][] = "must be at least $min characters";
					}
				} else if (str_starts_with($rule, 'max')) {
					$max = explode(':', $rule)[1];
					if (strlen($value) > $max) {
						$errors[$key][] = "must be at most $max characters";
					}
				} else if ($rule === 'email') {
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
