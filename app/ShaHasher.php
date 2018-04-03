<?php

namespace App;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class ShaHasher implements HasherContract {

    public function info($hashedValue) {
        return sha1($value);
    }

    public function make($value, array $options = array()) {
        return sha1($value);
    }

    public function check($value, $hashedValue, array $options = array()) {
        return $this->make($value) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = array()) {
        return false;
    }

}
